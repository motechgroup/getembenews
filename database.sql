-- Getembe News MySQL Database Dump
-- Generated on 2026-07-06 17:06:12
-- Optimized for shared hosting (MySQL 5.7+ / 8.0+)

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'subscriber',
  `bio` text DEFAULT NULL,
  `photo_url` varchar(255) DEFAULT NULL,
  `social_links` text DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `role`, `bio`, `photo_url`, `social_links`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@getembenews.com', NULL, '$2y$12$dR9w/GTYp10C2KOtU1j0juu.oJBIyNWgOJLTOksV47F2w93w2Rw/y', NULL, 'admin', 'Lead administrator and editorial director at Getembe News.', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=200&h=200', NULL, '2026-07-06 10:08:19', '2026-07-06 10:08:19'),
(2, 'Editor User', 'editor@getembenews.com', NULL, '$2y$12$XRqBpPtgspHj9KWGo6K29OyXdgyp/XOjRSdH3t0iB8exqjeeMa3mm', NULL, 'editor', 'Senior editor managing daily news operations.', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200&h=200', NULL, '2026-07-06 10:08:19', '2026-07-06 10:08:19'),
(3, 'Reporter John', 'reporter@getembenews.com', NULL, '$2y$12$VT6SaKcw9Ks5an.534U93.bBLIF02QPTR2DWoICGFTMtrcwH5EJ6.', NULL, 'reporter', 'Investigative journalist specializing in politics and local affairs.', 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=200&h=200', '{"twitter":"https:\/\/twitter.com\/reporterjohn","facebook":"https:\/\/facebook.com\/reporterjohn"}', '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(4, 'John Doe', 'test@example.com', NULL, '$2y$12$FN3icOzyO5PE5xOR9johuONsBEyTpQ/JDLduEabeXEWeUrF8ZA.s6', NULL, 'subscriber', NULL, NULL, NULL, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(5, 'Author User', 'author@getembenews.com', NULL, '$2y$12$vLLN02TWEaDISOHpNb7VJemZtFahJrJ0olGz/X2FGZ5zDUoLQLzXW', NULL, 'author', NULL, NULL, NULL, '2026-07-06 14:41:20', '2026-07-06 14:41:20');

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT '0',
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_parent_id_foreign` (`parent_id`),
  CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `parent_id`, `order`, `image_url`, `created_at`, `updated_at`) VALUES
(1, 'Politics', 'politics', 'Local, national and international political news and analysis.', NULL, 1, NULL, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(2, 'Business', 'business', 'Economy, finance, market updates, and corporate developments.', NULL, 2, NULL, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(3, 'Technology', 'technology', 'Latest innovations, tech trends, gadgets, and software updates.', NULL, 3, NULL, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(4, 'Sports', 'sports', 'Match highlights, tournament reports, and athletic updates.', NULL, 4, NULL, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(5, 'Opinion', 'opinion', 'Thought pieces, editorials, and community voices.', NULL, 5, NULL, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(6, 'Africa', 'africa', 'Stories from around the African continent.', NULL, 6, NULL, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(7, 'World', 'world', 'Global news, events, and foreign affairs.', NULL, 7, NULL, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(8, 'Health', 'health', 'Medical breakthroughs, public health, and wellness tips.', NULL, 8, NULL, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(9, 'Lifestyle', 'lifestyle', 'Culture, fashion, travel, food, and social trends.', NULL, 9, NULL, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(10, 'Education', 'education', 'Schools, universities, research, and policy changes.', NULL, 10, NULL, '2026-07-06 10:08:20', '2026-07-06 10:08:20');

DROP TABLE IF EXISTS `articles`;
CREATE TABLE `articles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `body` longtext NOT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_breaking` tinyint(1) NOT NULL DEFAULT '0',
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `published_at` timestamp NULL DEFAULT NULL,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` text DEFAULT NULL,
  `read_time` int(11) NOT NULL DEFAULT '0',
  `views_count` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `articles_slug_unique` (`slug`),
  KEY `articles_user_id_foreign` (`user_id`),
  KEY `articles_category_id_foreign` (`category_id`),
  CONSTRAINT `articles_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `articles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `articles` (`id`, `title`, `slug`, `subtitle`, `body`, `featured_image`, `user_id`, `category_id`, `status`, `is_featured`, `is_breaking`, `is_pinned`, `published_at`, `seo_title`, `seo_description`, `read_time`, `views_count`, `created_at`, `updated_at`) VALUES
(1, 'Getembe County Launches New Technology Hub for Youth Empowerment', 'getembe-county-launches-new-technology-hub-for-youth-empowerment', 'The multi-million shilling facility aims to bridge the digital divide in the region.', '<h3>A New Era of Innovation</h3><p>Getembe County has today commissioned a state-of-the-art innovation and technology hub designed to provide local youth with access to high-speed internet, advanced computing resources, and software engineering training. The center, funded in partnership with international technology companies, aims to support up to 5,000 students annually.</p><p>Speaking during the grand opening, the County Governor noted that the investment represents a key step towards positioning Getembe as a premier tech destination in East Africa. "We are empowering our youth with the digital skills required to compete globally," he stated.</p><h3>Bridging the Digital Divide</h3><p>The facility features coding bootcamps, hardware prototyping labs, and collaborative coworking spaces. Startups incubators will also offer mentorship and venture capital access. Local educators have praised the initiative, emphasizing that the hub will complement current university curricular in computing science.</p><p>Interested youth can sign up online for the first cohort starting next month, which focuses on web development and cloud technologies.</p>', 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&q=80&w=1200&h=675', 3, 3, 'published', 1, 0, 1, '2026-07-06 08:08:20', 'Getembe Launches Youth Tech Innovation Hub | Regional News', 'Discover how Getembe County is bridging the digital divide with its new state-of-the-art tech and innovation hub.', 1, 1252, '2026-07-06 10:08:20', '2026-07-06 16:08:32'),
(2, 'National Assembly Debates Crucial Tax Reform Bill Amid Public Concerns', 'national-assembly-debates-crucial-tax-reform-bill', 'Lawmakers are divided over proposed adjustments to the value-added tax rates.', '<h3>High Stakes in Parliament</h3><p>The national assembly today held a heated debate over the proposed Tax Reform Bill, which outlines major revisions to consumption and income taxes. Proponents argue the measures are essential to balancing the national budget and reducing external debt, while critics claim the adjustments will place an undue burden on low-income households.</p><p>Civil society groups have organized peaceful protests outside parliament, calling for a fairer distribution of taxes and increased transparency in public expenditure.</p><h3>Key Provisions of the Bill</h3><p>The bill proposes raising VAT on electronic services while lowering duties on agricultural inputs. A final vote is expected later this week after committee reviews are completed.</p>', 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?auto=format&fit=crop&q=80&w=1200&h=675', 3, 1, 'published', 0, 1, 0, '2026-07-06 09:38:20', 'Parliament Debates Tax Reform Bill', 'Parliamentary debate intensifies over controversial tax changes and VAT proposals.', 1, 893, '2026-07-06 10:08:20', '2026-07-06 16:22:16'),
(3, 'African Continental Free Trade Area Reports Strong First-Year Growth', 'african-continental-free-trade-area-growth', 'Intra-African trade has risen by 15% following reduction of custom tariffs.', '<h3>A New Milestone for Regional Commerce</h3><p>The secretariat of the African Continental Free Trade Area (AfCFTA) has announced a significant boost in cross-border trade transactions over the past twelve months. The removal of custom bottlenecks and standardized logistics operations have enabled small businesses to expand their reach across regional markets.</p><p>Economists predict that if the current growth trajectory holds, the trade agreement will lift millions out of extreme poverty by 2030.</p>', 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&q=80&w=1200&h=675', 2, 6, 'published', 0, 0, 0, '2026-07-05 10:08:20', 'AfCFTA Reports Strong First-Year Trade Growth', 'Intra-African commerce rises by 15% as AfCFTA custom tariff cuts take effect.', 1, 312, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(4, 'Getembe FC Secures Historic Victory in Regional League Finals', 'getembe-fc-secures-historic-victory-regional-league', 'A dramatic 92nd-minute penalty seals the championship title.', '<h3>Unbelievable Scenes at the Arena</h3><p>Getembe FC completed a dramatic comeback to win the Regional League Cup, defeating their arch-rivals 2-1 in a nail-biting final. The match seemed destined for extra time until a late foul inside the penalty box gave Getembe a golden opportunity.</p><p>The stadium erupted as the captain calmly slotted the ball home, securing their promotion to the premier national division.</p>', 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&q=80&w=1200&h=675', 3, 4, 'published', 0, 0, 0, '2026-07-06 02:08:20', 'Getembe FC Wins Regional League Cup', 'A late penalty guides Getembe FC to a historic league victory and promotion.', 1, 2450, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(5, 'The Future of Renewable Energy: Scaling Solar and Wind in Developing Countries', 'future-of-renewable-energy-rural-grids', 'How decentralized grids are revolutionizing power access in rural communities.', '<h3>Energy Democratization</h3><p>For decades, large-scale centralized power plants have struggled to reach remote rural communities. Today, however, micro-solar grids and compact wind systems are proving that decentralization is the most cost-effective path to universal electricity access.</p><p>This opinion piece explores the technological and economic trends shaping clean energy access.</p>', 'https://images.unsplash.com/photo-1509391366360-2e959784a276?auto=format&fit=crop&q=80&w=1200&h=675', 1, 5, 'published', 0, 0, 0, '2026-07-04 10:08:20', 'Opinion: The Future of Renewable Energy in Rural Areas', 'An analysis of how micro-grids are bringing affordable solar power to developing regions.', 1, 156, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(6, 'Why Digital Literacy Must Be Prioritized in Our Primary School Curriculum', 'why-digital-literacy-must-be-prioritized', 'Integrating basic computer concepts early prepares children for a technology-driven workforce.', '<h3>Teaching Code Alongside Arithmetic</h3><p>In a world increasingly driven by digital services, understanding how computers work is as fundamental as reading and basic maths. To ensure our students are not left behind, primary education policies must adapt to include computer literacy and computational logic in standard syllabus programs.</p>', 'https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?auto=format&fit=crop&q=80&w=1200&h=675', 1, 10, 'published', 0, 0, 0, '2026-07-03 10:08:20', 'Opinion: Primary School Digital Literacy Priorities', 'A call to action for reforming primary education with integrated basic digital literacy skills.', 1, 98, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(7, 'Getembe Local Businesses Report Strong Post-COVID Recovery and Digital Transition', 'getembe-local-businesses-report-strong-recovery', 'Over 60% of small retailers have adopted mobile money payments in the last year.', '<h3>Economic Renaissance</h3><p>Local businesses in Getembe have shown remarkable resilience, registering solid economic growth in the post-pandemic cycle. Small and medium enterprises (SMEs) have leveraged mobile banking and digital inventory systems to lower overheads and expand market range.</p><p>A survey by the Chamber of Commerce reveals that mobile payment solutions have accelerated rural transaction volumes by over 45%.</p>', 'https://images.unsplash.com/photo-1542222035-739e243907c2?auto=format&fit=crop&q=80&w=1200&h=675', 3, 2, 'published', 0, 0, 0, '2026-07-05 22:08:20', 'Getembe Business Digital Recovery Trends', 'How Getembe retailers and SMEs are leveraging digital technology and mobile payments to drive sales growth.', 1, 380, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(8, 'Global Climate Summit Agrees on New Finance Framework for Developing Nations', 'global-climate-summit-new-finance-framework', 'Delegates commit over $100 billion to assist vulnerable regions in infrastructure adaptation.', '<h3>A Landmark Climate Deal</h3><p>In a late-night session, delegates at the Global Climate Summit approved a historic climate finance framework designed to funnel resources to developing nations facing extreme weather patterns. The fund will subsidize clean energy grids and coastal reinforcements in East Africa and South Asia.</p>', 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&q=80&w=1200&h=675', 2, 7, 'published', 0, 0, 0, '2026-07-05 16:08:20', 'Global Climate Finance Framework Approved', 'Global Climate Summit approves $100B adaptation fund for developing nations clean infrastructure.', 1, 290, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(9, 'New Clean Water Initiative in Kisii County Drastically Reduces Waterborne Diseases', 'new-clean-water-initiative-reduces-diseases', 'Local health centers report a 40% decline in cholera and typhoid admissions over six months.', '<h3>Improving Public Health Standards</h3><p>A collaborative water purification program spanning three sub-counties has achieved significant progress in clean water access. By installing solar-powered sanitization systems, local dispensaries have observed a sharp drop in typhoid cases.</p><p>Health officers have encouraged rural households to continue practicing standard hygiene protocols to consolidate these gains.</p>', 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&q=80&w=1200&h=675', 3, 8, 'published', 0, 0, 0, '2026-07-02 10:08:20', 'Kisii County Clean Water Initiative Progress', 'Solar-powered water sanitization systems reduce waterborne diseases by 40% in Kisii County.', 1, 450, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(10, 'Celebrating Kisii Culture: Annual Cultural Festival Returns to Getembe Stadium', 'celebrating-kisii-culture-annual-festival-returns', 'Local cuisine, traditional soapstone art, and folk music take center stage.', '<h3>A Vibrant Cultural Showcase</h3><p>Getembe Stadium hosted thousands of participants today for the opening ceremony of the annual Cultural Festival. Highlighting traditional soapstone carvings, local culinary dishes, and energetic choir performance, the event promotes heritage conservation.</p><p>Organizers expressed optimism that the three-day festival will boost domestic tourism and support local artisan guilds.</p>', 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&q=80&w=1200&h=675', 3, 9, 'published', 0, 0, 0, '2026-07-01 10:08:20', 'Annual Getembe Cultural Festival Returns', 'Kisii heritage, folk dance, and soapstone arts celebrated at Getembe Stadium cultural festival.', 1, 520, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(11, 'County Assembly Proposes New Infrastructure Budget for Getembe Region', 'county-assembly-proposes-new-infrastructure-budget', 'Millions allocated to upgrading roads, clean water distribution, and market centers.', '<h3>Focus on Infrastructure</h3><p>The County Assembly has tabled a supplementary budget focusing heavily on regional connectivity. Members debated key allocations for road networks, sanitation facilities, and high-speed fiber installations in commercial areas.</p>', 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?auto=format&fit=crop&q=80&w=1200&h=675', 3, 1, 'published', 0, 0, 0, '2026-07-06 06:08:20', NULL, NULL, 1, 155, '2026-07-06 10:08:20', '2026-07-06 16:22:16'),
(12, 'Opposition Coalition Calls for Public Dialogue on Taxation Reforms', 'opposition-coalition-calls-for-public-dialogue', 'Political leaders argue for extended public hearings to refine proposed levies.', '<h3>Political Consensus Needed</h3><p>Leading coalition representatives held a press conference calling for extended citizen engagement. They argued that direct consultations on VAT rates will build consensus and policy stability.</p>', 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?auto=format&fit=crop&q=80&w=1200&h=675', 3, 1, 'published', 0, 0, 0, '2026-07-06 04:08:20', NULL, NULL, 1, 120, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(13, 'Governor Pledges to Enhance Security Measures in Local Commercial Hubs', 'governor-pledges-to-enhance-security-measures', 'Modern street lighting and enhanced community policing models to be deployed.', '<h3>Safe Business Environment</h3><p>Speaking to the Chamber of Commerce, the governor committed to round-the-clock safety initiatives. The deployment of solar street lighting and neighborhood police partnership programs is set to commence next month.</p>', 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?auto=format&fit=crop&q=80&w=1200&h=675', 3, 1, 'published', 0, 0, 0, '2026-07-06 00:08:20', NULL, NULL, 1, 205, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(14, 'Electoral Commission Announces Massive Civic Education Drive', 'electoral-commission-announces-civic-education-drive', 'Initiative targets first-time voters and youth cohorts ahead of local polls.', '<h3>Citizen Empowerment</h3><p>The commission has partnered with local universities to conduct workshops on democratic processes and voter rights, stressing the importance of active civic involvement.</p>', 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?auto=format&fit=crop&q=80&w=1200&h=675', 3, 1, 'published', 0, 0, 0, '2026-07-05 20:08:20', NULL, NULL, 1, 95, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(15, 'Kisii Soapstone Art Carvers Eye Lucrative International Export Markets', 'kisii-soapstone-carvers-international-markets', 'Artisans partner with digital global logistics platforms to list soapstone sculptures.', '<h3>Global Market Expansion</h3><p>Carving guilds in Tabaka have entered trade agreements with online marketplaces, allowing them to showcase soapstone sculptures directly to overseas collectors.</p>', 'https://images.unsplash.com/photo-1542222035-739e243907c2?auto=format&fit=crop&q=80&w=1200&h=675', 3, 2, 'published', 0, 0, 0, '2026-07-05 18:08:20', NULL, NULL, 1, 180, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(16, 'New Cooperative Society Formed to Support Local Avocado Farmers', 'new-cooperative-formed-support-avocado-farmers', 'Society provides cold-chain storage solutions and standard price contracts.', '<h3>Cold-Chain Infrastructure</h3><p>Local avocado farmers have formed a centralized cooperative to minimize post-harvest losses. The cooperative features modern cold rooms and direct contracts with exporters.</p>', 'https://images.unsplash.com/photo-1542222035-739e243907c2?auto=format&fit=crop&q=80&w=1200&h=675', 3, 2, 'published', 0, 0, 0, '2026-07-05 14:08:20', NULL, NULL, 1, 220, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(17, 'Inflation Rates Stabilize as Regional Food Supply Recovers', 'inflation-rates-stabilize-food-supply-recovers', 'Consumer pricing indexes show cooling commodity rates due to bumper harvests.', '<h3>Market Stabilization</h3><p>Favorable seasonal weather has boosted regional agricultural output, leading to reduced wholesale costs for essential household food items.</p>', 'https://images.unsplash.com/photo-1542222035-739e243907c2?auto=format&fit=crop&q=80&w=1200&h=675', 3, 2, 'published', 0, 0, 0, '2026-07-05 10:08:20', NULL, NULL, 1, 140, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(18, 'Getembe Tourism Board Launches Modern Digital Interactive Travel App', 'getembe-tourism-launches-travel-app', 'App highlights cultural sites, eco-lodges, and local soapstone craft centers.', '<h3>Eco-Tourism Digitization</h3><p>The interactive mobile app provides travelers with maps, contact directories, and itinerary bookings for major county attractions.</p>', 'https://images.unsplash.com/photo-1542222035-739e243907c2?auto=format&fit=crop&q=80&w=1200&h=675', 3, 2, 'published', 0, 0, 0, '2026-07-04 10:08:20', NULL, NULL, 1, 170, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(19, 'Kisii University Students Develop Smart Soil Testing Mobile App', 'kisii-university-students-smart-soil-app', 'App links to Bluetooth sensor pods to give real-time pH and fertilizer advice.', '<h3>Smart Farming Innovations</h3><p>A student developer team has won a national innovation award for their Bluetooth soil analyzer, offering affordable testing for rural farmers.</p>', 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&q=80&w=1200&h=675', 3, 3, 'published', 0, 0, 0, '2026-07-06 05:08:20', NULL, NULL, 1, 310, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(20, 'Local Telecommunications Operator Expands Fiber Broadband Coverage', 'local-telco-expands-fiber-broadband-coverage', 'High-speed internet services activated in major Getembe residential estates.', '<h3>Digital Connectivity Boost</h3><p>The telecommunications provider completed its phase 2 fiber roll-out, enabling thousands of homes to access unlimited cloud speeds.</p>', 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&q=80&w=1200&h=675', 3, 3, 'published', 0, 0, 0, '2026-07-06 01:08:20', NULL, NULL, 1, 280, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(21, 'E-Commerce Adoption Surges Among Fashion Designers in Getembe', 'ecommerce-adoption-surges-fashion-designers', 'Local boutiques report a 70% increase in social media orders and online payments.', '<h3>Digital Boutique Growth</h3><p>Bespoke tailoring shops are leveraging Instagram and mobile money payments to deliver outfits to clients in major cities.</p>', 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&q=80&w=1200&h=675', 3, 3, 'published', 0, 0, 0, '2026-07-05 19:08:20', NULL, NULL, 1, 340, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(22, 'Solar Energy Start-up Introduces Pay-As-You-Go Off-Grid Power Kits', 'solar-startup-pay-as-you-go-kits', 'New financing model aims to bring lighting and TV power to rural households.', '<h3>Affordable Rural Energy</h3><p>The clean energy start-up provides modular solar kits that customers activate via daily mobile money micro-payments.</p>', 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&q=80&w=1200&h=675', 3, 3, 'published', 0, 0, 0, '2026-07-05 10:08:20', NULL, NULL, 1, 410, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(23, 'Getembe Athletic Club Dominates National Track and Field Championships', 'getembe-athletic-club-dominates-national-championships', 'Sprinters and long-distance runners claim three gold and two silver medals.', '<h3>Podium Finishes</h3><p>Getembe runners delivered spectacular performances, claiming gold in both the 5,000m and 10,000m finals at the national stadium.</p>', 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&q=80&w=1200&h=675', 3, 4, 'published', 0, 0, 0, '2026-07-05 23:08:20', NULL, NULL, 1, 540, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(24, 'County Inter-School Games Kick Off at Getembe Primary Fields', 'county-interschool-games-kick-off', 'Over 30 schools compete in football, volleyball, and track events.', '<h3>Youth Sports Gala</h3><p>The annual county inter-school sports gala opened today, showcasing young athletic talent in soccer tournaments and relays.</p>', 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&q=80&w=1200&h=675', 3, 4, 'published', 0, 0, 0, '2026-07-05 15:08:20', NULL, NULL, 1, 380, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(25, 'Getembe FC Coach Emphasizes Need for Youth Talent Academies', 'getembe-fc-coach-youth-talent-academies', 'Post-match press briefing highlights plans for grassroots football investments.', '<h3>Nurturing Young Talents</h3><p>Following their cup victory, the head coach urged local sponsors to fund development academies to sustain long-term club success.</p>', 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&q=80&w=1200&h=675', 3, 4, 'published', 0, 0, 0, '2026-07-05 10:08:20', NULL, NULL, 1, 450, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(26, 'Kisii Annual Charity Marathon Slated for October Registration Opens', 'kisii-annual-charity-marathon-october-registration', 'Proceeds from the 21km road race to finance local community health projects.', '<h3>Running for Health</h3><p>Marathon organizers have opened corporate and individual registration desks, aiming for 2,000 runners to support health dispensary supplies.</p>', 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&q=80&w=1200&h=675', 3, 4, 'published', 0, 0, 0, '2026-07-04 10:08:20', NULL, NULL, 1, 610, '2026-07-06 10:08:20', '2026-07-06 10:08:20');

DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `body` text NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'approved',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comments_article_id_foreign` (`article_id`),
  KEY `comments_user_id_foreign` (`user_id`),
  KEY `comments_parent_id_foreign` (`parent_id`),
  CONSTRAINT `comments_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `comments` (`id`, `article_id`, `user_id`, `body`, `parent_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 'This is an excellent initiative! The region desperately needs tech workspaces for developer talent.', NULL, 'approved', '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(2, 1, 2, 'I agree. Hopefully local banks will step up to offer loans to start-ups emerging from the incubator.', NULL, 'approved', '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(3, 1, 4, 'Thanks for the details. Does anyone know when registration opens?', 2, 'approved', '2026-07-06 10:08:20', '2026-07-06 10:08:20');

DROP TABLE IF EXISTS `videos`;
CREATE TABLE `videos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `embed_url` varchar(255) NOT NULL,
  `thumbnail_url` varchar(255) DEFAULT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(255) NOT NULL DEFAULT 'published',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `videos_slug_unique` (`slug`),
  KEY `videos_category_id_foreign` (`category_id`),
  CONSTRAINT `videos_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `videos` (`id`, `title`, `slug`, `description`, `embed_url`, `thumbnail_url`, `category_id`, `is_featured`, `status`, `published_at`, `created_at`, `updated_at`) VALUES
(1, 'Inside Getembe County Innovation and Technology Hub', 'inside-getembe-county-innovation-hub', 'A visual tour of the newly opened technology facility and exclusive interviews with local programmers.', 'https://www.youtube.com/embed/5Peo-ivmupE', 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&q=80&w=400&h=225', 3, 1, 'published', '2026-07-06 10:08:20', '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(2, 'Highlights: Getembe FC vs Regional Rivals Championship Match', 'highlights-getembe-fc-championship', 'Watch the thrilling comeback and the late penalty that secured the title.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&q=80&w=400&h=225', 4, 0, 'published', '2026-07-06 05:08:20', '2026-07-06 10:08:20', '2026-07-06 10:08:20');

DROP TABLE IF EXISTS `saved_articles`;
CREATE TABLE `saved_articles` (
  `user_id` bigint(20) unsigned NOT NULL,
  `article_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`article_id`),
  KEY `saved_articles_article_id_foreign` (`article_id`),
  CONSTRAINT `saved_articles_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `saved_articles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `breaking_news`;
CREATE TABLE `breaking_news` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `priority` varchar(255) NOT NULL DEFAULT 'normal',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `breaking_news` (`id`, `title`, `link`, `priority`, `is_active`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'BREAKING: National Assembly Debates Tax Bill - live vote expected in 1 hour.', '/articles/national-assembly-debates-crucial-tax-reform-bill', 'high', 1, '2026-07-06 12:08:20', '2026-07-06 10:08:20', '2026-07-06 10:08:20');

DROP TABLE IF EXISTS `advertisements`;
CREATE TABLE `advertisements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `script_code` text DEFAULT NULL,
  `destination_url` varchar(255) DEFAULT NULL,
  `location` varchar(255) NOT NULL DEFAULT 'sidebar',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `starts_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `clicks` int(10) unsigned NOT NULL DEFAULT '0',
  `impressions` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `advertisements` (`id`, `title`, `image_url`, `script_code`, `destination_url`, `location`, `is_active`, `starts_at`, `expires_at`, `clicks`, `impressions`, `created_at`, `updated_at`) VALUES
(1, 'Top Banner Ad', 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&q=80&w=1200&h=150', NULL, 'https://example.com', 'top', 1, NULL, NULL, 0, 0, '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(2, 'Sidebar Premium Ad', 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&q=80&w=300&h=250', NULL, 'https://example.com', 'sidebar', 1, NULL, NULL, 0, 0, '2026-07-06 10:08:20', '2026-07-06 10:08:20');

DROP TABLE IF EXISTS `newsletters`;
CREATE TABLE `newsletters` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `newsletters_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'Getembe News', '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(2, 'live_tv_url', 'https://player.onestream.live/embed?token=MTExNzk5MA==&type=up', '2026-07-06 10:08:20', '2026-07-06 11:24:47'),
(3, 'live_radio_url', 'http://stream.zeno.fm/f5r7x1t1zv8uv', '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(4, 'weather_city', 'Kisii', '2026-07-06 10:08:20', '2026-07-06 10:08:20'),
(5, 'site_logo', '', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(6, 'brand_color', '#C8102E', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(7, 'favicon', '', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(8, 'website', '', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(9, 'facebook', '', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(10, 'twitter', '', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(11, 'instagram', '', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(12, 'linkedin', '', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(13, 'whatsapp', '', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(14, 'youtube', '', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(15, 'tiktok', '', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(16, 'telegram', '', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(17, 'pinterest', '', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(18, 'threads', '', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(19, 'contact_email', 'contact@getembenews.com', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(20, 'contact_phone', +254712345678, '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(21, 'contact_address', 'Kisii, Kenya', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(22, 'payment_methods', 'M-Pesa, Card', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(23, 'payment_gateways', 'Flutterwave, Stripe', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(24, 'currency', 'KES', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(25, 'currency_symbol', 'KSh', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(26, 'language', 'en', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(27, 'meta_title', 'Getembe News - Kisii County Leading Digital News Platform', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(28, 'meta_description', 'Your leading source for politics, business, technology, sports, and regional news.', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(29, 'meta_keywords', 'news, getembe, kisii, kenya, politics, sports', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(30, 'google_analytics_id', '', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(31, 'google_indexing_api', 0, '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(32, 'sitemap_frequency', 'daily', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(33, 'robots_txt_enabled', 1, '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(34, 'cookie_banner_enabled', 1, '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(35, 'cookie_position', 'bottom', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(36, 'cookie_approval_required', 0, '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(37, 'theme_font', 'Inter', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(38, 'theme_layout', 'standard', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(39, 'smtp_server', 'smtp.mailtrap.io', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(40, 'smtp_port', 2525, '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(41, 'smtp_username', '', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(42, 'smtp_password', '', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(43, 'smtp_encryption', 'tls', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(44, 'smtp_from_name', 'Getembe News', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(45, 'smtp_from_email', 'no-reply@getembenews.com', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(46, 'fb_comments_widget', 0, '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(47, 'fb_comments_position', 'bottom', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(48, 'home_page', 'welcome', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(49, 'about_page', 'about', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(50, 'contact_page', 'contact', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(51, 'privacy_page', 'privacy', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(52, 'footer_copyright', 'Getembe News. All rights reserved.', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(53, 'footer_bg_color', '#111827', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(54, 'footer_text_color', '#D1D5DB', '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(55, 'google_login', 0, '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(56, 'facebook_login', 0, '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(57, 'twitter_login', 0, '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(58, 'github_login', 0, '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(59, 'notifications_enabled', 1, '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(60, 'notifications_push', 0, '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(61, 'notifications_in_app', 1, '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(62, 'notifications_email', 1, '2026-07-06 11:24:47', '2026-07-06 11:24:47'),
(63, 'homepage_categories', 'politics,business,technology,sports', '2026-07-06 11:24:47', '2026-07-06 11:24:47');

DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `article_category`;
CREATE TABLE `article_category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `article_category_article_id_category_id_unique` (`article_id`,`category_id`),
  KEY `article_category_category_id_foreign` (`category_id`),
  CONSTRAINT `article_category_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `article_category_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
