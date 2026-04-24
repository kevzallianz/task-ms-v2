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

class TaskStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Project $project,
        public ProjectTask $task,
        public User $updatedBy,
        public string $oldStatus,
        public string $newStatus,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Task Status Changed: {$this->task->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.task-status-updated',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
