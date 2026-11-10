<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

#[Signature('import:old-data')]
#[Description('Import data siswa, guru, rombel, poin prestasi, dan poin pelanggaran dari sissma_old')]
class ImportOldData extends Command
{
    public function handle()
    {
        // Setup koneksi sissma_old
        Config::set('database.connections.mysql_old', [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'sissma_old',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ]);

        $this->info('Koneksi sissma_old berhasil dibuat sementara.');

        // Nonaktifkan foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Users
        $this->info('Mengimpor Users...');
        $oldUsers = DB::connection('mysql_old')->table('users')->get();
        foreach ($oldUsers as $old) {
            $user = User::find($old->id);
            if (! $user) {
                // If user doesn't exist, create it. Note we map role array
                $roles = [$old->role];
                DB::table('users')->insert([
                    'id' => $old->id,
                    'name' => $old->name,
                    'email' => $old->email,
                    'username' => $old->username,
                    'phone' => $old->phone,
                    'role' => $old->role,
                    'roles' => json_encode($roles),
                    'avatar' => $old->avatar,
                    'is_active' => $old->is_active,
                    'password' => $old->password,
                    'remember_token' => $old->remember_token,
                    'created_at' => $old->created_at,
                    'updated_at' => $old->updated_at,
                ]);
            } else {
                // Ignore or update? Let's just update if necessary
            }
        }

        // 2. Classes
        $this->info('Mengimpor Classes...');
        $oldClasses = DB::connection('mysql_old')->table('school_classes')->get();
        foreach ($oldClasses as $old) {
            DB::table('school_classes')->updateOrInsert(['id' => $old->id], (array) $old);
        }

        // 3. Teachers
        $this->info('Mengimpor Teachers...');
        $oldTeachers = DB::connection('mysql_old')->table('teachers')->get();
        foreach ($oldTeachers as $old) {
            DB::table('teachers')->updateOrInsert(['id' => $old->id], (array) $old);
        }

        // 4. Students
        $this->info('Mengimpor Students...');
        $oldStudents = DB::connection('mysql_old')->table('students')->get();
        foreach ($oldStudents as $old) {
            DB::table('students')->updateOrInsert(['id' => $old->id], (array) $old);
        }

        // 5. Achievement Categories & Items
        $this->info('Mengimpor Achievement Categories...');
        $oldAchCat = DB::connection('mysql_old')->table('achievement_categories')->get();
        foreach ($oldAchCat as $old) {
            DB::table('achievement_categories')->updateOrInsert(['id' => $old->id], (array) $old);
        }

        $this->info('Mengimpor Achievement Items...');
        $oldAchItem = DB::connection('mysql_old')->table('achievement_items')->get();
        foreach ($oldAchItem as $old) {
            DB::table('achievement_items')->updateOrInsert(['id' => $old->id], (array) $old);
        }

        // 6. Achievements
        $this->info('Mengimpor Achievements...');
        $oldAchievements = DB::connection('mysql_old')->table('achievements')->get();
        foreach ($oldAchievements as $old) {
            DB::table('achievements')->updateOrInsert(['id' => $old->id], (array) $old);
        }

        // 7. Violation Categories & Items
        $this->info('Mengimpor Violation Categories...');
        $oldViolCat = DB::connection('mysql_old')->table('violation_categories')->get();
        foreach ($oldViolCat as $old) {
            DB::table('violation_categories')->updateOrInsert(['id' => $old->id], (array) $old);
        }

        $this->info('Mengimpor Violation Items...');
        $oldViolItem = DB::connection('mysql_old')->table('violation_items')->get();
        foreach ($oldViolItem as $old) {
            DB::table('violation_items')->updateOrInsert(['id' => $old->id], (array) $old);
        }

        // 8. Violations
        $this->info('Mengimpor Violations...');
        $oldViolations = DB::connection('mysql_old')->table('violations')->get();
        foreach ($oldViolations as $old) {
            DB::table('violations')->updateOrInsert(['id' => $old->id], (array) $old);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->info('Import Selesai!');
    }
}
