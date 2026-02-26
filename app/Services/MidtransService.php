<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Midtrans\{Config, Notification, Snap};

class MidtransService
{
    public function __construct()
    {
        // Set Midtrans configuration
        Config::$serverKey = config('midtrans.serverKey');
        Config::$isProduction = config('midtrans.isProduction');
        Config::$isSanitized = config('midtrans.isSanitized');
        Config::$is3ds = config('midtrans.is3ds');
    }

    public function createSnapToken(array $params): string
    {
        try {
            return Snap::getSnapToken($params);
        } catch (\Exception $e) {
            Log::error('Failed to create Snap token: ' . $e->getMessage());
            throw $e;
        }
    }

    public function handleNotification(): array
    {
        try {
            $notification = new Notification(); // auto load from request

            return [
                'order_id' => $notification->order_id,
                'transaction_status' => $notification->transaction_status,
                'gross_amount' => $notification->gross_amount,
                'custom_field1' => $notification->custom_field1, // user id
                'custom_field2' => $notification->custom_field2, // mortgage request id
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans notification error: ' . $e->getMessage());
            throw $e;
        }
    }
}
