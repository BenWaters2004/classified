@extends('layout.admin')

@section('title', 'Create Blog Posts')

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
                <h2 class="page-header">Create Blog Post</h2>
            </div>
        </div>

        <form action="{{ env('APP_URL') }}admin/blog/store" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Image -->
            <div style="margin-bottom: 10px;">
                <label for="image" class="form-label">Post Image</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*" required>
            </div>

            <!-- Title -->
            <div style="margin-bottom: 10px;">
                <label for="title" class="form-label">Post Title</label>
                <input type="text" name="title" id="title" class="form-control" required>
            </div>

            <!-- Keywords -->
            <div style="margin-bottom: 10px;">
                <label for="keywords" class="form-label">Keywords</label>
                <p class="text-muted"><i class="fa-regular fa-circle-question"></i> Keywords are words or phrases that users type into search engines to find relevant content for their queries. You should seperate them by commas.</p>
                <input type="text" name="keywords" id="keywords" class="form-control" required>
            </div>

            <!-- Content -->
            <div>
                <label for="content" class="form-label">Post Content</label>
                <textarea name="content" id="content" rows="5" class="form-control"></textarea>
            </div>

            <button type="submit" class="btn" style="background-color: #C55359; color: white; margin-top: 15px;">Publish Article</button>
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

@section('pageJavascript')
@endsection
