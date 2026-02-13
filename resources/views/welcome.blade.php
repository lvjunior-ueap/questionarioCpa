@extends('layouts.survey')

@section('content')
    <p class="mb-4">
        Este questionário faz parte do processo de avaliação institucional da UEAP.
    </p>

    <p class="mb-4 text-ueap-muted">
        Suas respostas são anônimas e analisadas apenas de forma estatística.
    </p>

    <p class="mb-6">
        A participação é rápida e contribui para a melhoria da universidade.
    </p>


    <div class="mb-6 rounded-md border border-slate-200 bg-slate-50 p-4 text-sm text-ueap-muted">
        <p><strong>Duração média:</strong> cerca de 5 a 10 minutos.</p>
        <p><strong>Dica:</strong> responda em um único dispositivo para evitar perda de progresso.</p>
    </div>

    <a href="{{ route('perfil') }}">
        <button class="bg-ueap-blue text-white px-6 py-2 rounded hover:bg-blue-800">
            Iniciar questionário
        </button>
    </a>
@endsection
