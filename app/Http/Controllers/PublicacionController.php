<?php

namespace App\Http\Controllers;

use App\Models\Publicacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PublicacionController extends Controller
{
    public const IMAGENES = [
        'marte-atardecer.jpg', 'marte-panorama-exploracion.jpg',
        'marte-hielo.jpg', 'casquete-polar.jpg', 'monte-olimpo.jpg',
        'valles-marineris.jpg', 'canones-marcianos.jpg',
        'perseverance-rover.jpg', 'curiosity-rover.jpg',
        'rover-terreno.jpg', 'insight-lander.jpg', 'marte-panoramica.jpg',
        'rover.jpg', 'paisaje-marte.jpg', 'superficie-marte.jpg',
        'explorador-marte.jpg',
    ];

    public function index()
    {
        $publicaciones = Publicacion::orderBy('id')->get();
        return view('admin', compact('publicaciones'));
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'tipo' => ['required', Rule::in(['articulo', 'mision'])],
            'titulo' => ['required', 'string', 'max:90'],
            'resumen' => ['required', 'string', 'max:260'],
            'contenido' => ['required', 'string', 'max:20000'],
            'imagen' => ['required', Rule::in(self::IMAGENES)],
        ]);
    }

    public function guardar(Request $request): RedirectResponse
    {
        Publicacion::create($this->validar($request));
        return redirect()->route('admin.index')->with('status', 'Publicación creada correctamente.');
    }

    public function actualizar(Request $request, Publicacion $publicacion): RedirectResponse
    {
        $publicacion->update($this->validar($request));
        return redirect()->route('admin.index')->with('status', 'Publicación actualizada correctamente.');
    }

    public function eliminar(Publicacion $publicacion): RedirectResponse
    {
        $publicacion->delete();
        return redirect()->route('admin.index')->with('status', 'Publicación eliminada correctamente.');
    }
}
