<?php

namespace App\Http\Controllers;

use App\Models\Audience;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SurveyController extends Controller
{
    public function perfil()
    {
        $audiences = Audience::orderBy('name')->get();

        return view('perfil', compact('audiences'));
    }

    public function salvarPerfil(Request $request)
    {
        $request->validate([
            'audience_id' => 'required|exists:audiences,id'
        ]);

        session([
            'audience_id' => (int) $request->audience_id,
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
