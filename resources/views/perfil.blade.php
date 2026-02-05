@extends('layouts.survey')

@section('content')
    <p class="mb-4 text-ueap-muted">
        As informações abaixo são utilizadas apenas para fins estatísticos.
    </p>

    <form method="POST">
        @csrf

        <label class="block mb-6">
            <span class="font-medium">Você participa da UEAP como:</span>

            <select name="perfil"
                    required
                    class="mt-2 block w-full border border-slate-300 rounded px-3 py-2">
                <option value="">Selecione</option>
                <option value="discente">Discente</option>
                <option value="docente">Docente</option>
                <option value="tecnico">Técnico-administrativo</option>
                <option value="egresso">Egresso</option>
                <option value="comunidade">Comunidade externa</option>
            </select>
        </label>

        <button class="bg-ueap-blue text-white px-6 py-2 rounded">
            Continuar
        </button>
    </form>
@endsection
