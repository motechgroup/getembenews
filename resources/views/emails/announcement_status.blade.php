<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Announcement Status Update - Getembe News</title>
    <style>
        body {
            background-color: #f6f6f6;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        table {
            border-collapse: separate;
            width: 100%;
        }
        table td {
            font-family: sans-serif;
            font-size: 14px;
            vertical-align: top;
        }
        .container {
            display: block;
            margin: 0 auto !important;
            max-width: 580px;
            padding: 10px;
            width: 580px;
        }
        .content {
            display: block;
            margin: 0 auto;
            max-width: 580px;
            padding: 10px;
        }
        .main {
            background: #ffffff;
            border-radius: 8px;
            width: 100%;
            border: 1px solid #e9e9e9;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }
        .wrapper {
            box-sizing: border-box;
            padding: 30px;
        }
        .footer {
            clear: both;
            margin-top: 20px;
            text-align: center;
            width: 100%;
        }
        .footer td, .footer p, .footer span, .footer a {
            color: #999999;
            font-size: 12px;
            text-align: center;
        }
        h1 {
            color: #111111;
            font-family: sans-serif;
            font-weight: 800;
            line-height: 1.4;
            margin: 0;
            margin-bottom: 15px;
            font-size: 20px;
        }
        p {
            font-family: sans-serif;
            font-size: 14px;
            font-weight: normal;
            margin: 0;
            margin-bottom: 15px;
            color: #4a4a4a;
        }
        .status-badge {
            padding: 6px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11px;
            display: inline-block;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        .status-approved {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .info-box {
            background-color: #f9fafb;
            border-left: 4px solid #C8102E;
            padding: 20px;
            margin-bottom: 15px;
            font-family: sans-serif;
            font-size: 13px;
            color: #4b5563;
            white-space: pre-wrap;
            line-height: 1.6;
        }
        .header-logo {
            text-align: center;
            padding: 20px 0;
            background-color: #111827;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .header-logo span {
            color: #ffffff;
            font-size: 20px;
            font-weight: 900;
            font-family: 'Georgia', serif;
            letter-spacing: -0.5px;
        }
        .header-logo span .highlight {
            color: #C8102E;
        }
    </style>
</head>
<body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
        <tr>
            <td>&nbsp;</td>
            <td class="container">
                <div class="content">
                    <div class="header-logo">
                        <span>Getembe <span class="highlight">Digital</span></span>
                    </div>

                    <table role="presentation" class="main">
                        <tr>
                            <td class="wrapper">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>
                                            <h1>Announcement Status Update</h1>
                                            
                                            @if($announcement->is_approved)
                                                <div class="status-badge status-approved">✓ Approved & Active</div>
                                            @else
                                                <div class="status-badge status-pending">ℹ Awaiting Approval Review</div>
                                            @endif

                                            <div class="info-box">{!! nl2br(e($body)) !!}</div>

                                            <p>If you have any questions or require modifications to your notice, please contact our support team at <a href="mailto:{{ Setting::get('contact_email', 'support@getembenews.com') }}">{{ Setting::get('contact_email', 'support@getembenews.com') }}</a>.</p>
                                            <p>Thank you for choosing Getembe News.</p>
                                            <p>— The Getembe News Customer Desk</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <div class="footer">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="content-block">
                                    <span class="apple-link">Getembe News, Kisii, Kenya</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </td>
            <td>&nbsp;</td>
        </tr>
    </table>
</body>
</html>
