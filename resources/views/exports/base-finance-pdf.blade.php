<!-- resources/views/exports/base-finance-pdf.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #333; }
        .summary { background: #f8f9fa; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th { background-color: #343a40; color: white; padding: 10px; text-align: left; }
        .table td { padding: 8px 10px; border-bottom: 1px solid #ddd; }
        .table tr:nth-child(even) { background-color: #f8f9fa; }
        .total-row { background-color: #e9ecef !important; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; color: #666; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>
            Period: {{ date('M d, Y', strtotime($startDate)) }} - {{ date('M d, Y', strtotime($endDate)) }}
            @if($category)
                | Category: {{ $category->name }}
            @endif
        </p>
        <p>Generated on: {{ date('M d, Y H:i') }}</p>
    </div>

    <div class="summary">
        <strong>Total Records:</strong> {{ $records->count() }} | 
        <strong>Total Amount:</strong> ${{ number_format($totalAmount, 2) }}
    </div>

    @yield('table-content')

    <div class="footer">
        This report was generated automatically from the system.
    </div>
</body>
</html>