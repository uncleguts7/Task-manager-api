<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_create_product(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/products', [
            'product_name' => 'Test Product',
            'description' => 'A test product',
            'price' => 50,
            'stock' => 10,
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_successfully_creates_product(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $category = Category::create([
            'category_name' => 'Test Category',
            'description' => 'A test category',
        ]);

        $response = $this->postJson('/api/products', [
            'product_name' => 'Test Product',
            'description' => 'A test product',
            'price' => 50,
            'stock' => 10,
            'category_ids' => [$category->id],
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'product_name' => 'Test Product',
            'description' => 'A test product',
            'price' => 50,
            'stock' => 10,
        ]);

        $response->assertJsonStructure([
            'id',
            'product_name',
            'description',
            'price',
            'stock',
            'created_at',
            'updated_at',
        ]);
    }

    public function test_validation_admin_sends_invalid_request(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/products', [
            'product_name' => '',
            'description' => '',
            'price' => -10,
            'stock' => -5,
            'category_ids' => [],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['product_name', 'price', 'stock', 'category_ids']);
    }
}
