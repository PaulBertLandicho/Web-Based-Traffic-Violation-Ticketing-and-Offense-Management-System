<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Enforcer Registration</title>
</head>

<body>
    <h2>Hello {{ $enforcer['enforcer_name'] }},</h2>

    <p>You have been successfully registered as a <b>Traffic Enforcer</b>.</p>

    <p><strong>Your Details:</strong></p>
    <ul>
        <li><b>ID:</b> {{ $enforcer['enforcer_id'] }}</li>
        <li><b>Email:</b> {{ $enforcer['enforcer_email'] }}</li>
        <li><b>Assigned Area:</b> {{ $enforcer['assigned_area'] }}</li>
        <li><b>Gender:</b> {{ $enforcer['gender'] }}</li>
    </ul>

    <p>Please use your registered email and password to log in to the system.</p>

    <p>Thank you,<br>
        Traffic Admin</p>
</body>

</html>