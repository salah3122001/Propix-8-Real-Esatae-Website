<?php

namespace App\Filament\Widgets;

use App\Models\Unit;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestUnits extends TableWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public function getTableHeading(): string
    {
        return __('admin.navigation_groups.real_estate') . ' - ' . __('admin.widgets.latest_units');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Unit::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title_' . app()->getLocale())
                    ->label(__('admin.fields.title'))
                    ->limit(50),
                Tables\Columns\TextColumn::make('price')
                    ->label(__('admin.fields.price'))
                    // ->money(app()->getLocale() === 'ar' ? 'EGP' : 'EGP') // money might be tricky with locale, simple text for now or confirm
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('admin.fields.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('admin.fields.created_at'))
                    ->dateTime()
                    ->sortable(),
            ]);
    }
}
