<?php
namespace VivoTest\Storage\StorageCache\KeyNormalizer;

use PHPUnit_Framework_TestCase as TestCase;
use Vivo\Storage\StorageCache\KeyNormalizer\FileSystemCache as FsKeyNormalizer;

class FileSystemCacheTest extends TestCase
{
    /**
     * @var FsKeyNormalizer
     */
    protected $normalizer;

    protected function setUp()
    {
        $this->normalizer   = new FsKeyNormalizer();
    }

    public function testNormalizeKey()
    {
        $this->assertEquals('ab+cd+ef', $this->normalizer->normalizeKey('ab/cd/ef'), 'Slash');
        $this->assertEquals('ab+cd+ef', $this->normalizer->normalizeKey('ab\cd\ef'), 'Backslash');
        $this->assertEquals('ab+cd+ef', $this->normalizer->normalizeKey('ab\cd/ef'), 'Backslash');
    }
}