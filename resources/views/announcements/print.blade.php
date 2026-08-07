<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Announcement #{{ $announcement->id }} - Getembe Digital</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.6;
            color: #111827;
            max-width: 800px;
            margin: 20px auto;
            padding: 0 20px;
            background: #fff;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #cc6c3b;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #cc6c3b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .sublogo {
            font-size: 11px;
            font-weight: bold;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 4px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .meta-table td {
            padding: 10px 14px;
            border: 1px solid #e5e7eb;
            width: 50%;
            vertical-align: top;
            font-size: 13px;
        }
        .meta-label { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #6b7280; display: block; }
        .meta-value { font-weight: bold; color: #111827; margin-top: 2px; display: block; }
        .content-section {
            border: 1px solid #e5e7eb;
            border-left: 5px solid #cc6c3b;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            font-style: italic;
            line-height: 1.8;
            background: #ffffff;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            color: #4b5563;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .images-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .images-table td {
            width: 33.33%;
            padding: 4px;
            vertical-align: top;
        }
        .image-card img {
            width: 100%;
            max-height: 180px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }
        .footer {
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
            border-top: 1px solid #f3f4f6;
            padding-top: 14px;
            margin-top: 30px;
        }
        .no-print {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            font-weight: bold;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
            text-transform: uppercase;
            cursor: pointer;
            border: none;
        }
        .btn-print { background: #cc6c3b; color: #fff; }
        .btn-back { background: #f3f4f6; color: #374151; }
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 10px; max-width: 100%; }
        }
    </style>
</head>
<body @unless($isPdf ?? false) onload="window.print()" @endunless>
    @unless($isPdf ?? false)
        <div class="no-print">
            <a href="javascript:history.back()" class="btn btn-back">&larr; Back</a>
            <button onclick="window.print()" class="btn btn-print">🖨️ Print Sheet</button>
        </div>
    @endunless

    <div class="header">
        <div class="logo">Getembe Digital News</div>
        <div class="sublogo">Official Announcement Sheet</div>
    </div>

    <table class="meta-table">
        <tr>
            <td>
                <span class="meta-label">Announcement ID</span>
                <span class="meta-value">#{{ $announcement->id }}</span>
            </td>
            <td>
                <span class="meta-label">Announcement Type</span>
                <span class="meta-value">{{ ucfirst($announcement->type) }} Announcement</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="meta-label">Submitted By</span>
                <span class="meta-value">{{ $announcement->visitor_name }} ({{ $announcement->visitor_phone }})</span>
            </td>
            <td>
                <span class="meta-label">Media Channel</span>
                <span class="meta-value">{{ strtoupper($announcement->media) }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="meta-label">Airing Date</span>
                <span class="meta-value">
                    {{ $announcement->airing_date ? $announcement->airing_date->format('M d, Y') : 'N/A' }} 
                    ({{ $announcement->days_count }} {{ Str::plural('day', $announcement->days_count) }})
                </span>
            </td>
            <td>
                <span class="meta-label">Word Count & Cost</span>
                <span class="meta-value">{{ $announcement->word_count }} words &bull; KSh {{ number_format($announcement->total_amount) }}</span>
            </td>
        </tr>
    </table>

    <div class="section-title">Announcement Text Content</div>
    <div class="content-section">
        &ldquo;{{ $announcement->content }}&rdquo;
    </div>

    @if(isset($pdfImages) && count($pdfImages) > 0)
        <div class="section-title">Attached Images ({{ count($pdfImages) }})</div>
        <table class="images-table">
            <tr>
                @foreach($pdfImages as $src)
                    <td>
                        <div class="image-card">
                            <img src="{{ $src }}" alt="Announcement attached image">
                        </div>
                    </td>
                @endforeach
            </tr>
        </table>
    @elseif(!empty($announcement->images) && is_array($announcement->images))
        <div class="section-title">Attached Images ({{ count($announcement->images) }})</div>
        <table class="images-table">
            <tr>
                @foreach($announcement->images as $imgUrl)
                    <td>
                        <div class="image-card">
                            <img src="{{ asset($imgUrl) }}" alt="Announcement attached image">
                        </div>
                    </td>
                @endforeach
            </tr>
        </table>
    @endif

    <div class="footer">
        Getembe Digital &bull; Kisii County, Kenya &bull; Generated on {{ now()->format('F d, Y \a\t H:i') }}
    </div>
</body>
</html>
