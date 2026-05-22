<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

/**
 * @group Categorías
 *
 * Endpoints para administración y consulta de categorías.
 */
class CategoriaController extends Controller
{
    /**
     * Obtener todas las categorías
     */
    public function index()
    {
        $categorias = Categoria::orderBy('id', 'desc')->get();

        return $this->sendResponse(
            $categorias,
            'Categorías obtenidas correctamente.'
        );
    }

    /**
     * Crear una nueva categoría
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:categorias,nombre',
            'icono' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7|regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/',
        ]);

        $categoria = Categoria::create([
            'nombre' => $request->nombre,
            'icono' => $request->icono,
            'color' => $request->color,
        ]);

        return $this->sendResponse(
            $categoria,
            'Categoría creada correctamente.',
            201
        );
    }

    /**
     * Obtener una categoría por ID
     */
    public function show($id)
    {
        $categoria = Categoria::with('productos')->find($id);

        if (!$categoria) {

            return $this->sendError(
                'Categoría no encontrada.',
                ['error' => 'No existe una categoría con ese ID.'],
                404
            );
        }

        return $this->sendResponse(
            $categoria,
            'Categoría obtenida correctamente.'
        );
    }

    /**
     * Actualizar categoría
     */
    public function update(Request $request, $id)
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {

            return $this->sendError(
                'Categoría no encontrada.',
                ['error' => 'No existe una categoría con ese ID.'],
                404
            );
        }

        $request->validate([
            'nombre' => 'required|string|max:50|unique:categorias,nombre,' . $id,
            'icono' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7|regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/',
        ]);

        $categoria->update([
            'nombre' => $request->nombre,
            'icono' => $request->icono,
            'color' => $request->color,
        ]);

        return $this->sendResponse(
            $categoria,
            'Categoría actualizada correctamente.'
        );
    }

    /**
     * Eliminar categoría
     */
    public function destroy($id)
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {

            return $this->sendError(
                'Categoría no encontrada.',
                ['error' => 'No existe una categoría con ese ID.'],
                404
            );
        }

        // Validar si tiene productos relacionados
        if ($categoria->productos()->count() > 0) {

            return $this->sendError(
                'No se puede eliminar la categoría.',
                ['error' => 'La categoría tiene productos asociados.'],
                409
            );
        }

        $categoria->delete();

        return $this->sendResponse(
            [],
            'Categoría eliminada correctamente.'
        );
    }
}