<div>
    <p class="mb-6 text-sm text-ueap-muted">
        Página {{ $pagina }} de {{ $totalPages }}
    </p>

    <form wire:submit.prevent="submit">
        @foreach ($questions as $question)
            <div class="border border-slate-200 rounded p-4 mb-4">
                <p class="font-medium mb-2">
                    {{ $question->text }}
                </p>

                @if ($question->type === 'scale')
                    @for ($i = 1; $i <= 5; $i++)
                        <label class="mr-4">
                            <input type="radio"
                                   wire:model="answers.{{ $question->id }}"
                                   value="{{ $i }}"
                                   required>
                            {{ $i }}
                        </label>
                    @endfor
                @endif

                @if ($question->type === 'radio')
                    @foreach ($question->options as $opt)
                        <label class="block">
                            <input type="radio"
                                   wire:model="answers.{{ $question->id }}"
                                   value="{{ $opt->text }}"
                                   required>
                            {{ $opt->text }}
                        </label>
                    @endforeach
                @endif

                @if ($question->type === 'text')
                    <input type="text"
                           wire:model="answers.{{ $question->id }}"
                           class="mt-2 block w-full border border-slate-300 rounded px-3 py-2"
                           required>
                @endif
            </div>
        @endforeach

        <button class="bg-ueap-blue text-white px-6 py-2 rounded">
            {{ $pagina < $totalPages ? 'Próxima página' : 'Finalizar' }}
        </button>
    </form>
</div>
