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

    public function testQueryToStringWithFieldNames()
    {
        $query1         = new Query\Term(new IndexerTerm('foo value', 'foo_field'));
        $query2         = new Query\Wildcard(new IndexerTerm('bar*', 'bar_field'));
        $queryLeft      = new Query\BooleanOr($query1, $query2);

        $query3         = new Query\Range('baz_field', '5', '8', false, true);
        $queryRight     = new Query\BooleanNot($query3);

        $query          = new Query\BooleanAnd($queryLeft, $queryRight);
        $string         = $this->parser->queryToString($query);
        $expected       = '(((foo_field:"foo value") OR (bar_field:"bar*")) AND (NOT (baz_field:{"5" TO "8"])))';
        $this->assertEquals($expected, $string);
    }

    public function testQueryToStringWoFieldNames()
    {
        $query1         = new Query\Term(new IndexerTerm('foo value'));
        $query2         = new Query\Wildcard(new IndexerTerm('bar*'));
        $queryLeft      = new Query\BooleanOr($query1, $query2);

        $query3         = new Query\Range('baz_field', '5', '8', false, true);
        $queryRight     = new Query\BooleanNot($query3);

        $query          = new Query\BooleanAnd($queryLeft, $queryRight);
        $string         = $this->parser->queryToString($query);
        $expected       = '((("foo value") OR ("bar*")) AND (NOT (baz_field:{"5" TO "8"])))';
        $this->assertEquals($expected, $string);
    }

    public function testStringToQueryTerm()
    {
        $expected   = new Query\Term(new IndexerTerm('foovalue', 'foofield'));
        //With field no whitespace
        $string     = '(foofield:"foovalue")';
        $query      = $this->parser->stringToQuery($string);
        $this->assertEquals($expected, $query);
        //With field with whitespace
        $string     = '   (     foofield       :         "foovalue"     )   ';
        $query      = $this->parser->stringToQuery($string);
        $this->assertEquals($expected, $query);
        $expected   = new Query\Term(new IndexerTerm('foovalue'));
        //Without field no whitespace
        $string     = '("foovalue")';
        $query      = $this->parser->stringToQuery($string);
        $this->assertEquals($expected, $query);
        //Without field with whitespace
        $string     = '   (   "foovalue"  )  ';
        $query      = $this->parser->stringToQuery($string);
        $this->assertEquals($expected, $query);
    }

    public function testStringToQueryTermInvalidSyntaxMissingParentheses()
    {
        $string     = 'foofield:"foovalue"';
        $this->setExpectedException('Vivo\Indexer\Query\Parser\Exception\InvalidQuerySyntaxException');
        $this->parser->stringToQuery($string);
    }

    public function testStringToQueryTermInvalidSyntaxMissingQuotes()
    {
        $string     = '(foofield:foovalue)';
        $this->setExpectedException('Vivo\Indexer\Query\Parser\Exception\InvalidQuerySyntaxException');
        $this->parser->stringToQuery($string);
    }

    public function testStringToQueryWildcard()
    {
        //Ending asterisk
        $expected   = new Query\Wildcard(new IndexerTerm('foovalue*', 'foofield'));
        //With field no whitespace
        $string     = '(foofield:"foovalue*")';
        $query      = $this->parser->stringToQuery($string);
        $this->assertEquals($expected, $query);
        //With field with whitespace
        $string     = '   (     foofield       :         "foovalue*"     )   ';
        $query      = $this->parser->stringToQuery($string);
        $this->assertEquals($expected, $query);
        $expected   = new Query\Wildcard(new IndexerTerm('foovalue*'));
        //Without field no whitespace
        $string     = '("foovalue*")';
        $query      = $this->parser->stringToQuery($string);
        $this->assertEquals($expected, $query);
        //Without field with whitespace
        $string     = '   (   "foovalue*"  )  ';
        $query      = $this->parser->stringToQuery($string);
        $this->assertEquals($expected, $query);
        //Asterisk in the middle
        $expected   = new Query\Wildcard(new IndexerTerm('foovalue*bar', 'foofield'));
        //With field no whitespace
        $string     = '(foofield:"foovalue*bar")';
        $query      = $this->parser->stringToQuery($string);
        $this->assertEquals($expected, $query);
        //With field with whitespace
        $string     = '   (     foofield       :         "foovalue*bar"     )   ';
        $query      = $this->parser->stringToQuery($string);
        $this->assertEquals($expected, $query);
        $expected   = new Query\Wildcard(new IndexerTerm('foovalue*bar'));
        //Without field no whitespace
        $string     = '("foovalue*bar")';
        $query      = $this->parser->stringToQuery($string);
        $this->assertEquals($expected, $query);
        //Without field with whitespace
        $string     = '   (   "foovalue*bar"  )  ';
        $query      = $this->parser->stringToQuery($string);
        $this->assertEquals($expected, $query);
    }

    public function testStringToQueryWildcardInvalidSyntaxMissingParentheses()
    {
        $string     = 'foofield:"foovalue*"';
        $this->setExpectedException('Vivo\Indexer\Query\Parser\Exception\InvalidQuerySyntaxException');
        $this->parser->stringToQuery($string);
    }

    public function testStringToQueryWildcardInvalidSyntaxMissingQuotes()
    {
        $string     = '(foofield:foovalue*)';
        $this->setExpectedException('Vivo\Indexer\Query\Parser\Exception\InvalidQuerySyntaxException');
        $this->parser->stringToQuery($string);
    }

    /**
     * The wildcard (asterisk) is not allowed at the beginning of the pattern
     */
    public function testStringToQueryWildcardInvalidSyntaxLeadingAsterisk()
    {
        //TODO - the leading asterisk matches the Term query!
        $this->markTestSkipped('TODO - the leading asterisk matches the Term query');
        $string     = '(foofield:"*foovalue")';
        $this->setExpectedException('Vivo\Indexer\Query\Parser\Exception\InvalidQuerySyntaxException');
        $query      = $this->parser->stringToQuery($string);
    }

    public function testStringToQueryNot()
    {
        $queryExp   = new Query\Term(new IndexerTerm('foovalue', 'foofield'));
        $expected   = new Query\BooleanNot($queryExp);
        //With field no whitespace
        $string     = '(NOT(foofield:"foovalue"))';
        $query      = $this->parser->stringToQuery($string);
        $this->assertEquals($expected, $query);
        //With field with whitespace
        $string     = '  ( NOT  (     foofield       :         "foovalue"     )  ) ';
        $query      = $this->parser->stringToQuery($string);
        $this->assertEquals($expected, $query);
        //With field no whitespace lowercase
        $string     = '(not(foofield:"foovalue"))';
        $query      = $this->parser->stringToQuery($string);
        $this->assertEquals($expected, $query);


        $queryExp   = new Query\Term(new IndexerTerm('foovalue'));
        $expected   = new Query\BooleanNot($queryExp);
        //Without field no whitespace
        $string     = '(NOT("foovalue"))';
        $query      = $this->parser->stringToQuery($string);
        $this->assertEquals($expected, $query);
        //Without field with whitespace
        $string     = '   (  NOT   (   "foovalue"  )   ) ';
        $query      = $this->parser->stringToQuery($string);
        $this->assertEquals($expected, $query);
    }

    public function testStringToQueryNotInvalidSyntaxMissingParentheses()
    {
        $string     = 'NOT (foofield:"foovalue")';
        $this->setExpectedException('Vivo\Indexer\Query\Parser\Exception\InvalidQuerySyntaxException');
        $this->parser->stringToQuery($string);
    }

    public function testStringToQueryOr()
    {
        $queryLeft  = new Query\Term(new IndexerTerm('foovalue', 'foofield'));
        $queryRight = new Query\Term(new IndexerTerm('barvalue', 'barfield'));
        $expected   = new Query\BooleanOr($queryLeft, $queryRight);
        //With field no whitespace
        $string     = '((foofield:"foovalue")OR(barfield:"barvalue"))';
        $query      = $this->parser->stringToQuery($string);
        $this->assertEquals($expected, $query);
        //With field with whitespace
        $string     = '   (   (    foofield  :  "foovalue"   )    OR    (    barfield   :  "barvalue"   )  )  ';
        $query      = $this->parser->stringToQuery($string);
        $this->assertEquals($expected, $query);
        //With field no whitespace lowercase
        $string     = '((foofield:"foovalue")or(barfield:"barvalue"))';
        $query      = $this->parser->stringToQuery($string);
        $this->assertEquals($expected, $query);

        $queryLeft  = new Query\Term(new IndexerTerm('foovalue'));
        $queryRight = new Query\Term(new IndexerTerm('barvalue'));
        $expected   = new Query\BooleanOr($queryLeft, $queryRight);
        //Without field no whitespace
        $string     = '(("foovalue")OR("barvalue"))';
        $query      = $this->parser->stringToQuery($string);
        $this->assertEquals($expected, $query);
        //Without field with whitespace
        $string     = '   (   (      "foovalue"   )    OR    (     "barvalue"   )  )  ';
        $query      = $this->parser->stringToQuery($string);
        $this->assertEquals($expected, $query);
    }

    public function testStringToQueryOrInvalidSyntaxMissingParentheses()
    {
        //TODO - Bug: Incorrect match by Term
        $this->markTestSkipped('TODO - Bug: Incorrect match by Term');
        $string     = '(barfield:"barvalue") OR (foofield:"foovalue")';
        $this->setExpectedException('Vivo\Indexer\Query\Parser\Exception\InvalidQuerySyntaxException');
    }

    public function testStringToQueryAnd()
    {
        $queryLeft  = new Query\Term(new IndexerTerm('foovalue', 'foofield'));
        $queryRight = new Query\Term(new IndexerTerm('barvalue', 'barfield'));
        $expected   = new Query\BooleanAnd($queryLeft, $queryRight);
        //With field no whitespace
        $string     = '((foofield:"foovalue")AND(barfield:"barvalue"))';
        $query      = $this->parser->stringToQuery($string);
        $this->assertEquals($expected, $query);
        //With field with whitespace
        $string     = '   (   (    foofield  :  "foovalue"   )   AND    (    barfield   :  "barvalue"   )  )  ';
        $query      = $this->parser->stringToQuery($string);
        $this->assertEquals($expected, $query);
        //With field no whitespace lowercase
        $string     = '((foofield:"foovalue")and(barfield:"barvalue"))';
        $query      = $this->parser->stringToQuery($string);
        $this->assertEquals($expected, $query);

        $queryLeft  = new Query\Term(new IndexerTerm('foovalue'));
        $queryRight = new Query\Term(new IndexerTerm('barvalue'));
        $expected   = new Query\BooleanAnd($queryLeft, $queryRight);
        //Without field no whitespace
        $string     = '(("foovalue")AND("barvalue"))';
        $query      = $this->parser->stringToQuery($string);
        $this->assertEquals($expected, $query);
        //Without field with whitespace
        $string     = '   (   (      "foovalue"   )    AND    (     "barvalue"   )  )  ';
        $query      = $this->parser->stringToQuery($string);
        $this->assertEquals($expected, $query);
    }

    public function testStringToQueryAndInvalidSyntaxMissingParentheses()
    {
        //TODO - Bug: Incorrect match by Term
        $this->markTestSkipped('TODO - Bug: Incorrect match by Term');
        $string     = '(barfield:"barvalue") AND (foofield:"foovalue")';
        $this->setExpectedException('Vivo\Indexer\Query\Parser\Exception\InvalidQuerySyntaxException');
        $this->parser->stringToQuery($string);
    }
}
