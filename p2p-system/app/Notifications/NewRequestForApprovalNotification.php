<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PurchaseRequest;
use App\Models\User;

class NewRequestForApprovalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $purchaseRequest;
    protected $approverRole;

    /**
     * Create a new notification instance.
     */
    public function __construct(PurchaseRequest $purchaseRequest, string $approverRole)
    {
        $this->purchaseRequest = $purchaseRequest;
        $this->approverRole = $approverRole; // e.g., 'Finance', 'Manager'
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
                    ->subject(__('New Purchase Request Awaiting Your Approval'))
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line(__('A new purchase request has been submitted and requires your approval as :role.', ['role' => $this->approverRole]))
                    ->line('**Request Details:**')
                    ->line('• **Item:** ' . $this->purchaseRequest->item_name)
                    ->line('• **Amount:** ' . number_format($this->purchaseRequest->estimated_price, 2) . ' ' . $this->purchaseRequest->estimated_currency)
                    ->line('• **Requested by:** ' . $this->purchaseRequest->user->name)
                    ->line('• **Request ID:** #' . $this->purchaseRequest->id)
                    ->line('• **Date Requested:** ' . $this->purchaseRequest->created_at->format('M d, Y'))
                    ->action('Review & Approve Request', route('approval.show', $this->purchaseRequest))
                    ->line('⏰ **Please review this request at your earliest convenience.**')
                    ->line('Your prompt attention helps keep our procurement process running smoothly.')
                    ->salutation('Best regards,<br>The Click P2P Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        // Check if the user can access approval queue, otherwise send to general queue
        $url = $notifiable->can('is-approver') 
            ? route('approval.show', $this->purchaseRequest)
            : route('approval.queue');
            
        return [
            'message' => __('New Purchase Request Awaiting Approval'),
            'url' => $url,
            'purchase_request_id' => $this->purchaseRequest->id,
            'item_name' => $this->purchaseRequest->item_name,
        ];
    }
}
