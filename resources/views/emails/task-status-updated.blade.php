<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Status Updated</title>
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

        .status-change {
            background: #f9fafb;
            border-left: 3px solid #10b981;
            padding: 16px;
            margin: 20px 0;
            border-radius: 6px;
            text-align: center;
        }

        .status-label {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .status-badges {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-ongoing {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }

        .arrow {
            font-size: 20px;
            color: #9ca3af;
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
            <h1>Task Status Changed</h1>
            <div class="project">
                {{ $project->name }}
            </div>

            <!-- Intro -->
            <p>
                Hello,<br>
                The status of a task has been updated by {{ $updatedBy->name }}.
            </p>

            <div class="divider"></div>

            <!-- Task Title -->
            <div class="task-title">
                {{ $task->title }}
            </div>

            <!-- Status Change -->
            <div class="status-change">
                <div class="status-label">Status Changed</div>
                <div class="status-badges">
                    @php
                        $oldStatusClass = 'status-badge ';
                        $newStatusClass = 'status-badge ';

                        switch($oldStatus) {
                            case 'pending':
                                $oldStatusClass .= 'status-pending';
                                break;
                            case 'ongoing':
                                $oldStatusClass .= 'status-ongoing';
                                break;
                            case 'completed':
                                $oldStatusClass .= 'status-completed';
                                break;
                        }

                        switch($newStatus) {
                            case 'pending':
                                $newStatusClass .= 'status-pending';
                                break;
                            case 'ongoing':
                                $newStatusClass .= 'status-ongoing';
                                break;
                            case 'completed':
                                $newStatusClass .= 'status-completed';
                                break;
                        }
                    @endphp
                    <span class="{{ $oldStatusClass }}">{{ ucfirst($oldStatus) }}</span>
                    <span class="arrow">→</span>
                    <span class="{{ $newStatusClass }}">{{ ucfirst($newStatus) }}</span>
                </div>
            </div>

            <p style="font-size: 13px; color: #6b7280; margin-top: 20px;">
                Updated by <strong>{{ $updatedBy->name }}</strong> on {{ now()->format('F d, Y \a\t h:i A') }}
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            © {{ date('Y') }} {{ config('app.name') }}
        </div>
    </div>
</body>

</html>
