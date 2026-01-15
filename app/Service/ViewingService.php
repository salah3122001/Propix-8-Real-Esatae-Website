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
            ->with(['unit:id,title,address'])
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
    public function cancelViewing(Viewing $viewing): void
    {
        $viewing->update(['status' => 'cancelled']);
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

        foreach ($admins as $admin) {
            $admin->notify(new ViewingStatusNotification($viewing, $type));
        }
    }

    /**
     * Get viewing by ID for authenticated user
     */
    public function getUserViewing(int $id): Viewing
    {
        return Viewing::where('user_id', Auth::id())->findOrFail($id);
    }
}
