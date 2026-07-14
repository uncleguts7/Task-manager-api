<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_viewing_order_requires_authentication(): void
    {
        $user = User::factory()->create();

        $order = Order::create([
            'user_id'=> $user->id,
            'total_price'=> 350,
            'status'=> 'pending',
        ]);

        $response = $this->getJson('/api/orders/'. $order->id);
        $response->assertStatus(401);
    }

    public function test_user_can_view_their_own_order(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $order = Order::create([
            'user_id'=> $user->id,
            'total_price'=> 350,
            'status'=> 'pending',
        ]);

        $response = $this->getJson('/api/orders/'. $order->id);
        $response->assertStatus(200);
    }

    public function test_user_cannot_view_another_users_order(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Sanctum::actingAs($user1);

        $order = Order::create([
            'user_id'=> $user2->id,
            'total_price'=> 350,
            'status'=> 'pending',
        ]);

        $response = $this->getJson('/api/orders/'. $order->id);
        $response->assertStatus(403);
    }

    public function test_index_logged_in_users_orders():void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Sanctum::actingAs($user1);

        $order1 = Order::create([
            'user_id'=> $user1->id,
            'total_price'=> 350,
            'status'=> 'pending',
        ]);

        $order2 = Order::create([
            'user_id'=> $user2->id,
            'total_price'=> 450,
            'status'=> 'pending',
        ]);
        
        $response = $this->getJson('/api/orders');
        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $order1->id]);
        $response->assertJsonMissing(['id' => $order2->id]);
        
    }
}
