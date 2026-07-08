<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Getembe News Weekly Digest</title>
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
            padding: 25px;
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
        h1, h2, h3 {
            color: #111111;
            font-family: sans-serif;
            font-weight: 800;
            line-height: 1.3;
            margin: 0;
            margin-bottom: 15px;
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
        .article-card {
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .article-card:last-child {
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 0;
        }
        .article-title {
            font-size: 16px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 5px;
        }
        .article-title a {
            color: #111827;
            text-decoration: none;
        }
        .article-title a:hover {
            color: #C8102E;
            text-decoration: underline;
        }
        .article-meta {
            font-size: 11px;
            color: #9ca3af;
            margin-bottom: 8px;
        }
        .article-excerpt {
            font-size: 13px;
            color: #4b5563;
            line-height: 1.5;
        }
        .read-more {
            font-size: 12px;
            font-weight: bold;
            color: #C8102E;
            text-decoration: none;
            display: inline-block;
            margin-top: 5px;
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
                                <h2>Your Weekly Digest</h2>
                                <p>Here are the top stories and regional highlights you might have missed this week:</p>
                                
                                <div style="margin-top: 25px;">
                                    @forelse($articles as $article)
                                        <div class="article-card">
                                            <div class="article-title">
                                                <a href="{{ url('/articles/' . $article->slug) }}" target="_blank">{{ $article->title }}</a>
                                            </div>
                                            <div class="article-meta">
                                                In {{ $article->category->name }} &bull; By {{ $article->author->name }}
                                            </div>
                                            <div class="article-excerpt">
                                                {{ $article->seo_description ?: Str::limit(strip_tags($article->body), 130) }}
                                            </div>
                                            <a href="{{ url('/articles/' . $article->slug) }}" class="read-more" target="_blank">Read Story &rarr;</a>
                                        </div>
                                    @empty
                                        <p style="text-align: center; color: #9ca3af; padding: 20px 0;">No new stories this week.</p>
                                    @endforelse
                                </div>
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
