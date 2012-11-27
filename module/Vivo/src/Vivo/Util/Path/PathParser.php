<?php
namespace Vivo\Util\Path;

use Vivo\Util\Exception;

class PathParser
{
    private $options = array (
            'prefix' => '');

    public function __construct($options = array())
    {
        $this->options = array_merge($this->options, $options);
    }

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
        if (!preg_match("@{$this->options['prefix']}(?<type>.*?):/(?<module>.*?)/(?<path>.*)@", $path, $matches)) {
            throw new Exception\CanNotParsePathException(sprintf("%s Can\'t parse path '%s'", __DIR__, $path));
        }
        return $matches;
    }

    public function isPath() {
        return preg_match("@{$this->options['prefix']}.*:/.*?/.*@", $path) ? true : false;
    }
}
