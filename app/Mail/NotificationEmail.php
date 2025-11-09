<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $message;
    public $recipients;

    /**
     * Create a new message instance.
     *
     * @param string $subject
     * @param string $message
     * @param array $recipients
     * @return void
     */
    public function __construct($subject, $message, $recipients = [])
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->recipients = $recipients;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
                    ->view('emails.notification')
                    ->with([
                        'subject' => $this->subject,
                        'emailMessage' => $this->message,
                        'recipients' => $this->recipients,
                    ]);
    }
}
