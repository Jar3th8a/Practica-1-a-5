<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\StorePostWithAttachmentsRequest;
use Illuminate\Support\Facades\Gate;
use App\Http\Models\Attachment;
use App\Services\FileService;
use App\Models\Post;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StorePostWithAttachmentsRequest $request)
    {
        $post = auth()->user()->posts()->create($request->validated());
        if ($request->hasFile('attachments')) {
            $fileService = new FileService();
            foreach ($request->file('attachments') as $file) {
                $fileService->storeAttachment($file, $post->id);
            }
        }
        return redirect()->route('posts.show', $post);
    }
    public function update(StorePostRequest $request, Post $post)
    {
        Gate::authorize('update', $post); // Policy
        $post->update($request->validated());
        $post->tags()->sync($request->tags);
        return redirect()->route('posts.show', $post)->with('success', 'Post actualizado');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attachment $attachment)
    {
        $this->authorize('delete', $attachment->post);
        $fileService = new FileService();
        $fileService->deleteAttachment($attachment);
        return redirect()->back()->with('success', 'Archivo eliminado');
    }

}
