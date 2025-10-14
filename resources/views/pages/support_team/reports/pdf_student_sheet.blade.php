<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('msg.student_attendance_sheet') }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>{{ __('msg.student_attendance_sheet') }}</h1>
    <p><strong>{{ __('msg.student') }}:</strong> {{ $student->user->name }}</p>
    <p><strong>{{ __('msg.period') }}:</strong> {{ $start_date }} {{ __('msg.to') }} {{ $end_date }}</p>

    <table>
        <thead>
            <tr>
                <th>{{ __('msg.date') }}</th>
                <th>{{ __('msg.status') }}</th>
                <th>{{ __('msg.note') }}</th>
                <th>{{ __('msg.marked_by') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($history as $att)
                <tr>
                    <td>{{ $att->date->format('Y-m-d') }}</td>
                    <td>{{ __('msg.' . $att->status) }}</td>
                    <td>{{ $att->note }}</td>
                    <td>{{ $att->marker ? $att->marker->name : '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">{{ __('msg.no_records_found') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
