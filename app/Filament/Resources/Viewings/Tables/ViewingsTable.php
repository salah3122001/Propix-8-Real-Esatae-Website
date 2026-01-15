<?php

namespace App\Filament\Resources\Viewings\Tables;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Viewing;

class ViewingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('المستخدم')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('unit.title')
                    ->label('الوحدة')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                TextColumn::make('name')
                    ->label('اسم العميل')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable(),
                TextColumn::make('date')
                    ->label('التاريخ')
                    ->date()
                    ->sortable(),
                TextColumn::make('time')
                    ->label('الوقت')
                    // Removed ->time() because the column is string now, but if it's formatted string "2:00 PM" it's fine.
                    // If we want it strictly as time, we can keep it or use ->format().
                    // Since it's a string like "2:00 PM", default display is fine.
                    ->sortable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'قيد الانتظار',
                        'accepted' => 'مقبول',
                        'rejected' => 'مرفوض',
                        'cancelled' => 'ملغي',
                        'reschedule_admin' => 'اقتراح موعد جديد',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'accepted' => 'success',
                        'rejected', 'cancelled' => 'danger',
                        'reschedule_admin' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('user_message')
                    ->label('رسالة المستخدم')
                    ->limit(50)
                    ->tooltip(fn($record) => $record->user_message)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('accept')
                    ->label('قبول')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('قبول طلب المعاينة')
                    ->modalDescription('هل أنت متأكد من قبول هذا الطلب؟')
                    ->modalSubmitActionLabel('نعم، قبول')
                    ->modalCancelActionLabel('إلغاء')
                    ->action(function (Viewing $record) {
                        $record->update(['status' => 'accepted']);
                        if ($record->user) {
                            $record->user->notify(new \App\Notifications\ViewingStatusNotification($record, 'accepted'));
                        }
                    })
                    ->visible(fn(Viewing $record) => $record->status !== 'accepted'),

                Action::make('reschedule')
                    ->label('اقتراح موعد جديد')
                    ->icon('heroicon-o-calendar')
                    ->color('warning')
                    ->modalHeading('اقتراح موعد جديد')
                    ->modalSubmitActionLabel('إرسال الاقتراح')
                    ->modalCancelActionLabel('إلغاء')
                    ->form([
                        DatePicker::make('date')
                            ->label('التاريخ الجديد')
                            ->required()
                            ->default(fn(Viewing $record) => $record->date),
                        TimePicker::make('time') // TimePicker handles string/time format usually.
                            ->label('الوقت الجديد')
                            ->required()
                            ->default(fn(Viewing $record) => $record->time),
                    ])
                    ->action(function (Viewing $record, array $data) {
                        $record->update([
                            'date' => $data['date'],
                            'time' => $data['time'], // This confirms it needs to be string if stored as string
                            'status' => 'reschedule_admin',
                        ]);
                        if ($record->user) {
                            $record->user->notify(new \App\Notifications\ViewingStatusNotification($record, 'reschedule_admin'));
                        }
                    }),

                EditAction::make()->label('تعديل'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ]);
    }
}
