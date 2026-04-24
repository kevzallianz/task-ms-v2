<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Deleted</title>
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

        .deleted-task {
            background: #fef2f2;
            border-left: 3px solid #dc2626;
            padding: 16px;
            margin: 20px 0;
            border-radius: 6px;
        }

        .deleted-label {
            font-size: 12px;
            font-weight: 600;
            color: #dc2626;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .task-name {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
        }

        .info-box {
            background: #f9fafb;
            padding: 12px 16px;
            margin: 16px 0;
            border-radius: 6px;
            font-size: 13px;
            color: #4b5563;
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
            <h1>Task Deleted</h1>
            <div class="project">
                {{ $project->name }}
            </div>

            <!-- Intro -->
            <p>
                Hello,<br>
                A task has been deleted from the project by {{ $deletedBy->name }}.
            </p>

            <div class="divider"></div>

            <!-- Deleted Task Info -->
            <div class="deleted-task">
                <div class="deleted-label">Deleted Task</div>
                <div class="task-name">{{ $taskTitle }}</div>
            </div>

            <div class="info-box">
                Deleted by <strong>{{ $deletedBy->name }}</strong> on {{ now()->format('F d, Y \a\t h:i A') }}
            </div>

            <p style="font-size: 13px; color: #6b7280;">
                This task and all its associated data have been permanently removed from the project.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            © {{ date('Y') }} {{ config('app.name') }}
        </div>
    </div>
</body>

</html>
