<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run()
    {
        update_static_option('site_script_version','1.2.6');
        $this->call(MediFundDemoSeeder::class);
        $this->call(RolePermissionSeeder::class);
        $this->command->info('Assigning Super Admin role to medifund_admin...');
        $admin = \DB::table('admins')->where('username','medifund_admin')->first();
        if ($admin) {
            $role = \Spatie\Permission\Models\Role::where('name','Super Admin')->where('guard_name','admin')->first();
            if ($role) {
                \DB::table('model_has_roles')->updateOrInsert(
                    ['model_id' => $admin->id, 'model_type' => 'App\Admin'],
                    ['role_id' => $role->id]
                );
            }
        }
    }
}
