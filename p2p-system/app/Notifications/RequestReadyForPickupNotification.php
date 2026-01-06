<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PurchaseRequest;

class RequestReadyForPickupNotification extends Notification implements ShouldQueue
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
                    ->subject('🎉 Your Purchase Request is Ready for Pickup!')
                    ->greeting('Hello ' . $notifiable->name . '!')
                    ->line('Great news! Your purchase request has been processed and is ready for pickup.')
                    ->line('**Item:** ' . $this->purchaseRequest->item_name)
                    ->line('**Request ID:** #' . $this->purchaseRequest->id)
                    ->line('**Status:** Ready for Pickup')
                    ->action('View Request Details', route('requests.show', $this->purchaseRequest))
                    ->line('📍 **Next Steps:**')
                    ->line('• Visit the procurement department to collect your item')
                    ->line('• Don\'t forget to log in and "Confirm Receipt" after pickup')
                    ->line('• Contact procurement if you have any questions')
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
            'message' => 'Your Purchase Request is Ready!',
            'url' => route('requests.show', $this->purchaseRequest),
        ];
    }
}
