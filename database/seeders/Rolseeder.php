<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class Rolseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Este seeder crea y asigna todos los roles y permisos de la aplicación.
     */
    public function run(): void
    {
        // Limpiar caché de permisos para evitar errores
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. DEFINICIÓN DE ROLES PRINCIPALES
        $superAdminRole = Role::firstOrCreate(['name' => 'superadmin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']); // Delegados de Puntos GOB
        $userRole = Role::firstOrCreate(['name' => 'usuario']); // Cliente final

        // Lista de recursos principales que necesitan CRUD
        $resources = ['ubicaciones', 'users', 'roles', 'permissions', 'pgobs', 'soportes', 'appointments', 'testimonios'];

        // 2. CREACIÓN DE PERMISOS
        $permissions = [];
        foreach ($resources as $resource) {
            $permissions[] = Permission::firstOrCreate(['name' => 'create ' . $resource]);
            $permissions[] = Permission::firstOrCreate(['name' => 'read ' . $resource]);
            $permissions[] = Permission::firstOrCreate(['name' => 'update ' . $resource]);
            $permissions[] = Permission::firstOrCreate(['name' => 'delete ' . $resource]);
        }

        // Permiso especial de control total
        $manageAll = Permission::firstOrCreate(['name' => 'manage all']);

        // Asignar los permisos creados (en lote)
        // No es necesario asignarlos en lote si usamos firstOrCreate, pero sirve como referencia:
        // $permissions = array_map(fn($p) => $p->name, $permissions);


        // 3. ASIGNACIÓN DE PERMISOS POR ROL

        // A. SUPER ADMIN (Control Total)
        // Le damos el permiso maestro 'manage all' y todos los permisos de recursos.
        $superAdminRole->givePermissionTo($manageAll);
        $superAdminRole->givePermissionTo(Permission::all());

        // B. ADMIN (Delegados de Puntos GOB - Gestión Operacional)
        // Solo necesita ver y actualizar soportes y citas para su PGOBy gestion.
        $adminRole->givePermissionTo([
            'read soportes',
            'update soportes',
            'read appointments',
            'update appointments',
        ]);

        // C. USUARIO (Cliente Final - Auto-Servicio)
        // Necesitan permisos para interactuar con la plataforma y crear sus propios recursos.
        $userRole->givePermissionTo([
            'create soportes',      // Abrir un ticket de soporte
            'create appointments',  // Agendar una cita
            'read appointments',    // Ver sus citas
            'update appointments',  // Cambiar/Cancelar sus citas
            'create testimonios',   // Dejar un testimonio
        ]);
    }
}
