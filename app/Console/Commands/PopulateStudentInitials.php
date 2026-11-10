<?php

namespace App\Console\Commands;

use App\Models\Student;
use Illuminate\Console\Command;

class PopulateStudentInitials extends Command
{
    protected $signature = 'students:populate-initials';

    protected $description = 'Populate initials column for all students';

    public function handle(): int
    {
        $this->info('Populating initials for all students...');
        $count = 0;

        foreach (Student::cursor() as $student) {
            $words = explode(' ', trim($student->name));
            $initials = collect($words)->map(fn ($w) => strtoupper(substr($w, 0, 1)))->join('');
            $student->initials = substr($initials, 0, 10);
            $student->saveQuietly();
            $count++;
        }

        $this->info("Successfully populated initials for {$count} students.");

        return Command::SUCCESS;
    }
}
