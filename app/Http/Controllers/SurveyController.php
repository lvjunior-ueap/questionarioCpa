<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SurveyController extends Controller
{
    public function perfil()
    {
        return view('perfil');
    }

    public function salvarPerfil(Request $request)
    {
        $request->validate([
            'perfil' => 'required'
        ]);

        session([
            'perfil' => $request->perfil,
            'token' => Str::uuid()->toString(),
            'respostas' => []
        ]);

        return redirect('/survey/1');
    }

    public function survey($pagina)
    {
        $pagina = (int) $pagina;
        return view('survey', compact('pagina'));
    }
}
