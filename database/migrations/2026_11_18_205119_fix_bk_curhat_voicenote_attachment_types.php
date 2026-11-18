<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('bk_consultation_messages')) {
            DB::table('bk_consultation_messages')
                ->where('attachment_name', 'like', 'voicenote_%')
                ->update([
                    'attachment_type' => 'AUDIO',
                    'attachment_mime' => 'audio/webm',
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reversal needed
    }
};
