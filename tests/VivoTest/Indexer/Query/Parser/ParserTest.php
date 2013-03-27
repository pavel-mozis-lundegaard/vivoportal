<?php
namespace VivoTest\Indexer\Query\Parser;

use Vivo\Indexer\Query\Parser\Parser;
use Vivo\Indexer\Query;
use Vivo\Indexer\Term as IndexerTerm;
use Vivo\Indexer\Query\Parser\LexerInterface;
use Vivo\Indexer\Query\Parser\RpnConvertorInterface;
use Vivo\Indexer\Query\Parser\Token;
use Vivo\Indexer\Query\Parser\TokenInterface;
use Vivo\Indexer\QueryBuilder;

/**
 * ParserTest
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var LexerInterface
     */
    protected $lexer;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var RpnConvertorInterface
     */
    protected $rpnConvertor;

    protected function setUp()
    {
        $this->lexer        = $this->getMock('Vivo\Indexer\Query\Parser\LexerInterface', array(), array(), '', false);
        $this->rpnConvertor = $this->getMock('Vivo\Indexer\Query\Parser\RpnConvertorInterface',
                                         array(), array(), '', false);
        $this->queryBuilder = new QueryBuilder();
        $this->parser       = new Parser($this->lexer, $this->rpnConvertor, $this->queryBuilder);
    }

    public function testQueryToStringWithFieldNames()
    {
        $query1         = new Query\Term(new IndexerTerm('foo value', '\foo_field'));
        $query2         = new Query\Wildcard(new IndexerTerm('bar*', '\bar_field'));
        $queryLeft      = new Query\BooleanOr($query1, $query2);
        $query3         = new Query\Range('\baz_field', '5', '8', false, true);
        $queryRight     = new Query\BooleanNot($query3);
        $query          = new Query\BooleanAnd($queryLeft, $queryRight);
        $string         = $this->parser->queryToString($query);
        $expected       = '((\foo_field:"foo value") OR (\bar_field:"bar*")) AND (NOT (\baz_field:{"5" TO "8"]))';
        $this->assertEquals($expected, $string);
    }

    public function testQueryToStringWoFieldNames()
    {
        $query1         = new Query\Term(new IndexerTerm('foo value'));
        $query2         = new Query\Wildcard(new IndexerTerm('bar*'));
        $queryLeft      = new Query\BooleanOr($query1, $query2);
        $query3         = new Query\Range('\baz_field', '5', '8', false, true);
        $queryRight     = new Query\BooleanNot($query3);
        $query          = new Query\BooleanAnd($queryLeft, $queryRight);
        $string         = $this->parser->queryToString($query);
        $expected       = '(("foo value") OR ("bar*")) AND (NOT (\baz_field:{"5" TO "8"]))';
        $this->assertEquals($expected, $string);
    }

    public function testStringToQuery()
    {
        $string         = '(\foo_field:"foo value" or \bar_field:"bar*") and not \baz_field:[5 to 8]';
        $query1         = new Query\Term(new IndexerTerm('foo value', '\foo_field'));
        $query2         = new Query\Wildcard(new IndexerTerm('bar*', '\bar_field'));
        $queryLeft      = new Query\BooleanOr($query1, $query2);
        $query3         = new Query\Range('\baz_field', '5', '8', true, true);
        $queryRight     = new Query\BooleanNot($query3);
        $queryExpected  = new Query\BooleanAnd($queryLeft, $queryRight);
        $tokens         = array(
        );
        $tokensRpn      = array(
            new Token(TokenInterface::TYPE_FIELD_NAME, '\foo_field', null, '\foo_field'),
            new Token(TokenInterface::TYPE_STRING_LITERAL, '"foo value"', null, 'foo value'),
            new Token(TokenInterface::TYPE_OPERATOR, ':', null, ':'),
            new Token(TokenInterface::TYPE_FIELD_NAME, '\bar_field', null, '\bar_field'),
            new Token(TokenInterface::TYPE_STRING_LITERAL, '"bar*"', null, 'bar*'),
            new Token(TokenInterface::TYPE_OPERATOR, ':', null, ':'),
            new Token(TokenInterface::TYPE_OPERATOR, 'or', null, 'OR'),
            new Token(TokenInterface::TYPE_FIELD_NAME, '\baz_field', null, '\baz_field'),
            new Token(TokenInterface::TYPE_RANGE_LITERAL, '[5 to 8]', null, '[5 TO 8]'),
            new Token(TokenInterface::TYPE_OPERATOR, ':', null, ':'),
            new Token(TokenInterface::TYPE_OPERATOR, 'not', null, 'NOT'),
            new Token(TokenInterface::TYPE_OPERATOR, 'and', null, 'AND'),
        );
        $this->lexer->expects($this->once())
            ->method('tokenize')
            ->with($this->equalTo($string))
            ->will($this->returnValue($tokens));
        $this->rpnConvertor->expects($this->once())
            ->method('getRpn')
            ->with($this->equalTo($tokens))
            ->will($this->returnValue($tokensRpn));
        $query          = $this->parser->stringToQuery($string);

        $this->assertEquals($queryExpected, $query);
    }
}
