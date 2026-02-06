<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاتورة - {{ $invoice->number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Cairo', 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            color: #000;
            line-height: 1.6;
            direction: rtl;
            text-align: right;
        }
        
        .header {
            border-bottom: 2px solid #173343;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        
        .header-content {
            display: table;
            width: 100%;
            margin-bottom: 6px;
        }
        
        .logo-section {
            display: table-cell;
            vertical-align: top;
            width: 25%;
        }
        
        .logo-section img {
            max-width: 100px;
            max-height: 70px;
            object-fit: contain;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
            filter: brightness(1.1) contrast(1.1);
            background: transparent;
        }
        
        .office-info {
            display: table-cell;
            vertical-align: top;
            width: 75%;
            text-align: right;
        }
        
        .office-name {
            font-size: 12pt;
            font-weight: bold;
            color: #173343;
            margin-bottom: 4px;
        }
        
        .office-details {
            font-size: 7pt;
            color: #666;
            line-height: 1.4;
        }
        
        .document-title {
            text-align: center;
            font-size: 18pt;
            font-weight: bold;
            color: #173343;
            margin: 10px 0;
            padding: 5px;
        }
        
        .document-info {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            font-size: 8pt;
        }
        
        .document-info-left,
        .document-info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .document-info-right {
            text-align: left;
        }
        
        .info-row {
            margin-bottom: 4px;
            font-size: 8.5pt;
        }
        
        .info-label {
            font-weight: bold;
            color: #333;
            display: inline-block;
            width: 80px;
        }
        
        .info-value {
            color: #000;
        }
        
        .client-project-info {
            display: table;
            width: 100%;
            table-layout: fixed;
            margin-bottom: 15px;
            font-size: 8pt;
        }
        
        .client-project-info > div {
            display: table-cell;
            width: 50%;
            padding: 8px;
            border: 1px solid #ddd;
            background: #f9f9f9;
            text-align: right;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 9.5pt;
        }
        
        .summary-table th {
            background: #173343;
            color: #fff;
            padding: 10px;
            text-align: center;
            font-weight: bold;
        }
        
        .summary-table td {
            padding: 10px;
            text-align: right;
            border: 1px solid #ddd;
        }
        
        .summary-table .total-row {
            background: #f5f5f5;
            font-weight: bold;
            font-size: 11pt;
        }
        
        .payments-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 9pt;
        }
        
        .payments-table th {
            background: #173343;
            color: #fff;
            padding: 8px 6px;
            text-align: center;
            font-weight: bold;
        }
        
        .payments-table td {
            padding: 8px 6px;
            text-align: right;
            border: 1px solid #ddd;
        }
        
        .payments-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .notes-section {
            margin-top: 20px;
            padding: 10px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .notes-section h3 {
            font-size: 10pt;
            font-weight: bold;
            color: #173343;
            margin-bottom: 8px;
        }
        
        .notes-section p {
            font-size: 9pt;
            color: #333;
            line-height: 1.6;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 8pt;
            color: #666;
        }
        
        .signature-section {
            margin-top: 30px;
        }
        
        .signature-box {
            border-top: 1px solid #173343;
            padding-top: 5px;
            min-height: 40px;
        }
        
        .signature-label {
            font-size: 9pt;
            margin-top: 15px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="logo-section">
                @php
                    $logo = \App\Models\Setting::get('system_logo');
                    $logoBase64 = null;
                    $logoMime = 'image/png';
                    
                    if ($logo) {
                        // Try to get file from storage disk
                        if (\Storage::disk('public')->exists($logo)) {
                            $logoPath = \Storage::disk('public')->path($logo);
                            try {
                                $imageData = file_get_contents($logoPath);
                                if ($imageData !== false && strlen($imageData) > 0) {
                                    $logoBase64 = base64_encode($imageData);
                                    $imageInfo = @getimagesize($logoPath);
                                    if ($imageInfo !== false && isset($imageInfo['mime'])) {
                                        $logoMime = $imageInfo['mime'];
                                    }
                                }
                            } catch (\Exception $e) {
                                \Log::error('Error loading logo from storage for PDF: ' . $e->getMessage(), [
                                    'logo' => $logo,
                                    'logoPath' => $logoPath
                                ]);
                            }
                        }
                        
                        // If Storage didn't work, try different paths
                        if (!$logoBase64) {
                            $possiblePaths = [
                                storage_path('app/public/' . $logo),
                                public_path('storage/' . $logo),
                                storage_path('app/' . $logo),
                                public_path($logo),
                                base_path('storage/app/public/' . $logo),
                                $logo, // Absolute path
                            ];
                            
                            foreach ($possiblePaths as $path) {
                                if (file_exists($path) && is_file($path) && is_readable($path)) {
                                    try {
                                        $imageData = file_get_contents($path);
                                        if ($imageData !== false && strlen($imageData) > 0) {
                                            $logoBase64 = base64_encode($imageData);
                                            $imageInfo = @getimagesize($path);
                                            if ($imageInfo !== false && isset($imageInfo['mime'])) {
                                                $logoMime = $imageInfo['mime'];
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        \Log::error('Error loading logo for PDF: ' . $e->getMessage(), [
                                            'logo' => $logo,
                                            'logoPath' => $path
                                        ]);
                                    }
                                    break;
                                }
                            }
                        }
                    }
                @endphp
                @if($logoBase64)
                    <img src="data:{{ $logoMime }};base64,{{ $logoBase64 }}" alt="Logo" style="max-width: 100px; max-height: 70px; object-fit: contain; background: transparent;">
                @else
                    <div style="width: 100px; height: 70px; background: transparent; color: #173343; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 9pt; text-align: center; padding: 3px; border: 1px solid #173343;">
                        {{ \App\Models\Setting::get('system_name', 'مكتب المنار') }}
                    </div>
                @endif
            </div>
            <div class="office-info">
                <div class="office-name">{{ \App\Models\Setting::get('system_name', 'مكتب المنار للاستشارات الهندسية') }}</div>
                <div class="office-details">
                    @if(\App\Models\Setting::get('office_license'))
                        <div>رقم السجل/الترخيص: {{ \App\Models\Setting::get('office_license') }}</div>
                    @endif
                    @if(\App\Models\Setting::get('office_address'))
                        <div>العنوان: {{ \App\Models\Setting::get('office_address') }}</div>
                    @endif
                    @if(\App\Models\Setting::get('office_phone'))
                        <div>الهاتف: {{ \App\Models\Setting::get('office_phone') }}</div>
                    @endif
                    @if(\App\Models\Setting::get('office_email'))
                        <div>البريد الإلكتروني: {{ \App\Models\Setting::get('office_email') }}</div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="document-title">فاتورة</div>
        
        <div class="document-info">
            <div class="document-info-left">
                <div class="info-row">
                    <span class="info-label">رقم الفاتورة:</span>
                    <span class="info-value">{{ $invoice->number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">تاريخ الإصدار:</span>
                    <span class="info-value">{{ $invoice->issue_date ? $invoice->issue_date->format('Y-m-d') : '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">تاريخ الاستحقاق:</span>
                    <span class="info-value">{{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '-' }}</span>
                </div>
            </div>
            <div class="document-info-right">
                <div class="info-row">
                    <span class="info-label">الحالة:</span>
                    <span class="info-value">{{ $invoice->status_label }}</span>
                </div>
                @if($invoice->payment_method_label)
                <div class="info-row">
                    <span class="info-label">طريقة الدفع:</span>
                    <span class="info-value">{{ $invoice->payment_method_label }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Client & Project Information -->
    <div class="client-project-info">
        @if($invoice->client)
        <div>
            <span style="font-weight: bold; color: #173343;">العميل:</span>
            <span>{{ $invoice->client->name }}</span>
            @if($invoice->client->phone)
                <span style="color: #666; font-size: 7.5pt;"> - {{ $invoice->client->phone }}</span>
            @endif
        </div>
        @endif
        
        @if($invoice->project)
        <div>
            <span style="font-weight: bold; color: #173343;">المشروع:</span>
            <span>{{ $invoice->project->name }}</span>
        </div>
        @endif
    </div>
    
    <!-- Financial Summary -->
    <table class="summary-table">
        <thead>
            <tr>
                <th>المبلغ الإجمالي</th>
                <th>المبلغ المدفوع</th>
                <th>المبلغ المتبقي</th>
            </tr>
        </thead>
        <tbody>
            <tr class="total-row">
                <td>{{ number_format($invoice->total_amount ?? 0, 2) }} ر.س</td>
                <td>{{ number_format($invoice->paid_amount ?? 0, 2) }} ر.س</td>
                <td>{{ number_format($invoice->remaining_amount ?? 0, 2) }} ر.س</td>
            </tr>
        </tbody>
    </table>
    
    <!-- Payments Table -->
    @if($invoice->payments && $invoice->payments->count() > 0)
    <h3 style="font-size: 11pt; font-weight: bold; color: #173343; margin: 20px 0 10px 0;">الدفعات:</h3>
    <table class="payments-table">
        <thead>
            <tr>
                <th style="width: 15%;">رقم الدفعة</th>
                <th style="width: 20%;">المبلغ</th>
                <th style="width: 15%;">تاريخ الدفعة</th>
                <th style="width: 15%;">الحالة</th>
                <th style="width: 20%;">طريقة الدفع</th>
                <th style="width: 15%;">ملاحظات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->payments as $payment)
            <tr>
                <td>{{ $payment->payment_no ?? '-' }}</td>
                <td>{{ number_format($payment->amount ?? 0, 2) }} ر.س</td>
                <td>{{ $payment->paid_at ? $payment->paid_at->format('Y-m-d') : '-' }}</td>
                <td>{{ $payment->status_label ?? '-' }}</td>
                <td>{{ $payment->method_label ?? '-' }}</td>
                <td>{{ $payment->notes ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    
    <!-- Notes -->
    @if($invoice->notes)
    <div class="notes-section">
        <h3>ملاحظات:</h3>
        <p>{{ $invoice->notes }}</p>
    </div>
    @endif
    
    <!-- Signature Section -->
    <div class="signature-section">
        <div style="text-align: right; padding: 0 15px;">
            <div class="signature-box">
                <div class="signature-label">توقيع المكتب</div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>تم إنشاء هذه الفاتورة تلقائياً من نظام {{ \App\Models\Setting::get('system_name', 'المنار') }}</p>
        <p>تاريخ الطباعة: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
