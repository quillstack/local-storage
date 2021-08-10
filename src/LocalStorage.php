<?php

declare(strict_types=1);

namespace Quillstack\LocalStorage;

use Quillstack\LocalStorage\Exceptions\FileNotDeletedException;
use Quillstack\LocalStorage\Exceptions\FileNotExistsException;
use Quillstack\LocalStorage\Exceptions\FileNotSavedException;
use Quillstack\StorageInterface\StorageInterface;
use Throwable;

class LocalStorage implements StorageInterface
{
    /**
     * {@inheritDoc}
     */
    public function get(string $path): mixed
    {
        if (!is_file($path)) {
            throw new FileNotExistsException("File doesn't exist: {$path}");
        }

        return file_get_contents($path);
    }

    /**
     * {@inheritDoc}
     */
    public function exists(string $path): bool
    {
        return is_file($path);
    }

    /**
     * {@inheritDoc}
     */
    public function missing(string $path): bool
    {
        return !is_file($path);
    }

    /**
     * {@inheritDoc}
     */
    public function save(string $path, $contents): bool
    {
        try {
            $savedBytes = file_put_contents($path, $contents);
        } catch (Throwable $exception) {
            throw new FileNotSavedException("File not saved: {$path}", LocalStorageException::ERROR_CODE, $exception);
        }

        return $savedBytes > 0;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $path, string ...$more): bool
    {
        $this->deleteOne($path);

        foreach ($more as $path) {
            $this->deleteOne($path);
        }

        return true;
    }

    /**
     * Delete one file.
     *
     * @param string $path
     *
     * @return bool
     */
    private function deleteOne(string $path): bool
    {
        try {
            $deleted = unlink($path);
        } catch (Throwable $exception) {
            throw new FileNotDeletedException(
                "File not deleted: {$path}",
                LocalStorageException::ERROR_CODE,
                $exception
            );
        }

        return $deleted;
    }
}
