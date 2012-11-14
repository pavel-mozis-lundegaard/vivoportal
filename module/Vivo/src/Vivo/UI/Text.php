<?php
namespace Vivo\UI;

/**
 * Text component only display your content.
 */
class Text extends Component
{
    /**
     * @var string
     */
    private $text;

    /**
     * @param string
     */
    public function __construct($text = '')
    {
        $this->text = $text;
    }

    public function view()
    {
        return $this->text;
    }
}
