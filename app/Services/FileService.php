<?php

namespace App\Services;

use App\Enums\TenantUploadDirectory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class FileService
{
    private Filesystem $filesystem;

    public function __construct(?Filesystem $filesystem = null)
    {
        $this->filesystem = $filesystem ?? new Filesystem();
    }

    /**
     * Upload a tenant-specific file under the given directory and return its public school_asset URL.
     *
     * @param array $file The entry from $_FILES
     * @param TenantUploadDirectory|string $directory Tenant-relative directory path (e.g., pictures/store_items)
     * @param array<string,string> $allowedMimes Map of allowed mime => forced extension. Leave empty to accept any mime.
     */
    public function uploadTenantFile(array $file, TenantUploadDirectory|string $directory, array $allowedMimes = []): ?string
    {
        $status = $file['error'] ?? UPLOAD_ERR_NO_FILE;

        if ($status !== UPLOAD_ERR_OK) {
            return null;
        }

        $tmpPath = $file['tmp_name'] ?? null;

        if (!$tmpPath || !is_uploaded_file($tmpPath)) {
            return null;
        }

        $mime = mime_content_type($tmpPath) ?: null;

        if (!empty($allowedMimes)) {
            if (!$mime || !array_key_exists($mime, $allowedMimes)) {
                return null;
            }
        }

        $directory = trim($directory instanceof TenantUploadDirectory ? $directory->value : $directory, '/');
        $this->filesystem->ensureDirectoryExists(school_asset_path($directory), 0755, true);

        $extension = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));

        if (!empty($allowedMimes)) {
            $extension = $extension ?: $allowedMimes[$mime];
        } else {
            $extension = $extension ?: $this->guessExtensionFromMime($mime) ?: 'bin';
        }
        $filename = Str::uuid()->toString() . '.' . $extension;
        $relativePath = $directory . '/' . $filename;
        $absolutePath = school_asset_path($relativePath);
        $contents = file_get_contents($tmpPath);

        if ($contents === false) {
            return null;
        }

        $this->filesystem->put($absolutePath, $contents);

        return school_asset($relativePath);
    }

    public function uploadStoreItemPicture(?array $file): ?string
    {
        if (!$file) {
            return null;
        }

        return $this->uploadTenantFile($file, TenantUploadDirectory::STORE_ITEM_PICTURES, $this->imageMimeMap());
    }

    /**
     * Delete a tenant file referenced by a URL if it matches the provided directory restriction.
     */
    public function deleteTenantFileByUrl(?string $url, TenantUploadDirectory|string|null $expectedDirectory = null): void
    {
        if (!$url) {
            return;
        }

        $path = parse_url($url, PHP_URL_PATH) ?: null;

        if (!$path) {
            return;
        }

        $acronym = school_config('app.acronym');
        $prefix = '/' . $acronym . '/';

        if (!str_starts_with($path, $prefix)) {
            return;
        }

        $relativePath = ltrim(substr($path, strlen($prefix)), '/');
        if ($expectedDirectory instanceof TenantUploadDirectory) {
            $expectedDirectory = $expectedDirectory->value;
        }

        $expectedDirectory = $expectedDirectory ? trim($expectedDirectory, '/') : null;

        if ($expectedDirectory && !str_starts_with($relativePath, $expectedDirectory)) {
            return;
        }

        $absolutePath = school_asset_path($relativePath);

        if ($this->filesystem->exists($absolutePath)) {
            $this->filesystem->delete($absolutePath);
        }
    }

    public function deleteStoreItemPicture(?string $url): void
    {
        $this->deleteTenantFileByUrl($url, TenantUploadDirectory::STORE_ITEM_PICTURES);
    }

    public function resolveStoreItemPictureUrl(
        string $pictureSource,
        ?array $fileInput,
        ?string $urlInput,
        ?string $currentUrl = null
    ): ?string {
        $pictureSource = in_array($pictureSource, ['upload', 'url', 'none'], true) ? $pictureSource : 'none';

        if ($pictureSource === 'upload') {
            $uploadedUrl = $this->uploadStoreItemPicture($fileInput);

            if ($uploadedUrl) {
                $this->deleteStoreItemPicture($currentUrl);
                return $uploadedUrl;
            }

            return $currentUrl;
        }

        if ($pictureSource === 'url') {
            $sanitizedUrl = $this->sanitizeExternalUrl($urlInput);

            if ($sanitizedUrl) {
                if ($currentUrl !== $sanitizedUrl) {
                    $this->deleteStoreItemPicture($currentUrl);
                }

                return $sanitizedUrl;
            }

            return $currentUrl;
        }

        $this->deleteStoreItemPicture($currentUrl);

        return null;
    }

    private function imageMimeMap(): array
    {
        return [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        ];
    }

    private function guessExtensionFromMime(?string $mime): ?string
    {
        if (!$mime) {
            return null;
        }

        $map = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            'application/pdf' => 'pdf',
            'application/json' => 'json',
            'text/plain' => 'txt',
            'text/html' => 'html',
        ];

        if (isset($map[$mime])) {
            return $map[$mime];
        }

        $parts = explode('/', $mime);

        return isset($parts[1]) ? preg_replace('/[^a-z0-9]+/', '', strtolower($parts[1])) ?: null : null;
    }

    private function sanitizeExternalUrl(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        return filter_var($url, FILTER_VALIDATE_URL) ?: null;
    }
}
