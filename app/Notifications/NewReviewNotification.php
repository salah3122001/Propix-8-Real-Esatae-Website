<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class NewReviewNotification extends Notification
{
    use Queueable;

    public $review;

    /**
     * Create a new notification instance.
     */
    public function __construct(Review $review)
    {
        $this->review = $review;
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
            ->title(__('admin.notifications.new_review_posted'))
            ->body(__('admin.notifications.new_review_body', [
                'unit_id' => $this->review->unit_id,
                'rating' => $this->review->rating
            ]))
            ->icon('heroicon-o-star')
            ->iconColor('info')
            ->actions([
                Action::make('view')
                    ->label(__('admin.resources.review'))
                    ->url("/admin/reviews")
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
