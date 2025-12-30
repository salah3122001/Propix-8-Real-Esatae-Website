<?php

namespace App\Filament\Resources\Developers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DevelopersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\ImageColumn::make('logo')
                    ->label(__('admin.fields.logo'))
                    ->disk('public'),
                TextColumn::make('name_ar')->label(__('admin.fields.name_ar'))->searchable(),
                TextColumn::make('name_en')->label(__('admin.fields.name_en'))->searchable(),
                TextColumn::make('email')->label(__('admin.fields.email'))->searchable(),
                TextColumn::make('status')->label(__('admin.fields.status'))
                    ->badge()
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                    ])
                    ->formatStateUsing(fn($state) => __("admin.fields.statuses.{$state}")),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status_filter')
                    ->label(__('admin.fields.status'))
                    ->options([
                        'active' => __('admin.fields.statuses.active'),
                        'inactive' => __('admin.fields.statuses.inactive'),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data) {
                        if ($data['value']) {
                            $query->where('status', $data['value']);
                        }
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
