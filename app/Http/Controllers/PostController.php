<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Models\Comment;
use App\Models\SharedPost;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        // Fetch posts based on user role
        if (Auth::user()->role === 'admin') {
            $posts = Post::latest()->get(); // Admin sees all posts
        } else {
            $posts = Post::where('user_id', Auth::id())->latest()->get(); // Owners see only their posts
        }

        $owners = User::where('role', 'owner')->get(); // Fetch all owners
        $canShare = Auth::user()->role == 'owner'; // Controls sharing permissions

        return view('posts.index', compact('posts', 'owners', 'canShare'));
    }


    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        // Create the post
        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'image' => $imagePath,
            'user_id' => Auth::id(),
        ]);

        // Check if post was created
        if (!$post) {
            return back()->with('error', 'Failed to create post.');
        }

        return redirect()->route('posts.index')->with('success', 'Post created successfully!');
    }

    public function show(Post $post)
    {
        // Owner can only see and comment on their own posts
        if (Auth::user()->role === 'owner' && Auth::id() !== $post->user_id) {
            return abort(403, 'Unauthorized action.');
        }

        // Fetch comments for this post
        $comments = $post->comments()->latest()->get();

        return view('posts.show', compact('post', 'comments'));
    }

    public function edit(Post $post)
    {
        // Only the owner of the post or an admin can edit
        if (Auth::id() !== $post->user_id && Auth::user()->role !== 'admin') {
            return redirect()->route('posts.index')->with('error', 'You are not authorized to edit this post.');
        }

        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Only the owner of the post or an admin can update
        if (Auth::id() !== $post->user_id && Auth::user()->role !== 'admin') {
            return redirect()->route('posts.index')->with('error', 'You are not authorized to update this post.');
        }




        $post->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('posts.index')->with('success', 'Post updated successfully!');
    }

    public function destroy(Post $post)
    {
        // Only the owner of the post or an admin can delete
        if (Auth::id() !== $post->user_id && Auth::user()->role !== 'admin') {
            return redirect()->route('posts.index')->with('error', 'You are not authorized to delete this post.');
        }

        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully!');
    }

    public function share(Request $request, Post $post)
    {
        $request->validate([
            'shared_to' => 'required|array', // Expecting an array
            'shared_to.*' => 'exists:users,id' // Validate each ID
        ]);

        if (auth()->user()->role !== 'owner') {
            return back()->with('error', 'Only owners can share posts.');
        }

        foreach ($request->shared_to as $sharedUserId) {
            // Check if already shared
            $alreadyShared = SharedPost::where('post_id', $post->id)
                ->where('shared_to', $sharedUserId)
                ->exists();

            if (!$alreadyShared) {
                SharedPost::create([
                    'post_id' => $post->id,
                    'owner_id' => auth()->id(),
                    'shared_to' => $sharedUserId, // Store each user ID separately
                ]);
            }
        }

        return back()->with('success', 'Post shared successfully!');
    }





}
