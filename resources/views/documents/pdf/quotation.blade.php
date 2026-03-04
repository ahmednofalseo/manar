<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>عرض سعر - {{ $document->document_number }}</title>
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
            page-break-inside: avoid;
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
        
        .logo-section {
            background: transparent;
        }
        
        .office-info {
            display: table-cell;
            vertical-align: top;
            width: 75%;
            text-align: right;
        }
        
        .office-name {
            font-size: 14pt;
            font-weight: bold;
            color: #173343;
            margin-bottom: 4px;
        }
        
        .office-details {
            font-size: 8pt;
            color: #666;
            line-height: 1.5;
        }
        
        .document-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            color: #173343;
            margin: 6px 0;
            padding: 5px;
            background: #f5f5f5;
            border: 1px solid #173343;
        }
        
        .document-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
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
        
        .info-box {
            background: transparent;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 12px;
            border-radius: 5px;
            page-break-inside: avoid;
        }
        
        .info-box h3 {
            font-size: 10pt;
            font-weight: bold;
            color: #173343;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 2px solid #173343;
        }
        
        .info-row {
            margin-bottom: 6px;
            font-size: 8.5pt;
        }
        
        .info-label {
            font-weight: bold;
            color: #333;
            display: inline-block;
            width: 100px;
        }
        
        .info-value {
            color: #000;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 9.5pt;
            page-break-inside: avoid;
        }
        
        .items-table th {
            background: #173343;
            color: #fff;
            padding: 8px 6px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #000;
            font-size: 9.5pt;
        }
        
        .items-table td {
            padding: 6px 4px;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 9pt;
        }
        
        .items-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .items-table tr:hover {
            background: #f0f0f0;
        }
        
        /* Prevent page break inside table rows */
        .items-table tr {
            page-break-inside: avoid;
        }
        
        .items-table .item-name {
            text-align: right;
            font-weight: bold;
        }
        
        .items-table .item-description {
            text-align: right;
            font-size: 9pt;
            color: #666;
        }
        
        .items-table .number {
            text-align: left;
            font-family: 'Courier New', monospace;
        }
        
        .summary-section {
            margin-top: 15px;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            page-break-inside: avoid;
        }
        
        .summary-table td {
            padding: 8px 12px;
            border: 1px solid #ddd;
        }
        
        .summary-table .label {
            background: #f5f5f5;
            font-weight: bold;
            text-align: right;
            width: 70%;
        }
        
        .summary-table .value {
            text-align: left;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            width: 30%;
        }
        
        .summary-table .total-row {
            background: #1db8f8;
            color: #000;
            font-size: 13pt;
            font-weight: bold;
            border: 2px solid #173343;
        }
        
        .summary-table .total-row .label,
        .summary-table .total-row .value {
            color: #000;
            font-weight: bold;
        }
        
        .total-in-words {
            background: #e3f2fd;
            border: 2px solid #1db8f8;
            padding: 10px;
            margin: 12px 0;
            text-align: center;
            font-size: 10.5pt;
            font-weight: bold;
            color: #173343;
            page-break-inside: avoid;
        }
        
        .terms-section {
            margin-top: 30px;
            padding: 15px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .terms-section h3 {
            font-size: 12pt;
            font-weight: bold;
            color: #173343;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #173343;
        }
        
        .terms-section p {
            margin-bottom: 8px;
            font-size: 10pt;
            line-height: 1.8;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #173343;
            text-align: center;
            font-size: 9pt;
            color: #666;
            margin-bottom: 10px;
        }
        
        .signature-section {
            margin-top: 20px;
            width: 100%;
            page-break-inside: avoid;
        }
        
        .signature-container {
            display: table;
            width: 100%;
            table-layout: fixed;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .signature-left,
        .signature-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 0 15px;
        }
        
        .signature-box {
            border-top: 2px solid #173343;
            padding-top: 8px;
            text-align: center;
            min-height: 50px;
        }
        
        .signature-label {
            font-weight: bold;
            color: #173343;
            font-size: 9pt;
            margin-top: 25px;
        }
        
        .page-number {
            display: none;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div class="logo-section">
                @php
                    $logo = \App\Models\Setting::get('system_logo');
                    $logoBase64 = null;
                    $logoMime = 'image/png';
                    
                    if ($logo && !empty($logo)) {
                        // Try using Storage facade first (most reliable)
                        try {
                            if (\Storage::disk('public')->exists($logo)) {
                                $logoPath = storage_path('app/public/' . $logo);
                                if (file_exists($logoPath) && is_readable($logoPath)) {
                                    $imageData = file_get_contents($logoPath);
                                    if ($imageData !== false && strlen($imageData) > 0) {
                                        $logoBase64 = base64_encode($imageData);
                                        $imageInfo = @getimagesize($logoPath);
                                        if ($imageInfo !== false && isset($imageInfo['mime'])) {
                                            $logoMime = $imageInfo['mime'];
                                        }
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            // Continue to try alternative paths
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
                                            break;
                                        }
                                    } catch (\Exception $e) {
                                        continue;
                                    }
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
        
        <div class="document-title">عرض سعر</div>
        
        <div class="document-info">
            <div class="document-info-left">
                <div class="info-row">
                    <span class="info-label">رقم عرض السعر:</span>
                    <span class="info-value">{{ $document->document_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">تاريخ الإصدار:</span>
                    <span class="info-value">{{ $document->issue_date ? $document->issue_date->format('Y-m-d') : date('Y-m-d') }}</span>
                </div>
                @if($document->valid_until)
                <div class="info-row">
                    <span class="info-label">صالح حتى:</span>
                    <span class="info-value">{{ $document->valid_until->format('Y-m-d') }}</span>
                </div>
                @endif
            </div>
            <div class="document-info-right">
                <div class="info-row">
                    <span class="info-label">الحالة:</span>
                    <span class="info-value">
                        @if($document->status === 'draft') مسودة
                        @elseif($document->status === 'sent') مرسل
                        @elseif($document->status === 'accepted') مقبول
                        @elseif($document->status === 'expired') منتهي
                        @else {{ $document->status }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Client and Project Information - On One Line -->
    <div class="info-box">
        <div style="display: table; width: 100%;">
            @if($document->client)
            <div style="display: table-cell; width: 50%; padding-right: 10px;">
                <div class="info-row">
                    <span class="info-label">العميل:</span>
                    <span class="info-value">{{ $document->client->name }}</span>
                </div>
            </div>
            @endif
            @if($document->project)
            <div style="display: table-cell; width: 50%; padding-left: 10px;">
                <div class="info-row">
                    <span class="info-label">المشروع:</span>
                    <span class="info-value">{{ $document->project->name }}</span>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Items Table and Summary (Keep together) -->
    <div style="page-break-inside: avoid;">
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 30%;">اسم البند</th>
                    <th style="width: 25%;">الوصف</th>
                    <th style="width: 8%;">الكمية</th>
                    <th style="width: 8%;">الوحدة</th>
                    <th style="width: 12%;">سعر الوحدة</th>
                    <th style="width: 12%;">الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @forelse($document->quotationItems as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="item-name">{{ $item->item_name }}</td>
                    <td class="item-description">{{ $item->description ?: '-' }}</td>
                    <td class="number">{{ number_format($item->qty, 2) }}</td>
                    <td>{{ $item->unit }}</td>
                    <td class="number">{{ number_format($item->unit_price, 2) }} ر.س</td>
                    <td class="number">{{ number_format($item->line_total, 2) }} ر.س</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px; color: #999;">
                        لا توجد بنود
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary-section">
        <table class="summary-table">
            <tr>
                <td class="label">الإجمالي الفرعي:</td>
                <td class="value">{{ number_format($document->subtotal ?? 0, 2) }} ر.س</td>
            </tr>
            @if($document->discount_type && $document->discount_value > 0)
            <tr>
                <td class="label">
                    الخصم 
                    @if($document->discount_type === 'percent')
                        ({{ number_format($document->discount_value, 2) }}%)
                    @endif
                    :
                </td>
                <td class="value" style="color: #d32f2f;">
                    - {{ number_format(($document->discount_type === 'amount' ? $document->discount_value : (($document->subtotal ?? 0) * $document->discount_value / 100)), 2) }} ر.س
                </td>
            </tr>
            @endif
            @if($document->vat_percent > 0)
            <tr>
                <td class="label">الضريبة المضافة ({{ number_format($document->vat_percent, 2) }}%):</td>
                <td class="value">{{ number_format($document->vat_amount ?? 0, 2) }} ر.س</td>
            </tr>
            @endif
            <tr class="total-row">
                <td class="label">الإجمالي الكلي:</td>
                <td class="value">{{ number_format($document->total_price ?? 0, 2) }} ر.س</td>
            </tr>
        </table>
        
        @if($document->total_in_words)
        <div class="total-in-words">
            الإجمالي بالحروف: {{ $document->total_in_words }}
        </div>
        @endif
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div style="margin-bottom: 15px;">
            @if($document->valid_until)
                <strong>هذا العرض صالح حتى تاريخ: {{ $document->valid_until->format('Y-m-d') }}</strong>
            @else
                <strong>هذا العرض صالح لمدة 30 يوم من تاريخه</strong>
            @endif
        </div>
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div style="text-align: right; padding: 0 15px;">
            <div class="signature-box">
                <div class="signature-label">توقيع المكتب</div>
            </div>
        </div>
    </div>
</body>
</html>
