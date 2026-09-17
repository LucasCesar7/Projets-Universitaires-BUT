<?php

function detectUploadedFileMimeType(string $tmpPath): ?string
{
    if ($tmpPath === '' || !is_file($tmpPath)) {
        return null;
    }

    if (class_exists(finfo::class)) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($tmpPath);

        if (is_string($mimeType) && $mimeType !== '') {
            return strtolower($mimeType);
        }
    }

    if (function_exists('mime_content_type')) {
        $mimeType = mime_content_type($tmpPath);

        if (is_string($mimeType) && $mimeType !== '') {
            return strtolower($mimeType);
        }
    }

    return null;
}

function uploadedImageExtension(?array $file, array $allowedMimeMap): ?string
{
    if (!is_array($file) || empty($file['tmp_name']) || !is_string($file['tmp_name'])) {
        return null;
    }

    $mimeType = detectUploadedFileMimeType($file['tmp_name']);

    if ($mimeType === null) {
        return null;
    }

    $normalizedMap = [];

    foreach ($allowedMimeMap as $allowedMime => $extension) {
        $normalizedMap[strtolower((string) $allowedMime)] = strtolower(ltrim((string) $extension, '.'));
    }

    $extension = $normalizedMap[$mimeType] ?? null;

    return is_string($extension) && $extension !== '' ? $extension : null;
}