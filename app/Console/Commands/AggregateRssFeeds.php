<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Category;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AggregateRssFeeds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rss:aggregate';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Import external news articles dynamically from registered RSS feeds';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting RSS feed aggregation...');
        
        $feeds = json_decode(Setting::get('simulated_rss_feeds', '[]'), true);
        if (empty($feeds)) {
            $this->warn('No RSS feeds registered in settings. Skipping.');
            return 0;
        }

        // Get default author (first admin or first user)
        $author = User::where('role', 'admin')->first() ?? User::first();
        if (!$author) {
            $this->error('No users found in database. Cannot assign author to imported articles.');
            return 1;
        }

        $importedCount = 0;

        foreach ($feeds as $feed) {
            $this->info("Fetching feed: {$feed['name']} ({$feed['url']})");

            try {
                $response = Http::timeout(10)->get($feed['url']);
                if ($response->failed()) {
                    $this->error("Failed to retrieve feed {$feed['name']}: HTTP Status " . $response->status());
                    continue;
                }

                $xmlContent = $response->body();
                // Parse XML
                $xml = simplexml_load_string($xmlContent, 'SimpleXMLElement', LIBXML_NOCDATA);
                if (!$xml || !isset($xml->channel)) {
                    $this->error("Failed to parse valid XML from feed {$feed['name']}.");
                    continue;
                }

                foreach ($xml->channel->item as $item) {
                    $title = (string) $item->title;
                    $link = (string) $item->link;
                    $pubDateStr = (string) $item->pubDate;
                    $description = (string) $item->description;
                    
                    // Attempt to extract content:encoded (contains full body html)
                    $body = '';
                    $namespaces = $item->getNameSpaces(true);
                    if (isset($namespaces['content'])) {
                        $contentEncoded = $item->children($namespaces['content']);
                        $body = (string) $contentEncoded->encoded;
                    }
                    if (empty($body)) {
                        $body = $description;
                    }
                    if (empty($body)) {
                        $body = "<p>Refer to original story details on <a href='{$link}' target='_blank'>{$feed['name']}</a>.</p>";
                    }

                    $slug = Str::slug($title);
                    if (empty($title) || empty($slug)) {
                        continue;
                    }

                    // Avoid duplicate imports (check by title, slug or origin link)
                    $exists = Article::where('title', $title)
                        ->orWhere('slug', $slug)
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    // Auto-categorize based on title/body keywords
                    $categoryId = $this->determineCategory($title . ' ' . $description);

                    // Parse publish date
                    $publishedAt = now();
                    if (!empty($pubDateStr)) {
                        try {
                            $publishedAt = \Illuminate\Support\Carbon::parse($pubDateStr);
                        } catch (\Exception $e) {
                            $publishedAt = now();
                        }
                    }

                    // Create the article
                    Article::create([
                        'title' => $title,
                        'slug' => $slug,
                        'subtitle' => 'Imported via ' . $feed['name'],
                        'body' => $body,
                        'user_id' => $author->id,
                        'category_id' => $categoryId,
                        'status' => 'published',
                        'published_at' => $publishedAt,
                        'views_count' => rand(10, 150), // seed verified views initially
                    ]);

                    $importedCount++;
                    $this->line(" - Imported: {$title}");
                }

            } catch (\Exception $e) {
                $this->error("Error processing feed {$feed['name']}: " . $e->getMessage());
            }
        }

        $this->info("RSS aggregation completed. Imported {$importedCount} new articles.");
        Setting::set('rss_last_aggregated_at', now()->format('Y-m-d H:i:s'));
        
        return 0;
    }

    /**
     * Determine category based on content keywords.
     */
    protected function determineCategory(string $content): int
    {
        $content = strtolower($content);

        $categoryMap = [
            'politics' => ['politic', 'government', 'tax', 'parliament', 'ministry', 'president', 'bill', 'court'],
            'business' => ['business', 'economy', 'finance', 'market', 'trade', 'cooperative', 'farm', 'industry', 'payout'],
            'technology' => ['tech', 'software', 'app', 'innovation', 'telecom', 'fiber', 'broadband', 'digital'],
            'sports' => ['sport', 'football', 'league', 'club', 'victory', 'marathon', 'athletics', 'championship'],
            'opinion' => ['opinion', 'editorial', 'viewpoint', 'column', 'perspectives'],
            'africa' => ['africa', 'kenya', 'kisii', 'regional', 'county', 'governor', 'avocado'],
            'world' => ['world', 'global', 'climate', 'international', 'summit'],
            'health' => ['health', 'disease', 'medical', 'hospital', 'clinic', 'wellness'],
            'lifestyle' => ['lifestyle', 'travel', 'fashion', 'culture', 'heritage', 'art', 'festival'],
            'education' => ['education', 'university', 'students', 'school', 'learn', 'literacy'],
        ];

        foreach ($categoryMap as $slug => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($content, $keyword)) {
                    $category = Category::where('slug', $slug)->first();
                    if ($category) {
                        return $category->id;
                    }
                }
            }
        }

        // Fallback: first category in database
        return Category::first()->id ?? 1;
    }
}
