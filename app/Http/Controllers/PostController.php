<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(
            Post::with(['author', 'category', 'tags', 'comments'])
                ->latest()
                ->paginate(10)
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json(['message' => 'Formulario de creacion de post']);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StorePostRequest $request): RedirectResponse
    {
        $post = auth()->user()->posts()->create([
            'title' => $request->title,
            'content' => $request->content,
            'category_id' => $request->category_id,
            'published_at' => $request->published_at,
        ]);

        if ($request->has('tags')) {
            $post->tags()->attach($request->tags);
        }

        return redirect()->route('posts.show', $post)->with('success', 'Post creado exitosamente');
    }

    public function update(StorePostRequest $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post); // Policy

        $post->update($request->validated());
        $post->tags()->sync($request->tags ?? []);

        return redirect()->route('posts.show', $post)->with('success', 'Post actualizado');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return response()->json($post->load(['author', 'category', 'tags', 'comments']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        return response()->json(['message' => 'Formulario de edicion de post', 'post' => $post]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        $post->tags()->detach();
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post eliminado');
    }
}
