<?php
namespace Vivo\Http\Filter;

use Zend\Http\Response;

use Zend\Http\Request;

interface OutputFilterInterface {
    public function doFilter(Request $request, Response $response);
}