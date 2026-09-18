@extends('layout.admin')

@section('title', 'Edit Blog Post')

@section('content')

<!-- Place the first <script> tag in your HTML's <head> -->
<script src="https://cdn.tiny.cloud/1/d7f1y603g6xg6ub9a9agvb462o89bprz4bykfduod7gbtg4f/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

<!-- Place the following <script> and <textarea> tags your HTML's <body> -->
<script>
  tinymce.init({
    selector: 'textarea',
    plugins: [
      // Core editing features
      'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
    ],
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
    tinycomments_mode: 'embedded',
    tinycomments_author: 'Get ClassifIeD',
    mergetags_list: [
      { value: 'First.Name', title: 'First Name' },
      { value: 'Email', title: 'Email' },
    ],
    ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
  });
</script>

<div class="container-fluid">
    <section class="invoice">
        <!-- Title Row -->
        <div class="row">
            <div class="col-xs-12">
                <h2 class="page-header">Edit Blog Post</h2>
            </div>
        </div>

        <form action="{{ route('admin.blog.update', $post->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Current Image -->
            @if ($post->image_path)
                <div style="margin-bottom: 10px;">
                    <label class="form-label d-block">Current Image</label>
                    <img src="{{ asset($post->image_path) }}" class="img-fluid mb-2" style="max-width: 200px;">
                </div>
            @endif

            <!-- Image -->
            <div style="margin-bottom: 10px;">
                <label for="image" class="form-label">Replace Image (optional)</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*">
            </div>

            <!-- Title -->
            <div style="margin-bottom: 10px;">
                <label for="title" class="form-label">Post Title</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ $post->title }}" required>
            </div>

            <!-- Keywords -->
            <div style="margin-bottom: 10px;">
                <label for="keywords" class="form-label">Keywords</label>
                <p class="text-muted"><i class="fa-regular fa-circle-question"></i> Keywords are words or phrases that users type into search engines to find relevant content for their queries. You should seperate them by commas.</p>
                <input type="text" name="keywords" id="keywords" class="form-control" value="{{ $post->keywords }}" required>
            </div>

            <!-- Content -->
            <div style="margin-bottom: 10px;">
                <label for="content" class="form-label">Post Content</label>
                <textarea name="content" id="content" rows="5" class="form-control" required>{{ $post->content }}</textarea>
            </div>

            <!-- Date -->
            <div class="mb-3">
                <label for="published_at" class="form-label">Published Date</label>
                <input type="date" name="published_at" id="published_at" class="form-control" value="{{ \Carbon\Carbon::parse($post->published_at)->format('Y-m-d') }}" required>
            </div>

            <button type="submit" class="btn" style="background-color: #C55359; color: white; margin-top: 15px;">Update Post</button>
        </form>
    </section>
</div>
@endsection

@section('pageCSS')
<style>
    label {
        color: #2C3C64;
    }
</style>
@endsection