<?php
namespace Vivo\Util;

/**
 * MIME provides methods to works with Content-types and MIME types.
 */
class MIME implements MIMEInterface
{
    /**
     * @var array
     */
    protected $types = array();

    /**
     * @param array $types
     */
    public function __construct(array $types)
    {
        $this->types = $types;
    }

    /**
     * Returns the Content-type for file extension.
     * @param string $ext
     * @return string
     */
    protected function getType($ext)
    {
        $ext = strtolower($ext);
        foreach ($this->types as $type => $exts) {
            if (in_array($ext, $exts)) {
                return $type;
            }
        }
        return null;
    }

    /**
     * Returns the file extension for Content-type. It is reverse method to self::getType().
     * @param string $type
     * @return string|null
     */
    public function getExt($type)
    {
        $type = strtolower($type);
        return isset($this->types[$type][0]) ? $this->types[$type][0] : null;
    }

    public function detectByExtension($ext)
    {
        return $this->getType($ext);
    }

    public function detectByFileContent($fileName)
    {
        //TODO , detect using finfo php extension
    }

}
