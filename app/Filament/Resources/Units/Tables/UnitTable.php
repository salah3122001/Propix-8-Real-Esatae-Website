<?php

namespace App\Filament\Resources\Units\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use App\Filament\Exports\UnitExporter;
use Filament\Actions\BulkAction;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class UnitTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\ImageColumn::make('media.url')
                    ->label(__('admin.fields.image'))
                    ->disk('public')
                    ->limit(1)
                    ->circular(),
                TextColumn::make('title_ar')->label(__('admin.fields.title_ar'))->searchable(['title_ar', 'title_en'])->visible(fn() => app()->getLocale() === 'ar'),
                TextColumn::make('title_en')->label(__('admin.fields.title_en'))->searchable(['title_ar', 'title_en'])->visible(fn() => app()->getLocale() === 'en'),
                TextColumn::make('price')->label(__('admin.fields.price'))->money('EGP')->searchable(),
                TextColumn::make('offer_type')
                    ->label(__('admin.fields.offer_type'))
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'sale' => __('admin.fields.offer_types.sale'),
                        'rent' => __('admin.fields.offer_types.rent'),
                        default => $state,
                    }),
                TextColumn::make('city.name_ar')->label(__('admin.resources.city'))->searchable(['name_ar', 'name_en'])->visible(fn() => app()->getLocale() === 'ar'),
                TextColumn::make('city.name_en')->label(__('admin.resources.city'))->searchable(['name_ar', 'name_en'])->visible(fn() => app()->getLocale() === 'en'),
                TextColumn::make('development_status')
                    ->label(__('admin.fields.development_status'))
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => __('admin.fields.development_statuses.' . $state)),
                TextColumn::make('status')->label(__('admin.fields.status'))
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                        'info' => 'sold',
                        'gray' => 'rented',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => __('admin.fields.statuses.pending'),
                        'approved' => __('admin.fields.statuses.approved'),
                        'rejected' => __('admin.fields.statuses.rejected'),
                        'sold' => __('admin.fields.statuses.sold'),
                        'rented' => __('admin.fields.statuses.rented'),
                        default => $state,
                    })
                    ->searchable(),
                TextColumn::make('sold_at')
                    ->label(__('admin.fields.sold_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('rented_at')
                    ->label(__('admin.fields.rented_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])


            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status_filter')
                    ->label(__('admin.fields.status'))
                    ->options([
                        'pending' => __('admin.fields.statuses.pending'),
                        'approved' => __('admin.fields.statuses.approved'),
                        'rejected' => __('admin.fields.statuses.rejected'),
                        'sold' => __('admin.fields.statuses.sold'),
                        'rented' => __('admin.fields.statuses.rented'),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data) {
                        if ($data['value']) {
                            $query->where('status', $data['value']);
                        }
                    }),
                \Filament\Tables\Filters\SelectFilter::make('offer_type_filter')
                    ->label(__('admin.fields.offer_type'))
                    ->options([
                        'sale' => __('admin.fields.offer_types.sale'),
                        'rent' => __('admin.fields.offer_types.rent'),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data) {
                        if ($data['value']) {
                            $query->where('offer_type', $data['value']);
                        }
                    }),
                \Filament\Tables\Filters\SelectFilter::make('city_filter')
                    ->label(__('admin.resources.city'))
                    ->relationship('city', app()->getLocale() === 'ar' ? 'name_ar' : 'name_en'),
                \Filament\Tables\Filters\SelectFilter::make('unit_type_filter')
                    ->label(__('admin.resources.unit_type'))
                    ->relationship('type', app()->getLocale() === 'ar' ? 'name_ar' : 'name_en'),
                \Filament\Tables\Filters\SelectFilter::make('development_status_filter')
                    ->label(__('admin.fields.development_status'))
                    ->options([
                        'primary' => __('admin.fields.development_statuses.primary'),
                        'resale' => __('admin.fields.development_statuses.resale'),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data) {
                        if ($data['value']) {
                            $query->where('development_status', $data['value']);
                        }
                    }),
            ])

            ->recordActions([
                EditAction::make(),
                Action::make('approve')
                    ->label(__('admin.actions.approve'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->hidden(fn($record) => $record === null || in_array($record->status, ['approved', 'sold', 'rented']))
                    ->action(fn($record) => $record->update(['status' => 'approved'])),
                Action::make('reject')
                    ->label(__('admin.actions.reject'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->hidden(fn($record) => $record === null || in_array($record->status, ['rejected', 'sold', 'rented']))
                    ->action(fn($record) => $record->update(['status' => 'rejected'])),
                Action::make('mark_as_done')
                    ->label(fn($record) => $record->getAttribute('offer_type') === 'sale' ? __('admin.fields.statuses.sold') : __('admin.fields.statuses.rented'))
                    ->icon(fn($record) => $record->getAttribute('offer_type') === 'sale' ? 'heroicon-o-currency-dollar' : 'heroicon-o-key')
                    ->color('info')
                    ->hidden(fn($record) => $record === null || !in_array($record->status, ['approved']))
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->update([
                        'status' => $record->getAttribute('offer_type') === 'sale' ? 'sold' : 'rented',
                        'sold_at' => $record->getAttribute('offer_type') === 'sale' ? now() : $record->sold_at,
                        'rented_at' => $record->getAttribute('offer_type') === 'rent' ? now() : $record->rented_at,
                    ])),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(UnitExporter::class)
                    ->label(__('admin.actions.export' ?? 'Export')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->exporter(UnitExporter::class)
                        ->label(__('admin.actions.export' ?? 'Export')),
                    BulkAction::make('export_pdf')
                        ->label(__('admin.actions.export_pdf' ?? 'Export PDF'))
                        ->icon('heroicon-o-document-arrow-down')
                        ->action(function (Collection $records) {
                            return response()->streamDownload(function () use ($records) {
                                echo Pdf::loadView('pdf.units', ['units' => $records])->output();
                            }, 'units-export-' . now()->format('Y-m-d') . '.pdf');
                        }),
                ]),
            ]);
    }
}
