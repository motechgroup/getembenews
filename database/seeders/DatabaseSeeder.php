<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Video;
use App\Models\BreakingNews;
use App\Models\Advertisement;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Users with different roles
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@getembenews.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'bio' => 'Lead administrator and editorial director at Getembe News.',
            'photo_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=200&h=200',
        ]);

        $editor = User::create([
            'name' => 'Editor User',
            'email' => 'editor@getembenews.com',
            'password' => Hash::make('password'),
            'role' => 'editor',
            'bio' => 'Senior editor managing daily news operations.',
            'photo_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200&h=200',
        ]);

        $reporter = User::create([
            'name' => 'Reporter John',
            'email' => 'reporter@getembenews.com',
            'password' => Hash::make('password'),
            'role' => 'reporter',
            'bio' => 'Investigative journalist specializing in politics and local affairs.',
            'photo_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=200&h=200',
            'social_links' => [
                'twitter' => 'https://twitter.com/reporterjohn',
                'facebook' => 'https://facebook.com/reporterjohn',
            ],
        ]);

        $subscriber = User::create([
            'name' => 'John Doe',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'subscriber',
        ]);

        // 2. Seed Categories
        $categoriesData = [
            ['name' => 'Politics', 'slug' => 'politics', 'description' => 'Local, national and international political news and analysis.', 'order' => 1],
            ['name' => 'Business', 'slug' => 'business', 'description' => 'Economy, finance, market updates, and corporate developments.', 'order' => 2],
            ['name' => 'Technology', 'slug' => 'technology', 'description' => 'Latest innovations, tech trends, gadgets, and software updates.', 'order' => 3],
            ['name' => 'Sports', 'slug' => 'sports', 'description' => 'Match highlights, tournament reports, and athletic updates.', 'order' => 4],
            ['name' => 'Opinion', 'slug' => 'opinion', 'description' => 'Thought pieces, editorials, and community voices.', 'order' => 5],
            ['name' => 'Africa', 'slug' => 'africa', 'description' => 'Stories from around the African continent.', 'order' => 6],
            ['name' => 'World', 'slug' => 'world', 'description' => 'Global news, events, and foreign affairs.', 'order' => 7],
            ['name' => 'Health', 'slug' => 'health', 'description' => 'Medical breakthroughs, public health, and wellness tips.', 'order' => 8],
            ['name' => 'Lifestyle', 'slug' => 'lifestyle', 'description' => 'Culture, fashion, travel, food, and social trends.', 'order' => 9],
            ['name' => 'Education', 'slug' => 'education', 'description' => 'Schools, universities, research, and policy changes.', 'order' => 10],
        ];

        $categories = [];
        foreach ($categoriesData as $catData) {
            $categories[$catData['slug']] = Category::create($catData);
        }

        // 3. Seed Articles
        $articles = [
            [
                'title' => 'Getembe County Launches New Technology Hub for Youth Empowerment',
                'subtitle' => 'The multi-million shilling facility aims to bridge the digital divide in the region.',
                'slug' => 'getembe-county-launches-new-technology-hub-for-youth-empowerment',
                'body' => '<h3>A New Era of Innovation</h3><p>Getembe County has today commissioned a state-of-the-art innovation and technology hub designed to provide local youth with access to high-speed internet, advanced computing resources, and software engineering training. The center, funded in partnership with international technology companies, aims to support up to 5,000 students annually.</p><p>Speaking during the grand opening, the County Governor noted that the investment represents a key step towards positioning Getembe as a premier tech destination in East Africa. "We are empowering our youth with the digital skills required to compete globally," he stated.</p><h3>Bridging the Digital Divide</h3><p>The facility features coding bootcamps, hardware prototyping labs, and collaborative coworking spaces. Startups incubators will also offer mentorship and venture capital access. Local educators have praised the initiative, emphasizing that the hub will complement current university curricular in computing science.</p><p>Interested youth can sign up online for the first cohort starting next month, which focuses on web development and cloud technologies.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&q=80&w=1200&h=675',
                'user_id' => $reporter->id,
                'category_id' => $categories['technology']->id,
                'status' => 'published',
                'is_featured' => true,
                'is_breaking' => false,
                'is_pinned' => true,
                'published_at' => now()->subHours(2),
                'seo_title' => 'Getembe Launches Youth Tech Innovation Hub | Regional News',
                'seo_description' => 'Discover how Getembe County is bridging the digital divide with its new state-of-the-art tech and innovation hub.',
                'views_count' => 1245,
            ],
            [
                'title' => 'National Assembly Debates Crucial Tax Reform Bill Amid Public Concerns',
                'subtitle' => 'Lawmakers are divided over proposed adjustments to the value-added tax rates.',
                'slug' => 'national-assembly-debates-crucial-tax-reform-bill',
                'body' => '<h3>High Stakes in Parliament</h3><p>The national assembly today held a heated debate over the proposed Tax Reform Bill, which outlines major revisions to consumption and income taxes. Proponents argue the measures are essential to balancing the national budget and reducing external debt, while critics claim the adjustments will place an undue burden on low-income households.</p><p>Civil society groups have organized peaceful protests outside parliament, calling for a fairer distribution of taxes and increased transparency in public expenditure.</p><h3>Key Provisions of the Bill</h3><p>The bill proposes raising VAT on electronic services while lowering duties on agricultural inputs. A final vote is expected later this week after committee reviews are completed.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?auto=format&fit=crop&q=80&w=1200&h=675',
                'user_id' => $reporter->id,
                'category_id' => $categories['politics']->id,
                'status' => 'published',
                'is_featured' => false,
                'is_breaking' => true,
                'is_pinned' => false,
                'published_at' => now()->subMinutes(30),
                'seo_title' => 'Parliament Debates Tax Reform Bill',
                'seo_description' => 'Parliamentary debate intensifies over controversial tax changes and VAT proposals.',
                'views_count' => 840,
            ],
            [
                'title' => 'African Continental Free Trade Area Reports Strong First-Year Growth',
                'subtitle' => 'Intra-African trade has risen by 15% following reduction of custom tariffs.',
                'slug' => 'african-continental-free-trade-area-growth',
                'body' => '<h3>A New Milestone for Regional Commerce</h3><p>The secretariat of the African Continental Free Trade Area (AfCFTA) has announced a significant boost in cross-border trade transactions over the past twelve months. The removal of custom bottlenecks and standardized logistics operations have enabled small businesses to expand their reach across regional markets.</p><p>Economists predict that if the current growth trajectory holds, the trade agreement will lift millions out of extreme poverty by 2030.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&q=80&w=1200&h=675',
                'user_id' => $editor->id,
                'category_id' => $categories['africa']->id,
                'status' => 'published',
                'is_featured' => false,
                'is_breaking' => false,
                'is_pinned' => false,
                'published_at' => now()->subDay(),
                'seo_title' => 'AfCFTA Reports Strong First-Year Trade Growth',
                'seo_description' => 'Intra-African commerce rises by 15% as AfCFTA custom tariff cuts take effect.',
                'views_count' => 312,
            ],
            [
                'title' => 'Getembe FC Secures Historic Victory in Regional League Finals',
                'subtitle' => 'A dramatic 92nd-minute penalty seals the championship title.',
                'slug' => 'getembe-fc-secures-historic-victory-regional-league',
                'body' => '<h3>Unbelievable Scenes at the Arena</h3><p>Getembe FC completed a dramatic comeback to win the Regional League Cup, defeating their arch-rivals 2-1 in a nail-biting final. The match seemed destined for extra time until a late foul inside the penalty box gave Getembe a golden opportunity.</p><p>The stadium erupted as the captain calmly slotted the ball home, securing their promotion to the premier national division.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&q=80&w=1200&h=675',
                'user_id' => $reporter->id,
                'category_id' => $categories['sports']->id,
                'status' => 'published',
                'is_featured' => false,
                'is_breaking' => false,
                'is_pinned' => false,
                'published_at' => now()->subHours(8),
                'seo_title' => 'Getembe FC Wins Regional League Cup',
                'seo_description' => 'A late penalty guides Getembe FC to a historic league victory and promotion.',
                'views_count' => 2450,
            ],
            [
                'title' => 'The Future of Renewable Energy: Scaling Solar and Wind in Developing Countries',
                'subtitle' => 'How decentralized grids are revolutionizing power access in rural communities.',
                'slug' => 'future-of-renewable-energy-rural-grids',
                'body' => '<h3>Energy Democratization</h3><p>For decades, large-scale centralized power plants have struggled to reach remote rural communities. Today, however, micro-solar grids and compact wind systems are proving that decentralization is the most cost-effective path to universal electricity access.</p><p>This opinion piece explores the technological and economic trends shaping clean energy access.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1509391366360-2e959784a276?auto=format&fit=crop&q=80&w=1200&h=675',
                'user_id' => $admin->id,
                'category_id' => $categories['opinion']->id,
                'status' => 'published',
                'is_featured' => false,
                'is_breaking' => false,
                'is_pinned' => false,
                'published_at' => now()->subDays(2),
                'seo_title' => 'Opinion: The Future of Renewable Energy in Rural Areas',
                'seo_description' => 'An analysis of how micro-grids are bringing affordable solar power to developing regions.',
                'views_count' => 156,
            ],
            [
                'title' => 'Why Digital Literacy Must Be Prioritized in Our Primary School Curriculum',
                'subtitle' => 'Integrating basic computer concepts early prepares children for a technology-driven workforce.',
                'slug' => 'why-digital-literacy-must-be-prioritized',
                'body' => '<h3>Teaching Code Alongside Arithmetic</h3><p>In a world increasingly driven by digital services, understanding how computers work is as fundamental as reading and basic maths. To ensure our students are not left behind, primary education policies must adapt to include computer literacy and computational logic in standard syllabus programs.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?auto=format&fit=crop&q=80&w=1200&h=675',
                'user_id' => $admin->id,
                'category_id' => $categories['education']->id,
                'status' => 'published',
                'is_featured' => false,
                'is_breaking' => false,
                'is_pinned' => false,
                'published_at' => now()->subDays(3),
                'seo_title' => 'Opinion: Primary School Digital Literacy Priorities',
                'seo_description' => 'A call to action for reforming primary education with integrated basic digital literacy skills.',
                'views_count' => 98,
            ],
            [
                'title' => 'Getembe Local Businesses Report Strong Post-COVID Recovery and Digital Transition',
                'subtitle' => 'Over 60% of small retailers have adopted mobile money payments in the last year.',
                'slug' => 'getembe-local-businesses-report-strong-recovery',
                'body' => '<h3>Economic Renaissance</h3><p>Local businesses in Getembe have shown remarkable resilience, registering solid economic growth in the post-pandemic cycle. Small and medium enterprises (SMEs) have leveraged mobile banking and digital inventory systems to lower overheads and expand market range.</p><p>A survey by the Chamber of Commerce reveals that mobile payment solutions have accelerated rural transaction volumes by over 45%.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1542222035-739e243907c2?auto=format&fit=crop&q=80&w=1200&h=675',
                'user_id' => $reporter->id,
                'category_id' => $categories['business']->id,
                'status' => 'published',
                'is_featured' => false,
                'is_breaking' => false,
                'is_pinned' => false,
                'published_at' => now()->subHours(12),
                'seo_title' => 'Getembe Business Digital Recovery Trends',
                'seo_description' => 'How Getembe retailers and SMEs are leveraging digital technology and mobile payments to drive sales growth.',
                'views_count' => 380,
            ],
            [
                'title' => 'Global Climate Summit Agrees on New Finance Framework for Developing Nations',
                'subtitle' => 'Delegates commit over $100 billion to assist vulnerable regions in infrastructure adaptation.',
                'slug' => 'global-climate-summit-new-finance-framework',
                'body' => '<h3>A Landmark Climate Deal</h3><p>In a late-night session, delegates at the Global Climate Summit approved a historic climate finance framework designed to funnel resources to developing nations facing extreme weather patterns. The fund will subsidize clean energy grids and coastal reinforcements in East Africa and South Asia.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&q=80&w=1200&h=675',
                'user_id' => $editor->id,
                'category_id' => $categories['world']->id,
                'status' => 'published',
                'is_featured' => false,
                'is_breaking' => false,
                'is_pinned' => false,
                'published_at' => now()->subHours(18),
                'seo_title' => 'Global Climate Finance Framework Approved',
                'seo_description' => 'Global Climate Summit approves $100B adaptation fund for developing nations clean infrastructure.',
                'views_count' => 290,
            ],
            [
                'title' => 'New Clean Water Initiative in Kisii County Drastically Reduces Waterborne Diseases',
                'subtitle' => 'Local health centers report a 40% decline in cholera and typhoid admissions over six months.',
                'slug' => 'new-clean-water-initiative-reduces-diseases',
                'body' => '<h3>Improving Public Health Standards</h3><p>A collaborative water purification program spanning three sub-counties has achieved significant progress in clean water access. By installing solar-powered sanitization systems, local dispensaries have observed a sharp drop in typhoid cases.</p><p>Health officers have encouraged rural households to continue practicing standard hygiene protocols to consolidate these gains.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&q=80&w=1200&h=675',
                'user_id' => $reporter->id,
                'category_id' => $categories['health']->id,
                'status' => 'published',
                'is_featured' => false,
                'is_breaking' => false,
                'is_pinned' => false,
                'published_at' => now()->subDays(4),
                'seo_title' => 'Kisii County Clean Water Initiative Progress',
                'seo_description' => 'Solar-powered water sanitization systems reduce waterborne diseases by 40% in Kisii County.',
                'views_count' => 450,
            ],
            [
                'title' => 'Celebrating Kisii Culture: Annual Cultural Festival Returns to Getembe Stadium',
                'subtitle' => 'Local cuisine, traditional soapstone art, and folk music take center stage.',
                'slug' => 'celebrating-kisii-culture-annual-festival-returns',
                'body' => '<h3>A Vibrant Cultural Showcase</h3><p>Getembe Stadium hosted thousands of participants today for the opening ceremony of the annual Cultural Festival. Highlighting traditional soapstone carvings, local culinary dishes, and energetic choir performance, the event promotes heritage conservation.</p><p>Organizers expressed optimism that the three-day festival will boost domestic tourism and support local artisan guilds.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&q=80&w=1200&h=675',
                'user_id' => $reporter->id,
                'category_id' => $categories['lifestyle']->id,
                'status' => 'published',
                'is_featured' => false,
                'is_breaking' => false,
                'is_pinned' => false,
                'published_at' => now()->subDays(5),
                'seo_title' => 'Annual Getembe Cultural Festival Returns',
                'seo_description' => 'Kisii heritage, folk dance, and soapstone arts celebrated at Getembe Stadium cultural festival.',
                'views_count' => 520,
            ],
            // 4 more politics articles
            [
                'title' => 'County Assembly Proposes New Infrastructure Budget for Getembe Region',
                'subtitle' => 'Millions allocated to upgrading roads, clean water distribution, and market centers.',
                'slug' => 'county-assembly-proposes-new-infrastructure-budget',
                'body' => '<h3>Focus on Infrastructure</h3><p>The County Assembly has tabled a supplementary budget focusing heavily on regional connectivity. Members debated key allocations for road networks, sanitation facilities, and high-speed fiber installations in commercial areas.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?auto=format&fit=crop&q=80&w=1200&h=675',
                'user_id' => $reporter->id,
                'category_id' => $categories['politics']->id,
                'status' => 'published',
                'is_featured' => false,
                'is_breaking' => false,
                'is_pinned' => false,
                'published_at' => now()->subHours(4),
                'views_count' => 150,
            ],
            [
                'title' => 'Opposition Coalition Calls for Public Dialogue on Taxation Reforms',
                'subtitle' => 'Political leaders argue for extended public hearings to refine proposed levies.',
                'slug' => 'opposition-coalition-calls-for-public-dialogue',
                'body' => '<h3>Political Consensus Needed</h3><p>Leading coalition representatives held a press conference calling for extended citizen engagement. They argued that direct consultations on VAT rates will build consensus and policy stability.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?auto=format&fit=crop&q=80&w=1200&h=675',
                'user_id' => $reporter->id,
                'category_id' => $categories['politics']->id,
                'status' => 'published',
                'is_featured' => false,
                'is_breaking' => false,
                'is_pinned' => false,
                'published_at' => now()->subHours(6),
                'views_count' => 120,
            ],
            [
                'title' => 'Governor Pledges to Enhance Security Measures in Local Commercial Hubs',
                'subtitle' => 'Modern street lighting and enhanced community policing models to be deployed.',
                'slug' => 'governor-pledges-to-enhance-security-measures',
                'body' => '<h3>Safe Business Environment</h3><p>Speaking to the Chamber of Commerce, the governor committed to round-the-clock safety initiatives. The deployment of solar street lighting and neighborhood police partnership programs is set to commence next month.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?auto=format&fit=crop&q=80&w=1200&h=675',
                'user_id' => $reporter->id,
                'category_id' => $categories['politics']->id,
                'status' => 'published',
                'is_featured' => false,
                'is_breaking' => false,
                'is_pinned' => false,
                'published_at' => now()->subHours(10),
                'views_count' => 205,
            ],
            [
                'title' => 'Electoral Commission Announces Massive Civic Education Drive',
                'subtitle' => 'Initiative targets first-time voters and youth cohorts ahead of local polls.',
                'slug' => 'electoral-commission-announces-civic-education-drive',
                'body' => '<h3>Citizen Empowerment</h3><p>The commission has partnered with local universities to conduct workshops on democratic processes and voter rights, stressing the importance of active civic involvement.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?auto=format&fit=crop&q=80&w=1200&h=675',
                'user_id' => $reporter->id,
                'category_id' => $categories['politics']->id,
                'status' => 'published',
                'is_featured' => false,
                'is_breaking' => false,
                'is_pinned' => false,
                'published_at' => now()->subHours(14),
                'views_count' => 95,
            ],

            // 4 more business articles
            [
                'title' => 'Kisii Soapstone Art Carvers Eye Lucrative International Export Markets',
                'subtitle' => 'Artisans partner with digital global logistics platforms to list soapstone sculptures.',
                'slug' => 'kisii-soapstone-carvers-international-markets',
                'body' => '<h3>Global Market Expansion</h3><p>Carving guilds in Tabaka have entered trade agreements with online marketplaces, allowing them to showcase soapstone sculptures directly to overseas collectors.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1542222035-739e243907c2?auto=format&fit=crop&q=80&w=1200&h=675',
                'user_id' => $reporter->id,
                'category_id' => $categories['business']->id,
                'status' => 'published',
                'is_featured' => false,
                'is_breaking' => false,
                'is_pinned' => false,
                'published_at' => now()->subHours(16),
                'views_count' => 180,
            ],
            [
                'title' => 'New Cooperative Society Formed to Support Local Avocado Farmers',
                'subtitle' => 'Society provides cold-chain storage solutions and standard price contracts.',
                'slug' => 'new-cooperative-formed-support-avocado-farmers',
                'body' => '<h3>Cold-Chain Infrastructure</h3><p>Local avocado farmers have formed a centralized cooperative to minimize post-harvest losses. The cooperative features modern cold rooms and direct contracts with exporters.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1542222035-739e243907c2?auto=format&fit=crop&q=80&w=1200&h=675',
                'user_id' => $reporter->id,
                'category_id' => $categories['business']->id,
                'status' => 'published',
                'is_featured' => false,
                'is_breaking' => false,
                'is_pinned' => false,
                'published_at' => now()->subHours(20),
                'views_count' => 220,
            ],
            [
                'title' => 'Inflation Rates Stabilize as Regional Food Supply Recovers',
                'subtitle' => 'Consumer pricing indexes show cooling commodity rates due to bumper harvests.',
                'slug' => 'inflation-rates-stabilize-food-supply-recovers',
                'body' => '<h3>Market Stabilization</h3><p>Favorable seasonal weather has boosted regional agricultural output, leading to reduced wholesale costs for essential household food items.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1542222035-739e243907c2?auto=format&fit=crop&q=80&w=1200&h=675',
                'user_id' => $reporter->id,
                'category_id' => $categories['business']->id,
                'status' => 'published',
                'is_featured' => false,
                'is_breaking' => false,
                'is_pinned' => false,
                'published_at' => now()->subDays(1),
                'views_count' => 140,
            ],
            [
                'title' => 'Getembe Tourism Board Launches Modern Digital Interactive Travel App',
                'subtitle' => 'App highlights cultural sites, eco-lodges, and local soapstone craft centers.',
                'slug' => 'getembe-tourism-launches-travel-app',
                'body' => '<h3>Eco-Tourism Digitization</h3><p>The interactive mobile app provides travelers with maps, contact directories, and itinerary bookings for major county attractions.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1542222035-739e243907c2?auto=format&fit=crop&q=80&w=1200&h=675',
                'user_id' => $reporter->id,
                'category_id' => $categories['business']->id,
                'status' => 'published',
                'is_featured' => false,
                'is_breaking' => false,
                'is_pinned' => false,
                'published_at' => now()->subDays(2),
                'views_count' => 170,
            ],

            // 4 more tech articles
            [
                'title' => 'Kisii University Students Develop Smart Soil Testing Mobile App',
                'subtitle' => 'App links to Bluetooth sensor pods to give real-time pH and fertilizer advice.',
                'slug' => 'kisii-university-students-smart-soil-app',
                'body' => '<h3>Smart Farming Innovations</h3><p>A student developer team has won a national innovation award for their Bluetooth soil analyzer, offering affordable testing for rural farmers.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&q=80&w=1200&h=675',
                'user_id' => $reporter->id,
                'category_id' => $categories['technology']->id,
                'status' => 'published',
                'is_featured' => false,
                'is_breaking' => false,
                'is_pinned' => false,
                'published_at' => now()->subHours(5),
                'views_count' => 310,
            ],
            [
                'title' => 'Local Telecommunications Operator Expands Fiber Broadband Coverage',
                'subtitle' => 'High-speed internet services activated in major Getembe residential estates.',
                'slug' => 'local-telco-expands-fiber-broadband-coverage',
                'body' => '<h3>Digital Connectivity Boost</h3><p>The telecommunications provider completed its phase 2 fiber roll-out, enabling thousands of homes to access unlimited cloud speeds.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&q=80&w=1200&h=675',
                'user_id' => $reporter->id,
                'category_id' => $categories['technology']->id,
                'status' => 'published',
                'is_featured' => false,
                'is_breaking' => false,
                'is_pinned' => false,
                'published_at' => now()->subHours(9),
                'views_count' => 280,
            ],
            [
                'title' => 'E-Commerce Adoption Surges Among Fashion Designers in Getembe',
                'subtitle' => 'Local boutiques report a 70% increase in social media orders and online payments.',
                'slug' => 'ecommerce-adoption-surges-fashion-designers',
                'body' => '<h3>Digital Boutique Growth</h3><p>Bespoke tailoring shops are leveraging Instagram and mobile money payments to deliver outfits to clients in major cities.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&q=80&w=1200&h=675',
                'user_id' => $reporter->id,
                'category_id' => $categories['technology']->id,
                'status' => 'published',
                'is_featured' => false,
                'is_breaking' => false,
                'is_pinned' => false,
                'published_at' => now()->subHours(15),
                'views_count' => 340,
            ],
            [
                'title' => 'Solar Energy Start-up Introduces Pay-As-You-Go Off-Grid Power Kits',
                'subtitle' => 'New financing model aims to bring lighting and TV power to rural households.',
                'slug' => 'solar-startup-pay-as-you-go-kits',
                'body' => '<h3>Affordable Rural Energy</h3><p>The clean energy start-up provides modular solar kits that customers activate via daily mobile money micro-payments.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&q=80&w=1200&h=675',
                'user_id' => $reporter->id,
                'category_id' => $categories['technology']->id,
                'status' => 'published',
                'is_featured' => false,
                'is_breaking' => false,
                'is_pinned' => false,
                'published_at' => now()->subDays(1),
                'views_count' => 410,
            ],

            // 4 more sports articles
            [
                'title' => 'Getembe Athletic Club Dominates National Track and Field Championships',
                'subtitle' => 'Sprinters and long-distance runners claim three gold and two silver medals.',
                'slug' => 'getembe-athletic-club-dominates-national-championships',
                'body' => '<h3>Podium Finishes</h3><p>Getembe runners delivered spectacular performances, claiming gold in both the 5,000m and 10,000m finals at the national stadium.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&q=80&w=1200&h=675',
                'user_id' => $reporter->id,
                'category_id' => $categories['sports']->id,
                'status' => 'published',
                'is_featured' => false,
                'is_breaking' => false,
                'is_pinned' => false,
                'published_at' => now()->subHours(11),
                'views_count' => 540,
            ],
            [
                'title' => 'County Inter-School Games Kick Off at Getembe Primary Fields',
                'subtitle' => 'Over 30 schools compete in football, volleyball, and track events.',
                'slug' => 'county-interschool-games-kick-off',
                'body' => '<h3>Youth Sports Gala</h3><p>The annual county inter-school sports gala opened today, showcasing young athletic talent in soccer tournaments and relays.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&q=80&w=1200&h=675',
                'user_id' => $reporter->id,
                'category_id' => $categories['sports']->id,
                'status' => 'published',
                'is_featured' => false,
                'is_breaking' => false,
                'is_pinned' => false,
                'published_at' => now()->subHours(19),
                'views_count' => 380,
            ],
            [
                'title' => 'Getembe FC Coach Emphasizes Need for Youth Talent Academies',
                'subtitle' => 'Post-match press briefing highlights plans for grassroots football investments.',
                'slug' => 'getembe-fc-coach-youth-talent-academies',
                'body' => '<h3>Nurturing Young Talents</h3><p>Following their cup victory, the head coach urged local sponsors to fund development academies to sustain long-term club success.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&q=80&w=1200&h=675',
                'user_id' => $reporter->id,
                'category_id' => $categories['sports']->id,
                'status' => 'published',
                'is_featured' => false,
                'is_breaking' => false,
                'is_pinned' => false,
                'published_at' => now()->subDays(1),
                'views_count' => 450,
            ],
            [
                'title' => 'Kisii Annual Charity Marathon Slated for October Registration Opens',
                'subtitle' => 'Proceeds from the 21km road race to finance local community health projects.',
                'slug' => 'kisii-annual-charity-marathon-october-registration',
                'body' => '<h3>Running for Health</h3><p>Marathon organizers have opened corporate and individual registration desks, aiming for 2,000 runners to support health dispensary supplies.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&q=80&w=1200&h=675',
                'user_id' => $reporter->id,
                'category_id' => $categories['sports']->id,
                'status' => 'published',
                'is_featured' => false,
                'is_breaking' => false,
                'is_pinned' => false,
                'published_at' => now()->subDays(2),
                'views_count' => 610,
            ]
        ];

        foreach ($articles as $art) {
            $art['read_time'] = Article::calculateReadTime($art['body']);
            Article::create($art);
        }

        // 4. Seed Comments
        $dbArticle = Article::first();
        if ($dbArticle) {
            Comment::create([
                'article_id' => $dbArticle->id,
                'user_id' => $subscriber->id,
                'body' => 'This is an excellent initiative! The region desperately needs tech workspaces for developer talent.',
                'status' => 'approved',
            ]);

            $parentComment = Comment::create([
                'article_id' => $dbArticle->id,
                'user_id' => $editor->id,
                'body' => 'I agree. Hopefully local banks will step up to offer loans to start-ups emerging from the incubator.',
                'status' => 'approved',
            ]);

            Comment::create([
                'article_id' => $dbArticle->id,
                'user_id' => $subscriber->id,
                'body' => 'Thanks for the details. Does anyone know when registration opens?',
                'parent_id' => $parentComment->id,
                'status' => 'approved',
            ]);
        }

        // 5. Seed Videos
        Video::create([
            'title' => 'Inside Getembe County Innovation and Technology Hub',
            'slug' => 'inside-getembe-county-innovation-hub',
            'description' => 'A visual tour of the newly opened technology facility and exclusive interviews with local programmers.',
            'embed_url' => 'https://www.youtube.com/embed/5Peo-ivmupE',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&q=80&w=400&h=225',
            'category_id' => $categories['technology']->id,
            'is_featured' => true,
            'status' => 'published',
            'published_at' => now(),
        ]);

        Video::create([
            'title' => 'Highlights: Getembe FC vs Regional Rivals Championship Match',
            'slug' => 'highlights-getembe-fc-championship',
            'description' => 'Watch the thrilling comeback and the late penalty that secured the title.',
            'embed_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&q=80&w=400&h=225',
            'category_id' => $categories['sports']->id,
            'is_featured' => false,
            'status' => 'published',
            'published_at' => now()->subHours(5),
        ]);

        // 6. Seed Breaking News Alerts
        BreakingNews::create([
            'title' => 'BREAKING: National Assembly Debates Tax Bill - live vote expected in 1 hour.',
            'link' => '/articles/national-assembly-debates-crucial-tax-reform-bill',
            'priority' => 'high',
            'is_active' => true,
            'expires_at' => now()->addHours(2),
        ]);

        // 7. Seed Advertisements
        Advertisement::create([
            'title' => 'Top Banner Ad',
            'image_url' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&q=80&w=1200&h=150',
            'destination_url' => 'https://example.com',
            'location' => 'top',
            'is_active' => true,
        ]);

        Advertisement::create([
            'title' => 'Sidebar Premium Ad',
            'image_url' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&q=80&w=300&h=250',
            'destination_url' => 'https://example.com',
            'location' => 'sidebar',
            'is_active' => true,
        ]);

        // 8. Seed Site Settings
        Setting::set('site_name', 'Getembe News');
        Setting::set('live_tv_url', 'https://www.youtube.com/embed/5Peo-ivmupE');
        Setting::set('live_radio_url', 'http://stream.zeno.fm/f5r7x1t1zv8uv'); // A live radio URL placeholder
        Setting::set('weather_city', 'Kisii');
        Setting::set('app_play_store_url', 'https://play.google.com/store');
        Setting::set('app_app_store_url', 'https://www.apple.com/app-store');
        Setting::set('app_banner_title', 'Download Getembe Digital App Today');
        Setting::set('app_banner_desc', 'Download Getembe Digital App today for live news updates, breaking notifications, and seamless live streaming.');

        // 9. Seed Stream Schedules
        $defaultTvSchedule = [
            ['time' => '06:00 - 09:00', 'title' => 'Getembe Morning Call', 'desc' => 'Breakfast news and newspaper review.', 'is_playing' => false],
            ['time' => '09:00 - 12:00', 'title' => 'Business Daily', 'desc' => 'Economic trends, stock updates, and trade discussion.', 'is_playing' => false],
            ['time' => '12:00 - 14:00', 'title' => 'News Hour Live', 'desc' => 'Midday headlines, market check, and regional briefs.', 'is_playing' => true],
            ['time' => '14:00 - 16:00', 'title' => 'Health & Sports Highlights', 'desc' => 'Wellness insights and sporting roundups.', 'is_playing' => false],
            ['time' => '16:00 - 19:00', 'title' => 'Regional News Express', 'desc' => 'Community spotlights and county assembly briefings.', 'is_playing' => false],
            ['time' => '19:00 - 21:00', 'title' => 'Evening Prime Time News', 'desc' => 'Comprehensive summary of the day\'s major events.', 'is_playing' => false],
            ['time' => '21:00 - 23:00', 'title' => 'Late Night Spotlight', 'desc' => 'Documentary film showcases and talkshows.', 'is_playing' => false]
        ];
        Setting::set('tv_schedule', $defaultTvSchedule);

        $defaultRadioSchedule = [
            ['time' => '06:00 - 10:00', 'title' => 'The Morning Drive', 'desc' => 'Kickstart the day with updates and music.', 'is_playing' => false],
            ['time' => '10:00 - 13:00', 'title' => 'Midday Request Show', 'desc' => 'Listener choices, request lines, and interviews.', 'is_playing' => false],
            ['time' => '13:00 - 16:00', 'title' => 'Getembe Express Drive', 'desc' => 'Mid-afternoon drive show with regional topics and guest experts.', 'is_playing' => true],
            ['time' => '16:00 - 20:00', 'title' => 'Evening Jam & Sports', 'desc' => 'Local sports bulletins and afternoon reviews.', 'is_playing' => false],
            ['time' => '20:00 - 00:00', 'title' => 'Late Night Soul Session', 'desc' => 'Slow jams, classic tracks, and quiet storm conversations.', 'is_playing' => false]
        ];
        Setting::set('radio_schedule', $defaultRadioSchedule);

        // 10. Seed simulated polls
        $demoPolls = [
            [
                'id' => 'poll_1',
                'question' => 'Who will win the 2026 World Cup?',
                'options' => ['Argentina', 'France', 'Brazil', 'England', 'Other'],
                'created_at' => now()->format('Y-m-d H:i:s')
            ],
            [
                'id' => 'poll_2',
                'question' => 'What should Getembe County prioritize in the next budget cycle?',
                'options' => ['Road Networks', 'Youth Tech Hubs', 'Agriculture', 'Healthcare'],
                'created_at' => now()->subDays(2)->format('Y-m-d H:i:s')
            ],
            [
                'id' => 'poll_3',
                'question' => 'Do you support the proposed tax reform bill?',
                'options' => ['Yes, completely', 'No, reject it', 'Needs major revisions', 'Undecided'],
                'created_at' => now()->subDays(5)->format('Y-m-d H:i:s')
            ]
        ];
        Setting::set('simulated_polls', json_encode($demoPolls));

        // 11. Seed simulated quizzes
        $demoQuizzes = [
            [
                'id' => 'quiz_1',
                'title' => 'Getembe County History & Culture Trivia',
                'questions_count' => 3,
                'created_at' => now()->format('Y-m-d H:i:s')
            ],
            [
                'id' => 'quiz_2',
                'title' => 'Weekly News General Trivia - July 2026',
                'questions_count' => 5,
                'created_at' => now()->subDays(3)->format('Y-m-d H:i:s')
            ]
        ];
        Setting::set('simulated_quizzes', json_encode($demoQuizzes));
    }
}
