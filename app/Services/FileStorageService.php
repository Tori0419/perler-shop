<?php

namespace App\Services;

class FileStorageService
{
    public function readJson(string $filename, array $default = []): array
    {
        $this->ensureDataDirectoryExists();

        $path = $this->resolvePath($filename);

        if (! file_exists($path)) {
            $this->writeJson($filename, $default);

            return $default;
        }

        $contents = file_get_contents($path);

        if (! is_string($contents) || trim($contents) === '') {
            return $default;
        }

        $data = json_decode($contents, true);

        return is_array($data) ? $data : $default;
    }

    public function writeJson(string $filename, array $data): void
    {
        $this->ensureDataDirectoryExists();

        file_put_contents(
            $this->resolvePath($filename),
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );
    }

    private function resolvePath(string $filename): string
    {
        $dataDir = rtrim($this->dataDirectory(), '/');

        return $dataDir.'/'.$filename;
    }

    private function ensureDataDirectoryExists(): void
    {
        $dataDir = $this->dataDirectory();

        if (! is_dir($dataDir)) {
            mkdir($dataDir, 0755, true);
        }
    }

    private function dataDirectory(): string
    {
        $configured = trim((string) config('shop.data_dir', ''));

        if ($configured === '') {
            return storage_path('app/data');
        }

        if (str_starts_with($configured, '/')) {
            return $configured;
        }

        return base_path($configured);
    }
}
