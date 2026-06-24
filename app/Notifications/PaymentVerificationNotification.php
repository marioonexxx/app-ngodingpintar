<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Transaction $transaction,
        public string $action
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->greeting('Halo ' . $notifiable->name . '!');

        if ($this->action === 'approve') {
            $message->subject('Pembayaran Berhasil Divalidasi - ' . $this->transaction->invoice_number)
                ->line('Kabar baik! Pembayaran untuk pesanan Anda (' . $this->transaction->invoice_number . ') telah berhasil divalidasi.')
                ->line('Pesanan Anda sekarang sedang diproses atau sudah selesai.')
                ->action('Lihat Transaksi', url('/member/transactions/history'));
        } else {
            $message->subject('Pembayaran Ditolak - ' . $this->transaction->invoice_number)
                ->line('Mohon maaf, bukti pembayaran yang Anda unggah untuk pesanan (' . $this->transaction->invoice_number . ') tidak dapat kami validasi atau ditolak.');
            
            if ($this->transaction->rejection_reason) {
                $message->line('**Alasan Penolakan:** ' . $this->transaction->rejection_reason);
            }
            
            $message->line('Silakan buat transaksi baru jika Anda masih berminat dengan pesanan ini.')
                ->action('Buat Transaksi Baru', url('/products'));
        }

        $message->line('Terima kasih telah menggunakan layanan NgodingPintar.id.');

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'transaction_id' => $this->transaction->id,
            'invoice_number' => $this->transaction->invoice_number,
            'action' => $this->action,
        ];
    }
}
