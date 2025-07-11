<?php

namespace App\Http\Controllers;

use App\Models\Visita;
use App\Models\Paciente;
use App\Models\Medicamento;
use Illuminate\Http\Request;

class VisitaController extends Controller
{
    public function index()
    {
        return Visita::with(['usuario', 'paciente', 'medicamentos'])->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_apellido' => 'required',
            'identificacion' => 'required',
            'fecha' => 'required|date',
            'idusuario' => 'required|exists:usuarios,id',
            'idpaciente' => 'required|exists:pacientes,id',
            'medicamentos' => 'sometimes|array',
            'medicamentos.*.id' => 'required|exists:medicamentos,id',
            'medicamentos.*.indicaciones' => 'sometimes|string',
        ]);

        $visitaData = $request->except('medicamentos');
        $visita = Visita::create($visitaData);

        if ($request->has('medicamentos')) {
            foreach ($request->medicamentos as $medicamento) {
                $visita->medicamentos()->attach($medicamento['id'], [
                    'indicaciones' => $medicamento['indicaciones'] ?? null
                ]);
            }
        }

        return response()->json($visita->load(['usuario', 'paciente', 'medicamentos']), 201);
    }

    public function show($id)
    {
        return Visita::with(['usuario', 'paciente', 'medicamentos'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $visita = Visita::findOrFail($id);

        $request->validate([
            'nombre_apellido' => 'sometimes|required',
            'identificacion' => 'sometimes|required',
            'fecha' => 'sometimes|required|date',
            'idusuario' => 'sometimes|required|exists:usuarios,id',
            'idpaciente' => 'sometimes|required|exists:pacientes,id',
            'medicamentos' => 'sometimes|array',
            'medicamentos.*.id' => 'required_with:medicamentos|exists:medicamentos,id',
            'medicamentos.*.indicaciones' => 'sometimes|string',
        ]);

        $visitaData = $request->except('medicamentos');
        $visita->update($visitaData);

        if ($request->has('medicamentos')) {
            $medicamentosSync = [];
            foreach ($request->medicamentos as $medicamento) {
                $medicamentosSync[$medicamento['id']] = [
                    'indicaciones' => $medicamento['indicaciones'] ?? null
                ];
            }
            $visita->medicamentos()->sync($medicamentosSync);
        }

        return response()->json($visita->load(['usuario', 'paciente', 'medicamentos']));
    }

    public function destroy($id)
    {
        Visita::findOrFail($id)->delete();
        return response()->json(null, 204);
    }

    public function buscarPaciente($identificacion)
    {
        $paciente = Paciente::where('identificacion', $identificacion)->first();

        if (!$paciente) {
            return response()->json(['message' => 'Paciente no encontrado'], 404);
        }

        return response()->json([
            'nombre' => $paciente->nombre . ' ' . $paciente->apellido,
            'fecnacimiento' => $paciente->fecnacimiento,
            'idpaciente' => $paciente->id
        ]);
    }
}