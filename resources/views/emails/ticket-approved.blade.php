<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ticket Approved</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px;">
        <h1 style="color: #28a745; margin-top: 0;">Ticket Approved</h1>
        
        <p>Hello {{ $user->name ?? $user->username }},</p>
        
        <p>We are pleased to inform you that your ticket has been approved.</p>
        
        <div style="background-color: #fff; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #28a745;">
            <h2 style="margin-top: 0; color: #333;">Ticket Details</h2>
            <p><strong>Title:</strong> {{ $ticket->title }}</p>
            <p><strong>Description:</strong> {{ $ticket->description }}</p>
            <p><strong>Status:</strong> {{ $ticket->status->value }}</p>
        </div>
        
        <p>Thank you for your patience.</p>
        
        <p style="margin-top: 30px; color: #666; font-size: 14px;">
            Best regards,<br>
            The Team
        </p>
    </div>
</body>
</html>

