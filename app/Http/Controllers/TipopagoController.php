<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Tipopago;

class TipopagoController extends Controller
{
    public function index()
    {
        $tipospago = Tipopago::all();
        return response()->json($tipospago, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:50|unique:tipospago',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $tipospago = Tipopago::create($request->all());

        return response()->json([
            'message' => 'Tipo de pago creado exitosamente',
            'tipopago' => $tipospago
        ], 201);
    }

    public function show($id)
    {
        $tipospago = Tipopago::find($id);

        if (!$tipospago) {
            return response()->json(['message' => 'Tipo de pago no encontrado'], 404);
        }

        return response()->json($tipospago, 200);
    }

    public function update(Request $request, $id)
    {
        $tipospago = Tipopago::find($id);

        if (!$tipospago) {
            return response()->json(['message' => 'Tipo de pago no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:50|unique:tipospago,nombre,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $tipospago->update($request->all());

        return response()->json([
            'message' => 'Tipo de pago actualizado exitosamente',
            'tipopago' => $tipospago
        ], 200);
    }

    public function destroy($id)
    {
        $tipospago = Tipopago::find($id);

        if (!$tipospago) {
            return response()->json(['message' => 'Tipo de pago no encontrado'], 404);
        }

        $tipospago->delete();

        return response()->json(['message' => 'Tipo de pago eliminado exitosamente'], 200);
    }
}

