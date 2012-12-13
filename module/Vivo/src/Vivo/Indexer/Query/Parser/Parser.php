<?php
namespace Vivo\Indexer\Query\Parser;

use Vivo\Indexer\Query;

/**
 * Parser
 */
class Parser implements ParserInterface
{

    /**
     * Unserializes string representation of query into the Query object
     * @param $string
     * @throws Exception\UnsupportedQueryTypeException
     * @throws Exception\InvalidQuerySyntaxException
     * @return Query\QueryInterface
     */
    public function stringToQuery($string)
    {
        $regExps    = array(
            'and'   => '/^\s*\(\s*(\(.+\))\s*[aA][nN][dD]\s*(\(.+\))\s*\)\s*$/',
            'or'    => '/^\s*\(\s*(\(.+\))\s*[oO][rR]\s*(\(.+\))\s*\)\s*$/',
            'not'   => '/^\s*\(\s*[nN][oO][tT]\s*(\(.+\))\s*\)\s*$/',
            'wc'    => '/^\s*\((\s*(\w+)\s*:)?\s*"(.+\*.*)"\s*\)\s*$/',
            'term'  => '/^\s*\((\s*(\w+)\s*:)?\s*"(.+)"\s*\)\s*$/',
        );
        foreach ($regExps as $key => $regExp) {
            $matches    = array();
            $matched    = preg_match($regExp, $string, $matches);
            if ($matched === 1) {
                switch ($key) {
                    case 'and':
                        $queryLeft  = $this->stringToQuery(trim($matches[1]));
                        $queryRight = $this->stringToQuery(trim($matches[2]));
                        $query      = new Query\BooleanAnd($queryLeft, $queryRight);
                        return $query;
                        break;
                    case 'or':
                        $queryLeft  = $this->stringToQuery(trim($matches[1]));
                        $queryRight = $this->stringToQuery(trim($matches[2]));
                        $query      = new Query\BooleanOr($queryLeft, $queryRight);
                        return $query;
                        break;
                    case 'not':
                        $queryNot   = $this->stringToQuery(trim($matches[1]));
                        $query      = new Query\BooleanNot($queryNot);
                        return $query;
                        break;
                    case 'wc':
                        $field      = trim($matches[2]);
                        $patternStr = trim($matches[3]);
                        if ($field == '') {
                            $field  = null;
                        }
                        $pattern    = new \Vivo\Indexer\Term($patternStr, $field);
                        $query      = new \Vivo\Indexer\Query\Wildcard($pattern);
                        return $query;
                        break;
                    case 'term':
                        $field      = trim($matches[2]);
                        $value      = trim($matches[3]);
                        if ($field == '') {
                            $field  = null;
                        }
                        $term       = new \Vivo\Indexer\Term($value, $field);
                        $query      = new \Vivo\Indexer\Query\Term($term);
                        return $query;
                        break;
                    default:
                        throw new Exception\UnsupportedQueryTypeException(
                            sprintf("%s: Unsupported query type '%s'", __METHOD__, $key));
                        break;
                }
            }
        }
        //No pattern matched
        throw new Exception\InvalidQuerySyntaxException(sprintf("%s: Invalid query syntax '%s'", __METHOD__, $string));
    }

    /**
     * Serializes Query object to a string
     * @param Query\QueryInterface $query
     * @throws Exception\UnsupportedQueryTypeException
     * @return string
     */
    public function queryToString(Query\QueryInterface $query)
    {
        if ($query instanceof Query\TermInterface) {
            //Term query
            /** @var $query \Vivo\Indexer\Query\TermInterface */
            $term   = $query->getTerm();
            if ($term->getField()) {
                //Field specified
                $string = sprintf('(%s:"%s")', $term->getField(), $term->getText());
            } else {
                //Field not specified
                $string = sprintf('("%s")', $term->getText());
            }
        } elseif ($query instanceof Query\WildcardInterface) {
            //Wildcard query
            /** @var $query \Vivo\Indexer\Query\WildcardInterface */
            $term   = $query->getPattern();
            if ($term->getField()) {
                //Field specified
                $string = sprintf('(%s:"%s")', $term->getField(), $term->getText());
            } else {
                //Field not specified
                $string = sprintf('("%s")', $term->getText());
            }
        } elseif ($query instanceof Query\RangeInterface) {
            //Range query
            /** @var $query \Vivo\Indexer\Query\RangeInterface */
            $leftBracket    = $query->isLowerLimitInclusive() ? '[' : '{';
            $rightBracket   = $query->isUpperLimitInclusive() ? ']' : '}';
            $lowerLimit     = is_null($query->getLowerLimit()) ? '*' : $query->getLowerLimit();
            $upperLimit     = is_null($query->getUpperLimit()) ? '*' : $query->getUpperLimit();
            $string         = sprintf('(%s:%s"%s" TO "%s"%s)', $query->getField(),
                $leftBracket, $lowerLimit, $upperLimit, $rightBracket);
        } elseif ($query instanceof Query\BooleanAnd) {
            //BooleanAnd query
            /** @var $query \Vivo\Indexer\Query\BooleanAnd */
            $stringLeft     = $this->queryToString($query->getQueryLeft());
            $stringRight    = $this->queryToString($query->getQueryRight());
            $string         = sprintf('(%s AND %s)', $stringLeft, $stringRight);
        } elseif ($query instanceof Query\BooleanOr) {
            //BooleanOr query
            /** @var $query \Vivo\Indexer\Query\BooleanOr */
            $stringLeft     = $this->queryToString($query->getQueryLeft());
            $stringRight    = $this->queryToString($query->getQueryRight());
            $string         = sprintf('(%s OR %s)', $stringLeft, $stringRight);
        } elseif ($query instanceof Query\BooleanNot) {
            //BooleanNot query
            /** @var $query \Vivo\Indexer\Query\BooleanNot */
            $str            = $this->queryToString($query->getQuery());
            $string         = sprintf('(NOT %s)', $str);
        } else {
            //Unsupported query type
            throw new Exception\UnsupportedQueryTypeException(sprintf('%s: Unsupported query type', __METHOD__));
        }
        return $string;
    }



    /* ***************************************************************************** */

    protected function tokenize($exp)
    {
        $tokens = array();
        $pos    = 0;
        $len    = mb_strlen($exp);
        while (false) {
            $current    = $exp[$pos];
            if ($pos == $len) {
                $ahead  = null;
            } else {
                $ahead      = $exp[$pos+1];
            }
            if ($current == '(') {
                $tokens[]   = '(';
            } elseif ($current == ')') {
                $tokens = ')';
            } elseif ($current == ' ') {
                //Skip whitespace
            } else {
                //Anything else

            }
        }
    }

}