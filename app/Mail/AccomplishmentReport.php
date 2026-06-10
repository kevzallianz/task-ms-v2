<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class AccomplishmentReport extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $reporter,
        public string $accomplishments,
        public Collection $accomplishedTasks,
        public Collection $accomplishedProjects,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Daily Accomplishment Report - {$this->reporter->name} - ".now()->format('M d, Y'),
            replyTo: [$this->reporter->email],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.accomplishment-report',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
