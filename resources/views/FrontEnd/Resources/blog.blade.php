@extends('layout.frontEnd')

@section('title', 'Blog | ClassifIeD')
@section('meta_keywords', 'Blog, Employment News, employee background checks, employee verification, employment verification, ')
@section('meta_description', 'Take a look at all the background screening checks we are able to offer.')


@section('content')
<div class="container py-5">
    <div class="row align-items-center pageBanner animate">
        <!-- Text Section -->
        <div class="col-lg-6 text-white textBannerSection">
            <h1 class="display-5 fw-bold mb-4">Blog</h1>
            <p class="lead mb-4">Keep up-to-date with the world of background checks.</p>
        </div>
        <!-- Image Section -->
        <div class="col-lg-6 text-end pe-0">
            <img rel="preload" as="image" src="{{ env('APP_URL') }}images/FrontEnd/banner-lady-phone-window.webp" alt="Lady smiling with a phone" class="img-fluid" loading="eager" fetchpriority="high">
        </div>
    </div>

    {{-- Search bar --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="blog-search-wrapper">
                <form method="GET" action="{{ route('blog.index') }}" class="blog-search">
                    <input
                        type="text"
                        name="search"
                        class="blog-search-input"
                        placeholder="Search articles..."
                        value="{{ request('search') }}"
                    >
                    <button type="submit" class="blog-search-btn">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Search
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Blog Posts Grid -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mt-5">
        @forelse ($blogPosts as $post)
            <div class="col">
                <div class="card h-100 shadow-sm border-0">
                    <img src="{{ asset($post->image_path) }}" class="card-img-top" alt="{{ $post->title }}" style="object-fit: cover; height: 200px;">
                    <div class="card-body d-flex flex-column">
                        <p class="text-muted text-end mb-2">{{ \Carbon\Carbon::parse($post->published_at)->format('d M Y') }}</p>
                        <h5 class="card-title" style="color: #2C3C64;">{{ $post->title }}</h5>
                        <p class="card-text">{!! $post->description !!}</p>
                        <div class="mt-auto">
                            <a href="{{ url('blog/' . $post->slug) }}" class="btn btn-outline-primary btn-sm mt-3">Read more <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-center text-muted mt-4">
                    @if(request('search'))
                        No articles found for "<strong>{{ request('search') }}</strong>".
                    @else
                        No articles available yet.
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    <!-- Pagination Section -->
    @if ($blogPosts->hasPages())
        <div class="pagination-section mt-5">
            <nav class="blog-pagination" aria-label="Blog pagination">
                <ul class="pagination-list">

                    {{-- Previous --}}
                    @if ($blogPosts->onFirstPage())
                        <li class="pagination-item disabled">
                            <span>&laquo;</span>
                        </li>
                    @else
                        <li class="pagination-item">
                            <a href="{{ $blogPosts->previousPageUrl() }}" rel="prev">&laquo;</a>
                        </li>
                    @endif

                    {{-- Page numbers --}}
                    @foreach ($blogPosts->getUrlRange(1, $blogPosts->lastPage()) as $page => $url)
                        @if ($page == $blogPosts->currentPage())
                            <li class="pagination-item active">
                                <span>{{ $page }}</span>
                            </li>
                        @else
                            <li class="pagination-item">
                                <a href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach

                    {{-- Next --}}
                    @if ($blogPosts->hasMorePages())
                        <li class="pagination-item">
                            <a href="{{ $blogPosts->nextPageUrl() }}" rel="next">&raquo;</a>
                        </li>
                    @else
                        <li class="pagination-item disabled">
                            <span>&raquo;</span>
                        </li>
                    @endif

                </ul>
            </nav>
            <p class="text-center text-muted mt-2 mb-0">
                Showing {{ $blogPosts->firstItem() }} to {{ $blogPosts->lastItem() }} of {{ $blogPosts->total() }} results
            </p>
        </div>
    @endif


    <div class="container py-5 text-white" data-aos="fade-up">
        <div class="overlayContact">
            <div class="py-5">
                <div class="d-flex flex-column flex-md-row align-items-start justify-content-between">
                    <div class="contactText">
                        <h2 class="fw-bold mb-3 contactUnderline">Let's get in touch</h2>
                        <p class="mb-2">
                            Tel: <a href="tel:+441752724000" class="text-decoration-none text-light">+44 (0)1752 724 000</a>
                        </p>
                        <p>
                            Email: <a href="mailto:screening@thinkbitgroup.co.uk" class="text-decoration-none text-light">screening@thinkbitgroup.co.uk</a>
                        </p>
                    </div>
                    <a href="{{ env('APP_URL') }}ContactUs" class="btn btnContact mt-6">Speak to an expert</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('pageCSS')
<style>
/* Animation */
.animate {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.7s ease-out;
}

/* Fade-in Animation */
.animate.visible {
    opacity: 1;
    transform: translateY(0);
}

.btn-outline-primary {
    color: #C55359 !important;
    border-color: #C55359 !important;
}
.btn-outline-primary:hover {
    background-color: #C55359 !important;
    color: white !important;
}

/* Custom pagination styling – no Bootstrap required */
.blog-pagination {
    display: flex;
    justify-content: center;
}

.pagination-list {
    display: flex;
    gap: 0.5rem;
    list-style: none;
    padding: 0;
    margin: 0;
}

.pagination-item a,
.pagination-item span {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 0.75rem;
    border-radius: 999px;
    border: 1px solid #2C3C64;
    font-size: 1rem;
    color: #2C3C64;
    text-decoration: none;
    font-weight: 500;
    transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
}

.pagination-item.active span {
    background-color: #C55359;
    border-color: #C55359;
    color: #fff;
}

.pagination-item a:hover {
    background-color: #C55359;
    border-color: #C55359;
    color: #fff;
}

.pagination-item.disabled span {
    opacity: 0.4;
    border-color: #d2d2d2ff;
    cursor: not-allowed;
}

/* Blog search bar */
.blog-search-wrapper {
    display: flex;
    justify-content: flex-end; /* push search bar to the right */
}

.blog-search {
    position: relative;
    width: 100%;
    max-width: 400px; /* adjust width as you like */
}

.blog-search-input {
    width: 100%;
    padding: 0.6rem 3.1rem 0.6rem 1rem; /* extra right-padding to make room for button */
    border-radius: 999px;
    border: 1px solid #2C3C64;
    outline: none;
    font-size: 0.95rem;
    box-sizing: border-box;
}

.blog-search-input:focus {
    box-shadow: 0 0 0 2px rgba(197, 83, 89, 0.15);
}

.blog-search-btn {
    position: absolute;
    top: 50%;
    right: 4px;
    transform: translateY(-50%);
    border-radius: 999px;
    border: none;
    background-color: #C55359;
    color: #fff;
    padding: 0.4rem 0.9rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    cursor: pointer;
    white-space: nowrap;
    height: calc(100% - 8px); /* keep button nicely inside the input */
}

.blog-search-btn:hover {
    background-color: #a74449;
}
</style>
@endsection

@section('pageJavascript')
<script>
// Function to detect when elements are in view
function revealOnScroll() {
    let elements = document.querySelectorAll('.animate');
    let windowHeight = window.innerHeight;
    
    elements.forEach(el => {
        let position = el.getBoundingClientRect().top;
        if (position < windowHeight - 100) {
            el.classList.add('visible');
        }
    });
}

// Run on scroll and when the page loads
document.addEventListener("DOMContentLoaded", revealOnScroll);
document.addEventListener("scroll", revealOnScroll);
</script>
@endsection