<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_la_raiz_redirige_al_dashboard(): void
    {
        $this->get('/')->assertRedirect(route('dashboard'));
    }

    public function test_el_login_se_muestra(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_el_dashboard_requiere_autenticacion(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }
}
