<?php
namespace VivoTest\IO;

use Vivo\IO\FileOutputStream;

/**
 * FileOutputStream test case.
 */
class FileOutputStreamTest extends \PHPUnit_Framework_TestCase
{

    const filename = 'test.txt';

    /**
     * @var string
     */
    private $file;
    /**
     * @var Vivo\IO\FileInputStream
     */
    private $stream;

    /**
     * @var string
     */
    private $data = "Sample file content";

    /**
     * Test setup
     */
    protected function setUp()
    {
        $this->file = sys_get_temp_dir() . self::filename;
        $this->stream = new FileOutputStream($this->file);

    }

    protected function tearDown()
    {
        $this->stream->close();
    }

    public function testWrite()
    {
        $bytes = $this->stream->write($this->data);
        $data = file_get_contents($this->file);
        $this->assertEquals($this->data, $data);
        $this->assertEquals(strlen($this->data), $bytes);
    }

    public function testWriteEmptyData()
    {
        $bytes = $this->stream->write('');
        $data = file_get_contents($this->file);
        $this->assertEquals('', $data);
        $this->assertEquals(0, $bytes);
    }

    /**
     * @expectedException \Vivo\IO\Exception\RuntimeException
     */
    public function testWriteWhenClosed()
    {
        $this->stream->close();
        $this->stream->write($this->data);
    }
}
