<?php

declare(strict_types=1);

namespace Quillstack\Tests\LocalStorage;

use PHPUnit\Framework\TestCase;
use Quillstack\LocalStorage\Exceptions\LocalFileNotDeletedException;
use Quillstack\LocalStorage\Exceptions\LocalFileNotExistsException;
use Quillstack\LocalStorage\Exceptions\LocalFileNotSavedException;
use Quillstack\LocalStorage\LocalStorage;

final class LocalStorageTest extends TestCase
{
    /**
     * @var LocalStorage
     */
    private LocalStorage $storage;

    protected function setUp(): void
    {
        $this->storage = new LocalStorage();
    }

    public function testGetEmptyFile()
    {
        $path = dirname(__FILE__) . '/fixtures/empty.txt';
        $contests = $this->storage->get($path);

        $this->assertEmpty($contests);
    }

    public function testGetNonEmptyFile()
    {
        $path = dirname(__FILE__) . '/fixtures/word.txt';
        $contests = $this->storage->get($path);

        $this->assertEquals('hello', $contests);
    }

    public function testSaveFile()
    {
        $path = dirname(__FILE__) . '/fixtures/world.txt';
        $this->storage->save($path, 'world');
        $contests = $this->storage->get($path);
        $this->storage->delete($path);

        $this->assertEquals('world', $contests);
    }

    /**
     * @param string $filename
     *
     * @dataProvider filesToDeleteProvider
     */
    public function testDeleteOneFile(string $filename)
    {
        $path = dirname(__FILE__) . "/fixtures/{$filename}.txt";
        $this->storage->save($path, $filename);
        $contests = $this->storage->get($path);
        $deleted = $this->storage->delete($path);

        $this->assertEquals($filename, $contests);
        $this->assertTrue($deleted);
    }

    public function testDeleteManyFiles()
    {
        $files = [
            'one',
            'two',
            'three',
            'four',
            'five',
        ];

        foreach ($files as $file) {
            $this->storage->save($file, $file);
        }

        $deleted = $this->storage->delete(...$files);

        $this->assertTrue($deleted);
    }

    public function filesToDeleteProvider(): array
    {
        return [
            ['one'],
            ['two'],
            ['three'],
            ['four'],
            ['five'],
        ];
    }

    public function testNotExistingFile()
    {
        $this->expectException(LocalFileNotExistsException::class);

        $this->storage->get('not-exists');
    }

    public function testMissingFile()
    {
        $missing = $this->storage->missing('not-exists');
        $exists = $this->storage->exists('not-exists');

        $this->assertTrue($missing);
        $this->assertFalse($exists);
    }

    public function testExistsFile()
    {
        $path = dirname(__FILE__) . '/fixtures/empty.txt';
        $missing = $this->storage->missing($path);
        $exists = $this->storage->exists($path);

        $this->assertFalse($missing);
        $this->assertTrue($exists);
    }

    public function testNotSaved()
    {
        $this->expectException(LocalFileNotSavedException::class);

        $this->storage->save('/dir/not/exists', 'world');
    }

    public function testNotDeleted()
    {
        $this->expectException(LocalFileNotDeletedException::class);

        $this->storage->delete('/file-not-exists');
    }
}
