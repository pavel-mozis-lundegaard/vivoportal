<?php
namespace VivoTest\Util;

/**
 * Test class for \Vivo\Util\Images.
 */
class ImagesTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Images
     */
    protected $object;

    /**
     * @var string
     */
    protected $sourceFolder;

    /**
     * @var string
     */
    protected $expectedFolder;

    /**
     * @var string
     */
    protected $tmpfile;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Vivo\Util\Images();
        $this->sourceFolder = __DIR__ . '/images/source/';
        $this->expectedFolder = __DIR__ . '/images/expected/';
        $this->tmpFile = sys_get_temp_dir() . '/php_unit_test_' . md5(microtime());
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        if (file_exists($this->tmpFile)) {
            unlink($this->tmpFile);
        }
    }

    public function testResampleBW()
    {
        $options = array('bw' => true);
        $this->object->resample($this->sourceFolder . 'Praga_0003.JPG', $this->tmpFile, $options);
        $this->assertFileEquals($this->expectedFolder . __FUNCTION__, $this->tmpFile);
    }

    public function testResampleSize()
    {
        $options = array('size' => 200);
        $this->object->resample($this->sourceFolder . 'Praga_0003.JPG', $this->tmpFile, $options);
        $this->assertFileEquals($this->expectedFolder . __FUNCTION__, $this->tmpFile);
    }

    public function testResampleHeightWidth()
    {
        $options = array('height' => 200, 'width' => 300);
        $this->object->resample($this->sourceFolder . 'Praga_0003.JPG', $this->tmpFile, $options);
        $this->assertFileEquals($this->expectedFolder . __FUNCTION__, $this->tmpFile);
    }

    public function testResampleHeightWidthCrop()
    {
        $options = array('height' => 200, 'width' => 300, 'crop' => true);
        $this->object->resample($this->sourceFolder . 'Praga_0003.JPG', $this->tmpFile, $options);
        $this->assertFileEquals($this->expectedFolder . __FUNCTION__, $this->tmpFile);
    }

    public function testResampleRoundCorners()
    {
        $options = array('radius' => 100, 'bgcolor' => 'FFFFFF');
        $this->object->resample($this->sourceFolder . 'Praga_0003.JPG', $this->tmpFile, $options);
        $this->assertFileEquals($this->expectedFolder . __FUNCTION__, $this->tmpFile);
    }

    public function testResamplePng()
    {
        $options = array('height' => 200, 'outputType' => 'image/png');
        $this->object->resample($this->sourceFolder . 'logo.png', $this->tmpFile, $options);
//        $this->object->resample($this->sourceFolder.'logo.png', $this->expectedFolder . __FUNCTION__, $options);
        $this->assertFileEquals($this->expectedFolder . __FUNCTION__, $this->tmpFile);
    }

}
