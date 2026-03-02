<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Task</title>
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

        .campaign {
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

        .cta {
            display: inline-block;
            margin-top: 28px;
            padding: 12px 22px;
            background: #111827;
            color: #ffffff !important;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            border-radius: 10px;
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
            <h1>New task assigned</h1>
            <div class="campaign">
                {{ $campaign->name }}
            </div>

            <!-- Intro -->
            <p>
                Hi {{ $member->name }},<br>
                A new task has been added to your campaign.
            </p>

            <div class="divider"></div>

            <!-- Task Summary -->
            <div class="task-title">
                {{ $task->title }}
            </div>

            <div class="task-desc">
                {!! nl2br(e($task->description)) ?? 'No description provided.' !!}
            </div>

            @php
            $startDate = $task->start_date
            ? \Illuminate\Support\Carbon::parse($task->start_date)->format('F d, Y')
            : 'Not set';

            $targetDate = $task->target_date
            ? \Illuminate\Support\Carbon::parse($task->target_date)->format('F d, Y')
            : 'Not set';
            @endphp

            <!-- Meta Info (2-column layout, email-safe) -->
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
            </table>

            <!-- CTA -->
            <a href="{{ route('user.campaign') }}" class="cta">
                View task
            </a>

        </div>

        <!-- Footer -->
        <div class="footer">
            © {{ date('Y') }} {{ config('app.name') }}
        </div>
    </div>
</body>

</html>