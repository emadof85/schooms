<!DOCTYPE html>
<html>
<head>
    <title>{{ $subject }}</title>
</head>
<body>
    <h1>{{ $subject }}</h1>
    <div>
        {!! nl2br(e($emailMessage)) !!}
    </div>
    @if(isset($recipients) && is_array($recipients))
        <p><strong>Recipients:</strong></p>
        <ul>
            @foreach($recipients as $recipient)
                <li>{{ $recipient['name'] }} ({{ $recipient['email'] }}) - {{ $recipient['type'] }}</li>
            @endforeach
        </ul>
    @endif
    <br>
    <p>Best regards,<br>{{ config('app.name') }} Team</p>
</body>
</html>