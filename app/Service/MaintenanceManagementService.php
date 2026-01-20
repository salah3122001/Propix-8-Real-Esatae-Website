<?php

namespace App\Service;

use App\Models\MaintenanceBooking;
use App\Models\MaintenanceService;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class MaintenanceManagementService
{
    /**
     * Get all maintenance services grouped by category.
     *
     * @return Collection
     */
    public function getAllServicesGrouped(): Collection
    {
        return MaintenanceService::all()->groupBy('category');
    }

    /**
     * Create a new maintenance booking.
     *
     * @param array $data
     * @return MaintenanceBooking
     */
    public function createBooking(array $data): MaintenanceBooking
    {
        $booking = MaintenanceBooking::create([
            'maintenance_service_id' => $data['maintenance_service_id'],
            'user_id' => auth()->id(),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'message' => $data['message'] ?? null,
            'status' => 'pending',
        ]);

        $this->notifyAdmins($booking);

        return $booking;
    }

    /**
     * Get all bookings for the authenticated user.
     *
     * @return Collection
     */
    public function getUserBookings(): Collection
    {
        return MaintenanceBooking::where('user_id', auth()->id())
            ->with('service')
            ->latest()
            ->get();
    }

    /**
     * Update an existing maintenance booking.
     *
     * @param MaintenanceBooking $booking
     * @param array $data
     * @return MaintenanceBooking
     */
    public function updateBookingDetails(MaintenanceBooking $booking, array $data): MaintenanceBooking
    {
        $booking->update($data);
        $this->notifyAdminsOfUpdate($booking);
        return $booking;
    }

    /**
     * Delete a maintenance booking.
     *
     * @param MaintenanceBooking $booking
     * @return bool|null
     */
    public function deleteBooking(MaintenanceBooking $booking): ?bool
    {
        return $booking->delete();
    }

    /**
     * Check if user already has a pending booking for this service.
     *
     * @param int $serviceId
     * @param int $userId
     * @return bool
     */
    public function hasPendingBooking(int $serviceId, int $userId): bool
    {
        return MaintenanceBooking::where('maintenance_service_id', $serviceId)
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->exists();
    }

    /**
     * Notify all admin users about the new booking.
     *
     * @param MaintenanceBooking $booking
     * @return void
     */
    protected function notifyAdmins(MaintenanceBooking $booking): void
    {
        try {
            $admins = User::where('role', 'admin')->get();
            $locale = app()->getLocale();
            $userName = auth()->user()->name ?? 'مستخدم';
            $serviceTitle = $locale === 'ar' ? $booking->service->title_ar : ($booking->service->title_en ?? $booking->service->title_ar);

            foreach ($admins as $admin) {
                Notification::make()
                    ->title($locale === 'ar' ? 'طلب حجز خدمة جديد' : 'New Maintenance Booking Request')
                    ->body($locale === 'ar'
                        ? "قام {$userName} بطلب خدمة ({$serviceTitle})"
                        : "{$userName} requested service ({$serviceTitle})")
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->iconColor('success')
                    ->actions([
                        Action::make('view')
                            ->label($locale === 'ar' ? 'عرض التفاصيل' : 'View Details')
                            ->url('/admin/maintenance-bookings/' . $booking->id . '/edit')
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($admin);
            }
        } catch (\Exception $e) {
            Log::error('Maintenance Booking Notification failed: ' . $e->getMessage());
        }
    }

    /**
     * Notify all admin users about the update of a booking.
     *
     * @param MaintenanceBooking $booking
     * @return void
     */
    protected function notifyAdminsOfUpdate(MaintenanceBooking $booking): void
    {
        try {
            $admins = User::where('role', 'admin')->get();
            $locale = app()->getLocale();
            $userName = auth()->user()->name ?? 'مستخدم';
            $serviceTitle = $locale === 'ar' ? $booking->service->title_ar : ($booking->service->title_en ?? $booking->service->title_ar);

            foreach ($admins as $admin) {
                Notification::make()
                    ->title($locale === 'ar' ? 'تعديل طلب حجز خدمة' : 'Maintenance Booking Updated')
                    ->body($locale === 'ar'
                        ? "قام {$userName} بتعديل طلب خدمة ({$serviceTitle})"
                        : "{$userName} updated service request for ({$serviceTitle})")
                    ->icon('heroicon-o-pencil-square')
                    ->iconColor('info')
                    ->actions([
                        Action::make('view')
                            ->label($locale === 'ar' ? 'عرض التفاصيل' : 'View Details')
                            ->url('/admin/maintenance-bookings/' . $booking->id . '/edit')
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($admin);
            }
        } catch (\Exception $e) {
            Log::error('Maintenance Booking Update Notification failed: ' . $e->getMessage());
        }
    }
}
