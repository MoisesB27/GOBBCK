<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PgobRequest as StorePgobRequest;
use App\Models\Pgob;
use Illuminate\Http\Request;

class PgobController extends Controller
{
    public function index()
    {
        $pgobs = Pgob::with(['services', 'ubicacions'])->paginate(15);

        return response()->json($pgobs);
    }

    public function show($id)
    {
        $pgob = Pgob::with(['services', 'ubicacions'])->findOrFail($id);

        return response()->json($pgob);
    }

    public function store(StorePgobRequest $request)
    {
        $pgob = Pgob::create($request->validated());

        return response()->json($pgob, 201);
    }

    public function update(StorePgobRequest $request, $id)
    {
        $pgob = Pgob::findOrFail($id);
        $pgob->update($request->validated());

        return response()->json($pgob);
    }

    public function destroy($id)
    {
        $pgob = Pgob::findOrFail($id);
        $pgob->delete();

        return response()->json(null, 204);
    }
}
