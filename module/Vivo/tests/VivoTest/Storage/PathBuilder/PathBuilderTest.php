<?php
namespace VivoTest\Storage\PathBuilder;

use Vivo\Storage\PathBuilder\PathBuilder;

/**
 * PathBuilderTest
 * Path builder test
 */
class PathBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PathBuilder
     */
    protected $pathBuilder;

    protected $separator    = '/';

    protected function setUp()
    {
        $this->pathBuilder  = new PathBuilder($this->separator);
    }

    /**
     * testGetStoragePathSeparator
     */
    public function testGetStoragePathSeparator()
    {
        $this->assertEquals($this->separator, $this->pathBuilder->getStoragePathSeparator());
    }

    /**
     * Tests building storage paths
     */
    public function testBuildPath()
    {
        $sep        = $this->pathBuilder->getStoragePathSeparator();
        $elements   = array(
            'foo',
            'bar',
            $sep,
            'baz' . str_repeat($sep, 3) . 'bat',
            str_repeat($sep, 3) . 'qux' . str_repeat($sep, 5),
            str_repeat($sep, 2) . 'quux',
        );
        $expected   = 'foo' . $sep . 'bar' . $sep . 'baz'. $sep . 'bat' . $sep . 'qux' . $sep . 'quux';
        $this->assertEquals($expected, $this->pathBuilder->buildStoragePath($elements, false));
        $this->assertEquals($sep . $expected, $this->pathBuilder->buildStoragePath($elements, true));
        $elements   = array(
            str_repeat($sep, 3) . 'foo',
            'bar',
            'baz',
        );
        $expected   = 'foo' . $sep . 'bar' . $sep . 'baz';
        $this->assertEquals($expected, $this->pathBuilder->buildStoragePath($elements, false));
    }

    public function testGetStoragePathComponents()
    {
        $sep        = $this->pathBuilder->getStoragePathSeparator();
        $path       = str_repeat($sep, 5) . 'abc' . $sep . 'de' . str_repeat($sep, 2)
            . 'fgh' . str_repeat($sep, 2) . 'ijk' . str_repeat($sep, 2);
        $expected   = array('abc', 'de', 'fgh', 'ijk');
        $this->assertEquals($expected, $this->pathBuilder->getStoragePathComponents($path));
    }

    public function testIsAbsolute()
    {
        $absPath    = $this->pathBuilder->getStoragePathSeparator() . 'abc';
        $relPath    = 'abc';
        $this->assertTrue($this->pathBuilder->isAbsolute($absPath));
        $this->assertFalse($this->pathBuilder->isAbsolute($relPath));
    }

    public function testDirname()
    {
        $sep        = $this->pathBuilder->getStoragePathSeparator();
        $dir        = implode($sep, array('abc', 'def', 'ghi', 'jkl'));
        $path       = implode($sep, array($dir, 'file.txt'));
        $this->assertEquals($dir, $this->pathBuilder->dirname($path));
        $this->assertNull($this->pathBuilder->dirname('xyz'));
        $this->assertNull($this->pathBuilder->dirname(''));
    }

    /**
     * Tests building path with a segment equal to '0'
     * Mantis bug: 0025290
     */
    public function testBuildPathWithZero()
    {
        $elements   = array('path/to/entity', 'foo/0/bar', '0');
        $path       = $this->pathBuilder->buildStoragePath($elements,  true);
        $expected   = '/path/to/entity/foo/0/bar/0';
        $this->assertEquals($expected, $path);
    }

    /**
     * Tests building path with untrimmed segments
     */
    public function testBuildPathFromUntrimmed()
    {
        $elements   = array('    path/to /   entity   ', ' ', ' foo/0/bar', '0');
        $path       = $this->pathBuilder->buildStoragePath($elements,  true);
        $expected   = '/path/to/entity/foo/0/bar/0';
        $this->assertEquals($expected, $path);
    }
}