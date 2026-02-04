<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة {{ $invoice->number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .invoice-info {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .invoice-info-row {
            display: table-row;
        }
        .invoice-info-cell {
            display: table-cell;
            padding: 8px;
            width: 50%;
        }
        .invoice-info-label {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            text-align: right;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>فاتورة</h1>
            <p>رقم الفاتورة: {{ $invoice->number }}</p>
        </div>

        <div class="invoice-info">
            <div class="invoice-info-row">
                <div class="invoice-info-cell invoice-info-label">المشروع:</div>
                <div class="invoice-info-cell">{{ $invoice->project->name ?? 'غير محدد' }}</div>
            </div>
            <div class="invoice-info-row">
                <div class="invoice-info-cell invoice-info-label">العميل:</div>
                <div class="invoice-info-cell">{{ $invoice->client->name ?? 'غير محدد' }}</div>
            </div>
            <div class="invoice-info-row">
                <div class="invoice-info-cell invoice-info-label">تاريخ الإصدار:</div>
                <div class="invoice-info-cell">{{ $invoice->issue_date->format('Y-m-d') }}</div>
            </div>
            <div class="invoice-info-row">
                <div class="invoice-info-cell invoice-info-label">تاريخ الاستحقاق:</div>
                <div class="invoice-info-cell">{{ $invoice->due_date->format('Y-m-d') }}</div>
            </div>
            <div class="invoice-info-row">
                <div class="invoice-info-cell invoice-info-label">الحالة:</div>
                <div class="invoice-info-cell">{{ $invoice->status_label }}</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>المبلغ الإجمالي</th>
                    <th>المبلغ المدفوع</th>
                    <th>المبلغ المتبقي</th>
                </tr>
            </thead>
            <tbody>
                <tr class="total-row">
                    <td>{{ number_format($invoice->total_amount, 2) }} ر.س</td>
                    <td>{{ number_format($invoice->paid_amount, 2) }} ر.س</td>
                    <td>{{ number_format($invoice->remaining_amount, 2) }} ر.س</td>
                </tr>
            </tbody>
        </table>

        @if($invoice->payments->count() > 0)
        <h3 style="margin-bottom: 10px;">الدفعات:</h3>
        <table>
            <thead>
                <tr>
                    <th>رقم الدفعة</th>
                    <th>المبلغ</th>
                    <th>تاريخ الدفعة</th>
                    <th>الحالة</th>
                    <th>طريقة الدفع</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->payments as $payment)
                <tr>
                    <td>{{ $payment->payment_no }}</td>
                    <td>{{ number_format($payment->amount, 2) }} ر.س</td>
                    <td>{{ $payment->paid_at->format('Y-m-d') }}</td>
                    <td>{{ $payment->status_label }}</td>
                    <td>{{ $payment->method_label }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if($invoice->notes)
        <div style="margin-top: 20px;">
            <h3 style="margin-bottom: 10px;">ملاحظات:</h3>
            <p>{{ $invoice->notes }}</p>
        </div>
        @endif

        <div class="footer">
            <p>تم إنشاء هذه الفاتورة تلقائياً من نظام المنار</p>
            <p>تاريخ الطباعة: {{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</body>
</html>





