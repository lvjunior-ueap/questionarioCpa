<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerfilAudiencesTest extends TestCase
{
    use RefreshDatabase;

    public function test_perfil_exibe_novos_publicos_na_dropdown(): void
    {
        $response = $this->get('/perfil');

        $response->assertOk();
        $response->assertSee('Funcionário da Gestão da UEAP (atual ou ex-gestão)');
        $response->assertSee('Reitor, Diretor ou Pró-Reitor');
        $response->assertSee('Funcionário Transposto');
    }
}
