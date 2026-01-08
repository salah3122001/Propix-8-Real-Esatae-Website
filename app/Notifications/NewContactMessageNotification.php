<?php

namespace App\Notifications;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class NewContactMessageNotification extends Notification
{
    use Queueable;

    public $contact;

    /**
     * Create a new notification instance.
     */
    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
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
            ->title(__('admin.notifications.new_contact_message'))
            ->body(__('admin.notifications.new_contact_body', [
                'name' => $this->contact->name,
                'subject' => $this->contact->subject
            ]))
            ->icon('heroicon-o-envelope')
            ->iconColor('info')
            ->actions([
                Action::make('view')
                    ->label(__('admin.resources.contact'))
                    ->url("/admin/contacts")
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
