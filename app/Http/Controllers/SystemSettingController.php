<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\SchoolIdentity;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SystemSettingController extends Controller
{
    public function auditLogs(Request $request): View
    {
        $query = AuditLog::with('user');

        if ($module = $request->query('module')) {
            $query->where('module', $module);
        }

        if ($action = $request->query('action')) {
            $query->where('action', $action);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%");
            });
        }

        $logs = $query->latest('id')->paginate(20)->withQueryString();
        $modules = AuditLog::select('module')->distinct()->pluck('module');

        return view('settings.audit_logs', compact('logs', 'modules'));
    }

    public function backupIndex(): View
    {
        $backupDir = storage_path('app/backups');
        if (! File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $files = File::files($backupDir);
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'filename' => $file->getFilename(),
                'size' => round($file->getSize() / 1024, 2).' KB',
                'created_at' => date('d/m/Y H:i:s', $file->getMTime()),
            ];
        }

        usort($backups, fn ($a, $b) => strcmp($b['created_at'], $a['created_at']));

        return view('settings.backup', compact('backups'));
    }

    public function createBackup(): RedirectResponse
    {
        $backupDir = storage_path('app/backups');
        if (! File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = date('Y-m-d_His');
        $filename = "sikatas_backup_{$timestamp}.sqlite";
        $targetPath = $backupDir.'/'.$filename;

        // Backup SQLite DB or database dump
        $sourceDb = database_path('database.sqlite');
        if (File::exists($sourceDb)) {
            File::copy($sourceDb, $targetPath);
        } else {
            // Write JSON snapshot of critical master tables
            $snapshot = [
                'school' => SchoolIdentity::current()->toArray(),
                'timestamp' => now()->toIso8601String(),
                'version' => 'SIKATAS SMA1LE v1.0',
            ];
            File::put($backupDir."/sikatas_snapshot_{$timestamp}.json", json_encode($snapshot, JSON_PRETTY_PRINT));
        }

        AuditTrailService::log('backup', 'system', "Membuat cadangan basis data sistem: {$filename}");

        return back()->with('success', "Cadangan data berhasil dibuat: {$filename}");
    }

    public function downloadBackup(string $filename): BinaryFileResponse|RedirectResponse
    {
        $path = storage_path('app/backups/'.basename($filename));
        if (! File::exists($path)) {
            return back()->with('error', 'File cadangan tidak ditemukan.');
        }

        AuditTrailService::log('download', 'system', "Mengunduh file cadangan data: {$filename}");

        return response()->download($path);
    }

    public function destroyBackup(string $filename): RedirectResponse
    {
        $path = storage_path('app/backups/'.basename($filename));
        if (File::exists($path)) {
            File::delete($path);
            AuditTrailService::log('delete', 'system', "Menghapus file cadangan data: {$filename}");

            return back()->with('success', 'File cadangan berhasil dihapus.');
        }

        return back()->with('error', 'File cadangan tidak ditemukan.');
    }
}
