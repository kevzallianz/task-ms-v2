<?php

namespace App\Mail;

use App\Models\Project;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProjectContributorAdded extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Project $project,
        public Campaign $campaign,
        public User $recipient,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your campaign was added to project: {$this->project->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.project-contributor-added',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
