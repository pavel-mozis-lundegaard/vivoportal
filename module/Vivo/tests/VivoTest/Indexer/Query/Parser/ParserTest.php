<?php
namespace VivoTest\Indexer\Query\Parser;

use Vivo\Indexer\Query\Parser\Parser;
use Vivo\Indexer\Query;
use Vivo\Indexer\Term as IndexerTerm;

/**
 * ParserTest
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Parser
     */
    protected $parser;

    protected function setUp()
    {
        $this->parser   = new Parser();
    }

    public function testToStringWithFieldNames()
    {
        $query1         = new Query\Term(new IndexerTerm('foo value', 'foo_field'));
        $query2         = new Query\Wildcard(new IndexerTerm('bar*', 'bar_field'));
        $queryLeft      = new Query\BooleanOr($query1, $query2);

        $query3         = new Query\Term(new IndexerTerm('baz value', 'baz_field'));
        $queryRight     = new Query\BooleanNot($query3);

        $query          = new Query\BooleanAnd($queryLeft, $queryRight);
        $string         = $this->parser->queryToString($query);
        $expected       = '((foo_field:"foo value" OR bar_field:"bar*") AND (NOT baz_field:"baz value"))';
        $this->assertEquals($expected, $string);
    }

    public function testToStringWoFieldNames()
    {
        $query1         = new Query\Term(new IndexerTerm('foo value'));
        $query2         = new Query\Wildcard(new IndexerTerm('bar*'));
        $queryLeft      = new Query\BooleanOr($query1, $query2);

        $query3         = new Query\Term(new IndexerTerm('baz value'));
        $queryRight     = new Query\BooleanNot($query3);

        $query          = new Query\BooleanAnd($queryLeft, $queryRight);
        $string         = $this->parser->queryToString($query);
        $expected       = '(("foo value" OR "bar*") AND (NOT "baz value"))';
        $this->assertEquals($expected, $string);
    }

    public function testToQuery()
    {
        $reAnd  = '/^\s*\(\s*(\(.+\))\s+[aA][nN][dD]\s+(\(.+\))\s*\)\s*$/';
        $reNot  = '/^\s*\(\s*[nN][oO][tT]\s+(\(.+\))\s*\)\s*$/';
        $reTerm = '/^\s*((\w+)\s*:)?\s*"(.+)"\s*$/';
        $reWc   = '/^\s*\((\s*(\w+)\s*:)?\s*"(.+\*.*)"\s*\)\s*$/';
        //((\w+)\s*:)?\s*

//        $string     = ' ( ( xyz AND bbb )  AND ( www  AND  ( aaa AND ccc ) ) ) ';
//        $string     = ' (  NOT   (  NOT ( NOT    (aaa) ) ) ) ';
        $string         = '  (    foox   :    "foobar*"         )  ';
        $matches    = array();
        $matched    = preg_match($reWc, $string, $matches);

        \Zend\Debug\Debug::dump($matches);

        $this->assertEquals(1, $matched);
//        $this->assertEquals('foo', $matches[2]);
//        $this->assertEquals('this is * car', $matches[3]);
    }
}