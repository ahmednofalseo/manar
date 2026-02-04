<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $document->title }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Cairo', 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.8;
            direction: rtl;
            text-align: right;
        }
        
        .header {
            border-bottom: 3px solid #173343;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header-content {
            display: table;
            width: 100%;
        }
        
        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .header-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: left;
        }
        
        .logo {
            font-size: 24px;
            font-weight: 700;
            color: #173343;
            margin-bottom: 10px;
        }
        
        .office-info {
            font-size: 11px;
            color: #666;
            line-height: 1.6;
        }
        
        .document-info {
            text-align: right;
        }
        
        .document-title {
            font-size: 20px;
            font-weight: 700;
            color: #173343;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .document-number {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .content {
            margin: 30px 0;
            min-height: 400px;
        }
        
        .content p {
            margin-bottom: 15px;
        }
        
        .content h1, .content h2, .content h3 {
            color: #173343;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        
        .content h1 {
            font-size: 18px;
        }
        
        .content h2 {
            font-size: 16px;
        }
        
        .content h3 {
            font-size: 14px;
        }
        
        .content ul, .content ol {
            margin-right: 20px;
            margin-bottom: 15px;
        }
        
        .content table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .content table th,
        .content table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }
        
        .content table th {
            background-color: #173343;
            color: white;
            font-weight: 600;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        
        .signature-section {
            margin-top: 50px;
            display: table;
            width: 100%;
        }
        
        .signature {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 20px;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
        }
        
        @page {
            margin: 50px 30px;
            footer: html_footer;
        }
        
        .page-number {
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div class="header-left">
                <div class="logo">{{ \App\Helpers\SettingsHelper::systemName() }}</div>
                <div class="office-info">
                    <p>المملكة العربية السعودية</p>
                    <p>الرياض - المملكة العربية السعودية</p>
                    @php
                        $phone = \App\Helpers\SettingsHelper::get('office_phone', '');
                        $email = \App\Helpers\SettingsHelper::get('office_email', '');
                    @endphp
                    @if($phone)
                    <p>هاتف: {{ $phone }}</p>
                    @endif
                    @if($email)
                    <p>بريد إلكتروني: {{ $email }}</p>
                    @endif
                </div>
            </div>
            <div class="header-right">
                <div class="document-info">
                    <div class="document-number">رقم المستند: {{ $document->document_number }}</div>
                    <div style="font-size: 11px; color: #666;">التاريخ: {{ $document->created_at->format('Y-m-d') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Title -->
    <div class="document-title">{{ $document->title }}</div>

    <!-- Document Content -->
    <div class="content">
        {!! $document->replaceVariables() !!}
    </div>

    <!-- Signature Section -->
    @if($document->status === 'approved')
    <div class="signature-section">
        <div class="signature">
            <div class="signature-line">
                <p style="font-weight: 600;">{{ $document->approver->name ?? 'المعتمد' }}</p>
                <p style="font-size: 10px; color: #666;">التوقيع</p>
            </div>
        </div>
        <div class="signature">
            <div class="signature-line">
                <p style="font-weight: 600;">{{ $document->creator->name }}</p>
                <p style="font-size: 10px; color: #666;">المنشئ</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div class="page-number">
            صفحة <span class="page"></span> من <span class="topage"></span>
        </div>
        <div style="margin-top: 10px;">
            {{ \App\Helpers\SettingsHelper::systemName() }} - {{ date('Y') }}
        </div>
    </div>
</body>
</html>

