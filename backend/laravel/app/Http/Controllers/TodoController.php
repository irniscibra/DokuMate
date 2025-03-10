<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    public function index()
    {
        return Todo::where('assigned_to', Auth::id())->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        return Todo::create([
            'title' => $request->title,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'due_date' => $request->due_date,
        ]);
    }

    public function updateStatus($id)
    {
        $todo = Todo::where('id', $id)->where('assigned_to', Auth::id())->firstOrFail();
        $todo->status = 'erledigt';
        $todo->save();

        return response()->json(['message' => 'To-Do erledigt']);
    }
}
