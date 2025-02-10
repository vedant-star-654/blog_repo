@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Comment Details</h2>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Comment by: {{ $comment->user->name ?? 'Unknown User' }}</h5>
            <p class="card-text">{{ $comment->content }}</p>
            <small class="text-muted">Posted on: {{ $comment->created_at->format('F j, Y, g:i a') }}</small>
        </div>
    </div>

    <a href="{{ route('posts.show', $comment->post_id) }}" class="btn btn-primary mt-3">Back to Post</a>
</div>
@endsection
