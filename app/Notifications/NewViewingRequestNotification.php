<?php

namespace App\Notifications;

use App\Models\Viewing;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;

class NewViewingRequestNotification extends Notification
{
    use Queueable;

    protected $viewing;

    /**
     * Create a new notification instance.
     */
    public function __construct(Viewing $viewing)
    {
        $this->viewing = $viewing;
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
    public function toArray(object $notifiable): array
    {
        $locale = app()->getLocale();

        return [
            'title' => $locale === 'ar'
                ? 'طلب معاينة جديد'
                : 'New Viewing Request',
            'body' => $locale === 'ar'
                ? "طلب معاينة جديد من {$this->viewing->name} للوحدة #{$this->viewing->unit_id}"
                : "New viewing request from {$this->viewing->name} for unit #{$this->viewing->unit_id}",
            'viewing_id' => $this->viewing->id,
            'unit_id' => $this->viewing->unit_id,
            'user_name' => $this->viewing->name,
            'user_email' => $this->viewing->email,
            'user_phone' => $this->viewing->phone,
            'date' => $this->viewing->date,
            'time' => $this->viewing->time,
            'icon' => 'heroicon-o-calendar',
            'iconColor' => 'info',
        ];
    }

    /**
     * Get the Filament notification representation.
     */
    public function toFilament(): FilamentNotification
    {
        $locale = app()->getLocale();

        return FilamentNotification::make()
            ->title($locale === 'ar' ? 'طلب معاينة جديد' : 'New Viewing Request')
            ->body($locale === 'ar'
                ? "طلب معاينة جديد من {$this->viewing->name} للوحدة #{$this->viewing->unit_id}"
                : "New viewing request from {$this->viewing->name} for unit #{$this->viewing->unit_id}")
            ->icon('heroicon-o-calendar')
            ->iconColor('info')
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label($locale === 'ar' ? 'عرض التفاصيل' : 'View Details')
                    ->url('/admin/viewings/' . $this->viewing->id . '/edit')
                    ->markAsRead(),
            ]);
    }
}
