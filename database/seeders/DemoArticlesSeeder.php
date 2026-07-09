<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoArticlesSeeder extends Seeder
{
    public function run(): void
    {
        $reporter = User::where('role', 'reporter')->first() ?? User::where('role', 'editor')->first() ?? User::first();
        if (!$reporter) {
            return;
        }

        $categories = [
            'politics' => [
                'name' => 'Politics',
                'images' => [
                    'https://images.unsplash.com/photo-1541872703-74c5e44368f9?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1529107386315-e1a2ed48a620?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1540910419892-4a36d2c3266c?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&q=80&w=800&h=450',
                ],
                'titles' => [
                    'Senate Committee Commends Kisii County Financial Transparency Measures',
                    'Governor Convene Roundtable of Local Leaders to Discuss Devolution Success',
                    'Kisii County Assembly Passes Landmark Agricultural Development Bill',
                    'Electoral Regulators Conduct Grassroots Voter Education Drive in Kisii Town',
                    'New Biometric Attendance Checks Implemented in Getembe Local Assembly',
                    'Getembe Municipal Charter Formally Signed, Launching City Status Plan',
                    'Public Hearings Conclude on Supplementary Budget and Market Upgrades',
                    'Inter-County Trade Corridors Discussed to Reduce Tariffs and Custom Checks',
                    'Kisii Regional Coalition Announces Unified Developmental Manifestos',
                    'Town Hall Meetings Spark Vigorous debate on Community Infrastructure Priorities'
                ],
                'subtitles' => [
                    'Audit report rates the regional administrative performance highly.',
                    'The meeting aims to align sub-county programs with the national grid.',
                    'The legislation boosts subsidies and grants for organic farming inputs.',
                    'Volunteers visit markets to register youth voters for upcoming local polls.',
                    'County clerk asserts the measure enforces strict professional accountability.',
                    'Civic leaders expect city status to attract foreign real-estate developers.',
                    'Traders seek lower levies and upgraded sanitation facilities in urban areas.',
                    'Governors from bordering counties draft borderless trade partnership agreements.',
                    'Key leaders pledge focus on water, health, and solar lighting programs.',
                    'Residents voice contrasting views on municipal allocations and public spaces.'
                ]
            ],
            'business' => [
                'name' => 'Business',
                'images' => [
                    'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1542222035-739e243907c2?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?auto=format&fit=crop&q=80&w=800&h=450',
                ],
                'titles' => [
                    'Kisii Small Retailers Experience Unprecedented Mobile Payment Surge',
                    'Local Cooperative Announces Export Deal for Getembe Premium Avocados',
                    'SME Incubation Centers Launched to Boost Soapstone Art Marketing',
                    'Commercial Banks Partner with County to Offer Low-Interest Agribusiness Loans',
                    'Bumper Tea Harvest in Nyamache Drives Cooperative Dividend Payouts',
                    'E-Commerce Bootcamps Train 500 Young Entrepreneurs in Kisii Town',
                    'Kisii Town Real Estate Market Experiences Rapid Expansion and High Demand',
                    'Central Bank Report Highlights Inflation Drop as Food Supply Recovers',
                    'County Franchise Launches E-Logistics App to Connect Farm to Markets',
                    'Kisii Farmers Co-Op Establishes New State-of-the-Art Cold Storage Depot'
                ],
                'subtitles' => [
                    'Mobile cash systems account for over 65% of daily marketplace sales.',
                    'The contract secures European distribution channels for local farmers.',
                    'The centers offer digital marketing workshops and logistics training.',
                    'The micro-finance framework targets women and youth led enterprises.',
                    'Favorable weather patterns and improved factory processing boost returns.',
                    'Bootcamp participants receive free online store domain registrations.',
                    'New residential estates and modern commercial plazas change the skyline.',
                    'Improved distribution of farm produce stabilizes consumer pricing indexes.',
                    'App allows direct orders from local farms, eliminating intermediaries.',
                    'Facility will significantly reduce post-harvest avocado and vegetable losses.'
                ]
            ],
            'technology' => [
                'name' => 'Technology',
                'images' => [
                    'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1509391366360-2e959784a276?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&q=80&w=800&h=450',
                ],
                'titles' => [
                    'Solar Micro-Grids Transform Electricity Access in Rural Getembe',
                    'Kisii Developers Build Interactive Mobile App for Crop Disease Diagnosis',
                    'Regional Telecom Completes Fiber Optic Cable Roll-out in Kisii County',
                    'County Integrates Digital Health Records System to Shorten Queue Times',
                    'Getembe Tech Hub Cohort Graduates 150 Certified Software Engineers',
                    'Smart Farming Sensors Deployed to Monitor Soil Moisture and Acid Levels',
                    'FinTech Innovations Drive Banking Inclusivity for Remote Rural Markets',
                    'Kisii University Unveils Bluetooth Soil Testing Sensor Pod Prototype',
                    'Eco-Friendly Solar Cooking Kits Offered to Reduce Carbon Fuel Dependency',
                    'Local Startup Launches On-Demand Ride Hailing Service in Kisii Municipality'
                ],
                'subtitles' => [
                    'Clean energy grids bring television and cooling storage to villages.',
                    'Using artificial intelligence, app identifies coffee rust from photos.',
                    'Over 15 commercial areas and suburbs receive high-speed internet access.',
                    'Cloud-based software speeds up billing and prescription workflows.',
                    'Graduates land remote jobs with international software corporations.',
                    'The IoT sensor devices send irrigation recommendations directly to phones.',
                    'Decentralized networks enable micro-savings groups to track reserves.',
                    'Low-cost pods provide instant nitrogen and acidity readings to farmers.',
                    'Subsidized programs seek to transition rural kitchens to clean energy.',
                    'App provides secure navigation, digital payments, and fixed pricing.'
                ]
            ],
            'sports' => [
                'name' => 'Sports',
                'images' => [
                    'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1517649763962-0c623066013b?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1502014822147-1aedfb0676e0?auto=format&fit=crop&q=80&w=800&h=450',
                ],
                'titles' => [
                    'Getembe FC Wins Dramatic Penalty Shootout in Regional Cup Finals',
                    'Kisii Half Marathon Set for October; Registration Portal Now Open',
                    'School Sports Gala Showcases Gifted Sprint Talents in Kisii Town',
                    'Getembe Stadium Upgraded with Modern Artificial Turf and LED Lights',
                    'National Athletics Coaches Visit Kisii to Scout Long-Distance Runners',
                    'County Volleyball Tournament Draws Thousands of Enthusiastic Fans',
                    'Getembe Boxing Club Dominates Inter-County Amateur Championships',
                    'Local Cycling Club Hosts 50km Road Race to Promote Eco-Lifestyles',
                    'Getembe FC Coach Announces Grassroots Football Academy Investment Plan',
                    'Kisii Table Tennis Championship Crown Awarded to Local Teen Prodigy'
                ],
                'subtitles' => [
                    'Captain saves final spot-kick to secure promotion to the second division.',
                    'Proceeds will support county maternity ward equipment and supplies.',
                    'Scouts from national athletic federations praise high school sprinters.',
                    'Renovation will allow stadium to host international leagues and matches.',
                    'Coaches establish high-altitude training camp in Nyamira border region.',
                    'Local clubs showcase advanced defensive plays and spike structures.',
                    'Flyweight division fighter wins gold with unanimous decision victory.',
                    'Route spans hilly highlands, testing endurance and promoting fitness.',
                    'The academy will offer free coaching, meals, and academic tuition support.',
                    'Underdog player defeats former national champion in straight sets.'
                ]
            ],
            'opinion' => [
                'name' => 'Opinion',
                'images' => [
                    'https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1488190211105-8b0e65b80b4e?auto=format&fit=crop&q=80&w=800&h=450',
                ],
                'titles' => [
                    'Why Devolution is Key to Kisii Counties Agricultural Future',
                    'Digital Skills are the New Agribusiness: Empowering Our Youth',
                    'Decentralizing Health Services: Success Stories from Our Dispensaries',
                    'Preserving Soapstone Art: Safeguarding Our Unique Cultural Heritage',
                    'Sustaining Kisii Clean Water Programs Through Community Ownership',
                    'The Case for Implementing Basic Computer Coding in High School Curriculum',
                    'Urban Expansion vs. Agricultural Land Conservation in Getembe County',
                    'Agribusiness Cooperatives: The Backbone of Smallholder Farm Economies',
                    'Strengthening Civic Engagement: Youth Inclusion in Policy Planning',
                    'Why Tourism Boards Must Leverage E-Marketing to Showcase Local Heritage'
                ],
                'subtitles' => [
                    'Decentralized funding empowers farmers to negotiate competitive pricing.',
                    'Integrating crop sciences with mobile technology creates sustainable careers.',
                    'Equipping local clinics reduces pressure on major municipal hospitals.',
                    'Soapstone carvings define regional history; carvers deserve fair wages.',
                    'Cooperative governance models ensure clean water pumps remain active.',
                    'Software logic teaches analytical thinking skills essential for the future.',
                    'Zoning regulations must protect arable highlands from real-estate sprawl.',
                    'Bulk collection and cold storage prevent crop wastage and price drops.',
                    'County forums must invite student union representatives to budget panels.',
                    'Online campaigns introduce global travelers to soapstone art and eco-lodges.'
                ]
            ],
            'africa' => [
                'name' => 'Africa',
                'images' => [
                    'https://images.unsplash.com/photo-1547471080-7cc2caa01a7e?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1484156818044-c040038b0719?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1523805009345-7448845a9e53?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1509099836639-18ba1795216d?auto=format&fit=crop&q=80&w=800&h=450',
                ],
                'titles' => [
                    'East African Regional Trade Area Celebrates Breakthrough Tariffs Deal',
                    'Nairobi Tech Ecosystem Attracts Venture Capital Firms for Startups',
                    'Pan-African Payment System Expands Operations to Central Africa',
                    'Solar Power Projects Across Sahel Region Secure Multi-Billion Funding',
                    'African Development Bank Projects Steady Economic Recovery in 2027',
                    'Continent-Wide Conservation Accord Protects Crucial Wildlife Corridors',
                    'E-Commerce Platforms Fuel Growth of African Fashion Designer Boutiques',
                    'African Union Convenes Summit to Advance Free Movement Partnerships',
                    'Kenya Launches Modern Solar Rail Transit System Connecting Suburbs',
                    'Grassroots Agricultural Hubs Drive Seed Conservation Efforts in Africa'
                ],
                'subtitles' => [
                    'Tariff cuts on agricultural inputs speed up regional cross-border commerce.',
                    'Venture firms invest heavily in cloud and mobile payment solutions.',
                    'System facilitates direct transactions in local currencies, bypassing USD.',
                    'Decentralized solar grids will bring electricity to rural healthcare hubs.',
                    'Growth targets driven by agricultural trade and mineral processing reforms.',
                    'Joint patrols and community rangers mitigate human-wildlife conflicts.',
                    'Digital payment gateways allow global clients to purchase custom garments.',
                    'AU passport initiatives aim to ease professional travel blockages.',
                    'Rail system runs on solar-battery panels, lowering transport carbon footprint.',
                    'Seed cooperatives protect indigenous grains against changing climate trends.'
                ]
            ],
            'world' => [
                'name' => 'World',
                'images' => [
                    'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1489749798305-4fea3ae63d43?auto=format&fit=crop&q=80&w=800&h=450',
                ],
                'titles' => [
                    'Global Climate Coalition Allocates Climate Loss Fund to Vulnerable States',
                    'International Monetary Fund Projects Global Supply Chain Stabilization',
                    'World Health Summit Pledges Billions for Vaccine Research Operations',
                    'Space Agency Launches Satellite to Monitor Greenhouse Gas Emissions',
                    'Major Trade Summit Concludes with Agreements on Tech Import Duties',
                    'Global Oceans Treaty Signed to Establish Protected Marine Sanctuaries',
                    'Electric Vehicle Adoption Reaches Landmark Heights in Major Cities',
                    'World Food Program Expands Humanitarian Logistics Hubs in East Africa',
                    'Global Education Forum Outlines Framework for Hybrid Remote Learning',
                    'Renewable Hydrogen Energy Tech Advances with New Production Patents'
                ],
                'subtitles' => [
                    'Fund subsidizes weather-resilient clinics and farming infrastructure.',
                    'Trade reports indicate shipping rates cooling and inventory levels recovering.',
                    'Coordinated funding seeks to accelerate local distribution of vital medicine.',
                    'Satellite provides real-time heat maps tracking municipal methane leaks.',
                    'Treaty lowers tariffs on microchips and solar-battery assembly panels.',
                    'Framework restricts deep-sea mining and commercial fishing operations.',
                    'Battery innovations and tax incentives drive massive consumer sales surges.',
                    'Storage centers expedite drought relief response times in regional zones.',
                    'Framework provides digital curriculum licenses to low-income schools.',
                    'New fuel cells produce hydrogen fuel using low-voltage clean energy.'
                ]
            ],
            'health' => [
                'name' => 'Health',
                'images' => [
                    'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1584515979956-d9f6e5d09982?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1532938911079-1b06ac7ceec7?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1506126613408-eca07ce68773?auto=format&fit=crop&q=80&w=800&h=450',
                ],
                'titles' => [
                    'Mobile Health Vans Deliver Preventative Medical Screenings in Getembe',
                    'Solar Powered Water Filters Drastically Lower Typhoid Rates in Kisii',
                    'Local Health Dispensaries Equipped with Modern Emergency Diagnostic Gear',
                    'County Launches Immunization Campaign Targeting Rural Maternity Clinics',
                    'Community Nutritionists Promote Kitchen Gardens to Combat Malnourishment',
                    'County Health Office Reports Drastic Decline in Malaria Admissions',
                    'Mental Health Support Helplines Established for Local University Students',
                    'Training Seminars Certify 300 Midwives in Rural Healthcare Clinics',
                    'Hygiene Education Campaigns Conducted in Primary Schools Countywide',
                    'Kisii Referral Hospital Upgrades Pediatric Emergency ICU Ward Rooms'
                ],
                'subtitles' => [
                    'Vans offer free blood-pressure, diabetes, and dental examinations.',
                    'Filtration centers provide clean water access to 10,000 households.',
                    'New equipment enables instant testing for cholera and viral infections.',
                    'Subsidized vaccines safeguard newborns against respiratory illnesses.',
                    'Workshops teach households to plant nutrient-dense crops in backyards.',
                    'Distribution of insecticide-treated bed nets drives down transmission rates.',
                    'Helplines offer confidential counseling services and wellness tips.',
                    'Seminars focus on safe deliveries and neonatal resuscitation skills.',
                    'Handwashing stations installed in schools to foster hygienic habits.',
                    'ICU expansion features modern life-support systems and incubator chambers.'
                ]
            ],
            'lifestyle' => [
                'name' => 'Lifestyle',
                'images' => [
                    'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1488085061387-422e29b40080?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1501504905252-473c47e087f8?auto=format&fit=crop&q=80&w=800&h=450',
                ],
                'titles' => [
                    'Annual Kisii Cultural Festival Showcases Exquisite Soapstone Craft Art',
                    'Eco-Tourism Lodge Opens in Getembe Highlands, Offering Scenic Safaris',
                    'Traditional Culinary Recipes Celebrated in Local Organic Food Show',
                    'Local Handwoven Textile Artisans Gain Mainstream Fashion Spotlight',
                    'Soapstone Artists Teach Carving Skills in Weekly Community Workshops',
                    'Scenic Hiking Trails Developed Around Getembe Hilly Highlands',
                    'Organic Coffee Tasting Festival Draws Global Bean Roasters to Kisii',
                    'Getembe Youth Center Hosts Creative Photography and Poetry Evening',
                    'Urban Gardening Trend Gaining Fast Popularity in Kisii Municipal Homes',
                    'Local Music Guild Records Traditional Folk Songs for Archive Project'
                ],
                'subtitles' => [
                    'Artisans demonstrate carving techniques to international tourists.',
                    'Solar-powered eco-lodge targets conscious travelers seeking tranquility.',
                    'Show focuses on local grains like millet, traditional greens, and herbs.',
                    'Boutiques feature custom outfits blending modern cuts with heritage designs.',
                    'Workshops aim to pass soapstone legacy to the younger generation.',
                    'Trails feature clear signage, viewpoints, and forest picnic spaces.',
                    'Festival showcases premium highland arabica coffee beans and profiles.',
                    'Youth present landscape portfolios and recite spoken-word poetry.',
                    'Vertical planters and herb boxes allow city apartments to grow greens.',
                    'High-quality audio recordings preserve historical stories and tunes.'
                ]
            ],
            'education' => [
                'name' => 'Education',
                'images' => [
                    'https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&q=80&w=800&h=450',
                    'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&q=80&w=800&h=450',
                ],
                'titles' => [
                    'County Library Expansion Completed; New Digital Access Center Opened',
                    'Primary School Coding Initiative Introduced to Teach Software Logic',
                    'University Establishes Scholarship Fund Supporting Agribusiness Studies',
                    'Adult Literacy Programs Experience Surge in Enrollment Countywide',
                    'E-Learning Devices Distributed to Support Remote High School Classes',
                    'Local High School Wins National Science and Engineering Fair Trophy',
                    'Educators Emphasize Importance of Integrating Arts in STEM Curriculum',
                    'County Partners with Technical Colleges for Vocational Training Grants',
                    'Community Center Builds Homework Club and Mentorship Program for Kids',
                    'Teacher Training Workshops Focus on Modern Inclusive Education Models'
                ],
                'subtitles' => [
                    'Center features computers, online archives, and typing tutorials.',
                    'Students learn basic game design alongside mathematics.',
                    'Scholarship covers tuition fees for soil analysis and crop science majors.',
                    'Program empowers traders to learn bookkeeping and digital marketing.',
                    'Solar-charging tablets pre-loaded with textbooks sent to rural schools.',
                    'Innovative solar water heater design earns top honors from judges.',
                    'STEAM framework fosters creative problem-solving skills in developers.',
                    'Grants cover fee subsidies for plumbing, welding, and carpentry courses.',
                    'Volunteers offer free tutoring and college application guidance.',
                    'Workshops equip educators to support students with diverse learning needs.'
                ]
            ]
        ];

        foreach ($categories as $slug => $data) {
            $category = Category::where('slug', $slug)->first();
            if (!$category) {
                $category = Category::create([
                    'name' => $data['name'],
                    'slug' => $slug,
                    'description' => "Demo articles for {$data['name']}.",
                    'order' => rand(1, 10),
                ]);
            }

            for ($i = 0; $i < 10; $i++) {
                $title = $data['titles'][$i] ?? "Demo Post #{$i} for " . $data['name'];
                $subtitle = $data['subtitles'][$i] ?? "Subtitle description for demo post #{$i}.";
                $img = $data['images'][$i % count($data['images'])];
                $slugified = Str::slug($title) . '-' . rand(100, 999);

                $body = "
                    <h3>The Significance of This Trend</h3>
                    <p>In recent months, we have observed a major shift regarding <strong>" . e($title) . "</strong>. Local experts agree that this represents a key milestone for Getembe Digital and regional observers. Stakeholders have expressed diverse opinions, noting that the long-term impact will depend on execution.</p>
                    
                    <blockquote>
                        \"This development addresses several long-standing issues. It represents a vital step forward for the Kisii community.\"
                    </blockquote>

                    <h3>Key Actions and Highlights</h3>
                    <p>Moving forward, several initiatives have been drafted to capitalize on this progress. Notable highlights include:</p>
                    <ul>
                        <li><strong>Decentralized Infrastructure:</strong> Enhancing connectivity across municipal hubs.</li>
                        <li><strong>Skill Development Programs:</strong> Offering vocational bootcamps and scholarships.</li>
                        <li><strong>Sustainable Energy:</strong> transition to micro-grids and solar equipment.</li>
                    </ul>

                    <h3>Future Outlook and Community Impact</h3>
                    <p>As these developments continue to unfold, we will bring you more details and in-depth analysis. Readers are encouraged to share their feedback, follow our social handles, and subscribe to our newsletter alerts for real-time updates.</p>
                ";

                Article::create([
                    'title' => $title,
                    'subtitle' => $subtitle,
                    'slug' => $slugified,
                    'body' => $body,
                    'featured_image' => $img,
                    'user_id' => $reporter->id,
                    'category_id' => $category->id,
                    'status' => 'published',
                    'is_featured' => ($i === 0),
                    'is_breaking' => ($i === 1),
                    'is_pinned' => false,
                    'published_at' => now()->subDays(rand(1, 15))->subHours(rand(1, 23)),
                    'seo_title' => $title . " - Getembe News",
                    'seo_description' => Str::limit($subtitle, 155),
                    'views_count' => rand(150, 4500),
                    'read_time' => Article::calculateReadTime($body),
                ]);
            }
        }
    }
}
