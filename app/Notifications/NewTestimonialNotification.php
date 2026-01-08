<?php

namespace App\Notifications;

use App\Models\Testimonial;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class NewTestimonialNotification extends Notification
{
    use Queueable;

    public $testimonial;

    /**
     * Create a new notification instance.
     */
    public function __construct(Testimonial $testimonial)
    {
        $this->testimonial = $testimonial;
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
            ->title(__('admin.notifications.new_testimonial'))
            ->body(__('admin.notifications.new_testimonial_body', [
                'name' => $this->testimonial->name
            ]))
            ->icon('heroicon-o-chat-bubble-left-right')
            ->iconColor('info')
            ->actions([
                Action::make('view')
                    ->label(__('admin.resources.testimonial'))
                    ->url("/admin/testimonials")
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
