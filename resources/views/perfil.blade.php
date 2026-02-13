@extends('layouts.survey')

@section('content')
    <p class="mb-4 text-ueap-muted">
        As informações abaixo são utilizadas apenas para fins estatísticos.
    </p>

    <div class="mb-6 rounded-md border border-slate-200 bg-slate-50 p-4 text-sm text-ueap-muted">
        <p class="font-medium text-ueap-text mb-2">Quem é cada público?</p>
        <ul class="list-disc pl-5 space-y-1">
            @foreach ($audiences as $audience)
                <li>{{ $audienceDescriptions[$audience->slug] ?? $audience->name }}</li>
            @endforeach
        </ul>
    </div>

    <form method="POST">
        @csrf

        <label class="block mb-2">
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

        <p class="mb-6 text-xs text-ueap-muted">
            Se você tiver dúvida sobre o perfil, use as definições acima para escolher a opção que melhor representa sua situação atual.
        </p>

        <button class="bg-ueap-blue text-white px-6 py-2 rounded">
            Continuar
        </button>
    </form>
@endsection
