<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SeoSitemapController extends Controller
{
    /**
     * Generate the general XML sitemap.
     */
    public function sitemap(): Response
    {
        $xml = Cache::remember('seo_sitemap_xml', 900, function () {
            $siteUrl = rtrim(url('/'), '/');
            $now = now();

            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

            // 1. Homepage
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$siteUrl}/</loc>\n";
            $xml .= "    <changefreq>daily</changefreq>\n";
            $xml .= "    <priority>1.0</priority>\n";
            $xml .= "  </url>\n";

            // 2. Static Pages
            $staticPages = [
                '/about' => ['freq' => 'monthly', 'priority' => '0.4'],
                '/contact' => ['freq' => 'monthly', 'priority' => '0.4'],
                '/privacy' => ['freq' => 'monthly', 'priority' => '0.4'],
                '/announcements' => ['freq' => 'weekly', 'priority' => '0.5'],
                '/live-tv' => ['freq' => 'daily', 'priority' => '0.6'],
                '/live-radio' => ['freq' => 'daily', 'priority' => '0.6'],
            ];

            foreach ($staticPages as $path => $meta) {
                $xml .= "  <url>\n";
                $xml .= "    <loc>{$siteUrl}{$path}</loc>\n";
                $xml .= "    <changefreq>{$meta['freq']}</changefreq>\n";
                $xml .= "    <priority>{$meta['priority']}</priority>\n";
                $xml .= "  </url>\n";
            }

            // 3. Category Pages
            $categories = Category::all();
            foreach ($categories as $cat) {
                $xml .= "  <url>\n";
                $xml .= "    <loc>{$siteUrl}/{$cat->slug}</loc>\n";
                $xml .= "    <changefreq>daily</changefreq>\n";
                $xml .= "    <priority>0.8</priority>\n";
                $xml .= "  </url>\n";
            }

            // 4. Articles
            $articles = Article::published()->take(5000)->get();
            foreach ($articles as $article) {
                $isRecent = $article->published_at && $article->published_at->gt(now()->subDays(7));
                $priority = $isRecent ? '0.7' : '0.5';
                $freq = $isRecent ? 'daily' : 'weekly';
                $lastMod = $article->updated_at->toAtomString();

                $xml .= "  <url>\n";
                $xml .= "    <loc>{$siteUrl}/articles/{$article->slug}</loc>\n";
                $xml .= "    <lastmod>{$lastMod}</lastmod>\n";
                $xml .= "    <changefreq>{$freq}</changefreq>\n";
                $xml .= "    <priority>{$priority}</priority>\n";
                $xml .= "  </url>\n";
            }

            $xml .= '</urlset>';
            return $xml;
        });

        return response($xml, 200, [
            'Content-Type' => 'text/xml',
            'Cache-Control' => 'public, max-age=900'
        ]);
    }

    /**
     * Generate the 48-hour Google News XML sitemap.
     */
    public function newsSitemap(): Response
    {
        $xml = Cache::remember('seo_news_sitemap_xml', 900, function () {
            $siteUrl = rtrim(url('/'), '/');
            $siteName = htmlspecialchars(Setting::get('site_name', 'Getembe News'), ENT_XML1, 'UTF-8');
            $fortyEightHoursAgo = now()->subHours(48);

            // Fetch articles published in the last 48 hours
            $articles = Article::published()
                ->where('published_at', '>=', $fortyEightHoursAgo)
                ->get();

            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n";

            foreach ($articles as $article) {
                $loc = "{$siteUrl}/articles/{$article->slug}";
                $pubDate = $article->published_at ? $article->published_at->toIso8601String() : $article->created_at->toIso8601String();
                $title = htmlspecialchars($article->title, ENT_XML1, 'UTF-8');

                $xml .= "  <url>\n";
                $xml .= "    <loc>{$loc}</loc>\n";
                $xml .= "    <news:news>\n";
                $xml .= "      <news:publication>\n";
                $xml .= "        <news:name>{$siteName}</news:name>\n";
                $xml .= "        <news:language>en</news:language>\n";
                $xml .= "      </news:publication>\n";
                $xml .= "      <news:publication_date>{$pubDate}</news:publication_date>\n";
                $xml .= "      <news:title>{$title}</news:title>\n";
                $xml .= "    </news:news>\n";
                $xml .= "  </url>\n";
            }

            $xml .= '</urlset>';
            return $xml;
        });

        return response($xml, 200, [
            'Content-Type' => 'text/xml',
            'Cache-Control' => 'public, max-age=900'
        ]);
    }

    /**
     * Generate the strict Publisher Center RSS Feed for Google News.
     */
    public function googleNewsFeed(): Response
    {
        $xml = Cache::remember('seo_google_news_rss_feed', 900, function () {
            $siteUrl = rtrim(url('/'), '/');
            $siteName = htmlspecialchars(Setting::get('site_name', 'Getembe News'), ENT_XML1, 'UTF-8');
            $siteDesc = htmlspecialchars(Setting::get('site_description', 'Your leading source for politics, business, technology, sports, opinion, and global news.'), ENT_XML1, 'UTF-8');
            $fortyEightHoursAgo = now()->subHours(48);

            // Fetch articles published in the last 48 hours (limit to latest 100)
            $articles = Article::published()
                ->where('published_at', '>=', $fortyEightHoursAgo)
                ->take(100)
                ->get();

            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
            $xml .= "  <channel>\n";
            $xml .= "    <title>{$siteName}</title>\n";
            $xml .= "    <link>{$siteUrl}</link>\n";
            $xml .= "    <description>{$siteDesc}</description>\n";
            $xml .= "    <language>en-us</language>\n";
            $xml .= "    <lastBuildDate>" . now()->toRfc822String() . "</lastBuildDate>\n";
            $xml .= "    <atom:link href=\"{$siteUrl}/feed/google-news\" rel=\"self\" type=\"application/rss+xml\" />\n";

            foreach ($articles as $article) {
                $title = htmlspecialchars($article->title, ENT_XML1, 'UTF-8');
                $link = "{$siteUrl}/articles/{$article->slug}";
                $pubDate = $article->published_at ? $article->published_at->toRfc822String() : $article->created_at->toRfc822String();
                $author = htmlspecialchars($article->author->name ?? 'Staff Writer', ENT_XML1, 'UTF-8');
                $description = htmlspecialchars($article->seo_description ?: Str::limit(strip_tags($article->body), 150), ENT_XML1, 'UTF-8');
                
                // Content wrapped in CDATA for Google News read
                $bodyContent = "<![CDATA[" . $article->body . "]]>";

                $xml .= "    <item>\n";
                $xml .= "      <title>{$title}</title>\n";
                $xml .= "      <link>{$link}</link>\n";
                $xml .= "      <guid isPermaLink=\"true\">{$link}</guid>\n";
                $xml .= "      <pubDate>{$pubDate}</pubDate>\n";
                $xml .= "      <dc:creator>{$author}</dc:creator>\n";
                $xml .= "      <description>{$description}</description>\n";
                $xml .= "      <content:encoded>{$bodyContent}</content:encoded>\n";
                $xml .= "    </item>\n";
            }

            $xml .= "  </channel>\n";
            $xml .= '</rss>';
            return $xml;
        });

        return response($xml, 200, [
            'Content-Type' => 'application/rss+xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=900'
        ]);
    }
}
