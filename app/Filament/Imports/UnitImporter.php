<?php

namespace App\Filament\Imports;

use App\Models\Unit;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class UnitImporter extends Importer
{
    protected static ?string $model = Unit::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('title_ar')
                ->label('العنوان (عربي)')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('title_en')
                ->label('العنوان (إنجليزي)')
                ->rules(['max:255']),
            ImportColumn::make('description_ar')
                ->label('الوصف (عربي)')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('description_en')
                ->label('الوصف (إنجليزي)'),
            ImportColumn::make('address')
                ->label('العنوان بالتفصيل')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('price')
                ->label('السعر')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('price_per_m2')
                ->label('سعر المتر')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('offer_type')
                ->label('نوع العرض (sale/rent)')
                ->requiredMapping()
                ->rules(['required', 'in:sale,rent']),
            ImportColumn::make('area')
                ->label('المساحة')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('rooms')
                ->label('الغرف')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('bathrooms')
                ->label('الحمامات')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('garages')
                ->label('الجراجات')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('build_year')
                ->label('سنة البناء'),
            ImportColumn::make('land_area')
                ->label('مساحة الأرض')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('internal_area')
                ->label('المساحة الداخلية')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('is_visible')
                ->label('مرئي للجمهور (1 أو 0)')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('development_status')
                ->label('حالة التطوير (primary/resale)')
                ->rules(['max:255']),

            // البحث عن المدينة باسمها العربي
            ImportColumn::make('city')
                ->label('المدينة')
                ->relationship(lookupColumn: 'name_ar')
                ->requiredMapping()
                ->rules(['required']),

            // البحث عن نوع الوحدة باسمها العربي
            ImportColumn::make('type')
                ->label('نوع العقار')
                ->relationship(lookupColumn: 'name_ar')
                ->requiredMapping()
                ->rules(['required']),

            // البحث عن المجمع السكني (الكمبوند) باسمه
            ImportColumn::make('compound')
                ->label('الكمبوند')
                ->relationship(lookupColumn: 'name_ar'),

            // البحث عن المطور العقاري باسمه
            ImportColumn::make('developer')
                ->label('المطور العقاري')
                ->relationship(lookupColumn: 'name_ar'),

            // البحث عن المالك (البائع) بالإيميل لضمان الدقة
            ImportColumn::make('owner')
                ->label('إيميل المالك')
                ->relationship(lookupColumn: 'email'),

            ImportColumn::make('latitude')
                ->label('خط العرض')
                ->numeric(),
            ImportColumn::make('longitude')
                ->label('خط الطول')
                ->numeric(),
        ];
    }

    public function resolveRecord(): Unit
    {
        return new Unit();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'تم الانتهاء من استيراد الوحدات بنجاح. تم إضافة ' . Number::format($import->successful_rows) . ' وحدة.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' وفشل استيراد ' . Number::format($failedRowsCount) . ' وحدة بسبب أخطاء في البيانات.';
        }

        return $body;
    }
}
