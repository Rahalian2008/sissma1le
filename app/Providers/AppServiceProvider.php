<?php

namespace App\Providers;

use App\Models\SchoolIdentity;
use App\Models\SchoolSetting;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
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
        // Set default Carbon locale to Indonesian & enforce Asia/Jakarta timezone
        Carbon::setLocale('id');
        date_default_timezone_set(config('app.timezone', 'Asia/Jakarta'));

        // Standardize string length for MySQL utf8mb4 indexes on shared hostings (Hostinger)
        Schema::defaultStringLength(191);

        // Force HTTPS in production or when running behind Hostinger SSL/Cloudflare
        $isSecure = (! $this->app->runningInConsole() && request()->isSecure());
        if ($isSecure || $this->app->environment('production') || str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Automatic directory initialization for public storage on shared hostings
        try {
            if (! is_link(public_path('storage')) && ! file_exists(public_path('storage/branding'))) {
                @mkdir(public_path('storage/attendance'), 0755, true);
                @mkdir(public_path('storage/selfies'), 0755, true);
                @mkdir(public_path('storage/branding'), 0755, true);
                @mkdir(public_path('storage/avatars'), 0755, true);
            }
        } catch (\Throwable) {
            // Ignore any filesystem initialization exceptions
        }

        // Auto-heal missing LMS columns on shared hosting (e.g. Hostinger)
        if (! Cache::has('lms_schema_checked_20261111')) {
            try {
                if (Schema::hasTable('lms_materials') && ! Schema::hasColumn('lms_materials', 'learning_objective_id')) {
                    Schema::table('lms_materials', function (Blueprint $table) {
                        $table->unsignedBigInteger('learning_objective_id')->nullable()->after('course_class_id');
                    });
                }
                if (Schema::hasTable('lms_assignments') && ! Schema::hasColumn('lms_assignments', 'learning_objective_id')) {
                    Schema::table('lms_assignments', function (Blueprint $table) {
                        $table->unsignedBigInteger('learning_objective_id')->nullable()->after('course_class_id');
                    });
                }
                if (Schema::hasTable('lms_quizzes') && ! Schema::hasColumn('lms_quizzes', 'learning_objective_id')) {
                    Schema::table('lms_quizzes', function (Blueprint $table) {
                        $table->unsignedBigInteger('learning_objective_id')->nullable()->after('course_class_id');
                    });
                }
                if (Schema::hasTable('lms_quiz_questions') && ! Schema::hasColumn('lms_quiz_questions', 'learning_objective_id')) {
                    Schema::table('lms_quiz_questions', function (Blueprint $table) {
                        $table->unsignedBigInteger('learning_objective_id')->nullable()->after('quiz_id');
                    });
                }
                Cache::forever('lms_schema_checked_20261111', true);
            } catch (\Throwable) {
                // Silently ignore if already altered
            }
        }

        View::composer('*', function ($view) {
            try {
                $identity = Schema::hasTable('school_identities') ? SchoolIdentity::first() : null;
                $settings = Schema::hasTable('school_settings')
                    ? SchoolSetting::whereIn('key', [
                        'school_logo',
                        'school_kop',
                        'kop_surat_image',
                        'kop_image',
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
                    ])->pluck('value', 'key')
                    : collect();

                $logo = $settings->get('school_logo') ?: $identity?->logo_path;
                $resolvedLogoUrl = null;
                if ($logo && (Storage::disk('public')->exists($logo) || file_exists(public_path('storage/'.ltrim($logo, '/'))))) {
                    $resolvedLogoUrl = asset('storage/'.ltrim($logo, '/'));
                } elseif ($identity?->logo_url) {
                    $resolvedLogoUrl = $identity->logo_url;
                } elseif (file_exists(public_path('images/logo.png'))) {
                    $resolvedLogoUrl = asset('images/logo.png');
                }

                $kop = $settings->get('school_kop') ?: ($settings->get('kop_surat_image') ?: ($settings->get('kop_image') ?: $identity?->header_image_path));
                $province = $settings->get('school_province') ?: ($identity?->province ?: 'JAWA BARAT');
                $name = $settings->get('school_name') ?: ($identity?->name ?: 'SMA Negeri 1 Lengkong');
                $appName = $settings->get('app_name', 'SISWA SMA1LE');
                $appTagline = $settings->get('app_tagline', 'Sistem Informasi Siswa Terpadu');
                $branch = $settings->get('school_branch') ?: ($identity?->branch_office ?: 'CABANG DINAS PENDIDIKAN WILAYAH V');
                $address = $settings->get('school_address') ?: ($identity?->address ?: 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Provinsi Jawa Barat');
                $email = $settings->get('school_email') ?: ($identity?->email ?: 'sman1lengkong@gmail.com');
                $website = $settings->get('school_website') ?: ($identity?->website ?: 'https://sman1lengkong.school');
                $city = $settings->get('school_city') ?: ($identity?->city ?: 'Sukabumi');
                $npsn = $settings->get('school_npsn') ?: ($identity?->npsn ?: '20202265');
                $nss = $settings->get('school_nss') ?: ($identity?->nss ?: '301051408001');
                $accreditation = $settings->get('school_accreditation') ?: ($identity?->accreditation ?: 'A (Unggul)');
                $eduForm = $settings->get('school_education_form', 'Sekolah Menengah Atas (SMA)');

                $view->with([
                    'appLogoUrl' => $resolvedLogoUrl,
                    'schoolLogo' => $resolvedLogoUrl,
                    'schoolLogoUrl' => $resolvedLogoUrl,
                    'schoolFaviconUrl' => $resolvedLogoUrl ?: asset('images/logo.png'),
                    'kopImageUrl' => $kop && (Storage::disk('public')->exists($kop) || file_exists(public_path('storage/'.$kop)))
                        ? asset('storage/'.$kop)
                        : null,
                    'kopDarkModeUrl' => file_exists(public_path('storage/branding/kop_dark_compact.png'))
                        ? asset('storage/branding/kop_dark_compact.png')
                        : (file_exists(public_path('storage/branding/kop_dark_mode.png'))
                            ? asset('storage/branding/kop_dark_mode.png')
                            : null),
                    'schoolProvince' => $province ?: 'JAWA BARAT',
                    'schoolName' => $name ?: 'SMA Negeri 1 Lengkong',
                    'appName' => $appName ?: 'SISWA SMA1LE',
                    'appTagline' => $appTagline ?: 'Sistem Informasi Siswa Terpadu',
                    'schoolBranch' => $branch ?: 'CABANG DINAS PENDIDIKAN WILAYAH V',
                    'schoolAddress' => $address ?: 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Provinsi Jawa Barat',
                    'schoolEmail' => $email ?: 'sman1lengkong@gmail.com',
                    'schoolWebsite' => $website ?: 'https://sman1lengkong.school',
                    'schoolCity' => $city ?: 'Sukabumi',
                    'schoolNpsn' => $npsn ?: '20202265',
                    'schoolNss' => $nss ?: '301051408001',
                    'schoolAccreditation' => $accreditation ?: 'A (Unggul)',
                    'schoolEducationForm' => $eduForm ?: 'Sekolah Menengah Atas (SMA)',
                ]);
            } catch (\Throwable) {
                // Ignore any DB connection or schema issues during setup/early bootstrapping
            }
        });
    }
}
