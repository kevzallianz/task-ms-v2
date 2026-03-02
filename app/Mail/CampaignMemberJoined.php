<?php

namespace App\Mail;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CampaignMemberJoined extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Campaign $campaign,
        public User $member,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You've been added to campaign: {$this->campaign->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.campaign-member-joined',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
