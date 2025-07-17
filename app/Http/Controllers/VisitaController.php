<?php

namespace App\Http\Controllers;

use App\Models\Visita;
use App\Models\Paciente;
use App\Models\Medicamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VisitaController extends Controller
{
    public function index()
    {
        return Visita::with(['usuario', 'paciente', 'medicamentos'])->get();
    }

    public function store(Request $request)
    {
        // Log para debug
        Log::info('Recibiendo datos de visita:', $request->all());

        $request->validate([
            'nombre_apellido' => 'required|string',
            'identificacion' => 'required|string',
            'fecha' => 'required|date',
            'idusuario' => 'required|exists:usuarios,id',
            'idpaciente' => 'required|exists:pacientes,id',
            
            // Campos opcionales - CORREGIDOS
            'id' => 'sometimes|string',
            'hta' => 'sometimes|nullable|string',
            'dm' => 'sometimes|nullable|string',
            'telefono' => 'sometimes|nullable|string',
            'zona' => 'sometimes|nullable|string',
            'peso' => 'sometimes|nullable|numeric',
            'talla' => 'sometimes|nullable|numeric',
            'imc' => 'sometimes|nullable|numeric',
            'perimetro_abdominal' => 'sometimes|nullable|numeric',
            'frecuencia_cardiaca' => 'sometimes|nullable|integer',
            'frecuencia_respiratoria' => 'sometimes|nullable|integer',
            'tension_arterial' => 'sometimes|nullable|string',
            'glucometria' => 'sometimes|nullable|numeric',
            'temperatura' => 'sometimes|nullable|numeric',
            'familiar' => 'sometimes|nullable|string',
            'riesgo_fotografico' => 'sometimes|nullable|string',
            'abandono_social' => 'sometimes|nullable|string',
            'motivo' => 'sometimes|nullable|string',
            'factores' => 'sometimes|nullable|string',
            'conductas' => 'sometimes|nullable|string',
            'novedades' => 'sometimes|nullable|string',
            'proximo_control' => 'sometimes|nullable|date',
            'firma' => 'sometimes|nullable|string',
            
            // Arreglar medicamentos - SOLO UNA VEZ
            'medicamentos_data' => 'sometimes|array', // Cambiar nombre para evitar confusión
        ]);

        // Preparar datos para la visita
        $visitaData = $request->except(['medicamentos_data']);
        
        // Si viene un ID personalizado, usarlo
        if ($request->has('id')) {
            $visitaData['id'] = $request->id;
        }

        try {
            $visita = Visita::create($visitaData);

            // Procesar medicamentos si existen
            if ($request->has('medicamentos_data')) {
                foreach ($request->medicamentos_data as $medicamento) {
                    $visita->medicamentos()->attach($medicamento['id'], [
                        'indicaciones' => $medicamento['indicaciones'] ?? null
                    ]);
                }
            }

            Log::info('Visita creada exitosamente:', ['id' => $visita->id]);

            return response()->json([
                'success' => true,
                'data' => $visita->load(['usuario', 'paciente', 'medicamentos']),
                'message' => 'Visita creada exitosamente'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al crear visita:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear visita: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $visita = Visita::with(['usuario', 'paciente', 'medicamentos'])->findOrFail($id);
            return response()->json(['success' => true, 'data' => $visita]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Visita no encontrada'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $visita = Visita::findOrFail($id);
            
            $request->validate([
                'nombre_apellido' => 'sometimes|required|string',
                'identificacion' => 'sometimes|required|string',
                'fecha' => 'sometimes|required|date',
                'idusuario' => 'sometimes|required|exists:usuarios,id',
                'idpaciente' => 'sometimes|required|exists:pacientes,id',
                
                // Campos opcionales
                'hta' => 'sometimes|nullable|string',
                'dm' => 'sometimes|nullable|string',
                'telefono' => 'sometimes|nullable|string',
                'zona' => 'sometimes|nullable|string',
                'peso' => 'sometimes|nullable|numeric',
                'talla' => 'sometimes|nullable|numeric',
                'imc' => 'sometimes|nullable|numeric',
                'perimetro_abdominal' => 'sometimes|nullable|numeric',
                'frecuencia_cardiaca' => 'sometimes|nullable|integer',
                'frecuencia_respiratoria' => 'sometimes|nullable|integer',
                'tension_arterial' => 'sometimes|nullable|string',
                'glucometria' => 'sometimes|nullable|numeric',
                'temperatura' => 'sometimes|nullable|numeric',
                'familiar' => 'sometimes|nullable|string',
                'riesgo_fotografico' => 'sometimes|nullable|string',
                'abandono_social' => 'sometimes|nullable|string',
                'motivo' => 'sometimes|nullable|string',
                'factores' => 'sometimes|nullable|string',
                'conductas' => 'sometimes|nullable|string',
                'novedades' => 'sometimes|nullable|string',
                'proximo_control' => 'sometimes|nullable|date',
                'firma' => 'sometimes|nullable|string',
                
                'medicamentos_data' => 'sometimes|array',
            ]);

            $visitaData = $request->except(['medicamentos_data']);
            $visita->update($visitaData);

            if ($request->has('medicamentos_data')) {
                $medicamentosSync = [];
                foreach ($request->medicamentos_data as $medicamento) {
                    $medicamentosSync[$medicamento['id']] = [
                        'indicaciones' => $medicamento['indicaciones'] ?? null
                    ];
                }
                $visita->medicamentos()->sync($medicamentosSync);
            }

            return response()->json([
                'success' => true,
                'data' => $visita->load(['usuario', 'paciente', 'medicamentos']),
                'message' => 'Visita actualizada exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al actualizar visita:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar visita: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $visita = Visita::findOrFail($id);
            $visita->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Visita eliminada exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar visita'
            ], 500);
        }
    }

    public function buscarPaciente($identificacion)
    {
        $paciente = Paciente::where('identificacion', $identificacion)->first();

        if (!$paciente) {
            return response()->json([
                'success' => false,
                'message' => 'Paciente no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'nombre' => $paciente->nombre . ' ' . $paciente->apellido,
                'fecnacimiento' => $paciente->fecnacimiento,
                'idpaciente' => $paciente->id
            ]
        ]);
    }
}