<?php

namespace App\Mail;

use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Project $project,
        public ProjectTask $task,
        public User $updatedBy,
        public array $changes = [],
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Task Updated: {$this->task->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.task-updated',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
