<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Patients
            'view_any_patient',
            'view_patient',
            'create_patient',
            'update_patient',
            'delete_patient',
            'restore_patient',
            'force_delete_patient',

            // Médecins
            'view_any_medecin',
            'view_medecin',
            'create_medecin',
            'update_medecin',
            'delete_medecin',
            'restore_medecin',
            'force_delete_medecin',

            // Services
            'view_any_service',
            'view_service',
            'create_service',
            'update_service',
            'delete_service',
            'restore_service',
            'force_delete_service',

            // Rendez-vous
            'view_any_rendezvous',
            'view_rendezvous',
            'create_rendezvous',
            'update_rendezvous',
            'delete_rendezvous',
            'restore_rendezvous',
            'force_delete_rendezvous',
            'confirm_rendezvous',
            'cancel_rendezvous',

            // Personnel
            'view_any_personnel',
            'view_personnel',
            'create_personnel',
            'update_personnel',
            'delete_personnel',
            'restore_personnel',
            'force_delete_personnel',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::create(['name' => 'Super Admin']);

        $adminRole->givePermissionTo(Permission::all());

        $medecinRole = Role::create(['name' => 'medecin']);

        $medecinRole->givePermissionTo([
            'view_any_rendezvous',
            'view_rendezvous',
            'update_rendezvous',
            'confirm_rendezvous',
            'cancel_rendezvous',
            'view_any_patient',
            'view_patient',
        ]);

        $personnelRole = Role::create(['name' => 'personnel']);

        $personnelRole->givePermissionTo([
            'view_any_rendezvous',
            'view_rendezvous',
            'create_rendezvous',
            'update_rendezvous',
            'confirm_rendezvous',
            'cancel_rendezvous',
            'view_any_patient',
            'view_patient',
            'create_patient',
            'update_patient',
            'view_any_medecin',
            'view_medecin',
        ]);

        $patientRole = Role::create(['name' => 'patient']);

        $patientRole->givePermissionTo([
            'view_rendezvous',
            'create_rendezvous',
            'update_rendezvous',
            'cancel_rendezvous',
        ]);
    }
}
