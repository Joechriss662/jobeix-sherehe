<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $organizer = Role::firstOrCreate(['name' => 'organizer']);
        $guest = Role::firstOrCreate(['name' => 'guest']);
        $support = Role::firstOrCreate(['name' => 'support']);

        // Create permissions
        $permissions = [
            'manage users',
            'manage events',
            'manage invitations',
            'view rsvp',
            'rsvp',
            'edit own info',
            'view all events',
            'monitor statuses',
            'assist support',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Assign permissions to roles
        $admin->givePermissionTo(Permission::all());
        $organizer->givePermissionTo(['manage events', 'manage invitations', 'view rsvp']);
        $guest->givePermissionTo(['rsvp', 'edit own info']);
        $support->givePermissionTo(['view all events', 'monitor statuses', 'assist support']);
    }
}