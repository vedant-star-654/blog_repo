@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ $post->title }}</h2>
    <p>{{ $post->content }}</p>
    <p><strong>Posted by:</strong> {{ $post->user->name }}</p>

    <hr>

    <h4>Comments</h4>
    @foreach ($comments as $comment)
        <div class="card my-2">
            <div class="card-body">
                <p>{{ $comment->content }}</p>
                <small class="text-muted">By {{ $comment->user->name }} | {{ $comment->created_at->diffForHumans() }}</small>
            </div>
        </div>
    @endforeach

    <!-- Only allow owners to comment on their own posts -->
    @if (Auth::user()->role === 'owner' && Auth::id() === $post->user_id)
        <form action="{{ route('comments.store', $post->id) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="comment" class="form-label">Add a Comment:</label>
                <textarea class="form-control" name="content" id="comment" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Comment</button>
        </form>
    @endif
</div>
@endsection
