@extends('layouts.app')

@section('content')
<div class="container">
    <h2>All Blog Posts</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('posts.create') }}" class="btn btn-primary mb-3">Create New Post</a>



    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Content</th>
                <th>Image</th>
                <th>User Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="postTable">
            @foreach($posts as $post)
            <tr>
                <td>{{ $post->title }}</td>
                <td>{{ $post->content }}</td>
                <td>
                    @if($post->image)
                        <img src="{{ asset('storage/' . $post->image) }}" alt="Post Image" width="100">
                    @else
                        No Image
                    @endif
                </td>
                <td>{{ $post->user->name }}</td>
                <td>
                    <a href="{{ route('posts.show', $post->id) }}" class="btn btn-primary btn-sm">View</a>
                    @if(auth()->user()->role == 'owner' && $canShare)
                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#sharePostModal-{{ $post->id }}">
                        Share
                    </button>
                    @endif

                    @if(auth()->user()->id == $post->user_id || auth()->user()->role == 'admin')
                        <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('posts.destroy', $post->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                Delete
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @foreach($posts as $post)
    <!-- Modal -->
    <div class="modal fade" id="sharePostModal-{{ $post->id }}" tabindex="-1" aria-labelledby="sharePostModalLabel-{{ $post->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sharePostModalLabel-{{ $post->id }}">Share Post: {{ $post->title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="shareForm-{{ $post->id }}" action="{{ route('posts.share', $post->id) }}" method="POST">
                        @csrf

                        <p>Select owners to share this post:</p>

                        @foreach($owners as $owner)
                            @if($owner->id != auth()->id())
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="shared_to[]" value="{{ $owner->id }}" id="owner-{{ $post->id }}-{{ $owner->id }}">
                                    <label class="form-check-label" for="owner-{{ $post->id }}-{{ $owner->id }}">
                                        {{ $owner->name }}
                                    </label>
                                </div>
                            @endif
                        @endforeach

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Share</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
