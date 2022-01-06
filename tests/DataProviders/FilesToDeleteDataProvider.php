<?php

declare(strict_types=1);

namespace Quillstack\LocalStorage\Tests\DataProviders;

use Quillstack\UnitTests\DataProviderInterface;

class FilesToDeleteDataProvider implements DataProviderInterface
{
    public function provides(): array
    {
        return [
            ['one'],
            ['two'],
            ['three'],
            ['four'],
            ['five'],
        ];
    }
}
