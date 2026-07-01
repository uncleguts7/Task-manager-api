<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = $request->user()->tasks;
        return response()->json($data);
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'status' => 'nullable|in:pending,in_progress,completed',
        ]);

        $validated['user_id'] = $request->user()->id;
        $data = Task::create($validated);

        return response()->json($data,201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Task $task)
    {
        $userId = $request->user()->id;

        if($task->user_id === $userId)
        {
            return response()->json($task);
        }else{
            return response()->json(['message'=>'unauthorized!'], 403);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string',
            'description' => 'sometimes|nullable|string',
            'due_date' => 'sometimes|required|date',
            'status' => 'nullable|in:pending,in_progress,completed',
        ]);
        $userId = $request->user()->id;

        if($task->user_id === $userId)
        {
            $task->update($validated);
            return response()->json($task, 200);
        }else{
            return response()->json(['message'=>'unauthorized!'], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Task $task)
    {
        $userId = $request->user()->id;

        if($task->user_id === $userId)
        {
            $task->delete();
            return response()->json(null, 204);
        }else{
            return response()->json(['message'=>'unauthorized!'], 403);
        }
        
    }
}
