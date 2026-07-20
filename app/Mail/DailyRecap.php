<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyRecap extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public array $summary) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'صوت · ملخّص يومك — '.$this->summary['hijri'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-recap',
            with: ['user' => $this->user, 's' => $this->summary],
        );
    }
}
