<?php
namespace Vivo\Http\Filter;

use Vivo\IO\Filter;

use Zend\Http\Response;
use Zend\Http\Request;

class UpperCase implements OutputFilterInterface
{
    public function doFilter(Request $request, Response $response)
    {
        if ($response->getHeaders()->get('Content-Type')->getFieldValue() == 'text/plain')
        $response->setInputStream(
                        new Filter\UpperCase(
                                $response->getInputStream()
                                )
                        );
    }
}
