<!DOCTYPE html>
<html>
<head>
    <title>Welcome to {{ config('app.name') }}</title>
</head>
<body>
    <h1>Welcome {{ $student->user->name }}!</h1>
    <p>You have been successfully enrolled in our school management system.</p>
    <p>Your admission number is: {{ $student->adm_no }}</p>
    <p>Class: {{ $student->my_class->name }}</p>
    <p>Section: {{ $student->section->name }}</p>
    <br>
    <p>Best regards,<br>{{ config('app.name') }} Team</p>
</body>
</html>