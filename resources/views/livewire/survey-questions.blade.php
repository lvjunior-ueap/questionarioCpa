<div>
    @php($theme = $this->dimensionTheme)

        <div class="w-full max-w-3xl bg-white rounded-lg shadow-sm border border-slate-200 p-6">

            @if ($audienceIntro)
                <p class="mb-4 text-sm text-ueap-muted whitespace-pre-line">
                    {{ $audienceIntro }}
                </p>
            @endif

            @if ($showDimensionIntro)
                <div class="mb-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-ueap-muted mb-3">Nova dimensão</p>
                    <h3 class="text-lg font-semibold mb-2"
                        style="color: {{ $theme['primary'] }}">
                        {{ $dimensionTitle }}
                    </h3>
                    <p class="text-sm text-slate-700 mb-4">
                        {{ $this->dimensionIntroText }}
                    </p>
                    <button
                        type="button"
                        wire:click="continueDimension"
                        class="px-5 py-2 rounded text-white"
                        style="background-color: {{ $theme['primary'] }}"
                    >
                        Ok, continuar
                    </button>
                </div>
    @else
        <p class="mb-6 text-sm text-ueap-muted">
            Pergunta {{ $pagina }} de {{ $totalPages }}
        </p>

        @if ($dimensionTitle)
            <div class="mb-6 rounded-lg border p-4 dimension-header" style="border-color: {{ $theme['soft'] }}; background-color: {{ $theme['soft'] }};">
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
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs text-ueap-muted">
                        Dimensão {{ $this->indiceDimensaoAtual }} de {{ $this->totalDimensoes }}: {{ $this->respondidasDimensaoAtual }} / {{ $this->totalPerguntasDimensaoAtual }}
                    </span>
                    <span class="text-xs text-ueap-muted font-medium">{{ $this->progressoDimensao }}%</span>
                </div>

                <div class="w-full bg-slate-100 rounded-full h-2">
                    <div
                        class="h-2 rounded-full transition-all duration-500"
                        style="width: {{ $this->progressoDimensao }}%; background-color: {{ $theme['primary'] }}"
                        >
                    </div>
                </div>
            </div>
            </div>

            @if ($currentQuestion)
            <div class="dimension-question-shell">
            <div wire:key="question-{{ $currentQuestion->id }}" class="border border-slate-200 rounded p-4 bg-white dimension-question-card">
                <p class="font-medium mb-2">
                    {{ $currentQuestion->text }}
                </p>

                @if ($currentQuestion->type === 'scale')
                    <p class="text-xs text-ueap-muted mb-2">Escala: 1 = muito insatisfeito(a) e 5 = muito satisfeito(a).</p>
                    <div class="flex flex-wrap gap-4">
                        @for ($i = 1; $i <= 5; $i++)
                            <label class="inline-flex items-center gap-2">
                                <input type="radio"
                                    name="answers[{{ $currentQuestion->id }}]"
                                    wire:model="answers.{{ $currentQuestion->id }}"
                                    value="{{ $i }}"
                                    required>
                                <span>{{ $i }}</span>
                            </label>
                        @endfor
                    </div>
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
            </div>
            @endif

            <div class="flex items-center gap-3">
                @if ($this->paginaAnteriorUrl)
                    <a href="{{ $this->paginaAnteriorUrl }}" class="px-6 py-2 rounded border border-slate-300 text-slate-700 hover:bg-slate-50">
                        Voltar
                    </a>
                @endif

                <button class="bg-ueap-blue text-white px-6 py-2 rounded">
                    {{ $pagina < $totalPages ? 'Próxima pergunta' : 'Finalizar' }}
                </button>
            </div>

            <div class="sticky bottom-0 z-50 bg-white pt-4 mt-6 border-t border-slate-200">
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
        </form>
    @endif
</div> {{-- fecha card branco --}}
</div> {{-- fecha raiz Livewire --}}
