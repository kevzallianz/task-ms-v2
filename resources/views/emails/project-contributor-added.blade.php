<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaign Added to Project</title>
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
            box-shadow: 0 6px 20px rgba(0,0,0,0.05);
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

        .project-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .project-desc {
            font-size: 14px;
            color: #4b5563;
            margin-bottom: 20px;
            line-height: 1.7;
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
        <h1>Campaign added to project</h1>
        <div class="subtitle">
            {{ $campaign->name }}
        </div>

        <!-- Intro -->
        <p>
            Hi {{ $recipient->name }},
        </p>

        <p>
            Your campaign has been added as a collaborator to the project below.
        </p>

        <div class="divider"></div>

        <!-- Project Summary -->
        <div class="project-title">
            {{ $project->name }}
        </div>

        <div class="project-desc">
            {!! nl2br(e($project->description ?? 'No description provided.')) !!}
        </div>

        @php
            $startDate = $project->start_date
                ? \Illuminate\Support\Carbon::parse($project->start_date)->format('M d, Y')
                : 'Not set';

            $targetDate = $project->target_date
                ? \Illuminate\Support\Carbon::parse($project->target_date)->format('M d, Y')
                : 'Not set';
        @endphp

        <!-- Meta Info -->
        <table class="meta">
            <tr>
                <td><strong>Campaign</strong></td>
                <td>{{ $campaign->name }}</td>
            </tr>
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
                <td>{{ ucfirst(str_replace('_', ' ', $project->status)) }}</td>
            </tr>
        </table>

        <!-- CTA -->
        <a href="{{ route('user.projects') }}" class="cta">
            View project
        </a>

    </div>

    <!-- Footer -->
    <div class="footer">
        © {{ date('Y') }} {{ config('app.name') }}
    </div>
</div>
</body>
</html>
