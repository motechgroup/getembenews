<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Verify Email Address - Getembe News</title>
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
        a {
            color: #C8102E;
            text-decoration: underline;
        }
        .btn {
            box-sizing: border-box;
            width: 100%;
        }
        .btn table {
            width: auto;
        }
        .btn table td {
            background-color: #C8102E;
            border-radius: 5px;
            text-align: center;
        }
        .btn a {
            background-color: #C8102E;
            border: solid 1px #C8102E;
            border-radius: 5px;
            box-sizing: border-box;
            color: #ffffff;
            cursor: pointer;
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            padding: 12px 25px;
            text-decoration: none;
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
                                            <h1>Verify Email Address</h1>
                                            <p>Hello,</p>
                                            
                                            <div style="font-family: sans-serif; font-size: 14px; color: #4a4a4a; line-height: 1.6; margin-bottom: 25px;">
                                                {!! nl2br(e($body)) !!}
                                            </div>
                                            
                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn" style="margin: 25px 0;">
                                                <tbody>
                                                    <tr>
                                                        <td align="left">
                                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                                                <tbody>
                                                                    <tr>
                                                                        <td> <a href="{{ $url }}" target="_blank">Verify Email Address</a> </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <p style="font-size: 11px; color: #777; border-top: 1px solid #eee; pt-15; margin-top: 25px;">
                                                If you are having trouble clicking the "Verify Email Address" button, copy and paste the URL below into your web browser: <br>
                                                <a href="{{ $url }}" style="color: #C8102E; word-break: break-all;">{{ $url }}</a>
                                            </p>
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
