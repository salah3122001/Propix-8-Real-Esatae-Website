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
                ->rules(['required', 'max:255'])
                ->example('شقة فاخرة للبيع في المعادي'),
            ImportColumn::make('title_en')
                ->label('العنوان (إنجليزي) (اختياري)')
                ->guess(['العنوان (إنجليزي) (اختياري)', 'العنوان (إنجليزي)', 'title_en'])
                ->rules(['nullable', 'max:255'])
                ->example('Luxury Apartment for Sale in Maadi'),
            ImportColumn::make('description_ar')
                ->label('الوصف (عربي)')
                ->requiredMapping()
                ->rules(['required'])
                ->example('شقة 3 غرف نوم وصالة كبيرة...'),
            ImportColumn::make('description_en')
                ->label('الوصف (إنجليزي) (اختياري)')
                ->guess(['الوصف (إنجليزي) (اختياري)', 'الوصف (إنجليزي)', 'description_en'])
                ->rules(['nullable'])
                ->example('3 Bedroom apartment with large hall...'),
            ImportColumn::make('address')
                ->label('العنوان بالتفصيل')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('15 شارع النصر، المعادي'),
            ImportColumn::make('price')
                ->label('السعر')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer'])
                ->example('5000000'),
            ImportColumn::make('price_per_m2')
                ->label('سعر المتر (اختياري)')
                ->guess(['سعر المتر (اختياري)', 'سعر المتر', 'price_per_m2'])
                ->rules(['nullable', function ($attribute, $value, $fail) {
                    $cleaned = is_string($value) ? preg_replace('/^[\s\p{Zs}\p{Zl}\p{Zp}\x{00a0}]+|[\s\p{Zs}\p{Zl}\p{Zp}\x{00a0}]+$/u', '', $value) : $value;
                    if (blank($cleaned)) return;
                    if (!is_numeric($cleaned)) {
                        $fail('يجب أن يكون الحقل ' . $attribute . ' رقمًا.');
                    }
                }])
                ->example('25000'),
            ImportColumn::make('offer_type')
                ->label('نوع العرض (sale/rent)')
                ->requiredMapping()
                ->rules(['required', 'in:sale,rent'])
                ->example('sale'),
            ImportColumn::make('area')
                ->label('المساحة')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer'])
                ->example('200'),
            ImportColumn::make('rooms')
                ->label('الغرف (اختياري)')
                ->guess(['الغرف (اختياري)', 'الغرف', 'rooms'])
                ->rules(['nullable', function ($attribute, $value, $fail) {
                    $cleaned = is_string($value) ? preg_replace('/^[\s\p{Zs}\p{Zl}\p{Zp}\x{00a0}]+|[\s\p{Zs}\p{Zl}\p{Zp}\x{00a0}]+$/u', '', $value) : $value;
                    if (blank($cleaned)) return;
                    if (filter_var($cleaned, FILTER_VALIDATE_INT) === false) {
                        $fail('يجب أن يكون الحقل ' . $attribute . ' عددًا صحيحًا.');
                    }
                }])
                ->example('3'),
            ImportColumn::make('bathrooms')
                ->label('الحمامات (اختياري)')
                ->guess(['الحمامات (اختياري)', 'الحمامات', 'bathrooms'])
                ->rules(['nullable', function ($attribute, $value, $fail) {
                    $cleaned = is_string($value) ? preg_replace('/^[\s\p{Zs}\p{Zl}\p{Zp}\x{00a0}]+|[\s\p{Zs}\p{Zl}\p{Zp}\x{00a0}]+$/u', '', $value) : $value;
                    if (blank($cleaned)) return;
                    if (filter_var($cleaned, FILTER_VALIDATE_INT) === false) {
                        $fail('يجب أن يكون الحقل ' . $attribute . ' عددًا صحيحًا.');
                    }
                }])
                ->example('2'),
            ImportColumn::make('garages')
                ->label('الجراجات (اختياري)')
                ->guess(['الجراجات (اختياري)', 'الجراجات', 'garages'])
                ->rules(['nullable', function ($attribute, $value, $fail) {
                    $cleaned = is_string($value) ? preg_replace('/^[\s\p{Zs}\p{Zl}\p{Zp}\x{00a0}]+|[\s\p{Zs}\p{Zl}\p{Zp}\x{00a0}]+$/u', '', $value) : $value;
                    if (blank($cleaned)) return;
                    if (filter_var($cleaned, FILTER_VALIDATE_INT) === false) {
                        $fail('يجب أن يكون الحقل ' . $attribute . ' عددًا صحيحًا.');
                    }
                }])
                ->example('1'),
            ImportColumn::make('build_year')
                ->label('سنة البناء (اختياري)')
                ->guess(['سنة البناء (اختياري)', 'سنة البناء', 'build_year'])
                ->rules(['nullable'])
                ->example('2023'),
            ImportColumn::make('land_area')
                ->label('مساحة الأرض (اختياري)')
                ->guess(['مساحة الأرض (اختياري)', 'مساحة الأرض', 'land_area'])
                ->rules(['nullable', function ($attribute, $value, $fail) {
                    // Clean whitespace and check if empty
                    $cleaned = is_string($value) ? preg_replace('/^[\s\p{Zs}\p{Zl}\p{Zp}\x{00a0}]+|[\s\p{Zs}\p{Zl}\p{Zp}\x{00a0}]+$/u', '', $value) : $value;

                    // If value is empty/null after cleaning, allow it
                    if ($cleaned === null || $cleaned === '' || $cleaned === false) {
                        return;
                    }

                    // Validate that it's an integer
                    if (filter_var($cleaned, FILTER_VALIDATE_INT) === false) {
                        $fail('يجب أن يكون الحقل ' . $attribute . ' عددًا صحيحًا.');
                    }
                }])
                ->example('0'),
            ImportColumn::make('internal_area')
                ->label('المساحة الداخلية (اختياري)')
                ->guess(['المساحة الداخلية (اختياري)', 'المساحة الداخلية', 'internal_area'])
                ->rules(['nullable', function ($attribute, $value, $fail) {
                    // Clean whitespace and check if empty
                    $cleaned = is_string($value) ? preg_replace('/^[\s\p{Zs}\p{Zl}\p{Zp}\x{00a0}]+|[\s\p{Zs}\p{Zl}\p{Zp}\x{00a0}]+$/u', '', $value) : $value;

                    // If value is empty/null after cleaning, allow it
                    if ($cleaned === null || $cleaned === '' || $cleaned === false) {
                        return;
                    }

                    // Validate that it's an integer
                    if (filter_var($cleaned, FILTER_VALIDATE_INT) === false) {
                        $fail('يجب أن يكون الحقل ' . $attribute . ' عددًا صحيحًا.');
                    }
                }])
                ->example('180'),
            ImportColumn::make('is_visible')
                ->label('مرئي للجمهور (1 أو 0)')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean'])
                ->example('1'),
            ImportColumn::make('development_status')
                ->label('حالة التطوير (primary/resale) (اختياري)')
                ->guess(['حالة التطوير (primary/resale) (اختياري)', 'حالة التطوير', 'development_status'])
                ->rules(['nullable', 'max:255'])
                ->example('primary'),
            ImportColumn::make('status')
                ->label('الحالة (approved/pending/rejected) (اختياري)')
                ->guess(['الحالة (approved/pending/rejected) (اختياري)', 'الحالة', 'status'])
                ->rules(['nullable', 'in:approved,pending,rejected'])
                ->example('approved'),

            // البحث عن المدينة باسمها العربي
            ImportColumn::make('city')
                ->label('المدينة')
                ->relationship(resolveUsing: 'name_ar')
                ->requiredMapping()
                ->rules(['required'])
                ->example('القاهرة'),

            // البحث عن نوع الوحدة باسمها العربي
            ImportColumn::make('type')
                ->label('نوع العقار')
                ->relationship(resolveUsing: 'name_ar')
                ->requiredMapping()
                ->rules(['required'])
                ->example('شقة'),

            // البحث عن المجمع السكني (الكمبوند) باسمه
            ImportColumn::make('compound')
                ->label('الكمبوند (اختياري)')
                ->relationship(resolveUsing: 'name_ar')
                ->example('بالم هيلز الإسكندرية'),

            // البحث عن المطور العقاري باسمه
            ImportColumn::make('developer')
                ->label('المطور العقاري (اختياري)')
                ->relationship(resolveUsing: 'name_ar')
                ->example('اعمار مصر'),

            ImportColumn::make('latitude')
                ->label('خط العرض (اختياري)')
                ->guess(['خط العرض (اختياري)', 'خط العرض', 'latitude'])
                ->rules(['nullable', function ($attribute, $value, $fail) {
                    $cleaned = is_string($value) ? preg_replace('/^[\s\p{Zs}\p{Zl}\p{Zp}\x{00a0}]+|[\s\p{Zs}\p{Zl}\p{Zp}\x{00a0}]+$/u', '', $value) : $value;
                    if (blank($cleaned)) return;
                    if (!is_numeric($cleaned)) {
                        $fail('يجب أن يكون الحقل ' . $attribute . ' رقمًا.');
                    }
                }])
                ->example('30.0444'),
            ImportColumn::make('longitude')
                ->label('خط الطول (اختياري)')
                ->guess(['خط الطول (اختياري)', 'خط الطول', 'longitude'])
                ->rules(['nullable', function ($attribute, $value, $fail) {
                    $cleaned = is_string($value) ? preg_replace('/^[\s\p{Zs}\p{Zl}\p{Zp}\x{00a0}]+|[\s\p{Zs}\p{Zl}\p{Zp}\x{00a0}]+$/u', '', $value) : $value;
                    if (blank($cleaned)) return;
                    if (!is_numeric($cleaned)) {
                        $fail('يجب أن يكون الحقل ' . $attribute . ' رقمًا.');
                    }
                }])
                ->example('31.2357'),
        ];
    }

    public function resolveRecord(): Unit
    {
        // Get the first admin user to be the owner of imported units
        $adminId = \App\Models\User::where('role', 'admin')->first()?->id;

        $unit = new Unit();

        // Set default values for imported units
        $unit->status = $this->data['status'] ?? 'approved'; // Default to approved if not specified
        $unit->owner_id = $adminId; // Set admin as owner

        return $unit;
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