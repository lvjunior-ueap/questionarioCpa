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

    <form method="POST" x-data="{ selectedAudience: '' }">
        @csrf

        <label class="block mb-2">
            <span class="font-medium">Você participa da UEAP como:</span>

            <select name="audience_id"
                    x-model="selectedAudience"
                    required
                    class="mt-2 block w-full border border-slate-300 rounded px-3 py-2">
                <option value="">Selecione</option>
                @foreach ($audiences as $audience)
                    <option value="{{ $audience->id }}">{{ $audience->name }}</option>
                @endforeach
            </select>
        </label>

        @error('audience_id')
            <p class="mb-4 text-sm text-red-600">{{ $message }}</p>
        @enderror

        <div class="mb-6 rounded-md border border-slate-200 bg-white p-4 text-sm text-ueap-muted">
            <p class="font-medium text-ueap-text mb-1">Estimativa antes de começar</p>
            <p class="text-xs mb-2">Esse cálculo muda conforme o público selecionado.</p>

            @foreach ($audiences as $audience)
                <div
                    x-cloak
                    x-show="selectedAudience === '{{ $audience->id }}'"
                    class="rounded border border-slate-200 bg-slate-50 p-3"
                >
                    <p>
                        Você responderá aproximadamente
                        <strong>{{ $audienceStats[$audience->id]['questions'] ?? 0 }} perguntas</strong>
                        em
                        <strong>{{ $audienceStats[$audience->id]['dimensions'] ?? 0 }} dimensões</strong>.
                    </p>
                    <p class="mt-1">
                        Tempo estimado: <strong>{{ $audienceStats[$audience->id]['estimated_minutes'] ?? 0 }} minutos</strong>.
                    </p>
                </div>
            @endforeach

            <p x-show="!selectedAudience" class="text-xs text-ueap-muted">Selecione seu público para ver a estimativa.</p>
        </div>

        <p class="mb-4 text-xs text-ueap-muted">
            Se você tiver dúvida sobre o perfil, use as definições acima para escolher a opção que melhor representa sua situação atual.
        </p>

        <button class="bg-ueap-blue text-white px-6 py-2 rounded">
            Continuar
        </button>
    </form>
@endsection
