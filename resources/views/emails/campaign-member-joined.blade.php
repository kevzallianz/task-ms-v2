<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9; }
        .header { background: #28a745; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { background: white; padding: 20px; }
        .panel { background: #f5f5f5; padding: 15px; border-left: 4px solid #28a745; margin: 15px 0; }
        .button { display: inline-block; background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 15px 0; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Welcome to Campaign!</h2>
        </div>
        <div class="content">
            <p>Hello {{ $member->name }},</p>
            <p>You have been added as a member to the campaign <strong>{{ $campaign->name }}</strong>.</p>
            
            <div class="panel">
                <strong>Campaign Details</strong><br>
                <strong>Name:</strong> {{ $campaign->name }}<br>
                <strong>Description:</strong> {{ $campaign->description ?? 'No description provided' }}
            </div>
            
            <p>You can now access this campaign and collaborate with other team members on tasks and projects.</p>
            
            <a href="{{ route('user.campaign') }}" class="button">View Campaign</a>
            
            <p>Thanks,<br>
            {{ config('app.name') }}</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
