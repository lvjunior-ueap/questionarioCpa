@extends('layouts.survey')

@section('content')
    <p class="mb-4 text-ueap-muted">
        As informações abaixo são utilizadas apenas para fins estatísticos.
    </p>

    <form method="POST">
        @csrf

        <label class="block mb-6">
            <span class="font-medium">Você participa da UEAP como:</span>

            <select name="audience_id"
                    required
                    class="mt-2 block w-full border border-slate-300 rounded px-3 py-2">
                <option value="">Selecione</option>
                @foreach ($audiences as $audience)
                    <option value="{{ $audience->id }}">{{ $audience->name }}</option>
                @endforeach
            </select>
        </label>

        <button class="bg-ueap-blue text-white px-6 py-2 rounded">
            Continuar
        </button>
    </form>
@endsection
