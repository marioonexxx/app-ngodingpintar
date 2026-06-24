<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Transaction;

class OrderPlacedNotification extends Notification
{
    use Queueable;

    public function __construct(public Transaction $transaction)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
                    ->subject('Pesanan Diterima - ' . $this->transaction->invoice_number)
                    ->greeting('Halo ' . $notifiable->name . '!')
                    ->line('Terima kasih telah melakukan pemesanan di NgodingPintar.id.')
                    ->line('Detail Pesanan Anda:');
                    
        foreach ($this->transaction->items as $item) {
            $message->line('- ' . $item->product_name . ' (' . $item->quantity . 'x) - Rp ' . number_format($item->subtotal, 0, ',', '.'));
        }
        
        $message->line('Total: Rp ' . number_format($this->transaction->total, 0, ',', '.'));

        if ($this->transaction->payment_gateway === 'manual_transfer') {
            $message->line('Silakan lakukan pembayaran dan unggah bukti transfer di halaman transaksi aktif Anda.');
            $message->action('Upload Bukti Pembayaran', url('/member/transactions/active'));
        } else {
            $message->line('Anda memilih metode pembayaran online otomatis.');
            $message->action('Lihat Transaksi', url('/member/transactions/history'));
        }

        $message->line('Jika ada pertanyaan, jangan ragu untuk membalas email ini.');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
