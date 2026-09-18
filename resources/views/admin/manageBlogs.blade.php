@extends('layout.admin')

@section('title', 'Manage Blog Posts')

@section('content')
<div class="container-fluid">
    <section class="invoice">
        <!-- Title Row -->
        <div class="row">
            <div class="col-xs-12">
                <h2 class="page-header">Manage Blog Posts</h2>
                <a href="{{ env('APP_URL') }}admin/createBlog" class="btn" style="background-color: #C55359; color: white; margin-bottom: 5px;">New</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($posts->count())
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Title</th>
                    <th>Published</th>
                    <th style="width: 180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($posts as $post)
                <tr>
                    <td>{{ $post->title }}</td>
                    <td>{{ \Carbon\Carbon::parse($post->published_at)->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('admin.blog.edit', $post->id) }}" class="btn btn-sm btn-primary me-2">Edit</a>
                        <a href="{{ url('blog/' . $post->slug) }}" class="btn btn-sm btn-primary me-2">View</a>
                        <form action="{{ route('admin.blog.delete', $post->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Delete this post?');">
                            @csrf
                            <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
            <p>No blog posts found.</p>
        @endif
    </section>
</div>
@endsection
