<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Accomplishment Report</title>
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

        .subtitle {
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

        .info-box {
            background: #f9fafb;
            padding: 12px 16px;
            margin: 16px 0;
            border-radius: 6px;
            font-size: 13px;
            color: #4b5563;
        }

        .section-label {
            font-size: 12px;
            font-weight: 600;
            color: #2563eb;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 20px 0 8px;
        }

        .report-box {
            background: #f0fdf4;
            border-left: 3px solid #16a34a;
            padding: 16px;
            margin: 8px 0 16px;
            border-radius: 6px;
            font-size: 14px;
            color: #111827;
            white-space: pre-wrap;
            line-height: 1.6;
        }

        ul.item-list {
            margin: 0 0 16px;
            padding-left: 20px;
        }

        ul.item-list li {
            font-size: 13px;
            color: #374151;
            margin-bottom: 6px;
            line-height: 1.5;
        }

        ul.item-list li .meta {
            color: #6b7280;
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
            <h1>Daily Accomplishment Report</h1>
            <div class="subtitle">
                {{ now()->format('F d, Y') }}
            </div>

            <!-- Intro -->
            <p>
                Hello,<br>
                {{ $reporter->name }} has submitted their accomplishment report for today.
            </p>

            <div class="info-box">
                Submitted by <strong>{{ $reporter->name }}</strong> ({{ $reporter->email }}) on {{ now()->format('F d, Y \a\t h:i A') }}
            </div>

            <div class="divider"></div>

            <!-- Report Content -->
            <div class="section-label">Report</div>
            <div class="report-box">{{ $accomplishments }}</div>

            <!-- Accomplished Tasks -->
            @if ($accomplishedTasks->count() > 0)
            <div class="section-label">Tasks Accomplished Today ({{ $accomplishedTasks->count() }})</div>
            <ul class="item-list">
                @foreach ($accomplishedTasks as $task)
                <li>
                    {{ $task->title }}
                    <span class="meta">
                        &mdash; {{ $task->campaign->name ?? '—' }}
                        @if ($task->project)
                            &middot; {{ $task->project->title }}
                        @endif
                    </span>
                </li>
                @endforeach
            </ul>
            @endif

            <!-- Accomplished Projects -->
            @if ($accomplishedProjects->count() > 0)
            <div class="section-label">Projects Accomplished Today ({{ $accomplishedProjects->count() }})</div>
            <ul class="item-list">
                @foreach ($accomplishedProjects as $project)
                <li>
                    {{ $project->title }}
                    <span class="meta">&mdash; {{ $project->campaign->name ?? '—' }}</span>
                </li>
                @endforeach
            </ul>
            @endif

            <p style="font-size: 13px; color: #6b7280; margin-top: 20px;">
                You can reply directly to this email to respond to {{ $reporter->name }}.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            © {{ date('Y') }} {{ config('app.name') }}
        </div>
    </div>
</body>

</html>
