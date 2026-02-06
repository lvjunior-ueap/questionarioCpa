{{-- Tela de Perfil --}}
@if ($step === 'perfil')
    <div class="space-y-6">
        <p class="mb-4 text-ueap-muted">
            As informações abaixo são utilizadas apenas para fins estatísticos.
        </p>

        <div>
            <label class="block mb-2">
                <span class="font-medium">Você participa da UEAP como:</span>
            </label>

            <select wire:model="perfil"
                    class="mt-2 block w-full border border-slate-300 rounded px-3 py-2">
                <option value="">Selecione</option>
                <option value="discente">Discente</option>
                <option value="docente">Docente</option>
                <option value="tecnico">Técnico-administrativo</option>
                <option value="egresso">Egresso</option>
                <option value="comunidade">Comunidade externa</option>
            </select>
            @error('perfil')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <button wire:click="salvarPerfil"
                class="w-full bg-ueap-blue text-white px-6 py-2 rounded hover:bg-ueap-blue/90 font-medium">
            Continuar
        </button>
    </div>
@endif

{{-- Tela de Pesquisa --}}
@if ($step === 'survey')
    <div>
        <p class="text-sm text-ueap-muted mb-2">
            Etapa {{ $currentIndex + 1 }} de {{ $total }}
        </p>

        <h2 class="text-lg font-semibold mb-6">
            {{ $currentDimension }}
        </h2>

        <div class="space-y-4 mb-6">
            @foreach ($questions as $question)
                <div class="border border-slate-200 rounded p-4">
                    <p class="font-medium mb-3">
                        {{ $question['text'] ?? '' }}
                    </p>

                    {{-- Escala --}}
                    @if (($question['type'] ?? '') === 'scale')
                        <div class="space-y-2">
                            @if(isset($question['options']))
                                @foreach ($question['options'] as $opt)
                                    <label class="flex items-center">
                                        <input type="radio"
                                               wire:model="respostas.{{ $question['id'] }}"
                                               value="{{ $opt['text'] ?? '' }}"
                                               class="mr-2">
                                        {{ $opt['text'] ?? '' }}
                                    </label>
                                @endforeach
                            @endif
                        </div>
                    @endif

                    {{-- Múltipla escolha --}}
                    @if (($question['type'] ?? '') === 'multi')
                        <div class="space-y-2">
                            @if(isset($question['options']))
                                @foreach ($question['options'] as $opt)
                                    <label class="flex items-center">
                                        <input type="checkbox"
                                               wire:model="respostas.{{ $question['id'] }}"
                                               value="{{ $opt['text'] ?? '' }}"
                                               class="mr-2">
                                        {{ $opt['text'] ?? '' }}
                                    </label>
                                @endforeach
                            @endif
                        </div>
                    @endif

                    {{-- Texto livre --}}
                    @if (($question['type'] ?? '') === 'text')
                        <textarea wire:model="respostas.{{ $question['id'] }}"
                                  class="w-full border border-slate-300 rounded px-3 py-2"
                                  rows="3"></textarea>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="flex justify-between gap-4">
            @if ($currentIndex > 0)
                <button wire:click="paginaAnterior"
                        class="bg-slate-300 text-slate-800 px-6 py-2 rounded hover:bg-slate-400 font-medium">
                    ← Anterior
                </button>
            @else
                <div></div>
            @endif

            <button wire:click="proximaPagina"
                    type="button"
                    class="bg-ueap-blue text-white px-8 py-2 rounded hover:bg-ueap-blue/90 font-medium transition">
                {{ $currentIndex === $total - 1 ? 'Finalizar' : 'Próximo' }} →
            </button>
        </div>
    </div>
@endif

{{-- Tela de Finalização --}}
@if ($step === 'finalizado')
    <div class="text-center py-8">
        <h2 class="text-2xl font-semibold mb-4 text-ueap-blue">
            Obrigado!
        </h2>
        <p class="text-ueap-muted mb-6">
            Suas respostas foram registradas com sucesso.
        </p>
        <a href="/" class="bg-ueap-blue text-white px-6 py-2 rounded hover:bg-ueap-blue/90 inline-block">
            Voltar ao início
        </a>
    </div>
@endif
