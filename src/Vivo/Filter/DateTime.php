<?php
namespace Vivo\Filter;

use Zend\Filter\AbstractFilter;

class DateTime extends AbstractFilter
{
    public function filter($value)
    {
        $date = \DateTime::createFromFormat('Y.m.d', $value);
        $date = ($date instanceof \DateTime) ? $date : null;

        return $date;
    }
}
