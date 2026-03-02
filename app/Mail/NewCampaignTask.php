<?php

namespace App\Mail;

use App\Models\Campaign;
use App\Models\ProjectTask;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewCampaignTask extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Campaign $campaign,
        public ProjectTask $task,
        public User $member,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New task in campaign {$this->campaign->name}: {$this->task->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-campaign-task',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
