@extends('layouts.survey')

@section('content')

    <p class="text-sm text-ueap-muted mb-2">
        Etapa {{ $index + 1 }} de {{ $total }}
    </p>

    <h2 class="text-lg font-semibold mb-6">
        {{ $dimension }}
    </h2>

    <form method="POST">
        @csrf

        @foreach ($questions as $question)
            <div class="border border-slate-200 rounded p-4 mb-4">
                <p class="font-medium mb-2">
                    {{ $question->text }}
                </p>

                {{-- Escala --}}
                @if ($question->type === 'scale')
                    @foreach ($question->options as $opt)
                        <label class="block">
                            <input type="radio"
                                   name="answers[{{ $question->id }}]"
                                   value="{{ $opt->text }}"
                                   required>
                            {{ $opt->text }}
                        </label>
                    @endforeach
                @endif

                {{-- Múltipla escolha --}}
                @if ($question->type === 'multi')
                    @foreach ($question->options as $opt)
                        <label class="block">
                            <input type="checkbox"
                                   name="answers[{{ $question->id }}][]"
                                   value="{{ $opt->text }}">
                            {{ $opt->text }}
                        </label>
                    @endforeach
                @endif

                {{-- Texto livre --}}
                @if ($question->type === 'text')
                    <textarea
                        name="answers[{{ $question->id }}]"
                        rows="3"
                        class="mt-2 w-full border border-slate-300 rounded px-3 py-2">
                    </textarea>
                @endif
            </div>
        @endforeach

        <button class="bg-ueap-blue text-white px-6 py-2 rounded">
            Próxima etapa
        </button>
    </form>

@endsection
