<div>
    @if ($audienceIntro)
        <p class="mb-4 text-sm text-ueap-muted whitespace-pre-line">
            {{ $audienceIntro }}
        </p>
    @endif

    <p class="mb-6 text-sm text-ueap-muted">
        Pergunta {{ $pagina }} de {{ $totalPages }}
    </p>

    @if ($dimensionTitle)
        <div class="mb-6">
            <h2 class="text-lg font-semibold">{{ $dimensionTitle }}</h2>
            @if ($dimensionDescription)
                <p class="text-sm text-ueap-muted mt-1">
                    {{ $dimensionDescription }}
                </p>
            @endif
        </div>
    @endif

    <form wire:submit.prevent="submit">
        <div class="sticky top-0 z-50 bg-white pb-4 mb-6 border-b border-slate-200">
            <div class="mb-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs text-ueap-muted">
                        Progresso geral: {{ $this->respondidas }} / {{ $this->totalPerguntas }} perguntas respondidas
                    </span>

                    <span class="text-xs font-semibold {{ $this->progresso === 100 ? 'text-green-600' : 'text-ueap-muted' }}">
                        {{ $this->progresso }}%
                    </span>
                </div>

                <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
                    <div
                        class="h-3 rounded-full transition-all duration-500 ease-in-out {{ $this->progresso === 100 ? 'bg-green-500' : 'bg-ueap-blue' }}"
                        style="width: {{ $this->progresso }}%">
                    </div>
                </div>
            </div>

            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs text-ueap-muted">
                        Dimensão {{ $this->indiceDimensaoAtual }} de {{ $this->totalDimensoes }}: {{ $this->respondidasDimensaoAtual }} / {{ $this->totalPerguntasDimensaoAtual }}
                    </span>
                    <span class="text-xs text-ueap-muted font-medium">{{ $this->progressoDimensao }}%</span>
                </div>

                <div class="w-full bg-slate-100 rounded-full h-2">
                    <div
                        class="bg-slate-400 h-2 rounded-full transition-all duration-500"
                        style="width: {{ $this->progressoDimensao }}%">
                    </div>
                </div>
            </div>
        </div>

        @if ($currentQuestion)
            <div wire:key="question-{{ $currentQuestion->id }}" class="border border-slate-200 rounded p-4 mb-4">
                <p class="font-medium mb-2">
                    {{ $currentQuestion->text }}
                </p>

                @if ($currentQuestion->type === 'scale')
                    @for ($i = 1; $i <= 5; $i++)
                        <label class="mr-4">
                            <input type="radio"
                                name="answers[{{ $currentQuestion->id }}]"
                                wire:model="answers.{{ $currentQuestion->id }}"
                                value="{{ $i }}"
                                required>
                            {{ $i }}
                        </label>
                    @endfor
                @endif

                @if ($currentQuestion->type === 'radio')
                    @foreach ($currentQuestion->options as $opt)
                        <label class="block">
                            <input type="radio"
                                name="answers[{{ $currentQuestion->id }}]"
                                wire:model="answers.{{ $currentQuestion->id }}"
                                value="{{ $opt->text }}"
                                required>
                            {{ $opt->text }}
                        </label>
                    @endforeach
                @endif

                @if ($currentQuestion->type === 'text')
                    <input type="text"
                        wire:model="answers.{{ $currentQuestion->id }}"
                        class="mt-2 block w-full border border-slate-300 rounded px-3 py-2"
                        required>
                @endif
            </div>
        @endif

        <button class="bg-ueap-blue text-white px-6 py-2 rounded">
            {{ $pagina < $totalPages ? 'Próxima pergunta' : 'Finalizar' }}
        </button>
    </form>
</div>
