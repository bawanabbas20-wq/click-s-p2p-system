<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PurchaseRequest;

class RequestDeniedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $purchaseRequest;
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(PurchaseRequest $purchaseRequest, string $reason)
    {
        $this->purchaseRequest = $purchaseRequest;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('❌ Purchase Request Update - Action Required')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('We have reviewed your purchase request and unfortunately cannot approve it at this time.')
                    ->line('**Request Details:**')
                    ->line('• **Item:** ' . $this->purchaseRequest->item_name)
                    ->line('• **Request ID:** #' . $this->purchaseRequest->id)
                    ->line('• **Amount:** ' . number_format($this->purchaseRequest->estimated_price, 2) . ' ' . $this->purchaseRequest->estimated_currency)
                    ->line('**Reason for Denial:**')
                    ->line($this->reason)
                    ->action('View Full Request Details', route('requests.show', $this->purchaseRequest))
                    ->line('📝 **Next Steps:**')
                    ->line('• Review the reason for denial above')
                    ->line('• You may submit a new request with modifications if appropriate')
                    ->line('• Contact your manager or procurement team if you have questions')
                    ->salutation('Best regards,<br>The Click P2P Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your Purchase Request Has Been Denied',
            'url' => route('requests.show', $this->purchaseRequest),
        ];
    }
}
