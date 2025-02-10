<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\SharedPost;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StorePostRequest; // ✅ Import the correct request file
use App\Http\Requests\UpdateRequest; // ✅ Import the correct request file

class PostController extends Controller
{
    public function index()
    {
        // Admins see all posts, Owners see only their own posts
        $posts = Auth::user()->role === 'admin'
            ? Post::latest()->get()
            : Post::where('user_id', Auth::id())->latest()->get();

        $owners = User::where('role', 'owner')->get(); // Fetch all owners
        $canShare = auth()->user()->role == 'owner'; // Controls sharing permissions

        return view('posts.index', compact('posts', 'owners', 'canShare'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(StorePostRequest $request) // ✅ Corrected request
    {
        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        // Create the post
        Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'image' => $imagePath,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('posts.index')->with('success', 'Post created successfully!');
    }

    public function show(Post $post)
    {
        // Owners can only view their own posts
        if (Auth::user()->role === 'owner' && Auth::id() !== $post->user_id) {
            return abort(403, 'Unauthorized action.');
        }

        $comments = $post->comments()->latest()->get();
        return view('posts.show', compact('post', 'comments'));
    }

    public function edit(Post $post)
    {
        // Only the owner or an admin can edit
        if (Auth::id() !== $post->user_id && Auth::user()->role !== 'admin') {
            return redirect()->route('posts.index')->with('error', 'Unauthorized.');
        }

        return view('posts.edit', compact('post'));
    }

    public function update(UpdateRequest $request, Post $post)
    {
        // Only the owner or admin can update
        if (Auth::id() !== $post->user_id && Auth::user()->role !== 'admin') {
            return redirect()->route('posts.index')->with('error', 'Unauthorized.');
        }



        // Handle image upload if updated
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
            $post->update(['image' => $imagePath]);
        }

        $post->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('posts.index')->with('success', 'Post updated successfully!');
    }

    public function destroy(Post $post)
    {
        // Only the owner or admin can delete
        if (Auth::id() !== $post->user_id && Auth::user()->role !== 'admin') {
            return redirect()->route('posts.index')->with('error', 'Unauthorized.');
        }

        $post->delete();
        return redirect()->route('posts.index')->with('success', 'Post deleted successfully!');
    }

    public function share(Request $request, Post $post)
    {
        $request->validate([
            'shared_to' => 'required|array',
            'shared_to.*' => 'exists:users,id'
        ]);

        if (auth()->user()->role !== 'owner') {
            return back()->with('error', 'Only owners can share posts.');
        }

        foreach ($request->shared_to as $sharedUserId) {
            $alreadyShared = SharedPost::where('post_id', $post->id)
                ->where('shared_to', $sharedUserId)
                ->exists();

            if (!$alreadyShared) {
                SharedPost::create([
                    'post_id' => $post->id,
                    'owner_id' => auth()->id(),
                    'shared_to' => $sharedUserId,
                ]);
            }
        }

        return back()->with('success', 'Post shared successfully!');
    }

}
