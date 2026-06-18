<?php

function lab_rrmdir(string $path): void
{
    if (!is_dir($path)) {
        return;
    }

    $items = @scandir($path);
    if ($items === false) {
        @rmdir($path);
        return;
    }

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $fullPath = $path . DIRECTORY_SEPARATOR . $item;
        if (is_dir($fullPath)) {
            lab_rrmdir($fullPath);
            continue;
        }

        @unlink($fullPath);
    }

    @rmdir($path);
}

function lab_normalize_archive_path(string $path): string
{
    return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
}

function lab_export_xlsx_template(
    string $templatePath,
    string $outputPath,
    callable $sheetTransformer,
    string $sheetRelativePath = 'xl/worksheets/sheet1.xml'
): bool {
    if (!is_file($templatePath)) {
        return false;
    }

    if (class_exists('ZipArchive')) {
        if (!copy($templatePath, $outputPath)) {
            return false;
        }

        $zip = new ZipArchive();
        if ($zip->open($outputPath) !== true) {
            @unlink($outputPath);
            return false;
        }

        $sheetXml = $zip->getFromName($sheetRelativePath);
        if ($sheetXml === false) {
            $zip->close();
            @unlink($outputPath);
            return false;
        }

        $updatedSheet = $sheetTransformer($sheetXml);
        if (!is_string($updatedSheet) || $updatedSheet === '') {
            $zip->close();
            @unlink($outputPath);
            return false;
        }

        $zip->deleteName($sheetRelativePath);
        $zip->addFromString($sheetRelativePath, $updatedSheet);
        $zip->close();

        return is_file($outputPath) && filesize($outputPath) > 0;
    }

    if (!class_exists('PharData')) {
        return false;
    }

    $workDir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR)
        . DIRECTORY_SEPARATOR
        . 'lab_xlsx_' . uniqid('', true);

    if (!mkdir($workDir, 0777, true) && !is_dir($workDir)) {
        return false;
    }

    try {
        $archive = new PharData($templatePath);
        $archive->extractTo($workDir, null, true);

        $sheetPath = $workDir . DIRECTORY_SEPARATOR . lab_normalize_archive_path($sheetRelativePath);
        if (!is_file($sheetPath)) {
            return false;
        }

        $sheetXml = file_get_contents($sheetPath);
        if ($sheetXml === false) {
            return false;
        }

        $updatedSheet = $sheetTransformer($sheetXml);
        if (!is_string($updatedSheet) || $updatedSheet === '') {
            return false;
        }

        if (file_put_contents($sheetPath, $updatedSheet) === false) {
            return false;
        }

        @unlink($outputPath);
        $builder = new PharData($outputPath, 0, null, Phar::ZIP);
        $builder->buildFromDirectory($workDir);

        return is_file($outputPath) && filesize($outputPath) > 0;
    } catch (Throwable $e) {
        return false;
    } finally {
        lab_rrmdir($workDir);
    }
}
