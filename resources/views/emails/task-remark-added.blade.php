<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Remark Added</title>
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

        .remark-box {
            background: #f9fafb;
            border-left: 3px solid #8b5cf6;
            padding: 16px;
            margin: 20px 0;
            border-radius: 6px;
        }

        .remark-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
        }

        .remark-author {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
        }

        .remark-date {
            font-size: 12px;
            color: #6b7280;
        }

        .remark-content {
            font-size: 14px;
            color: #374151;
            line-height: 1.6;
            white-space: pre-wrap;
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
            <h1>New Remark Added</h1>
            <div class="project">
                {{ $project->name }}
            </div>

            <!-- Intro -->
            <p>
                Hello,<br>
                {{ $addedBy->name }} has added a new remark to a task.
            </p>

            <div class="divider"></div>

            <!-- Task Title -->
            <div class="task-title">
                {{ $task->title }}
            </div>

            <!-- Remark -->
            <div class="remark-box">
                <div class="remark-header">
                    <span class="remark-author">{{ $addedBy->name }}</span>
                    <span class="remark-date">• {{ $remark->created_at->format('F d, Y \a\t h:i A') }}</span>
                </div>
                <div class="remark-content">{!! nl2br(e($remark->remarks)) !!}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            © {{ date('Y') }} {{ config('app.name') }}
        </div>
    </div>
</body>

</html>
