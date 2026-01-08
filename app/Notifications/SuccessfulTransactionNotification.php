<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class SuccessfulTransactionNotification extends Notification
{
    use Queueable;

    public $transaction;

    /**
     * Create a new notification instance.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title(__('admin.notifications.successful_transaction'))
            ->body(__('admin.notifications.successful_transaction_body', [
                'amount' => number_format($this->transaction->amount, 2),
                'unit_id' => $this->transaction->unit_id
            ]))
            ->icon('heroicon-o-currency-dollar')
            ->iconColor('success')
            ->actions([
                Action::make('view')
                    ->label(__('admin.resources.transaction'))
                    ->url("/admin/transactions")
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
