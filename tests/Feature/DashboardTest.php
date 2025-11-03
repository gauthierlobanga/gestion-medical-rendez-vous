<?php

use App\Models\User;
use App\Models\Medecin;

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();

    $medecin = Medecin::factory()->create([
        'user_id' => $user->id,
        'specialite' => 'Cardiologie',
    ]);

    $this->actingAs($user);

    $response = $this->get('/dashboard');

    $response->assertStatus(200);
});
