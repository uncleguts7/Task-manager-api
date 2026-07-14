<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_create_category(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/categories', [
            'category_name' => 'Test Category',
            'description' => 'A test category',
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_successfully_creates_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/categories', [
            'category_name' => 'Test Category',
            'description' => 'A test category',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'category_name' => 'Test Category',
            'description' => 'A test category',
        ]);
    }

    public function test_admin_can_update_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $category = Category::create([
            'category_name' => 'Games',
            'description' => 'games category',
        ]);

        $response = $this->putJson('/api/categories/' . $category->id, [
            'category_name' => 'Updated Games',
            'description' => 'An updated games category',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'category_name' => 'Updated Games',
            'description' => 'An updated games category',
        ]);
    }

    public function test_non_admin_cannot_update_category(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $category = Category::create([
            'category_name' => 'Games',
            'description' => 'games category',
        ]);

        $response = $this->putJson('/api/categories/' . $category->id, [
            'category_name' => 'Updated Games',
            'description' => 'An updated games category',
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $category = Category::create([
            'category_name' => 'Games',
            'description' => 'games category',
        ]);

        $response = $this->deleteJson('/api/categories/' . $category->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_non_admin_cannot_delete_category(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $category = Category::create([
            'category_name' => 'Games',
            'description' => 'games category',
        ]);

        $response = $this->deleteJson('/api/categories/' . $category->id);

        $response->assertStatus(403);
    }

}
