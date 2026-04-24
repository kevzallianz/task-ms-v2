<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Deleted</title>
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

        .deleted-project {
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

        .project-name {
            font-size: 18px;
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

        .warning-box {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            padding: 12px 16px;
            margin: 20px 0;
            border-radius: 6px;
        }

        .warning-icon {
            display: inline-block;
            width: 16px;
            height: 16px;
            background: #f59e0b;
            border-radius: 50%;
            margin-right: 8px;
            vertical-align: middle;
        }

        .warning-text {
            font-size: 13px;
            color: #92400e;
            line-height: 1.5;
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
            <h1>Project Deleted</h1>
            <div class="campaign">
                {{ $campaignName }}
            </div>

            <!-- Intro -->
            <p>
                Hello,<br>
                A project has been permanently deleted by {{ $deletedBy->name }}.
            </p>

            <div class="divider"></div>

            <!-- Deleted Project Info -->
            <div class="deleted-project">
                <div class="deleted-label">Deleted Project</div>
                <div class="project-name">{{ $projectName }}</div>
            </div>

            <div class="info-box">
                Deleted by <strong>{{ $deletedBy->name }}</strong> on {{ now()->format('F d, Y \a\t h:i A') }}
            </div>

            <!-- Warning Box -->
            <div class="warning-box">
                <div class="warning-text">
                    <span class="warning-icon"></span>
                    <strong>Important:</strong> This project and all its associated data (tasks, remarks, activities, and contributors) have been permanently removed and cannot be recovered.
                </div>
            </div>

            <p style="font-size: 13px; color: #6b7280; margin-top: 20px;">
                If you have any questions or concerns about this deletion, please contact {{ $deletedBy->name }} or your campaign administrator.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            © {{ date('Y') }} {{ config('app.name') }}
        </div>
    </div>
</body>

</html>
