@extends('layout.frontEnd')

@section('title', $post->title . ' | Classified')
@section('meta_keywords', $post->keywords)
@section('meta_description', Str::limit(strip_tags($post->description), 160))

@section('meta')
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $post->title }}">
    <meta name="twitter:description" content="{{ Str::limit(strip_tags($post->description), 200) }}">
    <meta name="twitter:image" content="{{ asset($post->image_path) }}">
    <meta name="twitter:url" content="{{ Request::url() }}">

    <meta property="og:type" content="article">
    <meta property="og:site_name" content="Get ClassifIeD">
    <meta property="og:title" content="{{ $post->title }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($post->description), 200) }}">
    <meta property="og:image" content="{{ asset($post->image_path) }}">
    <meta property="og:url" content="{{ Request::url() }}">
@endsection


@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12 col-lg-10 offset-lg-1">

            {{-- Meta Row: Posted By + Share --}}
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('images/faviconPNG.png') }}" alt="Logo" class="me-2" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                    <span class="fw-semibold">Posted by Get ClassifIeD</span>
                </div>
                <div class="d-flex align-items-center gap-2 mt-3 mt-md-0 share">
                    <span class="fw-semibold me-2">Share:</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(Request::url()) }}" target="_blank" class="btn btn-outline-primary rounded-circle" title="Share on Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(Request::url()) }}&text={{ urlencode($post->title) }}" target="_blank" class="btn btn-outline-info rounded-circle" title="Share on Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(Request::url()) }}&title={{ urlencode($post->title) }}" target="_blank" class="btn btn-outline-secondary rounded-circle" title="Share on LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>

            {{-- Title and Date --}}
            <h1 class="fw-bold mb-2" style="color: #2C3C64;">{{ $post->title }}</h1>
            <p class="text-muted mb-4">{{ \Carbon\Carbon::parse($post->published_at)->format('d M Y') }}</p>

            {{-- Featured Image --}}
            @if($post->image_path)
                <div class="text-center">
                    <img src="{{ asset($post->image_path) }}" alt="{{ $post->title }}" class="img-fluid mb-4" style="max-width: 100%; height: auto;">
                </div>
            @endif

            {{-- Content --}}
            <div class="blog-content" style="line-height: 1.8;">
                {!! $post->content !!}
            </div>
        </div>
    </div>
</div>

<style>
    .share {
        justify-content: end;
    }
</style>
@endsection
