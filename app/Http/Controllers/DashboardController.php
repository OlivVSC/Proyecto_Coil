<?php

namespace App\Http\Controllers;

use App\Models\Publicacion;

class DashboardController extends Controller
{
    public function index()
    {
        // El JavaScript puro conserva las secciones y la lectura integrada.
        $publicaciones = Publicacion::orderBy('id')->get();
        return view('dashboard', compact('publicaciones'));
    }
}
