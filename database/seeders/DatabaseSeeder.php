<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Role;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        // Crear usuario admin
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Asignar rol admin
        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        // Usuario fijo para tus pruebas locales
        $johanUser = User::factory()->create([
            'name' => 'Johan User',
            'email' => 'johan6jareth55@gmail.com',
            'password' => bcrypt('password123'),
        ]);
        $johanUser->roles()->syncWithoutDetaching([$adminRole->id]);

        // Crear mas usuarios
        $users = User::factory(10)->create();
        $editorRole = Role::where('name', 'editor')->first();
        $users->random(5)->each(function (User $user) use ($editorRole): void {
            $user->roles()->attach($editorRole->id);
        });

        // Crear categorias
        $categories = Category::factory(5)->create();

        // Crear posts
        Post::factory(50)
            ->for($users->random(), 'author')
            ->for($categories->random(), 'category')
            ->create()
            ->each(function (Post $post) use ($users): void {
                // Agregar tags aleatorios
                $tags = Tag::factory(3)->create();
                $post->tags()->attach($tags->pluck('id'));

                // Agregar comentarios
                Comment::factory(5)
                    ->for($post)
                    ->for($users->random(), 'author')
                    ->create();
            });
    }
}
