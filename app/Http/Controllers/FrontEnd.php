<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class FrontEnd extends Controller
{
    public function homePage() {

    $latestPosts = \DB::table('blog_posts')
        ->orderBy('published_at', 'desc')
        ->limit(3)
        ->get();
        
    return view('FrontEnd.home', compact('latestPosts'));
    }

    public function blogPage(Request $request)
    {
        $search = $request->input('search');

        $query = \DB::table('blog_posts')
            ->select('*')
            ->orderBy('published_at', 'desc');

        // Apply search filter if there is a query
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $blogPosts = $query
            ->paginate(9)
            ->appends(['search' => $search]); // keep ?search=... on pagination URLs

        return view('FrontEnd.Resources.blog', compact('blogPosts'));
    }

    public function viewBlogPost($slug)
    {
        $post = \DB::table('blog_posts')->where('slug', $slug)->first();

        if (!$post) {
            abort(404); // Show 404 if not found
        }

        return view('FrontEnd.Resources.blogPost', compact('post'));
    }

}