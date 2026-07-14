<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
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

    public function test_non_admin_cannot_update_product(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $product = Product::create([
            'product_name' => 'Test Product',
            'description' => 'A test product',
            'price' => 50,
            'stock' => 10,
        ]);

        $response = $this->putJson('/api/products/' . $product->id, [
            'product_name' => 'Updated Product',
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_successfully_updates_product(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $product = Product::create([
            'product_name' => 'Test Product',
            'description' => 'A test product',
            'price' => 50,
            'stock' => 10,
        ]);

        $response = $this->putJson('/api/products/'. $product->id, [
            'product_name' => 'Updated Product',
            'price' => 60,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'product_name' => 'Updated Product',
            'price' => 60,
        ]);
    }

    public function test_category_syncing_on_product_update(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $category1 = Category::create([
            'category_name' => 'Category 1',
            'description' => 'First category',
        ]);

        $category2 = Category::create([
            'category_name' => 'Category 2',
            'description' => 'Second category',
        ]);

        $product = Product::create([
            'product_name' => 'Test Product',
            'description' => 'A test product',
            'price' => 50,
            'stock' => 10,
        ]);

        // Attach the first category
        $product->categories()->attach($category1->id);

        // Update the product to sync with the second category
        $response = $this->putJson('/api/products/'. $product->id, [
            'category_ids' => [$category2->id],
        ]);

        $response->assertStatus(200);
        
       $this->assertDatabaseMissing('category_product', [
            'product_id' => $product->id,
            'category_id' => $category1->id,
        ]);

        $this->assertDatabaseHas('category_product', [
            'product_id' => $product->id,
            'category_id' => $category2->id,
        ]);
    }

    public function test_non_admin_cannot_delete_product(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $product = Product::create([
            'product_name' => 'Test Product',
            'description' => 'A test product',
            'price' => 50,
            'stock' => 10,
        ]);

        $response = $this->deleteJson('/api/products/' . $product->id);
        $response->assertStatus(403);
    }

    public function test_admin_successfully_deletes_product(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $product = Product::create([
            'product_name' => 'Test Product',
            'description' => 'A test product',
            'price' => 50,
            'stock' => 10,
        ]);

        $category = Category::create([
            'category_name' => 'Test Category',
            'description' => 'A test category',
        ]);

        $product->categories()->attach($category->id);

        $response = $this->deleteJson('/api/products/' . $product->id);
        $response->assertStatus(204);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
        
        $this->assertDatabaseMissing('category_product', [
            'product_id' => $product->id,
            'category_id' => $category->id,
        ]);
    }
}
