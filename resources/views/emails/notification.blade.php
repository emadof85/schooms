<!DOCTYPE html>
<html>
<head>
    <title>{{ $subject }}</title>
</head>
<body>
    <h1>{{ $subject }}</h1>
    <div>
        {!! nl2br(e($message)) !!}
    </div>
    <br>
    <p>Best regards,<br>{{ config('app.name') }} Team</p>
</body>
</html>