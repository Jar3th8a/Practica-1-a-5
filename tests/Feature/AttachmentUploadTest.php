<?php

namespace Tests\Feature;

use App\Models\Attachment;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttachmentUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_create_post_with_valid_attachment(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $editorRole = Role::create(['name' => 'editor']);
        $user->roles()->attach($editorRole->id);

        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'Categoria prueba',
            'description' => 'Descripcion',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('posts.store'), [
            'title' => 'Post de prueba',
            'content' => str_repeat('A', 60),
            'category_id' => $categoryId,
            'attachments' => [
                UploadedFile::fake()->create('foto.jpg', 200, 'image/jpeg'),
            ],
        ]);

        $post = Post::first();

        $response->assertRedirect(route('posts.show', $post));
        $this->assertNotNull($post);
        $this->assertDatabaseCount('attachments', 1);

        $files = Storage::disk('public')->files('posts/' . $post->id);
        $this->assertCount(1, $files);
    }

    public function test_store_validates_mime_type_for_attachments(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $editorRole = Role::create(['name' => 'editor']);
        $user->roles()->attach($editorRole->id);

        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'Categoria mime',
            'description' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->from(route('posts.create'))
            ->post(route('posts.store'), [
                'title' => 'Post con archivo invalido',
                'content' => str_repeat('B', 60),
                'category_id' => $categoryId,
                'attachments' => [
                    UploadedFile::fake()->create('malicioso.exe', 100, 'application/octet-stream'),
                ],
            ]);

        $response->assertRedirect(route('posts.create'));
        $response->assertSessionHasErrors('attachments.0');
        $this->assertDatabaseCount('attachments', 0);
    }

    public function test_store_validates_max_size_for_attachments(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $editorRole = Role::create(['name' => 'editor']);
        $user->roles()->attach($editorRole->id);

        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'Categoria tamano',
            'description' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->from(route('posts.create'))
            ->post(route('posts.store'), [
                'title' => 'Post con archivo grande',
                'content' => str_repeat('C', 60),
                'category_id' => $categoryId,
                'attachments' => [
                    UploadedFile::fake()->create('grande.pdf', 6000, 'application/pdf'),
                ],
            ]);

        $response->assertRedirect(route('posts.create'));
        $response->assertSessionHasErrors('attachments.0');
        $this->assertDatabaseCount('attachments', 0);
    }

    public function test_owner_can_delete_attachment_file_and_record(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $post = $user->posts()->create([
            'title' => 'Post para borrar archivo',
            'content' => str_repeat('D', 60),
            'category_id' => DB::table('categories')->insertGetId([
                'name' => 'Categoria delete',
                'description' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]),
        ]);

        $path = 'posts/' . $post->id . '/archivo.pdf';
        Storage::disk('public')->put($path, 'contenido');

        $attachment = Attachment::create([
            'post_id' => $post->id,
            'filename' => 'archivo.pdf',
            'original_name' => 'archivo.pdf',
            'mime_type' => 'application/pdf',
            'size' => 1000,
            'path' => $path,
        ]);

        $response = $this->actingAs($user)
            ->delete(route('attachments.destroy', $attachment));

        $response->assertRedirect();
        $this->assertDatabaseMissing('attachments', ['id' => $attachment->id]);
        Storage::disk('public')->assertMissing($path);
    }
}
