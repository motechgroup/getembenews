<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Welcome to Getembe News!</title>
    <style>
        body {
            background-color: #f6f6f6;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }
        table {
            border-collapse: separate;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
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
        h1, h2, h3, h4 {
            color: #111111;
            font-family: sans-serif;
            font-weight: 800;
            line-height: 1.4;
            margin: 0;
            margin-bottom: 15px;
        }
        p, ul, ol {
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
            background-color: #ffffff;
            border-radius: 5px;
            text-align: center;
        }
        .btn a {
            background-color: #ffffff;
            border: solid 1px #C8102E;
            border-radius: 5px;
            box-sizing: border-box;
            color: #C8102E;
            cursor: pointer;
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            padding: 12px 25px;
            text-decoration: none;
            text-transform: capitalize;
        }
        .btn-primary table td {
            background-color: #C8102E;
        }
        .btn-primary a {
            background-color: #C8102E;
            border-color: #C8102E;
            color: #ffffff;
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
                    <!-- Brand Header -->
                    <div class="header-logo">
                        <span>Getembe <span class="highlight">Digital</span></span>
                    </div>

                    <!-- Main Email Card -->
                    <table role="presentation" class="main">
                        <tr>
                            <td class="wrapper">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>
                                            <h1>Welcome to Getembe News!</h1>
                                            <p>Hi there,</p>
                                            <p>Thank you for subscribing to Getembe News. You are now part of our growing community of readers who value fast, reliable, and in-depth local news and analysis from Kisii County and beyond.</p>
                                            <p>Here is what you can look forward to:</p>
                                            <ul>
                                                <li><strong>Breaking News Alerts</strong>: Be the first to know about local updates.</li>
                                                <li><strong>Weekly Digests</strong>: A curated list of the most read stories from our desk.</li>
                                                <li><strong>Exclusive Insights</strong>: In-depth reporting on business, politics, and culture.</li>
                                            </ul>
                                            
                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary" style="margin: 25px 0;">
                                                <tbody>
                                                    <tr>
                                                        <td align="left">
                                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                                                <tbody>
                                                                    <tr>
                                                                        <td> <a href="{{ url('/') }}" target="_blank">Visit Getembe News</a> </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            
                                            <p>If you have any feedback or news tips, feel free to contact us by reply or through our tip lines.</p>
                                            <p>Happy Reading!</p>
                                            <p>— The Getembe News Editorial Team</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <!-- Footer -->
                    <div class="footer">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="content-block">
                                    <span class="apple-link">Getembe News, Kisii, Kenya</span>
                                    <br> Don't want these emails? <a href="{{ url('/newsletter/unsubscribe?email=' . urlencode($email ?? '')) }}">Unsubscribe instantly</a>.
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
