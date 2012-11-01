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

}