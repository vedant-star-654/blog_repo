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
        <tbody>
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
                    <!-- Owners can only view or share -->
                    <a href="{{ route('posts.show', $post->id) }}" class="btn btn-primary btn-sm">View</a>

                    @if(auth()->user()->role == 'owner' && $canShare)
                        <!-- Share Button to Open Modal -->
                        <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#shareModal{{ $post->id }}">
                            Share
                        </button>

                        <!-- Share Modal -->
                        <div class="modal fade" id="shareModal{{ $post->id }}" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Share Post: {{ $post->title }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('posts.share', $post->id) }}" method="POST">
                                            @csrf
                                            <p>Select Owners to Share With:</p>
                                            <div class="form-group">
                                                @foreach($owners as $owner)
                                                    @if($owner->id != auth()->id()) <!-- Exclude logged-in owner -->
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="shared_to[]" value="{{ $owner->id }}" id="owner{{ $post->id }}{{ $owner->id }}">
                                                            <label class="form-check-label" for="owner{{ $post->id }}{{ $owner->id }}">
                                                                {{ $owner->name }}
                                                            </label>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">Share</button>
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Only post owners or admins can edit/delete -->
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
</div>

<!-- Bootstrap & jQuery for Modal Functionality -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
@endsection
