<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>SMTP Connection Test - Getembe News</title>
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
            background-color: #d1fae5;
            color: #065f46;
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 12px;
            display: inline-block;
            margin-bottom: 15px;
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
                                            <h1>SMTP Connection Test</h1>
                                            <div class="status-badge">✓ SMTP CONNECTION SUCCESSFUL</div>
                                            <p>Hi Admin,</p>
                                            <p>This is a test email sent from the Getembe News administration panel to verify your SMTP mail server configurations.</p>
                                            <p>If you received this message, it means your SMTP server settings are correctly configured, and the mail system is functioning properly.</p>
                                            <p><strong>Mail Recipient:</strong> {{ $recipient }}</p>
                                            <p><strong>Time Sent:</strong> {{ now()->toDayDateTimeString() }}</p>
                                            <p>— Getembe News Mail System</p>
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
                                    <span class="apple-link">Getembe News Admin System</span>
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
