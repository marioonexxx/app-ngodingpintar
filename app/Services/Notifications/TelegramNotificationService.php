<?php

namespace App\Services\Notifications;

use Illuminate\Support\Facades\Http;

class TelegramNotificationService
{
    public function send(string $message): bool
    {
        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (! $token || ! $chatId) {
            return false;
        }

        $response = Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
        ]);

        return $response->successful();
    }
}
