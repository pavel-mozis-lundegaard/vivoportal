<?php
namespace Vivo\CMS\Stream;

use Zend\View\Stream;

/**
 * Stream for loading UI components templates.
 * @author kormik
 *
 */
class Template extends Stream {
	
	const STREAM_NAME = 'cms.template';
	
	public function __construct() {
		return false;
	}
	
	public function stream_open($path, $mode, $options, &$opened_path) {

        $path = str_replace(self::STREAM_NAME.'://', '', $path);
		
        //TODO implement a logic that adds ability to load the templates from different sources (site, vmodules etc.) 
		 
        $dir = __DIR__.'/../../../../view/';

        $this->data = file_get_contents($dir.$path);
        if ($this->data === false) {
            $this->stat = stat($dir.$path);
            return false;
        }
        $this->stat = stat($dir.$path);
        return true;
    }
     	
	public static function register() {
		return stream_wrapper_register(self::STREAM_NAME, __CLASS__);
	}
}
