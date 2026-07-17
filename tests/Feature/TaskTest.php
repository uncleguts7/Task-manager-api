<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class TaskTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_logged_in_users_can_view_all_tasks(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Sanctum::actingAs($user1);

        $task1 = Task::create([
            'user_id'=> $user1->id,
            'name'=> 'Task 1',
            'description'=> 'First task',
            'due_date'=> '1980-12-31',
            'status'=> 'pending',
        ]);

        $task2 = Task::create([
            'user_id'=> $user2->id,
            'name'=> 'Task 2',
            'description'=> 'Second task',
            'due_date'=> '1999-12-31',
            'status'=> 'completed',
        ]);

        $response = $this->getJson('/api/v1/tasks');
        $response->assertStatus(200);

        $response->assertJsonFragment([
            'name' => $task1->name,
            'description' => $task1->description,
            'due_date' => $task1->due_date,
            'status' => $task1->status,
        ]);

        $response->assertJsonMissing([
            'name' => $task2->name,
            'description' => $task2->description,
            'due_date' => $task2->due_date,
            'status' => $task2->status,
        ]);
        
    }

    public function test_user_can_review_their_own_task(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $task = Task::create([
            'user_id'=> $user->id,
            'name'=> 'Task 1',
            'description'=> 'First task',
            'due_date'=> '1980-12-31',
            'status'=> 'pending',
        ]);

        $response = $this->getJson('/api/v1/tasks/'. $task->id);
        $response->assertStatus(200);

        $response->assertJson([
            'name' => $task->name,
            'description' => $task->description,
            'due_date' => $task->due_date,
            'status' => $task->status,
        ]);
    }

    public function test_user_cannot_review_another_users_task(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Sanctum::actingAs($user1);

        $task = Task::create([
            'user_id'=> $user2->id,
            'name'=> 'Task 2',
            'description'=> 'Second task',
            'due_date'=> '1999-12-31',
            'status'=> 'completed',
        ]);

        $response = $this->getJson('/api/v1/tasks/'. $task->id);
        $response->assertStatus(403);
    }

    public function test_user_can_create_task(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $taskData = [
            'name'=> 'New Task',
            'description'=> 'A new task',
            'due_date'=> '2025-12-31',
            'status'=> 'pending',
        ];

        $response = $this->postJson('/api/v1/tasks', $taskData);
        $response->assertStatus(201);

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'name' => 'New Task',
            'description' => 'A new task',
            'due_date' => '2025-12-31',
            'status' => 'pending',
        ]);
    }

    public function test_user_can_update_their_own_task(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $task = Task::create([
            'user_id'=> $user->id,
            'name'=> 'Task 1',
            'description'=> 'First task',
            'due_date'=> '1980-12-31',
            'status'=> 'pending',
        ]);

        $updateData = [
            'name'=> 'Updated Task',
            'description'=> 'Updated description',
            'due_date'=> '2026-12-31',
            'status'=> 'completed',
        ];

        $response = $this->putJson('/api/v1/tasks/'. $task->id, $updateData);
        $response->assertStatus(200);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Updated Task',
            'description' => 'Updated description',
            'due_date' => '2026-12-31',
            'status' => 'completed',
        ]);
    }

    public function test_user_cannot_update_another_users_task(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Sanctum::actingAs($user1);

        $task = Task::create([
            'user_id'=> $user2->id,
            'name'=> 'Task 2',
            'description'=> 'Second task',
            'due_date'=> '1999-12-31',
            'status'=> 'completed',
        ]);

        $updateData = [
            'name'=> 'Updated Task',
            'description'=> 'Updated description',
            'due_date'=> '2026-12-31',
            'status'=> 'completed',
        ];

        $response = $this->putJson('/api/v1/tasks/'. $task->id, $updateData);
        $response->assertStatus(403);
    }

    public function test_user_can_delete_their_own_task(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $task = Task::create([
            'user_id'=> $user->id,
            'name'=> 'Task 1',
            'description'=> 'First task',
            'due_date'=> '1980-12-31',
            'status'=> 'pending',
        ]);

        $response = $this->deleteJson('/api/v1/tasks/'. $task->id);
        $response->assertStatus(204);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_task(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Sanctum::actingAs($user1);

        $task = Task::create([
            'user_id'=> $user2->id,
            'name'=> 'Task 2',
            'description'=> 'Second task',
            'due_date'=> '1999-12-31',
            'status'=> 'completed',
        ]);

        $response = $this->deleteJson('/api/v1/tasks/'. $task->id);
        $response->assertStatus(403);
    }

}
