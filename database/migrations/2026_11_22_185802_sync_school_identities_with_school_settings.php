<?php

use App\Models\SchoolIdentity;
use App\Models\SchoolSetting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $identity = SchoolIdentity::first();
        if ($identity) {
            $identity->update([
                'name' => SchoolSetting::get('school_name', 'SMA Negeri 1 Lengkong'),
                'npsn' => SchoolSetting::get('school_npsn', '20202265'),
                'nss' => SchoolSetting::get('school_nss', '301020613045'),
                'accreditation' => SchoolSetting::get('school_accreditation', 'A (Unggul)'),
                'address' => SchoolSetting::get('school_address', 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Provinsi Jawa Barat'),
                'phone' => SchoolSetting::get('school_phone', '-'),
                'email' => SchoolSetting::get('school_email', 'sman1lengkong@gmail.com'),
                'website' => SchoolSetting::get('school_website', 'https://sman1lengkong.school'),
                'headmaster_name' => SchoolSetting::get('headmaster_name', 'Pian Sopian, S.Pd., M.M.Pd'),
                'headmaster_nip' => SchoolSetting::get('headmaster_nip', '198009132008011005'),
                'operator_name' => 'Rachmat Sugiarto, A.Md.',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
