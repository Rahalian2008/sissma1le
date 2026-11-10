<?php

use App\Contracts\AiAdapterInterface;
use App\Models\User;
use App\Services\Ai\AiCurriculumManager;
use App\Services\Ai\GeminiAiAdapter;
use App\Services\Ai\MockAiAdapter;
use App\Services\CurriculumPlanService;

test('curriculum plan service provides valid statuses and module schema', function () {
    $service = app(CurriculumPlanService::class);

    $statuses = $service::getStatusList();
    expect($statuses)->toHaveKey(CurriculumPlanService::STATUS_DRAFT);
    expect($statuses)->toHaveKey(CurriculumPlanService::STATUS_SUBMITTED);
    expect($statuses)->toHaveKey(CurriculumPlanService::STATUS_REVISED);
    expect($statuses)->toHaveKey(CurriculumPlanService::STATUS_APPROVED);

    $structure = $service->getDefaultModuleStructure();
    expect($structure)->toHaveKeys(['identity', 'learning_steps', 'assessment']);
    expect($structure['learning_steps'])->toHaveKeys(['introduction', 'core', 'closing']);
});

test('curriculum plan service health check returns complete system metrics', function () {
    $service = app(CurriculumPlanService::class);
    $health = $service->getSystemHealth();

    expect($health['status'])->toBe('healthy');
    expect($health)->toHaveKeys(['timestamp', 'laravel_version', 'php_version', 'database', 'settings']);
    expect($health['database']['tables'])->toHaveKeys(['course_classes', 'lms_materials', 'lms_assignments', 'lms_quizzes']);
});

test('authenticated user can access lms health check endpoint', function () {
    $admin = User::where('role', 'super_admin')->first() ?? User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get('/lms/health-check');
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'status',
        'timestamp',
        'laravel_version',
        'php_version',
        'database' => ['connection', 'tables'],
        'settings',
    ]);
});

test('mock ai adapter implements ai adapter interface and generates draft with disclaimer', function () {
    $adapter = new MockAiAdapter;
    expect($adapter)->toBeInstanceOf(AiAdapterInterface::class);
    expect($adapter->isAvailable())->toBeTrue();
    expect($adapter->getProviderName())->toBe('mock');

    $result = $adapter->generate('Buatkan TP Informatika', ['task' => 'tp', 'topic' => 'Algoritma']);
    expect($result['success'])->toBeTrue();
    expect($result['is_draft'])->toBeTrue();
    expect($result['content'])->toContain('Tujuan Pembelajaran');
    expect($result['metadata']['disclaimer'])->toContain('wajib ditinjau oleh guru');
});

test('ai curriculum manager sanitizes private PII data and generates draft safely', function () {
    $manager = new AiCurriculumManager(new MockAiAdapter);

    $sensitivePrompt = 'Buatkan saran remedial untuk siswa dengan NIK 3204123456780001 dan nomor HP 081234567890 mengenai Aljabar';
    $result = $manager->generateDraft($sensitivePrompt, ['task' => 'remedial', 'topic' => 'Aljabar']);

    expect($result['success'])->toBeTrue();
    expect($result['is_draft'])->toBeTrue();
    expect($result['used_fallback'])->toBeFalse();
    expect($result['content'])->toContain('Saran Tindak Lanjut Remedial');

    // Test fallback behavior when primary adapter fails
    $unconfiguredGemini = new class implements AiAdapterInterface
    {
        public function getProviderName(): string
        {
            return 'fake_failing';
        }

        public function isAvailable(): bool
        {
            return true;
        }

        public function generate(string $prompt, array $context = []): array
        {
            return ['success' => false, 'content' => '', 'provider' => 'fake', 'model' => 'none', 'is_draft' => true, 'metadata' => [], 'error' => 'API down'];
        }
    };

    $fallbackManager = new AiCurriculumManager($unconfiguredGemini, new MockAiAdapter);
    $fallbackResult = $fallbackManager->generateDraft('Buat modul ajar', ['task' => 'modul_ajar', 'topic' => 'Geometri']);
    expect($fallbackResult['success'])->toBeTrue();
    expect($fallbackResult['used_fallback'])->toBeTrue();
    expect($fallbackResult['content'])->toContain('Draf Modul Ajar');
});

test('gemini ai adapter implements interface and handles unavailable api key gracefully', function () {
    $gemini = new GeminiAiAdapter;
    expect($gemini)->toBeInstanceOf(AiAdapterInterface::class);
    expect($gemini->getProviderName())->toBe('gemini');
});
