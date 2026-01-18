<?php

namespace App\Service;

use App\Models\Viewing;
use App\Models\User;
use App\Notifications\ViewingStatusNotification;
use Illuminate\Support\Facades\Auth;

class ViewingService
{
    /**
     * Get all viewings for the authenticated user
     */
    public function getUserViewings()
    {
        return Viewing::where('user_id', Auth::id())
            ->with(['unit.media', 'unit.city', 'unit.type', 'unit.compound', 'unit.developer'])
            ->latest()
            ->get();
    }

    /**
     * Create a new viewing request
     */
    public function createViewing(array $data): Viewing
    {
        return Viewing::create([
            'user_id' => Auth::id(),
            'unit_id' => $data['unit_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'date' => $data['date'],
            'time' => $data['time'],
            'notes' => $data['notes'] ?? null,
            'status' => 'pending',
        ]);
    }

    /**
     * Cancel a viewing request
     */
    public function cancelViewing(Viewing $viewing, array $data = []): void
    {
        $viewing->update([
            'status' => 'cancelled',
            'user_message' => $data['user_message'] ?? $viewing->user_message,
        ]);

        // Notify Admins about cancellation
        $this->notifyAdmins($viewing, 'cancelled');
    }

    /**
     * Accept admin's suggested time
     */
    public function acceptViewing(Viewing $viewing): void
    {
        $viewing->update(['status' => 'accepted']);
    }

    /**
     * Propose a new time for viewing
     */
    public function proposeNewTime(Viewing $viewing, array $data): void
    {
        $viewing->update([
            'date' => $data['date'] ?? $viewing->date,
            'time' => $data['time'] ?? $viewing->time,
            'user_message' => $data['user_message'] ?? '',
            'status' => 'pending',
        ]);

        // Notify all admin users
        $this->notifyAdmins($viewing, 'user_response');
    }

    /**
     * Notify all admin users
     */
    protected function notifyAdmins(Viewing $viewing, string $type): void
    {
        $admins = User::where('role', 'admin')->get();
        $locale = app()->getLocale();

        foreach ($admins as $admin) {
            $title = '';
            $body = '';
            $color = 'info';

            // Define message based on type
            if ($type === 'user_response') {
                $title = $locale === 'ar' ? 'تحديث على طلب المعاينة' : 'Viewing Request Updated';
                $body = $locale === 'ar'
                    ? "قام {$viewing->name} بتحديث طلب المعاينة للوحدة #{$viewing->unit_id}"
                    : "{$viewing->name} updated the viewing request for unit #{$viewing->unit_id}";
                $color = 'warning';
            } elseif ($type === 'cancelled') {
                $title = $locale === 'ar' ? 'إلغاء طلب المعاينة' : 'Viewing Request Cancelled';
                $body = $locale === 'ar'
                    ? "قام {$viewing->name} بإلغاء طلب المعاينة للوحدة #{$viewing->unit_id}"
                    : "{$viewing->name} cancelled the viewing request for unit #{$viewing->unit_id}";
                $color = 'danger';
            } else {
                // Fallback for generic updates
                $title = $locale === 'ar' ? 'تحديث طلب معاينة' : 'Viewing Request Update';
                $body = $locale === 'ar'
                    ? "تحديث جديد على طلب المعاينة #{$viewing->id}"
                    : "New update on viewing request #{$viewing->id}";
            }

            try {
                \Filament\Notifications\Notification::make()
                    ->title($title)
                    ->body($body)
                    ->icon('heroicon-o-calendar')
                    ->iconColor($color)
                    ->actions([
                        \Filament\Actions\Action::make('view')
                            ->label($locale === 'ar' ? 'عرض التفاصيل' : 'View Details')
                            ->url('/admin/viewings/' . $viewing->id . '/edit')
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($admin);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Admin Notification failed: ' . $e->getMessage());
            }
        }
    }

    /**
     * Get viewing by ID for authenticated user
     */
    public function getUserViewing(int $id): Viewing
    {
        return Viewing::where('user_id', Auth::id())
            ->with(['unit.media', 'unit.city', 'unit.type', 'unit.compound', 'unit.developer'])
            ->findOrFail($id);
    }
}
