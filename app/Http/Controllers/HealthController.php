<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class HealthController extends Controller
{
    /**
     * Perform comprehensive health check
     */
    public function check(): JsonResponse
    {
        $startTime = microtime(true);
        $checks = [];
        $overallStatus = 'healthy';
        $httpStatus = 200;

        // Database connectivity check
        $checks['database'] = $this->checkDatabase();

        // Cache system check
        $checks['cache'] = $this->checkCache();

        // Storage system check
        $checks['storage'] = $this->checkStorage();

        // Application status check
        $checks['application'] = $this->checkApplication();

        // Determine overall status
        foreach ($checks as $check) {
            if ($check['status'] === 'unhealthy') {
                $overallStatus = 'unhealthy';
                $httpStatus = 503;
                break;
            } elseif ($check['status'] === 'degraded' && $overallStatus === 'healthy') {
                $overallStatus = 'degraded';
                $httpStatus = 200; // Still operational but with issues
            }
        }

        $responseTime = round((microtime(true) - $startTime) * 1000, 2);

        $response = [
            'status' => $overallStatus,
            'timestamp' => now()->toISOString(),
            'response_time_ms' => $responseTime,
            'version' => config('app.version', '1.0.0'),
            'environment' => config('app.env'),
            'checks' => $checks,
        ];

        return response()->json($response, $httpStatus);
    }

    /**
     * Check database connectivity
     */
    private function checkDatabase(): array
    {
        try {
            $startTime = microtime(true);
            DB::connection()->getPdo();

            // Test a simple query
            DB::select('SELECT 1');

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return [
                'status' => 'healthy',
                'message' => 'Database connection successful',
                'response_time_ms' => $responseTime,
                'connection' => config('database.default'),
            ];
        } catch (Exception $e) {
            Log::error('Health check database failure: '.$e->getMessage());

            return [
                'status' => 'unhealthy',
                'message' => 'Database connection failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Connection error',
            ];
        }
    }

    /**
     * Check cache system
     */
    private function checkCache(): array
    {
        try {
            $startTime = microtime(true);
            $testKey = 'health_check_'.time();
            $testValue = 'test_value';

            // Test cache write
            Cache::put($testKey, $testValue, 60);

            // Test cache read
            $retrievedValue = Cache::get($testKey);

            // Clean up
            Cache::forget($testKey);

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            if ($retrievedValue === $testValue) {
                return [
                    'status' => 'healthy',
                    'message' => 'Cache system operational',
                    'response_time_ms' => $responseTime,
                    'driver' => config('cache.default'),
                ];
            } else {
                return [
                    'status' => 'degraded',
                    'message' => 'Cache read/write mismatch',
                    'driver' => config('cache.default'),
                ];
            }
        } catch (Exception $e) {
            Log::error('Health check cache failure: '.$e->getMessage());

            return [
                'status' => 'unhealthy',
                'message' => 'Cache system failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Cache error',
            ];
        }
    }

    /**
     * Check storage system
     */
    private function checkStorage(): array
    {
        try {
            $startTime = microtime(true);
            $testFile = 'health_check_'.time().'.txt';
            $testContent = 'health check test';

            // Test storage write
            Storage::put($testFile, $testContent);

            // Test storage read
            $retrievedContent = Storage::get($testFile);

            // Test storage delete
            Storage::delete($testFile);

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            if ($retrievedContent === $testContent) {
                return [
                    'status' => 'healthy',
                    'message' => 'Storage system operational',
                    'response_time_ms' => $responseTime,
                    'driver' => config('filesystems.default'),
                ];
            } else {
                return [
                    'status' => 'degraded',
                    'message' => 'Storage read/write mismatch',
                ];
            }
        } catch (Exception $e) {
            Log::error('Health check storage failure: '.$e->getMessage());

            return [
                'status' => 'unhealthy',
                'message' => 'Storage system failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Storage error',
            ];
        }
    }

    /**
     * Check application status
     */
    private function checkApplication(): array
    {
        try {
            $checks = [];

            // Check if app is in maintenance mode
            if (app()->isDownForMaintenance()) {
                $checks['maintenance'] = 'Application is in maintenance mode';
            }

            // Check memory usage
            $memoryUsage = memory_get_usage(true);
            $memoryLimit = $this->parseMemoryLimit(ini_get('memory_limit'));
            $memoryPercentage = ($memoryUsage / $memoryLimit) * 100;

            if ($memoryPercentage > 90) {
                $checks['memory'] = 'High memory usage: '.round($memoryPercentage, 2).'%';
            }

            // Check disk space (storage path)
            $storagePath = storage_path();
            if (function_exists('disk_free_space')) {
                $freeBytes = disk_free_space($storagePath);
                $totalBytes = disk_total_space($storagePath);
                if ($freeBytes && $totalBytes) {
                    $freePercentage = ($freeBytes / $totalBytes) * 100;
                    if ($freePercentage < 10) {
                        $checks['disk_space'] = 'Low disk space: '.round($freePercentage, 2).'% free';
                    }
                }
            }

            $status = empty($checks) ? 'healthy' : 'degraded';
            $message = empty($checks) ? 'Application running normally' : 'Application has warnings';

            $result = [
                'status' => $status,
                'message' => $message,
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
            ];

            if (! empty($checks)) {
                $result['warnings'] = $checks;
            }

            return $result;
        } catch (Exception $e) {
            Log::error('Health check application failure: '.$e->getMessage());

            return [
                'status' => 'unhealthy',
                'message' => 'Application check failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Application error',
            ];
        }
    }

    /**
     * Parse memory limit string to bytes
     */
    private function parseMemoryLimit(string $memoryLimit): int
    {
        $memoryLimit = trim($memoryLimit);
        $last = strtolower($memoryLimit[strlen($memoryLimit) - 1]);
        $value = (int) $memoryLimit;

        switch ($last) {
            case 'g':
                $value *= 1024 * 1024 * 1024;
                break;
            case 'm':
                $value *= 1024 * 1024;
                break;
            case 'k':
                $value *= 1024;
                break;
        }

        return $value;
    }
}
