<?php
namespace Vivo\CMS\UI\Content;

use Vivo\Util\Redirector;
use Vivo\CMS\UI;

/**
 * UI to redirects to the URL.
 */
class Hyperlink extends UI\Component
{

    public function __construct(Redirector $redirector)
    {
        $this->redirector = $redirector;
    }

    /**
     * Hyperlink initialization.
     */
    public function init()
    {
        $url = $this->content->getUrl();
        $this->redirector->redirect($url);
//TODO add meta redirect
//        $this->parent('Vivo\UI\Page')->metas[] = array(
//                'http-equiv' => 'Refresh', 'content' => '0;url=' . $this->url);
    }
}

