<?php
namespace VivoTest\Indexer\Query\Parser;

use Vivo\Indexer\Query\Parser\Lexer;
use Vivo\Indexer\Query\Parser\Token;

/**
 * LexerTest
 */
class LexerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Lexer
     */
    protected $lexer;

    protected function setUp()
    {
        $this->lexer    = new Lexer();
    }

    public function testLexer()
    {
        $inputStr1  =
            ' $foo : "ab$()[]c and üÜöÖäÄ" and ( $bar: "def" Or $baz : "bat" ) OR "bazbat" AND $rng : [qqq to zzz] ';
        $inputStr2  =
            '$foo:"ab$()[]c and üÜöÖäÄ" and ($bar:"def" Or $baz:"bat") OR "bazbat" AND $rng:[qqq to zzz]';
        $expectedTokens1 = array(
            new Token(Token::TYPE_FIELD_NAME, '$foo', 1, 'foo'),
            new Token(Token::TYPE_FIELD_LIKE, ':', 6, ':'),
            new Token(Token::TYPE_STRING_LITERAL, '"ab$()[]c and üÜöÖäÄ"', 8, 'ab$()[]c and üÜöÖäÄ'),
            new Token(Token::TYPE_OPERATOR, 'and', 30, 'AND'),
            new Token(Token::TYPE_LEFT_PARENTHESIS, '(', 34, '('),
            new Token(Token::TYPE_FIELD_NAME, '$bar', 36, 'bar'),
            new Token(Token::TYPE_FIELD_LIKE, ':', 40, ':'),
            new Token(Token::TYPE_STRING_LITERAL, '"def"', 42, 'def'),
            new Token(Token::TYPE_OPERATOR, 'Or', 48, 'OR'),
            new Token(Token::TYPE_FIELD_NAME, '$baz', 51, 'baz'),
            new Token(Token::TYPE_FIELD_LIKE, ':', 56, ':'),
            new Token(Token::TYPE_STRING_LITERAL, '"bat"', 58, 'bat'),
            new Token(Token::TYPE_RIGHT_PARENTHESIS, ')', 64, ')'),
            new Token(Token::TYPE_OPERATOR, 'OR', 66, 'OR'),
            new Token(Token::TYPE_STRING_LITERAL, '"bazbat"', 69, 'bazbat'),
            new Token(Token::TYPE_OPERATOR, 'AND', 78, 'AND'),
            new Token(Token::TYPE_FIELD_NAME, '$rng', 82, 'rng'),
            new Token(Token::TYPE_FIELD_LIKE, ':', 87, ':'),
            new Token(Token::TYPE_RANGE, '[qqq to zzz]', 89, '[qqq TO zzz]'),
        );
        $expectedTokens2 = array(
            new Token(Token::TYPE_FIELD_NAME, '$foo', 0, 'foo'),
            new Token(Token::TYPE_FIELD_LIKE, ':', 4, ':'),
            new Token(Token::TYPE_STRING_LITERAL, '"ab$()[]c and üÜöÖäÄ"', 5, 'ab$()[]c and üÜöÖäÄ'),
            new Token(Token::TYPE_OPERATOR, 'and', 27, 'AND'),
            new Token(Token::TYPE_LEFT_PARENTHESIS, '(', 31, '('),
            new Token(Token::TYPE_FIELD_NAME, '$bar', 32, 'bar'),
            new Token(Token::TYPE_FIELD_LIKE, ':', 36, ':'),
            new Token(Token::TYPE_STRING_LITERAL, '"def"', 37, 'def'),
            new Token(Token::TYPE_OPERATOR, 'Or', 43, 'OR'),
            new Token(Token::TYPE_FIELD_NAME, '$baz', 46, 'baz'),
            new Token(Token::TYPE_FIELD_LIKE, ':', 50, ':'),
            new Token(Token::TYPE_STRING_LITERAL, '"bat"', 51, 'bat'),
            new Token(Token::TYPE_RIGHT_PARENTHESIS, ')', 56, ')'),
            new Token(Token::TYPE_OPERATOR, 'OR', 58, 'OR'),
            new Token(Token::TYPE_STRING_LITERAL, '"bazbat"', 61, 'bazbat'),
            new Token(Token::TYPE_OPERATOR, 'AND', 70, 'AND'),
            new Token(Token::TYPE_FIELD_NAME, '$rng', 74, 'rng'),
            new Token(Token::TYPE_FIELD_LIKE, ':', 78, ':'),
            new Token(Token::TYPE_RANGE, '[qqq to zzz]', 79, '[qqq TO zzz]'),
        );
        $tokens     = $this->lexer->tokenize($inputStr1);
        $this->assertEquals($expectedTokens1, $tokens);
        $tokens     = $this->lexer->tokenize($inputStr2);
        $this->assertEquals($expectedTokens2, $tokens);
    }

}
