<?php
namespace Vivo\Storage;

use Vivo\IO\FileInputStream;
use Vivo\IO\fileOutputStream;

class Storage {

	public function read($path) {
		return new FileInputStream(__DIR__.'/../../../../../data/repository/test.txt');
	}
	
	public function write($path) {
		return new FileOutputStream(__DIR__.'/../../../../../data/repository/test2.txt');
	}
}
