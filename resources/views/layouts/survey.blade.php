<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Avaliação Institucional UEAP' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body
    class="bg-ueap-bg text-ueap-text min-h-screen transition-all duration-700"
>



<div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-3xl bg-white rounded-lg shadow-sm border border-slate-200">

        <div class="bg-ueap-blue text-white px-6 py-4 rounded-t-lg">
            <h1 class="text-lg font-semibold">
                Avaliação Institucional – UEAP
            </h1>
        </div>

        <div class="p-6">
            @yield('content')
        </div>

    </div>
</div>

@livewireScripts


<script>
    document.addEventListener('livewire:init', () => {

        Livewire.on('updateBackground', (data) => {

            if (!data.pattern) {
                // Volta para fundo padrão (tábua)
                document.body.classList.add('bg-ueap-bg');
                document.body.style.backgroundImage = '';
                document.body.style.backgroundRepeat = '';
                document.body.style.backgroundSize = '';
                document.body.style.backgroundAttachment = '';
                return;
            }

            // Remove fundo padrão
            document.body.classList.remove('bg-ueap-bg');

            // Aplica fundo dinâmico
            document.body.style.backgroundImage = data.pattern;
            document.body.style.backgroundRepeat = 'repeat';
            document.body.style.backgroundSize = '120px 120px';
            document.body.style.backgroundAttachment = 'fixed';
        });

    });
</script>



</body>
</html>
