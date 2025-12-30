<?php

namespace App\Filament\Exports;

use App\Models\Unit;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class UnitExporter extends Exporter
{
    protected static ?string $model = Unit::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('title_ar'),
            ExportColumn::make('title_en'),
            ExportColumn::make('description_ar'),
            ExportColumn::make('description_en'),
            ExportColumn::make('price'),
            ExportColumn::make('price_per_m2'),
            ExportColumn::make('offer_type'),
            ExportColumn::make('area'),
            ExportColumn::make('rooms'),
            ExportColumn::make('bathrooms'),
            ExportColumn::make('garages'),
            ExportColumn::make('build_year'),
            ExportColumn::make('land_area'),
            ExportColumn::make('internal_area'),
            ExportColumn::make('status'),
            ExportColumn::make('owner_id'),
            ExportColumn::make('city_id'),
            ExportColumn::make('unit_type_id'),
            ExportColumn::make('compound_id'),
            ExportColumn::make('developer_id'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('latitude'),
            ExportColumn::make('longitude'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your unit export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
