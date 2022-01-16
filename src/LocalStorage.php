<?php

declare(strict_types=1);

namespace Quillstack\LocalStorage;

use Quillstack\LocalStorage\Exceptions\LocalFileNotDeletedException;
use Quillstack\LocalStorage\Exceptions\LocalFileNotExistsException;
use Quillstack\LocalStorage\Exceptions\LocalFileNotSavedException;
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
            throw new LocalFileNotExistsException("File doesn't exist: {$path}");
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
    public function save(string $path, mixed $contents): bool
    {
        return $this->saveLocally($path, $contents, true);
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
     * Deletes one file.
     */
    private function deleteOne(string $path): bool
    {
        try {
            $deleted = @unlink($path);
        } catch (Throwable $exception) {
            throw new LocalFileNotDeletedException(
                "File not deleted: {$path}",
                LocalStorageException::ERROR_CODE,
                $exception
            );
        }

        if ($deleted === false) {
            throw new LocalFileNotDeletedException("File not deleted: {$path}", LocalStorageException::ERROR_CODE);
        }

        return $deleted;
    }

    /**
     * {@inheritDoc}
     */
    public function add(string $path, mixed $contents): bool
    {
        return $this->saveLocally($path, $contents, false);
    }

    /**
     * Saves locally and overwrite or not.
     */
    private function saveLocally(string $path, mixed $contents, bool $overwrite = true)
    {
        try {
            $savedBytes = @file_put_contents($path, $contents, $overwrite ? LOCK_EX : FILE_APPEND | LOCK_EX);
        } catch (Throwable $exception) {
            throw new LocalFileNotSavedException("File not saved: {$path}", LocalStorageException::ERROR_CODE, $exception);
        }

        if ($savedBytes === false) {
            throw new LocalFileNotSavedException("File not saved: {$path}", LocalStorageException::ERROR_CODE);
        }

        return $savedBytes > 0;
    }
}
