
<?php

// Fichier : tests/Pest.php

use Spatie\Permission\Models\Role;

uses(Tests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Test Case Setup
|--------------------------------------------------------------------------
| La fonction beforeEach() s'exécute avant chaque test.
| Nous l'utilisons pour nous assurer que le rôle 'patient' existe.
*/
beforeEach(function () {
    if (app()->environment('testing')) {
        // 1. Créer le rôle 'patient' s'il n'existe pas.
        Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);

        // 2. Si vous utilisez un rôle 'medecin' dans vos tests (comme le suggère le log), créez-le aussi.
        // Role::firstOrCreate(['name' => 'medecin', 'guard_name' => 'web']);

        // 3. Vider le cache de Spatie pour garantir que le nouveau rôle est disponible
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
});

/* ... Le reste de votre fichier Pest.php ... */
