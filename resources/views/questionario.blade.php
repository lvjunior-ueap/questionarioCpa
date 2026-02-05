<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Questionário – Página {{ $pagina }}</title>
</head>
<body>

<h2>Página {{ $pagina }} de 3</h2>

<form method="POST">
@csrf

@foreach ($questions as $question)
    <div style="margin-bottom:20px;">
        <strong>{{ $question->text }}</strong><br>

        @if ($question->type === 'scale')
            @for ($i = 1; $i <= 5; $i++)
                <label>
                    <input type="radio"
                           name="answers[{{ $question->id }}]"
                           value="{{ $i }}"
                           required>
                    {{ $i }}
                </label>
            @endfor
        @endif

        @if ($question->type === 'radio')
            @foreach ($question->options as $opt)
                <label>
                    <input type="radio"
                           name="answers[{{ $question->id }}]"
                           value="{{ $opt->text }}"
                           required>
                    {{ $opt->text }}
                </label><br>
            @endforeach
        @endif

        @if ($question->type === 'text')
            <input type="text"
                   name="answers[{{ $question->id }}]"
                   required>
        @endif
    </div>
@endforeach

<button type="submit">
    {{ $pagina < 3 ? 'Próxima página' : 'Finalizar' }}
</button>

</form>

</body>
</html>
