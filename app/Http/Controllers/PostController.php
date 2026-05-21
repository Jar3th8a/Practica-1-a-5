<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\StorePostWithAttachmentsRequest;
use App\Models\Attachment;
use App\Models\Category;
use App\Services\FileService;
use App\Models\Post;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with(['author', 'category', 'attachments'])->latest()->get();

        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('posts.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StorePostWithAttachmentsRequest $request)
    {
        $post = auth()->user()->posts()->create([
            'title' => $request->title,
            'content' => $request->content,
            'category_id' => $request->category_id,
        ]);

        if ($request->hasFile('attachments')) {
            $fileService = new FileService();
            foreach ($request->file('attachments') as $file) {
                $fileService->storeAttachment($file, $post->id);
            }
        }

        return redirect()->route('posts.show', $post)->with('success', 'Post creado exitosamente');
    }

    public function update(StorePostRequest $request, Post $post)
    {
        Gate::authorize('update', $post); // Policy
        $post->update($request->validated());

        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        return redirect()->route('posts.show', $post)->with('success', 'Post actualizado');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->load(['author', 'category', 'tags', 'attachments']);

        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        return redirect()->route('posts.show', $post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attachment $attachment)
    {
        Gate::authorize('delete', $attachment->post);
        $fileService = new FileService();
        $fileService->deleteAttachment($attachment);
        return redirect()->back()->with('success', 'Archivo eliminado');
    }
}
