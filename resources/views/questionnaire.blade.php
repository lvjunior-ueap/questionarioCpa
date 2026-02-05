<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>{{ $questionnaire->title }}</title>
</head>
<body>

<h1>{{ $questionnaire->title }}</h1>
<p>{{ $questionnaire->description }}</p>

<form method="POST" action="{{ route('submit') }}">
    @csrf

    <input type="hidden" name="questionnaire_id" value="{{ $questionnaire->id }}">

    @foreach ($questionnaire->questions as $question)
        <div style="margin-bottom:20px;">
            <strong>{{ $question->text }}</strong><br>

            {{-- Texto livre --}}
            @if ($question->type === 'text')
                <input type="text" name="answers[{{ $question->id }}]" style="width:300px;">
            @endif

            {{-- Múltipla escolha --}}
            @if ($question->type === 'radio')
                @foreach ($question->options as $option)
                    <label>
                        <input type="radio"
                               name="answers[{{ $question->id }}]"
                               value="{{ $option->text }}">
                        {{ $option->text }}
                    </label><br>
                @endforeach
            @endif

            {{-- Escala 1 a 5 --}}
            @if ($question->type === 'scale')
                @for ($i = 1; $i <= 5; $i++)
                    <label>
                        <input type="radio"
                               name="answers[{{ $question->id }}]"
                               value="{{ $i }}">
                        {{ $i }}
                    </label>
                @endfor
            @endif
        </div>
    @endforeach

    <button type="submit">Enviar</button>
</form>

</body>
</html>
