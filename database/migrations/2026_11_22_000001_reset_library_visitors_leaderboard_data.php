<?php

use App\Models\LibraryVisitor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('library_visitors')) {
            Schema::disableForeignKeyConstraints();
            LibraryVisitor::truncate();
            Schema::enableForeignKeyConstraints();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed for reset operation
    }
};
