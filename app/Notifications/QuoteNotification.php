<?php
/*
 * CODE
 * Notification Controller
*/

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use League\CommonMark\Extension\SmartPunct\Quote;

class QuoteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     * @param User $user
     *
     * @return void
     */
    public function __construct(public User $user)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return MailMessage
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        // phpcs:ignore
        return (new MailMessage)
            ->subject('Notificación de cotización')
            // phpcs:ignore
            ->line('El usuario ' . $this->user->name . ' genero una nueva cotización')
//            ->action('Notification Action', url('/'))
            ->line('Incia sesión en la aplicación para poder revisar los detalles.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray(mixed $notifiable): array
    {
        return [
            //
        ];
    }
}
