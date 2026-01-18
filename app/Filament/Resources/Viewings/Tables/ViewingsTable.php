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
use Illuminate\Support\Facades\Notification;

class ViewingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('viewing.columns.user'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('unit.title')
                    ->label(__('viewing.columns.unit'))
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                TextColumn::make('name')
                    ->label(__('viewing.columns.client_name'))
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(__('viewing.columns.phone'))
                    ->searchable(),
                TextColumn::make('date')
                    ->label(__('viewing.columns.date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('time')
                    ->label(__('viewing.columns.time'))
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('viewing.columns.status'))
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => __('viewing.statuses.pending'),
                        'accepted' => __('viewing.statuses.accepted'),
                        'rejected' => __('viewing.statuses.rejected'),
                        'cancelled' => __('viewing.statuses.cancelled'),
                        'reschedule_admin' => __('viewing.statuses.reschedule_admin'),
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
                    ->label(__('viewing.columns.user_message'))
                    ->limit(50)
                    ->tooltip(fn($record) => $record->user_message)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('created_at')
                    ->label(__('viewing.columns.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->searchPlaceholder(__('viewing.search_placeholder'))
            ->filters([
                //
            ])
            ->actions([
                Action::make('accept')
                    ->label(__('viewing.actions.accept'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(__('viewing.actions.accept_heading'))
                    ->modalDescription(__('viewing.actions.accept_description'))
                    ->modalSubmitActionLabel(__('viewing.actions.confirm_accept'))
                    ->modalCancelActionLabel(__('viewing.actions.cancel_modal'))
                    ->action(function (Viewing $record) {
                        $record->update(['status' => 'accepted']);

                        // Send Database Notification to Registered User (if exists)
                        if ($record->user) {
                            $record->user->notify(new \App\Notifications\ViewingStatusNotification($record, 'accepted', ['database']));
                        }

                        // Send Email Notification to Viewing Email
                        if ($record->email) {
                            try {
                                Notification::route('mail', $record->email)
                                    ->notify(new \App\Notifications\ViewingStatusNotification($record, 'accepted', ['mail']));
                            } catch (\Exception $e) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Failed to send email')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }
                    })
                    ->visible(fn(Viewing $record) => $record->status !== 'accepted'),

                Action::make('reschedule')
                    ->label(__('viewing.actions.propose_new_time'))
                    ->icon('heroicon-o-calendar')
                    ->color('warning')
                    ->modalHeading(__('viewing.actions.propose_heading'))
                    ->modalSubmitActionLabel(__('viewing.actions.send_proposal'))
                    ->modalCancelActionLabel(__('viewing.actions.cancel_modal'))
                    ->form([
                        DatePicker::make('date')
                            ->label(__('viewing.forms.new_date'))
                            ->required()
                            ->default(fn(Viewing $record) => $record->date),
                        TimePicker::make('time')
                            ->label(__('viewing.forms.new_time'))
                            ->required()
                            ->default(fn(Viewing $record) => $record->time),
                    ])
                    ->action(function (Viewing $record, array $data) {
                        $record->update([
                            'date' => $data['date'],
                            'time' => $data['time'],
                            'status' => 'reschedule_admin',
                        ]);

                        // Send Database Notification to Registered User (if exists)
                        if ($record->user) {
                            $record->user->notify(new \App\Notifications\ViewingStatusNotification($record, 'reschedule_admin', ['database']));
                        }

                        // Send Email Notification to Viewing Email
                        if ($record->email) {
                            try {
                                Notification::route('mail', $record->email)
                                    ->notify(new \App\Notifications\ViewingStatusNotification($record, 'reschedule_admin', ['mail']));
                            } catch (\Exception $e) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Failed to send email')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }
                    }),

                EditAction::make()
                    ->label(__('viewing.actions.edit'))
                    ->using(function (Viewing $record, array $data) {
                        $oldStatus = $record->status;
                        $oldDate = $record->date;
                        $oldTime = $record->time;

                        \Illuminate\Support\Facades\Log::info('EditAction triggered', [
                            'viewing_id' => $record->id,
                            'old_status' => $oldStatus,
                            'new_status' => $data['status'] ?? 'N/A',
                            'email' => $record->email
                        ]);

                        $record->update($data);

                        // Check if status changed to accepted
                        if ($record->status === 'accepted' && $oldStatus !== 'accepted') {
                            \Illuminate\Support\Facades\Log::info('Status changed to accepted');
                            if ($record->email) {
                                try {
                                    Notification::route('mail', $record->email)
                                        ->notify(new \App\Notifications\ViewingStatusNotification($record, 'accepted', ['mail']));
                                    \Illuminate\Support\Facades\Log::info('Accepted email sent');
                                } catch (\Exception $e) {
                                    \Illuminate\Support\Facades\Log::error('Error sending accepted email: ' . $e->getMessage());
                                    \Filament\Notifications\Notification::make()
                                        ->title('Failed to send email')
                                        ->body($e->getMessage())
                                        ->danger()
                                        ->send();
                                }
                            } else {
                                \Illuminate\Support\Facades\Log::warning('No email found for viewing');
                            }
                        }

                        // Check if rescheduled (status changed to reschedule_admin OR date/time changed while status is reschedule_admin)
                        $isRescheduled = $record->status === 'reschedule_admin' && (
                            $oldStatus !== 'reschedule_admin' ||
                            $oldDate != $record->date ||
                            $oldTime != $record->time
                        );

                        if ($isRescheduled) {
                            \Illuminate\Support\Facades\Log::info('Reschedule condition met');
                            if ($record->email) {
                                try {
                                    Notification::route('mail', $record->email)
                                        ->notify(new \App\Notifications\ViewingStatusNotification($record, 'reschedule_admin', ['mail']));
                                    \Illuminate\Support\Facades\Log::info('Reschedule email sent');
                                } catch (\Exception $e) {
                                    \Illuminate\Support\Facades\Log::error('Error sending reschedule email: ' . $e->getMessage());
                                    \Filament\Notifications\Notification::make()
                                        ->title('Failed to send email')
                                        ->body($e->getMessage())
                                        ->danger()
                                        ->send();
                                }
                            } else {
                                \Illuminate\Support\Facades\Log::warning('No email found for viewing (reschedule)');
                            }
                        }

                        return $record;
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label(__('viewing.actions.delete_selected')),
                ]),
            ]);
    }
}
