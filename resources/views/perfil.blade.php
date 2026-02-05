<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Perfil do Respondente</title>
</head>
<body>

<h1>Perfil do Respondente</h1>

<p>
As informações abaixo servem apenas para fins estatísticos.
Nenhuma resposta será associada à sua identidade.
</p>

<form method="POST" action="{{ route('perfil') }}">
    @csrf

    <label>
        Você participa da UEAP como:
        <br>
        <select name="perfil" required>
            <option value="">Selecione</option>
            <option value="discente">Discente</option>
            <option value="docente">Docente</option>
            <option value="tecnico">Técnico-administrativo</option>
            <option value="egresso">Egresso</option>
            <option value="comunidade">Comunidade externa</option>
        </select>
    </label>

    <br><br>

    <button type="submit">Continuar</button>
</form>

</body>
</html>
