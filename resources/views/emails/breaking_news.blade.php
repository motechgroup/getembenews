<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>🔴 BREAKING NEWS - Getembe News</title>
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
            color: #C8102E;
            font-family: sans-serif;
            font-weight: 900;
            line-height: 1.2;
            margin: 0;
            margin-bottom: 15px;
            font-size: 22px;
        }
        p {
            font-family: sans-serif;
            font-size: 14px;
            font-weight: normal;
            margin: 0;
            margin-bottom: 15px;
            color: #4a4a4a;
        }
        .news-banner {
            width: 100%;
            height: 200px;
            background-color: #eaeaea;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 15px;
        }
        .news-banner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .btn-breaking {
            background-color: #C8102E;
            border: solid 1px #C8102E;
            border-radius: 5px;
            box-sizing: border-box;
            color: #ffffff;
            cursor: pointer;
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
            margin: 15px 0;
            padding: 12px 25px;
            text-decoration: none;
            text-transform: uppercase;
        }
        .header-logo {
            text-align: center;
            padding: 20px 0;
            background-color: #C8102E;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .header-logo span {
            color: #ffffff;
            font-size: 22px;
            font-weight: 900;
            font-family: 'Helvetica Neue', Arial, sans-serif;
            letter-spacing: 0.5px;
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
                        <span>🚨 BREAKING NEWS UPDATE</span>
                    </div>

                    <table role="presentation" class="main">
                        <tr>
                            <td class="wrapper">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>
                                            <h1>{{ $article->title }}</h1>
                                            
                                            @if($article->featured_image)
                                                <div class="news-banner">
                                                    <img src="{{ Str::startsWith($article->featured_image, 'http') ? $article->featured_image : asset($article->featured_image) }}" alt="Featured Image">
                                                </div>
                                            @endif

                                            <div style="font-family: sans-serif; font-size: 14px; color: #4a4a4a; line-height: 1.6; margin-bottom: 25px;">
                                                {!! nl2br(e($body)) !!}
                                            </div>
                                            
                                            <div style="text-align: center;">
                                                <a href="{{ url('/articles/' . $article->slug) }}" class="btn-breaking" target="_blank">Read Full Story Online</a>
                                            </div>

                                            <p>— The Getembe Newsroom</p>
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
                                    <br> You are receiving this as a newsletter subscriber. <a href="{{ url('/newsletter/unsubscribe?email=' . urlencode($email ?? '')) }}">Unsubscribe instantly</a>.
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
