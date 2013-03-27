<?php
namespace Vivo\Util\Path;

use Vivo\Util\Exception;

/**
 * Parser for path of module files.
 * @todo replace by zend router
 */
class PathParser
{
    public function getPath($path)
    {
        $parts = $this->resolve($path);
        return $parts['path'];
    }

    public function getModule($path)
    {
        $parts = $this->resolve($path);
        return $parts['module'];
    }

    public function getType($path) {
        $parts = $this->resolve($path);
        return $parts['type'];
    }

    public function parse($path) {
        if (!preg_match("@\.(?<module>.*?)\.(?<type>.*?)/(?<path>.*)@", $path, $matches)) {
            throw new Exception\CanNotParsePathException(sprintf("%s Can\'t parse path '%s'", __DIR__, $path));
        }
        return $matches;
    }

    public function isPath($path) {
        return preg_match("@\..*?\..*?/.*@", $path) ? true : false;
    }
}
