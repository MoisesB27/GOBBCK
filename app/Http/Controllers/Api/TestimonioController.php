<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TestimonioRequest as StoreTestimonioRequest;
use App\Models\Testimonio;

class TestimonioController extends Controller
{
     public function index()
    {
        $testimonios = Testimonio::with('user')->paginate(15);
        return response()->json($testimonios);
    }

    public function show($id)
    {
        $testimonio = Testimonio::with('user')->findOrFail($id);
        return response()->json($testimonio);
    }

    public function store(StoreTestimonioRequest $request)
    {
        $testimonio = Testimonio::create($request->validated());
        return response()->json($testimonio, 201);
    }

    public function update(StoreTestimonioRequest $request, $id)
    {
        $testimonio = Testimonio::findOrFail($id);
        $testimonio->update($request->validated());
        return response()->json($testimonio);
    }

    public function destroy($id)
    {
        $testimonio = Testimonio::findOrFail($id);
        $testimonio->delete();
        return response()->json(null, 204);
    }
}
