<?php

namespace App\Policies;

use App\Models\LessonPlan;
use App\Models\User;

class LessonPlanPolicy
{
    /**
     * Izinkan melihat daftar modul ajar.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'wakasek_kurikulum', 'guru', 'kepala_sekolah']);
    }

    /**
     * Izinkan melihat detail modul ajar tertentu (IDOR Prevention).
     */
    public function view(User $user, LessonPlan $lessonPlan): bool
    {
        if ($user->hasAnyRole(['super_admin', 'admin', 'wakasek_kurikulum', 'kepala_sekolah'])) {
            return true;
        }

        // Guru hanya boleh melihat modul ajar miliknya sendiri atau jika satu tim mapel
        if ($user->teacher && $lessonPlan->teacher_id === $user->teacher->id) {
            return true;
        }

        // Siswa hanya boleh melihat jika modul ajar sudah disetujui & dipublikasikan untuk kelasnya
        if ($user->isStudent() && $user->student) {
            return $lessonPlan->is_published
                && $lessonPlan->courseClass
                && $lessonPlan->courseClass->class_id === $user->student->class_id;
        }

        return false;
    }

    /**
     * Izinkan membuat modul ajar baru.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'wakasek_kurikulum', 'guru']);
    }

    /**
     * Izinkan memperbarui modul ajar.
     */
    public function update(User $user, LessonPlan $lessonPlan): bool
    {
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return true;
        }

        if ($user->teacher && $lessonPlan->teacher_id === $user->teacher->id) {
            // Guru dapat mengubah selama status bukan APPROVED terkunci
            return in_array($lessonPlan->status, ['DRAFT', 'REVISED', 'SUBMITTED'], true);
        }

        return false;
    }

    /**
     * Izinkan menghapus modul ajar.
     */
    public function delete(User $user, LessonPlan $lessonPlan): bool
    {
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return true;
        }

        if ($user->teacher && $lessonPlan->teacher_id === $user->teacher->id) {
            return $lessonPlan->status === 'DRAFT';
        }

        return false;
    }

    /**
     * Otorisasi khusus Wakasek Kurikulum / Admin untuk meninjau dan menyetujui modul ajar.
     */
    public function review(User $user, LessonPlan $lessonPlan): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'wakasek_kurikulum']);
    }
}
