<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AnnouncementController extends Controller
{
    /**
     * Download announcement as plain text (.txt) document.
     */
    public function downloadTxt($id)
    {
        $announcement = Announcement::findOrFail($id);

        $airingDate = $announcement->airing_date ? $announcement->airing_date->format('F d, Y') : 'N/A';
        $expiryDate = $announcement->expiry_date ? $announcement->expiry_date->format('F d, Y') : 'N/A';
        $type = ucfirst($announcement->type) . ' Announcement';
        $media = strtoupper($announcement->media);

        $text = "==================================================\n";
        $text .= "GETEMBE DIGITAL - ANNOUNCEMENT DESK\n";
        $text .= "==================================================\n";
        $text .= "Announcement ID : #{$announcement->id}\n";
        $text .= "Type            : {$type}\n";
        $text .= "Media Target    : {$media}\n";
        $text .= "Submitted By    : {$announcement->visitor_name}\n";
        $text .= "Phone Number    : {$announcement->visitor_phone}\n";
        $text .= "Airing Date     : {$airingDate}\n";
        $text .= "Airing Duration : {$announcement->days_count} Days (Expires: {$expiryDate})\n";
        $text .= "Word Count      : {$announcement->word_count} words\n";
        $text .= "--------------------------------------------------\n";
        $text .= "ANNOUNCEMENT CONTENT:\n";
        $text .= "{$announcement->content}\n";
        $text .= "--------------------------------------------------\n";
        $text .= "Generated from Getembe Digital (https://getembetv.co.ke)\n";
        $text .= "Date: " . now()->format('Y-m-d H:i:s') . "\n";
        $text .= "==================================================\n";

        $fileName = "announcement_{$announcement->id}_" . Str::slug($announcement->visitor_name) . ".txt";

        return response($text, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Download announcement as Word (.doc) document.
     */
    public function downloadDoc($id)
    {
        $announcement = Announcement::findOrFail($id);

        $airingDate = $announcement->airing_date ? $announcement->airing_date->format('F d, Y') : 'N/A';
        $expiryDate = $announcement->expiry_date ? $announcement->expiry_date->format('F d, Y') : 'N/A';
        $type = ucfirst($announcement->type) . ' Announcement';
        $media = strtoupper($announcement->media);

        $imagesHtml = '';
        if (!empty($announcement->images) && is_array($announcement->images)) {
            $imagesHtml .= "<div style='margin-top:20px;'><h4 style='font-size:12px; text-transform:uppercase; color:#666;'>Attached Images:</h4><div style='display:flex; gap:10px;'>";
            foreach ($announcement->images as $imgUrl) {
                $fullUrl = asset($imgUrl);
                $imagesHtml .= "<img src='{$fullUrl}' style='width:150px; height:150px; object-fit:cover; border:1px solid #ccc; border-radius:4px; margin-right:10px;'>";
            }
            $imagesHtml .= "</div></div>";
        }

        $doc = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
        <head><meta charset='utf-8'><title>{$type}</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #222; margin: 40px; }
            .header { text-align: center; border-bottom: 2px solid #cc6c3b; padding-bottom: 15px; margin-bottom: 20px; }
            .title { color: #cc6c3b; font-size: 24px; font-weight: bold; text-transform: uppercase; margin: 0; }
            .subtitle { color: #666; font-size: 13px; margin-top: 5px; }
            .details-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; background: #f9f9f9; }
            .details-table td { padding: 10px 15px; border: 1px solid #eee; font-size: 13px; }
            .label { font-weight: bold; color: #444; width: 30%; }
            .content-box { font-size: 15px; background: #fff; border-left: 4px solid #cc6c3b; padding: 20px; font-style: italic; margin-bottom: 25px; line-height: 1.8; }
            .footer { font-size: 11px; color: #888; text-align: center; border-top: 1px solid #eee; padding-top: 15px; }
        </style>
        </head>
        <body>
            <div class='header'>
                <h1 class='title'>Getembe Digital News</h1>
                <div class='subtitle'>Official Announcement Document</div>
            </div>
            <table class='details-table'>
                <tr><td class='label'>Announcement ID:</td><td>#{$announcement->id}</td></tr>
                <tr><td class='label'>Announcement Type:</td><td>{$type}</td></tr>
                <tr><td class='label'>Media Channel:</td><td>{$media}</td></tr>
                <tr><td class='label'>Submitted By:</td><td>{$announcement->visitor_name} ({$announcement->visitor_phone})</td></tr>
                <tr><td class='label'>Airing Schedule:</td><td>{$airingDate} ({$announcement->days_count} Days - Expires {$expiryDate})</td></tr>
                <tr><td class='label'>Word Count:</td><td>{$announcement->word_count} words</td></tr>
            </table>
            <h3 style='font-size: 14px; text-transform: uppercase; color: #333;'>Announcement Content:</h3>
            <div class='content-box'>
                &ldquo;" . nl2br(e($announcement->content)) . "&rdquo;
            </div>
            {$imagesHtml}
            <div class='footer'>
                Getembe Digital &bull; Kisii County, Kenya &bull; Generated on " . now()->format('Y-m-d H:i') . "
            </div>
        </body>
        </html>";

        $fileName = "announcement_{$announcement->id}_" . Str::slug($announcement->visitor_name) . ".doc";

        return response($doc, 200, [
            'Content-Type' => 'application/msword; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Download announcement as PDF (.pdf) document.
     */
    /**
     * Download announcement as PDF (.pdf) document.
     */
    public function downloadPdf($id)
    {
        $announcement = Announcement::findOrFail($id);

        $processedImages = [];
        if (!empty($announcement->images) && is_array($announcement->images)) {
            foreach ($announcement->images as $imgUrl) {
                $path = public_path(ltrim($imgUrl, '/'));
                if (file_exists($path)) {
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $processedImages[] = 'data:image/' . $type . ';base64,' . base64_encode($data);
                }
            }
        }

        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);

        $dompdf = new \Dompdf\Dompdf($options);
        $html = view('announcements.print', [
            'announcement' => $announcement,
            'isPdf' => true,
            'pdfImages' => $processedImages,
        ])->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $fileName = "announcement_{$announcement->id}_" . Str::slug($announcement->visitor_name) . ".pdf";

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Render clean printable announcement sheet.
     */
    public function print($id)
    {
        $announcement = Announcement::findOrFail($id);

        return view('announcements.print', compact('announcement'));
    }
}
