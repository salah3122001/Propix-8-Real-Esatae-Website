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
                ->guess(['العنوان (عربي)', 'العنوان', 'title_ar'])
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
                ->guess(['الوصف (عربي)', 'الوصف', 'description_ar'])
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
                ->guess(['العنوان بالتفصيل', 'العنوان بالكامل', 'address'])
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('15 شارع النصر، المعادي'),
            ImportColumn::make('price')
                ->label('السعر')
                ->guess(['السعر', 'سعر', 'price'])
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'numeric'])
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
                ->label('نوع العرض')
                ->guess(['نوع العرض (sale/rent)', 'نوع العرض', 'offer_type'])
                ->requiredMapping()
                ->castStateUsing(function (string $state): string {
                    $state = trim($state);
                    return match ($state) {
                        'بيع', 'sale' => 'sale',
                        'إيجار', 'ايجار', 'rent' => 'rent',
                        default => $state,
                    };
                })
                ->rules(['required', 'in:sale,rent'])
                ->example('بيع'),
            ImportColumn::make('area')
                ->label('المساحة')
                ->guess(['المساحة', 'مساحة', 'area'])
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'numeric'])
                ->example('200'),
            ImportColumn::make('rooms')
                ->label('الغرف (اختياري)')
                ->guess(['الغرف (اختياري)', 'الغرف', 'rooms'])
                ->rules(['nullable', function ($attribute, $value, $fail) {
                    $cleaned = is_string($value) ? preg_replace('/^[\s\p{Zs}\p{Zl}\p{Zp}\x{00a0}]+|[\s\p{Zs}\p{Zl}\p{Zp}\x{00a0}]+$/u', '', $value) : $value;
                    if (blank($cleaned)) return;
                    if (!is_numeric($cleaned)) {
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
                    if (!is_numeric($cleaned)) {
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
                    if (!is_numeric($cleaned)) {
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
                    $cleaned = is_string($value) ? preg_replace('/^[\s\p{Zs}\p{Zl}\p{Zp}\x{00a0}]+|[\s\p{Zs}\p{Zl}\p{Zp}\x{00a0}]+$/u', '', $value) : $value;
                    if (blank($cleaned)) return;

                    if (!is_numeric($cleaned)) {
                        $fail('يجب أن يكون الحقل ' . $attribute . ' رقمًا. القيمة الحالية: ' . $value);
                    }
                }])
                ->example('0'),
            ImportColumn::make('internal_area')
                ->label('المساحة الداخلية (اختياري)')
                ->guess(['المساحة الداخلية (اختياري)', 'المساحة الداخلية', 'internal_area'])
                ->rules(['nullable', function ($attribute, $value, $fail) {
                    $cleaned = is_string($value) ? preg_replace('/^[\s\p{Zs}\p{Zl}\p{Zp}\x{00a0}]+|[\s\p{Zs}\p{Zl}\p{Zp}\x{00a0}]+$/u', '', $value) : $value;
                    if (blank($cleaned)) return;

                    if (!is_numeric($cleaned)) {
                        $fail('يجب أن يكون الحقل ' . $attribute . ' رقمًا. القيمة الحالية: ' . $value);
                    }
                }])
                ->example('180'),
            ImportColumn::make('is_visible')
                ->label('مرئي للجمهور')
                ->guess(['مرئي للجمهور (1 أو 0)', 'مرئي للجمهور', 'is_visible'])
                ->requiredMapping()
                ->castStateUsing(function (string $state): int {
                    $state = trim($state);
                    return match ($state) {
                        'نعم', 'مرئي', '1' => 1,
                        'لا', 'مخفي', '0' => 0,
                        default => (int) $state,
                    };
                })
                ->boolean()
                ->rules(['required', 'boolean'])
                ->example('1'),
            ImportColumn::make('development_status')
                ->label('حالة التطوير')
                ->guess(['حالة التطوير (primary/resale) (اختياري)', 'حالة التطوير', 'development_status'])
                ->castStateUsing(function (?string $state): ?string {
                    if (blank($state)) return null;
                    $state = trim($state);
                    return match ($state) {
                        'أولي', 'اولى', 'جديد', 'primary' => 'primary',
                        'إعادة بيع', 'اعادة بيع', 'resale' => 'resale',
                        default => $state,
                    };
                })
                ->rules(['nullable', 'max:255', 'in:primary,resale'])
                ->example('أولي'),
            ImportColumn::make('status')
                ->label('الحالة')
                ->guess(['الحالة (approved/pending) (اختياري)', 'الحالة', 'status'])
                ->castStateUsing(function (?string $state): ?string {
                    if (blank($state)) return null;
                    $state = trim($state);
                    return match ($state) {
                        'مقبول', 'موافقة', 'تم الموافقة', 'approved' => 'approved',
                        'قيد الانتظار', 'انتظار', 'pending' => 'pending',
                        // 'مرفوض', 'rejected' => 'rejected',
                        default => $state,
                    };
                })
                ->rules(['nullable', 'in:approved,pending'])
                ->example('مقبول'),

            // البحث عن المدينة باسمها العربي
            ImportColumn::make('city')
                ->label('المدينة')
                ->guess(['المدينة', 'مدينة', 'city'])
                ->relationship(resolveUsing: 'name_ar')
                ->requiredMapping()
                ->rules(['required'])
                ->example('القاهرة'),

            // البحث عن نوع الوحدة باسمها العربي
            ImportColumn::make('type')
                ->label('نوع العقار')
                ->guess(['نوع العقار', 'النوع', 'type'])
                ->relationship(resolveUsing: 'name_ar')
                ->requiredMapping()
                ->rules(['required'])
                ->example('شقة'),

            // البحث عن المجمع السكني (الكمبوند) باسمه
            ImportColumn::make('compound')
                ->label('الكمبوند (اختياري)')
                ->guess(['الكمبوند (اختياري)', 'الكمبوند', 'المجمع السكني', 'compound'])
                ->relationship(resolveUsing: 'name_ar')
                ->example('بالم هيلز الإسكندرية'),

            // البحث عن المطور العقاري باسمه
            ImportColumn::make('developer')
                ->label('المطور العقاري (اختياري)')
                ->guess(['المطور العقاري (اختياري)', 'المطور العقاري', 'المطور', 'developer'])
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
            ImportColumn::make('images')
                ->label('(اختياري) الوسائط (الأسماء مفصولة بفاصلة). للفيديو: video:name.mp4')
                ->guess(['الوسائط (الأسماء مفصولة بفاصلة). للفيديو: video:name.mp4', 'الوسائط', 'الصور', 'images'])
                ->fillRecordUsing(fn () => null)
                ->rules(['nullable', 'string'])
                ->example('img1.jpg,video:tour.mp4,floorplan:p1.jpg'),
        ];
    }

    public function resolveRecord(): Unit
    {
        // Get the first admin user to be the owner of imported units
        $adminId = \App\Models\User::where('role', 'admin')->first()?->id;

        $unit = new Unit();

        // Set default values for imported units
        // Default status logic: pending if no images, approved if images exist
        $defaultStatus = empty($this->data['images']) ? 'pending' : 'approved';
        $unit->status = $this->data['status'] ?? $defaultStatus;
        $unit->owner_id = $adminId; // Set admin as owner

        return $unit;
    }

    protected function afterSave(): void
    {
        $unit = $this->record;

        // Handle Images
        if (!empty($this->data['images'])) {
            $mediaItems = array_map('trim', explode(',', $this->data['images']));

            // Handle options (decode if string)
            $options = $this->options;
            if (is_string($options)) {
                $options = json_decode($options, true);
            }
            $imagesSourcePath = $options['images_source_path'] ?? null;

            if ($imagesSourcePath && is_dir($imagesSourcePath)) {
                foreach ($mediaItems as $mediaItem) {
                     // Determine type and filename
                    $type = 'image';
                    $filename = $mediaItem;

                    if (str_starts_with($mediaItem, 'video:')) {
                        $type = 'video';
                        $filename = substr($mediaItem, 6);
                    } elseif (str_starts_with($mediaItem, '3d:')) {
                        $type = '3d';
                        $filename = substr($mediaItem, 3);
                    } elseif (str_starts_with($mediaItem, 'floorplan:')) {
                        $type = 'floorplan';
                        $filename = substr($mediaItem, 10);
                    }

                    $filename = trim($filename);
                    $sourceFile = $imagesSourcePath . DIRECTORY_SEPARATOR . $filename;

                    if (file_exists($sourceFile)) {
                        // Copy to media destination
                        // We use a unique name to avoid conflicts
                        $newFilename = uniqid('unit_' . $unit->id . '_') . '_' . $filename;
                        $destinationPath = 'units/media/' . $newFilename;

                        // Storage::disk('public')->put() requires content, but copy is better.
                        // But source is absolute path, destination is relative to disk.
                        // We can use php copy() to the disk's full path.

                        $disk = \Illuminate\Support\Facades\Storage::disk('public');
                        $fullDestPath = $disk->path($destinationPath);

                        // Ensure directory exists
                        if (!file_exists(dirname($fullDestPath))) {
                            mkdir(dirname($fullDestPath), 0755, true);
                        }

                        if (copy($sourceFile, $fullDestPath)) {
                            // Create UnitMedia record
                            \App\Models\UnitMedia::create([
                                'unit_id' => $unit->id,
                                'type' => $type,
                                'url' => $destinationPath,
                                'processing_status' => $type === 'video' ? 'pending' : 'completed',
                            ]);
                        }
                    }
                }
            }
        }
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
