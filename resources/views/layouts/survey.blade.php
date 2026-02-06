<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Avaliação Institucional UEAP' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-ueap-bg text-ueap-text min-h-screen">

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
</body>
</html></body>
</html>
