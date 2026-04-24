<?php

namespace App\Mail;

use App\Models\Project;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskDeleted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Project $project,
        public string $taskTitle,
        public User $deletedBy,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Task Deleted: {$this->taskTitle}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.task-deleted',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
