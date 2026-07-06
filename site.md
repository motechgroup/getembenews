# GETEMBE NEWS – Comprehensive Development Specification

## Project Overview

Develop a modern, fast, SEO-optimized news website for **Getembe News**, built using **Laravel 12**, **Blade**, **Livewire 3**, **Tailwind CSS**, **Alpine.js**, **Vite**, and **Supabase PostgreSQL**.

The design philosophy should be inspired by **BBC News**, **Al Jazeera**, and **Sky News**—clean, minimal, professional, content-first, and highly readable.

This is **not** a flashy website. The focus is speed, typography, usability, accessibility, and excellent user experience.

---

# Technology Stack

* Laravel 12
* Blade Templates
* Livewire 3
* Tailwind CSS
* Alpine.js
* Vite
* Supabase PostgreSQL
* Railway Deployment
* GitHub CI/CD

---

# Design Requirements

The website should feel premium and modern.

### Design Principles

* Minimalist
* White background
* Generous spacing
* Large readable typography
* Clean cards
* Rounded corners
* Soft shadows
* Responsive
* Mobile-first
* Fast loading
* Excellent Core Web Vitals

Use smooth animations only where necessary.

Avoid excessive gradients.

Avoid flashy colors.

The homepage should feel similar to BBC, Sky News and Al Jazeera.

---

# Color Palette

Primary

Red (#C8102E)

Secondary

Dark Gray (#222222)

Accent

Light Gray (#F7F7F7)

Background

White

Text

Black

---

# Typography

Modern sans-serif

Examples

* Inter
* Source Sans
* IBM Plex Sans

Large headlines

Comfortable line spacing

Readable body text

---

# Public Website Features

## Homepage

Large Hero Story

Breaking News Ticker

Latest News

Trending News

Top Stories

Featured Story

Politics

Business

Technology

Sports

Entertainment

Health

Education

Lifestyle

Opinion

Africa

World

Videos

Photo Gallery

Live TV Button

Live Radio Button

Editor's Picks

Most Read

Most Shared

Latest Videos

Weather Widget

Newsletter Subscription

Advertisement Sections

Footer

---

# Header

Sticky Navigation

Logo

Search

Categories

Live TV

Live Radio

Dark Mode Toggle

User Account

Notifications

Language Switcher (Future)

---

# Search

Instant Search

Search Articles

Search Categories

Search Authors

Search Videos

---

# Article Page

Large Featured Image

Headline

Subtitle

Author

Reporter Profile

Publication Time

Reading Time

Category

Tags

Social Sharing

Related Articles

Recommended Articles

Previous

Next

Comment Section

Advertisement Blocks

Newsletter Signup

Print Article

Copy Link

---

# Categories

Politics

Business

Technology

Sports

Entertainment

Education

Health

Lifestyle

Opinion

Africa

World

Crime

Environment

Religion

Agriculture

County News

Breaking News

---

# Live TV

Embedded Live Player

Programme Schedule

Current Programme

Upcoming Programmes

Fullscreen

Picture in Picture

Share Stream

---

# Live Radio

Embedded Audio Player

Current Show

Upcoming Show

Programme Schedule

Background Playback

---

# Video Section

Latest Videos

Interviews

Features

Politics

Sports

Entertainment

Documentaries

Search Videos

Categories

---

# Photo Gallery

Albums

Categories

High Resolution Images

Slideshow

Share

Download Disabled

---

# Authors

Author Profile

Biography

Photo

Social Links

Latest Articles

Popular Articles

---

# Contact

Contact Form

Map

Email

Phone

Social Media

News Tip Submission

---

# Newsletter

Email Signup

Subscription Confirmation

Admin Management

---

# Advertisement System

Top Banner

Sidebar Ads

Inline Ads

Footer Ads

Sticky Mobile Ads

Google AdSense Ready

Custom Advertisements

Advert Rotation

Expiry Dates

Analytics

---

# Authentication

Laravel Breeze

Login

Register

Forgot Password

Email Verification

Profile

Avatar

Saved Articles

Notifications

---

# User Dashboard

Saved Stories

Reading History

Comment History

Profile

Settings

Password

Notifications

---

# Admin Panel

Modern Dashboard

Statistics Cards

Charts

Quick Actions

Activity Feed

Dark Mode

Responsive

---

# Admin Modules

## Dashboard

Visitors

Articles

Categories

Users

Comments

Videos

Advertisements

Breaking News

Live TV Status

Live Radio Status

Recent Activities

---

## Articles

Create

Edit

Delete

Publish

Schedule

Drafts

Featured

Breaking News

Pin Article

SEO

Tags

Slug

Categories

Preview

Version History

---

## Categories

Unlimited Categories

Subcategories

Icons

Images

SEO

Ordering

---

## Reporters

Profile

Biography

Photo

Social Links

Status

Assignments

---

## Users

Admin

Editor

Reporter

Contributor

Subscriber

Permissions

Activity

---

## Roles & Permissions

Role Management

Permission Management

Access Control

---

## Comments

Approve

Reject

Spam

Ban User

Moderation Queue

---

## Videos

Upload

Embed

Categories

Featured

Thumbnail

Publish

SEO

---

## Photo Gallery

Albums

Photos

Categories

Captions

Sorting

---

## Live TV

Stream URL

Backup URL

Player Settings

Programme Schedule

Status

---

## Live Radio

Stream URL

Programme Schedule

Current Show

---

## Breaking News

Create Alert

Priority

Expiry

Ticker

Push Notification

---

## Newsletter

Subscribers

Import

Export

Campaigns

Analytics

---

## Advertisements

Clients

Campaigns

Locations

Schedules

Analytics

Clicks

Impressions

---

## Settings

Website Name

Logo

Favicon

Theme

Social Links

SEO

Analytics

SMTP

Maintenance Mode

Cache

---

# SEO

SEO Friendly URLs

Meta Titles

Meta Descriptions

Canonical URLs

Open Graph

Twitter Cards

XML Sitemap

RSS Feed

Robots.txt

Schema.org

Breadcrumbs

Lazy Loading

Image Optimization

---

# Performance

Server-side Rendering

Caching

Image Optimization

Pagination

Infinite Scroll (Optional)

Lazy Loading

Compression

Asset Minification

---

# Security

CSRF Protection

XSS Protection

Rate Limiting

Spam Protection

Role-based Access

Activity Logs

Login History

2FA Ready

---

# Analytics

Page Views

Most Read

Visitor Locations

Traffic Sources

Devices

Browsers

Realtime Visitors

Trending Articles

---

# Notifications

Browser Notifications

Breaking News Alerts

Email Notifications

Admin Notifications

---

# API

REST API

Authentication

Article Endpoints

Category Endpoints

Video Endpoints

Search API

Live Stream API

Future Mobile App Ready

---

# Future Expansion

Android App

iOS App

Smart TV App

Podcast Platform

AI Article Summaries

AI Search

Voice Search

Multilingual Support

Premium Membership

Subscriptions

Donations

Podcast Hosting

Live Event Streaming

Election Results

Opinion Polls

Community Reports

---

# Database

Use **Supabase PostgreSQL** as the primary database.

Use Laravel Eloquent ORM.

Optimize all database queries.

---

# Deployment

Source Control: GitHub

Backend Hosting: Railway

Database: Supabase

Images: Cloudflare Images (future)

Streaming: MediaMTX (future)

---

# Overall Goal

Build a **world-class digital news platform** that is fast, scalable, secure, mobile-friendly, SEO-optimized, and maintainable. The codebase should follow Laravel best practices, be modular and well-documented, and be designed to support millions of page views as the platform grows. The finished product should feel comparable in quality and usability to major international news organizations while reflecting the identity and needs of Getembe News.
