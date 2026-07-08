<?php

namespace App\Support;

use Illuminate\Support\Str;
use App\Models\Setting;
use App\Models\Article;
use App\Models\Category;

class Seo
{
    /**
     * Generate an enhanced, standardized canonical URL.
     * Strips tracking queries and standardizes scheme and host.
     */
    public static function canonicalUrl(): string
    {
        $request = request();
        $url = $request->url(); // Returns url without query parameters
        $queryParams = $request->query();

        // Standardize scheme and host based on APP_URL config if configured
        $appUrl = config('app.url', 'http://localhost');
        if (Str::startsWith($appUrl, 'http')) {
            $parsedApp = parse_url($appUrl);
            $parsedCurrent = parse_url($url);
            
            $scheme = $parsedApp['scheme'] ?? $parsedCurrent['scheme'] ?? 'http';
            $host = $parsedApp['host'] ?? $parsedCurrent['host'] ?? 'localhost';
            $port = isset($parsedApp['port']) ? ':' . $parsedApp['port'] : (isset($parsedCurrent['port']) ? ':' . $parsedCurrent['port'] : '');
            $path = $parsedCurrent['path'] ?? '';
            
            $url = "{$scheme}://{$host}{$port}{$path}";
        }

        // Clean tracking/duplicate query parameters
        $allowedParams = [];
        
        // Keep pagination parameter
        if (isset($queryParams['page'])) {
            $allowedParams['page'] = $queryParams['page'];
        }

        // Keep search parameter only on the search page
        if ($request->is('search*') && isset($queryParams['q'])) {
            $allowedParams['q'] = $queryParams['q'];
        }

        if (!empty($allowedParams)) {
            $url .= '?' . http_build_query($allowedParams);
        }

        return $url;
    }

    /**
     * Implement smart indexing logic to protect crawl budget.
     * Returns false if the page should be set to "noindex".
     */
    public static function shouldIndex(array $viewData): bool
    {
        $request = request();
        $route = $request->route();

        // 1. Search result pages
        if ($request->is('search*')) {
            return false;
        }

        // 2. Auth pages, verification, passwords
        $noindexPaths = [
            'login*',
            'register*',
            'forgot-password*',
            'reset-password*',
            'verify-email*',
            'confirm-password*',
            'dashboard*',
            'profile*'
        ];
        foreach ($noindexPaths as $path) {
            if ($request->is($path)) {
                return false;
            }
        }

        // 3. Empty Categories
        $category = null;
        if (isset($viewData['category']) && $viewData['category'] instanceof Category) {
            $category = $viewData['category'];
        } elseif ($route && $route->getName() === 'category.show') {
            $slug = $route->parameter('slug');
            $category = Category::where('slug', $slug)->first();
        }

        if ($category) {
            $articlesCount = Article::published()->forCategory($category->id)->count();
            if ($articlesCount === 0) {
                return false;
            }
        }

        // 4. Empty Profiles / Author pages (if applicable)
        if (isset($viewData['author']) && isset($viewData['articles'])) {
            if (method_exists($viewData['articles'], 'isEmpty') && $viewData['articles']->isEmpty()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Generate dynamic JSON-LD Structured Data Schema.
     */
    public static function generateSchema(array $viewData): string
    {
        $schemas = [];
        $siteName = Setting::get('site_name', 'Getembe News');
        $siteUrl = url('/');
        $request = request();
        $route = $request->route();

        // 1. Base WebSite Schema
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteName,
            'url' => $siteUrl,
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => "{$siteUrl}/search?q={search_term_string}",
                'query-input' => 'required name=search_term_string'
            ]
        ];

        // 2. Page Specific Schemas
        $article = null;
        if (isset($viewData['article']) && $viewData['article'] instanceof Article) {
            $article = $viewData['article'];
        } elseif ($route && $route->getName() === 'articles.show') {
            $slug = $route->parameter('slug');
            $article = Article::where('slug', $slug)->first();
        }

        if ($article) {
            $articleUrl = url("/articles/{$article->slug}");
            $imageUrl = $article->featured_image ?: 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&q=80&w=600&h=400';
            
            // Check if it's a Recipe (belongs to recipes category or keyword match)
            $isRecipe = false;
            if ($article->category && (Str::contains(strtolower($article->category->slug), ['recipe', 'food', 'cooking', 'kitchen']) || Str::contains(strtolower($article->title), ['recipe', 'how to cook', 'how to make']))) {
                $isRecipe = true;
            }

            // Check if it has FAQ structure (has headers ending with question marks)
            $faqs = self::parseFaqs($article->body);

            if ($isRecipe) {
                // Recipe Schema
                $recipeData = self::parseRecipe($article->body);
                $schemas[] = [
                    '@context' => 'https://schema.org',
                    '@type' => 'Recipe',
                    'name' => $article->title,
                    'description' => $article->seo_description ?: Str::limit(strip_tags($article->body), 150),
                    'image' => $imageUrl,
                    'datePublished' => $article->published_at ? $article->published_at->toIso8601String() : $article->created_at->toIso8601String(),
                    'author' => [
                        '@type' => 'Person',
                        'name' => $article->author->name ?? 'Staff Writer'
                    ],
                    'recipeIngredient' => $recipeData['ingredients'],
                    'recipeInstructions' => array_map(function($step) {
                        return [
                            '@type' => 'HowToStep',
                            'text' => $step
                        ];
                    }, $recipeData['instructions'])
                ];
            } else {
                // NewsArticle Schema
                $schemas[] = [
                    '@context' => 'https://schema.org',
                    '@type' => 'NewsArticle',
                    'mainEntityOfPage' => [
                        '@type' => 'WebPage',
                        '@id' => $articleUrl
                    ],
                    'headline' => $article->title,
                    'description' => $article->seo_description ?: Str::limit(strip_tags($article->body), 150),
                    'image' => $imageUrl,
                    'datePublished' => $article->published_at ? $article->published_at->toIso8601String() : $article->created_at->toIso8601String(),
                    'dateModified' => $article->updated_at->toIso8601String(),
                    'author' => [
                        '@type' => 'Person',
                        'name' => $article->author->name ?? 'Staff Writer',
                        'jobTitle' => 'Journalist'
                    ],
                    'publisher' => [
                        '@type' => 'Organization',
                        'name' => $siteName,
                        'logo' => [
                            '@type' => 'ImageObject',
                            'url' => "{$siteUrl}/images/logo.png"
                        ]
                    ]
                ];
            }

            // Output FAQ Schema if questions are found
            $finalFaqs = array_merge($faqs, $article->faq_items ?: []);
            if (!empty($finalFaqs)) {
                $faqSchema = [
                    '@context' => 'https://schema.org',
                    '@type' => 'FAQPage',
                    'mainEntity' => []
                ];
                foreach ($finalFaqs as $faq) {
                    if (!empty($faq['question']) && !empty($faq['answer'])) {
                        $faqSchema['mainEntity'][] = [
                            '@type' => 'Question',
                            'name' => $faq['question'],
                            'acceptedAnswer' => [
                                '@type' => 'Answer',
                                'text' => $faq['answer']
                            ]
                        ];
                    }
                }
                if (!empty($faqSchema['mainEntity'])) {
                    $schemas[] = $faqSchema;
                }
            }
        }

        // 3. Quiz Schema (if present in sidebar or content)
        $quizzes = json_decode(Setting::get('simulated_quizzes', '[]'), true);
        if (!empty($quizzes)) {
            $quiz = $quizzes[0];
            $schemas[] = [
                '@context' => 'https://schema.org',
                '@type' => 'Quiz',
                'name' => $quiz['title'] ?? 'Getembe Trivia Quiz',
                'description' => 'Test your knowledge on regional updates and cultural heritage!',
                'learningResourceType' => 'Quiz',
                'about' => [
                    '@type' => 'Thing',
                    'name' => 'Getembe County History & Trivia'
                ],
                'hasPart' => [
                    [
                        '@type' => 'Question',
                        'name' => 'Which facility was recently launched in Getembe County to benefit the youth?',
                        'suggestedAnswer' => [
                            ['@type' => 'Answer', 'text' => 'A modern stadium', 'value' => 'false'],
                            ['@type' => 'Answer', 'text' => 'A state-of-the-art tech and innovation hub', 'value' => 'true'],
                            ['@type' => 'Answer', 'text' => 'An agricultural training college', 'value' => 'false']
                        ]
                    ],
                    [
                        '@type' => 'Question',
                        'name' => 'What is the main cultural art attraction Kisii County is globally known for?',
                        'suggestedAnswer' => [
                            ['@type' => 'Answer', 'text' => 'Traditional Beadwork', 'value' => 'false'],
                            ['@type' => 'Answer', 'text' => 'Soapstone Carvings', 'value' => 'true'],
                            ['@type' => 'Answer', 'text' => 'Pottery & Ceramics', 'value' => 'false']
                        ]
                    ],
                    [
                        '@type' => 'Question',
                        'name' => 'What is the primary currency utilized in Getembe News settings?',
                        'suggestedAnswer' => [
                            ['@type' => 'Answer', 'text' => 'KSH (Kenyan Shilling)', 'value' => 'true'],
                            ['@type' => 'Answer', 'text' => 'USD (US Dollar)', 'value' => 'false'],
                            ['@type' => 'Answer', 'text' => 'EUR (Euro)', 'value' => 'false']
                        ]
                    ]
                ]
            ];
        }

        $html = '';
        foreach ($schemas as $schema) {
            $html .= '<script type="application/ld+json">' . json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "</script>\n";
        }

        return $html;
    }

    /**
     * Heuristic to parse Q&As from HTML.
     */
    public static function parseFaqs(string $html): array
    {
        $faqs = [];
        preg_match_all('/<(h[3-6]|strong|b)>(.*?)\??<\/\1>/i', $html, $matches);
        
        if (!empty($matches[2])) {
            foreach ($matches[2] as $index => $questionText) {
                $questionText = trim(strip_tags($questionText));
                if (str_ends_with($questionText, '?')) {
                    $escapedQuestion = preg_quote($matches[0][$index], '/');
                    $nextPattern = isset($matches[0][$index + 1]) ? preg_quote($matches[0][$index + 1], '/') : '$';
                    preg_match('/' . $escapedQuestion . '(.*?)(?:' . $nextPattern . ')/is', $html, $bodyMatch);
                    
                    $answerText = isset($bodyMatch[1]) ? trim(strip_tags($bodyMatch[1])) : '';
                    if ($questionText && $answerText) {
                        $faqs[] = [
                            'question' => rtrim($questionText, '?') . '?',
                            'answer' => $answerText
                        ];
                    }
                }
            }
        }
        return $faqs;
    }

    /**
     * Heuristic to parse recipe ingredients and instructions from HTML.
     */
    public static function parseRecipe(string $html): array
    {
        $ingredients = [];
        $instructions = [];

        if (preg_match('/<ul[^>]*>(.*?)<\/ul>/is', $html, $listMatch)) {
            preg_match_all('/<li>(.*?)<\/li>/is', $listMatch[1], $liMatches);
            if (!empty($liMatches[1])) {
                foreach ($liMatches[1] as $li) {
                    $ingredients[] = trim(strip_tags($li));
                }
            }
        }

        if (preg_match('/<ol[^>]*>(.*?)<\/ol>/is', $html, $listMatch)) {
            preg_match_all('/<li>(.*?)<\/li>/is', $listMatch[1], $liMatches);
            if (!empty($liMatches[1])) {
                foreach ($liMatches[1] as $li) {
                    $instructions[] = trim(strip_tags($li));
                }
            }
        }

        if (empty($ingredients)) {
            $ingredients = ['Refer to article body for ingredients list.'];
        }
        if (empty($instructions)) {
            $instructions = ['Refer to article body for step-by-step instructions.'];
        }

        return [
            'ingredients' => $ingredients,
            'instructions' => $instructions
        ];
    }
}
