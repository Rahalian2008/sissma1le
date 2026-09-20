<?php

namespace App\Providers;

use App\Models\SchoolSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            try {
                if (Schema::hasTable('school_settings')) {
                    $settings = SchoolSetting::whereIn('key', [
                        'school_logo',
                        'school_kop',
                        'school_province',
                        'school_name',
                        'app_name',
                        'app_tagline',
                        'school_branch',
                        'school_address',
                        'school_email',
                        'school_website',
                        'school_city',
                        'school_npsn',
                        'school_nss',
                        'school_accreditation',
                        'school_education_form',
                    ])->pluck('value', 'key');

                    $logo = $settings->get('school_logo');
                    $kop = $settings->get('school_kop');
                    $province = $settings->get('school_province', 'JAWA BARAT');
                    $name = $settings->get('school_name', 'SMA Negeri 1 Lengkong');
                    $appName = $settings->get('app_name', 'SISWA SMA1LE');
                    $appTagline = $settings->get('app_tagline', 'Sistem Informasi Siswa Terpadu');
                    $branch = $settings->get('school_branch', 'CABANG DINAS PENDIDIKAN WILAYAH V');
                    $address = $settings->get('school_address', 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Provinsi Jawa Barat');
                    $email = $settings->get('school_email', 'sman1lengkong@gmail.com');
                    $website = $settings->get('school_website', 'https://sman1lengkong.sch.id');
                    $city = $settings->get('school_city', 'Sukabumi');
                    $npsn = $settings->get('school_npsn', '20539123');
                    $nss = $settings->get('school_nss', '301051408001');
                    $accreditation = $settings->get('school_accreditation', 'A (Unggul)');
                    $eduForm = $settings->get('school_education_form', 'Sekolah Menengah Atas (SMA)');

                    $view->with([
                        'appLogoUrl' => $logo && (Storage::disk('public')->exists($logo) || file_exists(public_path('storage/'.$logo)))
                            ? Storage::disk('public')->url($logo)
                            : null,
                        'kopImageUrl' => $kop && (Storage::disk('public')->exists($kop) || file_exists(public_path('storage/'.$kop)))
                            ? Storage::disk('public')->url($kop)
                            : null,
                        'schoolProvince' => $province ?: 'JAWA BARAT',
                        'schoolName' => $name ?: 'SMA Negeri 1 Lengkong',
                        'appName' => $appName ?: 'SISWA SMA1LE',
                        'appTagline' => $appTagline ?: 'Sistem Informasi Siswa Terpadu',
                        'schoolBranch' => $branch ?: 'CABANG DINAS PENDIDIKAN WILAYAH V',
                        'schoolAddress' => $address ?: 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Provinsi Jawa Barat',
                        'schoolEmail' => $email ?: 'sman1lengkong@gmail.com',
                        'schoolWebsite' => $website ?: 'https://sman1lengkong.sch.id',
                        'schoolCity' => $city ?: 'Sukabumi',
                        'schoolNpsn' => $npsn ?: '20539123',
                        'schoolNss' => $nss ?: '301051408001',
                        'schoolAccreditation' => $accreditation ?: 'A (Unggul)',
                        'schoolEducationForm' => $eduForm ?: 'Sekolah Menengah Atas (SMA)',
                    ]);
                }
            } catch (\Throwable) {
                // Ignore any DB connection or schema issues during setup/early bootstrapping
            }
        });
    }
}
