<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Updated</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Inter, Arial, sans-serif;
            color: #111827;
        }

        .wrapper {
            width: 100%;
            padding: 40px 16px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 14px;
            padding: 32px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
        }

        h1 {
            font-size: 20px;
            font-weight: 600;
            margin: 0 0 6px;
        }

        .project {
            font-size: 13px;
            color: #2563eb;
            font-weight: 500;
            margin-bottom: 24px;
        }

        p {
            font-size: 14px;
            color: #374151;
            margin: 0 0 16px;
            line-height: 1.6;
        }

        .divider {
            height: 1px;
            background: #e5e7eb;
            margin: 24px 0;
        }

        .task-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .task-desc {
            font-size: 14px;
            color: #4b5563;
            margin-bottom: 20px;
        }

        .meta {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .meta td {
            padding: 6px 0;
            color: #374151;
        }

        .meta strong {
            color: #111827;
            font-weight: 500;
        }

        .changes {
            background: #f9fafb;
            border-left: 3px solid #3b82f6;
            padding: 12px 16px;
            margin: 16px 0;
            border-radius: 6px;
        }

        .changes-title {
            font-size: 12px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .change-item {
            font-size: 13px;
            color: #4b5563;
            margin: 4px 0;
        }

        .change-label {
            font-weight: 500;
            color: #111827;
        }

        .old-value {
            color: #dc2626;
            text-decoration: line-through;
        }

        .new-value {
            color: #16a34a;
            font-weight: 500;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            margin-top: 28px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container">

            <!-- Header -->
            <h1>Task Updated</h1>
            <div class="project">
                {{ $project->name }}
            </div>

            <!-- Intro -->
            <p>
                Hello,<br>
                A task in the project has been updated by {{ $updatedBy->name }}.
            </p>

            <div class="divider"></div>

            <!-- Task Summary -->
            <div class="task-title">
                {{ $task->title }}
            </div>

            <div class="task-desc">
                {!! nl2br(e($task->description)) ?? 'No description provided.' !!}
            </div>

            @if (!empty($changes))
            <div class="changes">
                <div class="changes-title">Changes Made</div>
                @foreach ($changes as $field => $change)
                <div class="change-item">
                    <span class="change-label">{{ ucfirst(str_replace('_', ' ', $field)) }}:</span>
                    <span class="old-value">{{ $change['from'] ?? 'N/A' }}</span>
                    →
                    <span class="new-value">{{ $change['to'] ?? 'N/A' }}</span>
                </div>
                @endforeach
            </div>
            @endif

            @php
            $startDate = $task->start_date
                ? \Illuminate\Support\Carbon::parse($task->start_date)->format('F d, Y')
                : 'Not set';

            $targetDate = $task->target_date
                ? \Illuminate\Support\Carbon::parse($task->target_date)->format('F d, Y')
                : 'Not set';
            @endphp

            <!-- Meta Info -->
            <table class="meta">
                <tr>
                    <td><strong>Start date</strong></td>
                    <td>{{ $startDate }}</td>
                </tr>
                <tr>
                    <td><strong>Target date</strong></td>
                    <td>{{ $targetDate }}</td>
                </tr>
                <tr>
                    <td><strong>Status</strong></td>
                    <td>{{ ucfirst(str_replace('_', ' ', $task->status)) }}</td>
                </tr>
                <tr>
                    <td><strong>Updated by</strong></td>
                    <td>{{ $updatedBy->name }}</td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            © {{ date('Y') }} {{ config('app.name') }}
        </div>
    </div>
</body>

</html>
