<?php

namespace App\Notifications;

use App\Models\Unit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class NewUnitNotification extends Notification
{
    use Queueable;

    public $unit;

    /**
     * Create a new notification instance.
     */
    public function __construct(Unit $unit)
    {
        $this->unit = $unit;
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
            ->title(__('admin.notifications.new_unit_added'))
            ->body(__('admin.notifications.new_unit_body', [
                'title' => $this->unit->title,
                'seller' => $this->unit->owner->name ?? 'Unknown'
            ]))
            ->icon('heroicon-o-home-modern')
            ->iconColor('info')
            ->actions([
                Action::make('view')
                    ->label(__('admin.resources.unit'))
                    ->url("/admin/units/{$this->unit->id}/edit")
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
