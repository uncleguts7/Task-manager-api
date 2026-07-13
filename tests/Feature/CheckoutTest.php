<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Laravel\Sanctum\Sanctum;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_requires_authentication(): void
    {
        $response = $this->postJson('/api/checkout');

        $response->assertStatus(401);
    }

    public function test_checkout_succeeds_with_items_in_cart(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);
        
        $product = Product::create([
            'product_name' => 'Test Product',
            'description' => 'A test product',
            'price' => 50,
            'stock' => 10,
        ]);

        $cart = Cart::create([
            'user_id' => $user->id,
        ]);

        $cart_item = CartItem::create([
            'cart_id'=> $cart->id,
            'product_id'=> $product->id,
            'quantity'=> 10,
        ]);

        $response = $this->postJson('/api/checkout');
        
        $response->assertStatus(201);
        $response->assertJson([
            'total_price'=> 500,
        ]);
    }

    public function test_checkout_fails_with_empty_cart(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $cart = Cart::create([
            'user_id' => $user->id,
        ]);

        $response = $this->postJson('/api/checkout');
        $response->assertStatus(422);
    }

    public function test_checkout_fails_with_no_cart(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/checkout');
        $response->assertStatus(404);
    }
}
