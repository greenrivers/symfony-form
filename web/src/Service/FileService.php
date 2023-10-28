<?php

declare(strict_types=1);

namespace App\Service;

use LimitIterator;
use SplFileObject;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class FileService
{
    public function __construct(private readonly Filesystem $filesystem)
    {
    }

    public function fileExists(string $filePath): bool
    {
        return $this->filesystem->exists($filePath);
    }

    public function checkExtension(string $filePath, string $extension): bool
    {
        return Path::hasExtension($filePath, $extension);
    }

    public function getCsvFile(string $filePath): SplFileObject
    {
        $file = new SplFileObject($filePath);
        $file->setFlags(
            SplFileObject::READ_CSV |
            SplFileObject::SKIP_EMPTY |
            SplFileObject::DROP_NEW_LINE |
            SplFileObject::READ_AHEAD
        );

        return $file;
    }

    public function getNumberOfRows(SplFileObject $file): int
    {
        $file->seek(PHP_INT_MAX);
        return $file->key() + 1;
    }

    public function getReader(SplFileObject $file, int $offset = 1): LimitIterator
    {
        return new LimitIterator($file, $offset);
    }
}
