<?php

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('picket_schedules')) {
            Schema::create('picket_schedules', function (Blueprint $table) {
                $table->id();
                $table->string('day_name', 20); // Senin, Selasa, Rabu, Kamis, Jumat, Sabtu
                $table->unsignedTinyInteger('day_number')->default(1); // 1 = Senin ... 6 = Sabtu
                $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('person_name', 150);
                $table->string('role_type', 50)->default('GURU_PIKET'); // GURU_PIKET, TU_PIKET, KOORDINATOR_PIKET
                $table->string('location', 150)->default('Pos Piket Gerbang & Lobi Utama');
                $table->time('start_time')->default('06:30:00');
                $table->time('end_time')->default('15:30:00');
                $table->text('notes')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            // Seed default initial picket roster for Monday - Friday
            $days = [
                1 => 'Senin',
                2 => 'Selasa',
                3 => 'Rabu',
                4 => 'Kamis',
                5 => 'Jumat',
                6 => 'Sabtu',
            ];

            // Attempt to seed from existing teachers & staff
            $teachers = Teacher::where('is_active', true)->take(12)->get();
            $staffUsers = User::whereIn('role', ['staff', 'tu', 'tu_piket', 'admin'])->take(6)->get();

            $teacherIndex = 0;
            $staffIndex = 0;

            foreach ($days as $dayNum => $dayName) {
                // 1. Guru Piket
                $t = $teachers[$teacherIndex % max(1, $teachers->count())] ?? null;
                $tName = $t ? $t->name : "Guru Piket {$dayName}";
                $tId = $t ? $t->id : null;
                $tUserId = $t ? $t->user_id : null;
                $teacherIndex++;

                DB::table('picket_schedules')->insert([
                    'day_name' => $dayName,
                    'day_number' => $dayNum,
                    'teacher_id' => $tId,
                    'user_id' => $tUserId,
                    'person_name' => $tName,
                    'role_type' => 'GURU_PIKET',
                    'location' => 'Pos Piket Gerbang Utama & Ketertiban',
                    'start_time' => '06:30:00',
                    'end_time' => '15:30:00',
                    'notes' => 'Pemeriksaan atribut seragam, kedisiplinan gerbang, dan persetujuan izin siswa',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 2. TU Piket
                $s = $staffUsers[$staffIndex % max(1, $staffUsers->count())] ?? null;
                $sName = $s ? $s->name : "Staf TU Piket {$dayName}";
                $sUserId = $s ? $s->id : null;
                $staffIndex++;

                DB::table('picket_schedules')->insert([
                    'day_name' => $dayName,
                    'day_number' => $dayNum,
                    'teacher_id' => null,
                    'user_id' => $sUserId,
                    'person_name' => $sName,
                    'role_type' => 'TU_PIKET',
                    'location' => 'Lobi Layanan Administrasi & Tamu',
                    'start_time' => '07:00:00',
                    'end_time' => '15:30:00',
                    'notes' => 'Buku tamu, persuratan masuk, dan pencatatan siswa izin/pulang',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('picket_schedules');
    }
};
