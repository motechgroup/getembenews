<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin Dashboard - Getembe News' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans text-gray-900 bg-gray-50 dark:bg-gray-950 dark:text-gray-100 antialiased min-h-screen flex flex-col md:flex-row transition-colors duration-200">

    <!-- Sidebar Navigation -->
    <aside class="w-full md:w-64 bg-gray-900 text-gray-300 flex flex-col border-r border-gray-800 shrink-0">
        <!-- Sidebar Brand Header -->
        <div class="h-16 px-6 border-b border-gray-800 flex items-center justify-between">
            <a href="/" class="flex items-center space-x-2">
                <span class="bg-[#C8102E] text-white font-extrabold text-lg px-1.5 py-0.5 rounded tracking-tighter">G</span>
                <span class="font-serif font-black text-sm tracking-tight text-white">GETEMBE <span class="text-[#C8102E]">NEWS</span></span>
            </a>
            <span class="text-[9px] bg-gray-800 text-gray-400 font-bold px-1.5 py-0.5 rounded uppercase tracking-wider">ADMIN</span>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-grow p-4 space-y-4 overflow-y-auto max-h-[calc(100vh-8rem)] scrollbar-thin scrollbar-thumb-gray-800">
            
            <!-- Category: Content & Core -->
            <div class="space-y-1">
                <div class="text-[9px] uppercase tracking-wider font-extrabold text-gray-500 pl-2">Content & Core</div>
                
                <a href="/admin" class="flex items-center space-x-3 px-3 py-2 text-xs font-semibold rounded hover:bg-gray-800 hover:text-white transition {{ request()->is('admin') ? 'bg-gray-800 text-white border-l-4 border-[#C8102E] pl-2' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"/>
                    </svg>
                    <span>Overview</span>
                </a>
                
                @can('article management')
                <a href="/admin/articles" class="flex items-center space-x-3 px-3 py-2 text-xs font-semibold rounded hover:bg-gray-800 hover:text-white transition {{ request()->is('admin/articles*') ? 'bg-gray-800 text-white border-l-4 border-[#C8102E] pl-2' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 4a2 2 0 012 2v6a2 2 0 01-2 2h-2"/>
                    </svg>
                    <span>Manage Articles</span>
                </a>
                @endcan

                @can('category management')
                <a href="/admin/categories" class="flex items-center space-x-3 px-3 py-2 text-xs font-semibold rounded hover:bg-gray-800 hover:text-white transition {{ request()->is('admin/categories*') ? 'bg-gray-800 text-white border-l-4 border-[#C8102E] pl-2' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                    <span>Manage Categories</span>
                </a>
                @endcan

                @can('page management')
                <a href="/admin/menus" class="flex items-center space-x-3 px-3 py-2 text-xs font-semibold rounded hover:bg-gray-800 hover:text-white transition {{ request()->is('admin/menus*') ? 'bg-gray-800 text-white border-l-4 border-[#C8102E] pl-2' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                    <span>Navigation Menus</span>
                </a>
                @endcan

                @can('comment management')
                <a href="/admin/comments" class="flex items-center space-x-3 px-3 py-2 text-xs font-semibold rounded hover:bg-gray-800 hover:text-white transition {{ request()->is('admin/comments') ? 'bg-gray-800 text-white border-l-4 border-[#C8102E] pl-2' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <span>Comment Moderation</span>
                </a>
                <a href="/admin/settings/fb-comments" class="flex items-center space-x-3 px-3 py-2 text-xs font-semibold rounded hover:bg-gray-800 hover:text-white transition {{ request()->is('admin/settings/fb-comments*') ? 'bg-gray-800 text-white border-l-4 border-[#C8102E] pl-2' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                    </svg>
                    <span>Facebook Comments</span>
                </a>
                @endcan
            </div>

            <!-- Section: Users & Inbox -->
            <div class="space-y-1">
                <div class="text-[9px] uppercase tracking-wider font-extrabold text-gray-500 pl-2">Users & Mail</div>

                @can('user management')
                <a href="/admin/users" class="flex items-center space-x-3 px-3 py-2 text-xs font-semibold rounded hover:bg-gray-800 hover:text-white transition {{ request()->is('admin/users*') ? 'bg-gray-800 text-white border-l-4 border-[#C8102E] pl-2' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span>User Accounts</span>
                </a>
                @endcan

                @can('contact message management')
                <a href="/admin/messages" class="flex items-center space-x-3 px-3 py-2 text-xs font-semibold rounded hover:bg-gray-800 hover:text-white transition {{ request()->is('admin/messages*') ? 'bg-gray-800 text-white border-l-4 border-[#C8102E] pl-2' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>Contact Inbox</span>
                </a>
                @endcan

                @can('subscription management')
                <a href="/admin/settings/subscriptions" class="flex items-center space-x-3 px-3 py-2 text-xs font-semibold rounded hover:bg-gray-800 hover:text-white transition {{ request()->is('admin/settings/subscriptions*') ? 'bg-gray-800 text-white border-l-4 border-[#C8102E] pl-2' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/>
                    </svg>
                    <span>Subscribers List</span>
                </a>
                @endcan
            </div>

            <!-- Section: System Settings -->
            <div class="space-y-1">
                <div class="text-[9px] uppercase tracking-wider font-extrabold text-gray-500 pl-2">System Config</div>

                @can('settings management')
                <a href="/admin/settings/identity" class="flex items-center space-x-3 px-3 py-2 text-xs font-semibold rounded hover:bg-gray-800 hover:text-white transition {{ request()->is('admin/settings/identity*') ? 'bg-gray-800 text-white border-l-4 border-[#C8102E] pl-2' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>General settings</span>
                </a>
                <a href="/admin/settings/socials" class="flex items-center space-x-3 px-3 py-2 text-xs font-semibold rounded hover:bg-gray-800 hover:text-white transition {{ request()->is('admin/settings/socials*') ? 'bg-gray-800 text-white border-l-4 border-[#C8102E] pl-2' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                    <span>Social Links</span>
                </a>
                <a href="/admin/settings/contact" class="flex items-center space-x-3 px-3 py-2 text-xs font-semibold rounded hover:bg-gray-800 hover:text-white transition {{ request()->is('admin/settings/contact*') ? 'bg-gray-800 text-white border-l-4 border-[#C8102E] pl-2' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <span>Contact Info</span>
                </a>
                <a href="/admin/settings/pages" class="flex items-center space-x-3 px-3 py-2 text-xs font-semibold rounded hover:bg-gray-800 hover:text-white transition {{ request()->is('admin/settings/pages*') ? 'bg-gray-800 text-white border-l-4 border-[#C8102E] pl-2' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                    </svg>
                    <span>Page Settings & Footer</span>
                </a>
                @endcan

                @can('email management')
                <a href="/admin/settings/email" class="flex items-center space-x-3 px-3 py-2 text-xs font-semibold rounded hover:bg-gray-800 hover:text-white transition {{ request()->is('admin/settings/email*') ? 'bg-gray-800 text-white border-l-4 border-[#C8102E] pl-2' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l8-5.333a2 2 0 012.22 0l8 5.333A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-2.25-1.5a2 2 0 00-2.22 0l-2.25 1.5"/>
                    </svg>
                    <span>SMTP Mail Server</span>
                </a>
                @endcan

                @can('social login management')
                <a href="/admin/settings/social-login" class="flex items-center space-x-3 px-3 py-2 text-xs font-semibold rounded hover:bg-gray-800 hover:text-white transition {{ request()->is('admin/settings/social-login*') ? 'bg-gray-800 text-white border-l-4 border-[#C8102E] pl-2' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v3a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h8a2 2 0 012 2v3"/>
                    </svg>
                    <span>Social OAuth Integrations</span>
                </a>
                @endcan

                @can('payment management')
                <a href="/admin/settings/payments" class="flex items-center space-x-3 px-3 py-2 text-xs font-semibold rounded hover:bg-gray-800 hover:text-white transition {{ request()->is('admin/settings/payments*') ? 'bg-gray-800 text-white border-l-4 border-[#C8102E] pl-2' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    <span>Payments & Currency</span>
                </a>
                @endcan

                @can('seo management')
                <a href="/admin/settings/seo" class="flex items-center space-x-3 px-3 py-2 text-xs font-semibold rounded hover:bg-gray-800 hover:text-white transition {{ request()->is('admin/settings/seo*') ? 'bg-gray-800 text-white border-l-4 border-[#C8102E] pl-2' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>SEO & Cookie Consents</span>
                </a>
                @endcan
            </div>

            <!-- Section: Engagement & Interactive -->
            <div class="space-y-1">
                <div class="text-[9px] uppercase tracking-wider font-extrabold text-gray-500 pl-2">Engagement</div>

                @can('polls management')
                <a href="/admin/settings/polls" class="flex items-center space-x-3 px-3 py-2 text-xs font-semibold rounded hover:bg-gray-800 hover:text-white transition {{ request()->is('admin/settings/polls*') ? 'bg-gray-800 text-white border-l-4 border-[#C8102E] pl-2' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2zm9 0v-8a2 2 0 00-2-2h-2a2 2 0 00-2 2v8a2 2 0 002 2h2a2 2 0 002-2z"/>
                    </svg>
                    <span>Polls Management</span>
                </a>
                @endcan

                @can('quizzes management')
                <a href="/admin/settings/quizzes" class="flex items-center space-x-3 px-3 py-2 text-xs font-semibold rounded hover:bg-gray-800 hover:text-white transition {{ request()->is('admin/settings/quizzes*') ? 'bg-gray-800 text-white border-l-4 border-[#C8102E] pl-2' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364.364l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 113.536 0V21h-3.536v-5.457z"/>
                    </svg>
                    <span>Quizzes Management</span>
                </a>
                @endcan

                @can('rss management')
                <a href="/admin/settings/rss" class="flex items-center space-x-3 px-3 py-2 text-xs font-semibold rounded hover:bg-gray-800 hover:text-white transition {{ request()->is('admin/settings/rss*') ? 'bg-gray-800 text-white border-l-4 border-[#C8102E] pl-2' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 5c7.18 0 13 5.82 13 13M6 11a7 7 0 017 7m-6 0a1 1 0 11-2 0 1 1 0 012 0z"/>
                    </svg>
                    <span>RSS Feeds Import</span>
                </a>
                @endcan
            </div>

            <!-- Section: System Tools -->
            <div class="space-y-1">
                <div class="text-[9px] uppercase tracking-wider font-extrabold text-gray-500 pl-2">System Tools</div>

                @can('roles and permissions management')
                <a href="/admin/settings/roles" class="flex items-center space-x-3 px-3 py-2 text-xs font-semibold rounded hover:bg-gray-800 hover:text-white transition {{ request()->is('admin/settings/roles*') ? 'bg-gray-800 text-white border-l-4 border-[#C8102E] pl-2' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span>Roles & Permissions</span>
                </a>
                @endcan

                @can('webhooks management')
                <a href="/admin/settings/webhooks" class="flex items-center space-x-3 px-3 py-2 text-xs font-semibold rounded hover:bg-gray-800 hover:text-white transition {{ request()->is('admin/settings/webhooks*') ? 'bg-gray-800 text-white border-l-4 border-[#C8102E] pl-2' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>
                    </svg>
                    <span>Outgoing Webhooks</span>
                </a>
                @endcan

                @can('api keys management')
                <a href="/admin/settings/api-keys" class="flex items-center space-x-3 px-3 py-2 text-xs font-semibold rounded hover:bg-gray-800 hover:text-white transition {{ request()->is('admin/settings/api-keys*') ? 'bg-gray-800 text-white border-l-4 border-[#C8102E] pl-2' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m-5 8a2 2 0 01-2-2V9a2 2 0 012-2h5a2 2 0 012 2v5a2 2 0 01-2 2h-5zM4 15l4-4 4 4M4 11l4-4 4 4"/>
                    </svg>
                    <span>REST API Keys</span>
                </a>
                @endcan

                @can('cache management')
                <a href="/admin/settings/cache" class="flex items-center space-x-3 px-3 py-2 text-xs font-semibold rounded hover:bg-gray-800 hover:text-white transition {{ request()->is('admin/settings/cache*') ? 'bg-gray-800 text-white border-l-4 border-[#C8102E] pl-2' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 12H19M4 20v-5h.581m0 0a8.003 8.003 0 0115.357-2M15 15h.01M19 15h.01M11 15h.01M7 15h.01"/>
                    </svg>
                    <span>Cache System Control</span>
                </a>
                @endcan

                @can('backup management')
                <a href="/admin/settings/backup" class="flex items-center space-x-3 px-3 py-2 text-xs font-semibold rounded hover:bg-gray-800 hover:text-white transition {{ request()->is('admin/settings/backup*') ? 'bg-gray-800 text-white border-l-4 border-[#C8102E] pl-2' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Database Backups</span>
                </a>
                @endcan

                @can('audit logs management')
                <a href="/admin/settings/audit" class="flex items-center space-x-3 px-3 py-2 text-xs font-semibold rounded hover:bg-gray-800 hover:text-white transition {{ request()->is('admin/settings/audit*') ? 'bg-gray-800 text-white border-l-4 border-[#C8102E] pl-2' : '' }}">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Security Audit Logs</span>
                </a>
                @endcan
            </div>
            <hr class="border-gray-800 my-4">
            <a href="/" class="flex items-center space-x-3 px-3 py-2 text-xs font-semibold rounded hover:bg-gray-800 hover:text-white transition">
                <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                <span>View Main Site</span>
            </a>
        </nav>

        <!-- User Profile Footer in Sidebar -->
        <div class="p-4 border-t border-gray-800 flex items-center justify-between text-xs">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center font-bold text-white overflow-hidden">
                    @if(auth()->user()->photo_url)
                        <img src="{{ auth()->user()->photo_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    @endif
                </div>
                <div>
                    <div class="font-bold text-white truncate max-w-28">{{ auth()->user()->name }}</div>
                    <div class="text-[10px] text-gray-500 capitalize">{{ auth()->user()->role }}</div>
                </div>
            </div>
            
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-gray-500 hover:text-white transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-grow flex flex-col min-w-0">
        
        <!-- Top Navigation / Search Bar / Theme toggling -->
        <header class="h-16 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between px-6">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Getembe News Management Panel
            </div>
            
            <!-- Dark mode toggle -->
            <button @click="$store.theme.toggle()" class="p-2 text-gray-550 hover:text-gray-750 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none rounded-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                <svg x-show="!$store.theme.darkMode" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
                <svg x-show="$store.theme.darkMode" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                </svg>
            </button>
        </header>

        <!-- Dynamic Content Slot -->
        <main class="p-6 flex-grow overflow-y-auto">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
