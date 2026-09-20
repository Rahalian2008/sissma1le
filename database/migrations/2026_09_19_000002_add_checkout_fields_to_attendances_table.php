<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->time('time_out')->nullable()->after('time');
            $table->string('out_method', 30)->nullable()->after('method');
            $table->string('out_selfie_path')->nullable()->after('selfie_path');
            $table->decimal('out_latitude', 10, 8)->nullable()->after('longitude');
            $table->decimal('out_longitude', 11, 8)->nullable()->after('out_latitude');
            $table->string('out_device_info')->nullable()->after('device_info');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'time_out',
                'out_method',
                'out_selfie_path',
                'out_latitude',
                'out_longitude',
                'out_device_info',
            ]);
        });
    }
};
