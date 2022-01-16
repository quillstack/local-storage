<?php

declare(strict_types=1);

namespace Quillstack\LocalStorage\Tests\Unit;

use Quillstack\LocalStorage\Exceptions\LocalFileNotDeletedException;
use Quillstack\LocalStorage\Exceptions\LocalFileNotExistsException;
use Quillstack\LocalStorage\Exceptions\LocalFileNotSavedException;
use Quillstack\LocalStorage\LocalStorage;
use Quillstack\LocalStorage\Tests\DataProviders\FilesToDeleteDataProvider;
use Quillstack\UnitTests\AssertEmpty;
use Quillstack\UnitTests\AssertEqual;
use Quillstack\UnitTests\AssertExceptions;
use Quillstack\UnitTests\Attributes\ProvidesDataFrom;
use Quillstack\UnitTests\Types\AssertBoolean;

class TestLocalStorage
{
    public function __construct(
        private LocalStorage $storage,
        private AssertExceptions $assertExceptions,
        private AssertEqual $assertEqual,
        private AssertBoolean $assertBoolean,
        private AssertEmpty $assertEmpty
    ) {
        //
    }

    public function getEmptyFile()
    {
        $path = dirname(__FILE__) . '/../Fixtures/empty.txt';
        $contests = $this->storage->get($path);

        $this->assertEmpty->isEmpty($contests);
    }

    public function getNonEmptyFile()
    {
        $path = dirname(__FILE__) . '/../Fixtures/word.txt';
        $contests = $this->storage->get($path);

        $this->assertEqual->equal('hello', $contests);
    }

    public function saveFile()
    {
        $path = dirname(__FILE__) . '/../Storage/world.txt';
        $this->storage->save($path, 'world');
        $contests = $this->storage->get($path);
        $this->storage->delete($path);

        $this->assertEqual->equal('world', $contests);
    }

    public function saveMultipleLines()
    {
        $path = dirname(__FILE__) . '/../Storage/hello.txt';
        $this->storage->save($path, 'world');
        $this->storage->save($path, 'hello');
        $contests = $this->storage->get($path);
        $this->storage->delete($path);

        $this->assertEqual->equal('hello', $contests);
    }

    public function addToFile()
    {
        $path = dirname(__FILE__) . '/../Storage/one.txt';
        $this->storage->add($path, 'one');
        $contests = $this->storage->get($path);
        $this->storage->delete($path);

        $this->assertEqual->equal('one', $contests);
    }

    public function addMultipleLines()
    {
        $path = dirname(__FILE__) . '/../Storage/two.txt';
        $this->storage->add($path, 'one' . PHP_EOL);
        $this->storage->add($path, 'two');
        $contests = $this->storage->get($path);
        $this->storage->delete($path);

        $this->assertEqual->equal("one\ntwo", $contests);
    }

    #[ProvidesDataFrom(FilesToDeleteDataProvider::class)]
    public function deleteOneFile(string $filename)
    {
        $path = dirname(__FILE__) . "/../Storage/{$filename}.txt";
        $this->storage->save($path, $filename);
        $contests = $this->storage->get($path);
        $deleted = $this->storage->delete($path);

        $this->assertEqual->equal($filename, $contests);
        $this->assertBoolean->isTrue($deleted);
    }

    public function deleteManyFiles()
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

        $this->assertBoolean->isTrue($deleted);
    }

    public function notExistingFile()
    {
        $this->assertExceptions->expect(LocalFileNotExistsException::class);

        $this->storage->get('not-exists');
    }

    public function missingFile()
    {
        $missing = $this->storage->missing('not-exists');
        $exists = $this->storage->exists('not-exists');

        $this->assertBoolean->isTrue($missing);
        $this->assertBoolean->isFalse($exists);
    }

    public function existsFile()
    {
        $path = dirname(__FILE__) . '/../Fixtures/empty.txt';
        $missing = $this->storage->missing($path);
        $exists = $this->storage->exists($path);

        $this->assertBoolean->isFalse($missing);
        $this->assertBoolean->isTrue($exists);
    }

    public function notSaved()
    {
        $this->assertExceptions->expect(LocalFileNotSavedException::class);

        $this->storage->save('/dir/not/exists', 'world');
    }

    public function notDeleted()
    {
        $this->assertExceptions->expect(LocalFileNotDeletedException::class);

        $this->storage->delete('/file-not-exists');
    }
}
