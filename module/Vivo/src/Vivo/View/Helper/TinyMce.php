<?php
namespace Vivo\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\HeadScript;

/**
 * TinyMce
 * TinyMce helper initializes TinyMCE WYSIWYG editor
 */
class TinyMce extends AbstractHelper
{
    /**
     * All textareas are converted to TinyMCE editor
     */
    const MODE_TEXTAREAS            = 'all_textareas';

    /**
     * Only textareas designated with specific class are converted to TinyMCE editor
     */
    const MODE_SELECTED_TEXTAREAS   = 'selected_textareas';

    /**
     * Only textareas which are NOT designated with specific class are converted to TinyMCE editor
     */
    const MODE_DESELECTED_TEXTAREAS = 'deselected_textareas';

    /**
     * Only elements with specific IDs are converted to TinyMCE editor
     */
    const MODE_EXACT                = 'exact';

    /**
     * No elements are converted to TinyMCE editor
     */
    const MODE_NONE                 = 'none';

    /**
     * Supported TinyMCE initialization modes
     * @var array
     */
    protected $modes                = array(
        self::MODE_TEXTAREAS,
        self::MODE_SELECTED_TEXTAREAS,
        self::MODE_DESELECTED_TEXTAREAS,
        self::MODE_EXACT,
        self::MODE_NONE,
    );

    /**
     * Invoke the helper as a PhpRenderer method call
     * Initializes TinyMCE
     * See http://www.tinymce.com/wiki.php/Configuration:mode for Mode description
     * @param string $mode
     * @param string $modeSpec editor_selector, editor_deselector or elements
     * @param bool $full
     * @param string $language
     * @param string $advancedStyles
     * @param string $blockFormats
     * @param int $wysiwygHeight
     * @return TinyMce
     */
    public function __invoke($mode = null,
                             $modeSpec = null,
                             $full = true,
                             $language = null,
                             $advancedStyles = null,
                             $blockFormats = null,
                             $wysiwygHeight = null)
    {
        if (!$mode) {
            return $this;
        }
        return $this->init($mode, $modeSpec, $full, $language, $advancedStyles, $blockFormats, $wysiwygHeight);
    }

    /**
     * Initialize TinyMCE
     * See http://www.tinymce.com/wiki.php/Configuration:mode for Mode description
     * @param string $mode
     * @param string $modeSpec editor_selector, editor_deselector or elements
     * @param bool $full
     * @param string $language
     * @param string $advancedStyles
     * @param string $blockFormats
     * @param int $wysiwygHeight
     * @return TinyMce
     * @throws Exception\InvalidArgumentException
     */
    public function init($mode,
                         $modeSpec = null,
                         $full = true,
                         $language = null,
                         $advancedStyles = null,
                         $blockFormats = null,
                         $wysiwygHeight = null)
    {
        if (!in_array($mode, $this->modes)) {
            throw new Exception\InvalidArgumentException(
                sprintf("%s: TinyMCE initialization mode '%s' not supported", __METHOD__, $mode));
        }
        /** @var $headScriptVh HeadScript */
        $headScriptVh   = $this->getView()->plugin('headScript');
        /** @var $resourceVh \Vivo\View\Helper\Resource */
        $resourceVh     = $this->getView()->plugin('resource');
        $headScriptVh->appendFile($resourceVh('js/tiny_mce/tiny_mce.js', 'TinyMCE3_5_6_Vivo'));
        $headScriptVh->appendFile($resourceVh('js/init.js', 'TinyMCE3_5_6_Vivo'));
        $script         = sprintf("initTinyMce('%s', %s, %s, %s, %s, %s, %s);",
                            $mode,
                            $modeSpec ? "'$modeSpec'" : 'null',
                            $full ? 'true' : 'false',
                            $language ? "'$language'" : 'null',
                            $advancedStyles ? "'$advancedStyles'" : 'null',
                            $blockFormats ? "'$blockFormats'" : 'null',
                            $wysiwygHeight ?: 'null');
        $headScriptVh->appendScript($script);
        return $this;
    }
}
