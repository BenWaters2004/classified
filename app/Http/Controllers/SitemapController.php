<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Str;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = [
            url('/'),
            url('/login'),
            url('/Services'),
            url('/Services/DBS-Basic'),
            url('/Services/BPSS'),
            url('/Services/Digital-ID-Verification'),
            url('/Services/right-to-work'),
            url('/Services/academic-history'),
            url('/Services/employment-history'),
            url('/Services/personal-references'),
            url('/Candidates'),
            url('/Clients'),
            url('/Resources/glossary'),
            url('/sample-report.pdf'),
            url('/AboutUs'),
            url('/ContactUs'),
            url('/Contact/accessibility-feedback'),
            url('/privacypolicy.pdf'),
            url('/AboutUs/security-at-classified'),
            url('/sitemap'),
            url('/Blog'),
            url('/AboutUs/Certifications'),
            url('/Resources'),
        ];

        // Dynamic blog post URLs
        $posts = \DB::table('blog_posts')->select('title')->get();

        foreach ($posts as $post) {
            $slug = \Str::slug($post->title);
            $urls[] = url('/blog/' . $slug);
        }

        $xml = view('sitemap.xml', compact('urls'));

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }

    public function sitemapHtml() {
        $blogPosts = \DB::table('blog_posts')
            ->select('*')
            ->orderBy('published_at', 'desc')
            ->get();

        return view('FrontEnd.sitemap', compact('blogPosts'));
    }
}
