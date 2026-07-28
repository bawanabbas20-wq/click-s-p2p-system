<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\PurchaseRequest;

class RequestSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $purchaseRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(PurchaseRequest $purchaseRequest)
    {
        $this->purchaseRequest = $purchaseRequest;
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
                    ->subject('✅ Purchase Request Submitted Successfully')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('Your purchase request has been successfully submitted and is now pending approval.')
                    ->line('**Request Details:**')
                    ->line('• **Item:** ' . $this->purchaseRequest->item_name)
                    ->line('• **Amount:** ' . number_format($this->purchaseRequest->estimated_price, 2) . ' ' . $this->purchaseRequest->estimated_currency)
                    ->line('• **Request ID:** #' . $this->purchaseRequest->id)
                    ->line('• **Current Status:** ' . $this->purchaseRequest->status)
                    ->action('View Request Status', route('requests.show', $this->purchaseRequest))
                    ->line('You will receive further notifications as your request moves through the approval process.')
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
            'message' => 'Purchase Request Submitted',
            'url' => route('requests.show', $this->purchaseRequest),
            'purchase_request_id' => $this->purchaseRequest->id,
        ];
    }
}
