<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_item_increases_quantity_on_duplicate_add(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $product = Product::create([
            'product_name'=> 'Test Product',
            'description'=> 'Test Description',
            'price'=> 10.00,
            'stock'=> 100,
        ]);

        $response1 = $this->postJson('/api/v1/cart/add', ['product_id'=> $product->id]);
        $response1->assertStatus(200);

        $response2 = $this->postJson('/api/v1/cart/add', ['product_id'=> $product->id]);
        $response2->assertStatus(200);

        $this->assertDatabaseCount('cart_items', 1);
        $this->assertDatabaseHas('cart_items', [
            'product_id'=> $product->id,
            'quantity'=> 2,
        ]);

    }

    public function test_show_only_returns_logged_in_users_cart(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Sanctum::actingAs($user1);

        $product1 = Product::create([
            'product_name'=> 'Product 1',
            'description'=> 'Description 1',
            'price'=> 10.00,
            'stock'=> 100,
        ]);

        $product2 = Product::create([
            'product_name'=> 'Product 2',
            'description'=> 'Description 2',
            'price'=> 20.00,
            'stock'=> 50,
        ]);

        $this->postJson('/api/v1/cart/add', ['product_id'=> $product1->id]);
        Sanctum::actingAs($user2);
        $this->postJson('/api/v1/cart/add', ['product_id'=> $product2->id]);

        Sanctum::actingAs($user1);
        $response = $this->getJson('/api/v1/cart');
        $response->assertStatus(200);

        $response->assertJsonFragment([
            'product_id' => $product1->id,
        ]);

        $response->assertJsonMissing([
            'product_id' => $product2->id,
        ]);
    }

    public function test_user_can_update_own_cart_item_quantity(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $product = Product::create([
            'product_name'=> 'Test Product',
            'description'=> 'Test Description',
            'price'=> 10.00,
            'stock'=> 100,
        ]);

        $this->postJson('/api/v1/cart/add', ['product_id'=> $product->id]);

        $cartItem = $user->cart->cartItems()->first();

        $response = $this->putJson('/api/v1/cart/items/'. $cartItem->id, ['quantity'=> 5]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('cart_items', [
            'cart_id'=> $user->cart->id,
            'product_id'=> $product->id,
            'quantity'=> 5,
        ]);
    }

    public function test_user_cannot_update_another_users_cart_item(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Sanctum::actingAs($user1);

        $product = Product::create([
            'product_name'=> 'Test Product',
            'description'=> 'Test Description',
            'price'=> 10.00,
            'stock'=> 100,
        ]);

        $this->postJson('/api/v1/cart/add', ['product_id'=> $product->id]);

        $cartItem = $user1->cart->cartItems()->first();

        Sanctum::actingAs($user2);
        $response = $this->putJson('/api/v1/cart/items/'. $cartItem->id, ['quantity'=> 5]);
        $response->assertStatus(403);
    }

    public function test_user_can_remove_own_cart_item(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $product = Product::create([
            'product_name'=> 'Test Product',
            'description'=> 'Test Description',
            'price'=> 10.00,
            'stock'=> 100,
        ]);

        $this->postJson('/api/v1/cart/add', ['product_id'=> $product->id]);

        $cartItem = $user->cart->cartItems()->first();

        $response = $this->deleteJson('/api/v1/cart/items/'. $cartItem->id);
        $response->assertStatus(204);

        $this->assertDatabaseMissing('cart_items', [
            'id'=> $cartItem->id,
        ]);
    }

    public function test_user_cannot_remove_another_users_cart_item(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Sanctum::actingAs($user1);

        $product = Product::create([
            'product_name'=> 'Test Product',
            'description'=> 'Test Description',
            'price'=> 10.00,
            'stock'=> 100,
        ]);

        $this->postJson('/api/v1/cart/add', ['product_id'=> $product->id]);

        $cartItem = $user1->cart->cartItems()->first();

        Sanctum::actingAs($user2);
        $response = $this->deleteJson('/api/v1/cart/items/'. $cartItem->id);
        $response->assertStatus(403);
    }
}
