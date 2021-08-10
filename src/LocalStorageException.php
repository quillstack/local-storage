<?php

declare(strict_types=1);

namespace Quillstack\LocalStorage;

use RuntimeException;

class LocalStorageException extends RuntimeException
{
    /**
     * @var int
     */
    public const ERROR_CODE = 500;
}
