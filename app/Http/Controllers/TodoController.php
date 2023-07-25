<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class TodoController extends Controller
{
    public function index()
    {
        $todos = Todo::all();
        return response()->json($todos);
    }

    // Create or input Todo data
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string',
                'description' => 'nullable|string',
            ]);

            $todo = Todo::create($validatedData);
            return response()->json($todo, 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 400);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }

    // Read or display Todo data
    public function show($id)
    {
        $todo = Todo::find($id);
        if (!$todo) {
            return response()->json(['error' => 'Todo not found.'], 404);
        }
        return response()->json($todo);
    }

    // Update or edit Todo data
    public function update(Request $request, $id)
    {
        try {
            $todo = Todo::find($id);
            if (!$todo) {
                return response()->json(['error' => 'Todo not found.'], 404);
            }

            $validatedData = $request->validate([
                'title' => 'required|string',
                'description' => 'nullable|string',
            ]);

            $todo->update($validatedData);
            return response()->json($todo, 200);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 400);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }

    // Delete or remove Todo data
    public function destroy($id)
    {
        $todo = Todo::find($id);
        if (!$todo) {
            return response()->json(['error' => 'Todo not found.'], 404);
        }

        $todo->delete();
        return response()->json(null, 204);
    }
}
