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
            ' \\foo\\x : "ab\\()[]c and üÜöÖäÄ" and ( \\bar\\y: "def" Or \\baz\\z : "bat" ) OR "bazbat" AND \\rng\\r : [qqq to zzz] ';
        $inputStr2  =
            '\\foo\\x:"ab\\()[]c and üÜöÖäÄ" and (\\bar\\y:"def" Or \\baz\\z:"bat") OR "bazbat" AND \\rng\\r:[qqq to zzz]';
        $expectedTokens1 = array(
            new Token(Token::TYPE_FIELD_NAME, '\\foo\\x', 1, '\\foo\\x'),
            new Token(Token::TYPE_OPERATOR, ':', 8, ':'),
            new Token(Token::TYPE_STRING_LITERAL, '"ab\\()[]c and üÜöÖäÄ"', 10, 'ab\\()[]c and üÜöÖäÄ'),
            new Token(Token::TYPE_OPERATOR, 'and', 32, 'AND'),
            new Token(Token::TYPE_LEFT_PARENTHESIS, '(', 36, '('),
            new Token(Token::TYPE_FIELD_NAME, '\\bar\\y', 38, '\\bar\\y'),
            new Token(Token::TYPE_OPERATOR, ':', 44, ':'),
            new Token(Token::TYPE_STRING_LITERAL, '"def"', 46, 'def'),
            new Token(Token::TYPE_OPERATOR, 'Or', 52, 'OR'),
            new Token(Token::TYPE_FIELD_NAME, '\\baz\\z', 55, '\\baz\\z'),
            new Token(Token::TYPE_OPERATOR, ':', 62, ':'),
            new Token(Token::TYPE_STRING_LITERAL, '"bat"', 64, 'bat'),
            new Token(Token::TYPE_RIGHT_PARENTHESIS, ')', 70, ')'),
            new Token(Token::TYPE_OPERATOR, 'OR', 72, 'OR'),
            new Token(Token::TYPE_STRING_LITERAL, '"bazbat"', 75, 'bazbat'),
            new Token(Token::TYPE_OPERATOR, 'AND', 84, 'AND'),
            new Token(Token::TYPE_FIELD_NAME, '\\rng\\r', 88, '\\rng\\r'),
            new Token(Token::TYPE_OPERATOR, ':', 95, ':'),
            new Token(Token::TYPE_RANGE_LITERAL, '[qqq to zzz]', 97, '[qqq TO zzz]'),
        );
        $expectedTokens2 = array(
            new Token(Token::TYPE_FIELD_NAME, '\\foo\\x', 0, '\\foo\\x'),
            new Token(Token::TYPE_OPERATOR, ':', 6, ':'),
            new Token(Token::TYPE_STRING_LITERAL, '"ab\\()[]c and üÜöÖäÄ"', 7, 'ab\\()[]c and üÜöÖäÄ'),
            new Token(Token::TYPE_OPERATOR, 'and', 29, 'AND'),
            new Token(Token::TYPE_LEFT_PARENTHESIS, '(', 33, '('),
            new Token(Token::TYPE_FIELD_NAME, '\\bar\\y', 34, '\\bar\\y'),
            new Token(Token::TYPE_OPERATOR, ':', 40, ':'),
            new Token(Token::TYPE_STRING_LITERAL, '"def"', 41, 'def'),
            new Token(Token::TYPE_OPERATOR, 'Or', 47, 'OR'),
            new Token(Token::TYPE_FIELD_NAME, '\\baz\\z', 50, '\\baz\\z'),
            new Token(Token::TYPE_OPERATOR, ':', 56, ':'),
            new Token(Token::TYPE_STRING_LITERAL, '"bat"', 57, 'bat'),
            new Token(Token::TYPE_RIGHT_PARENTHESIS, ')', 62, ')'),
            new Token(Token::TYPE_OPERATOR, 'OR', 64, 'OR'),
            new Token(Token::TYPE_STRING_LITERAL, '"bazbat"', 67, 'bazbat'),
            new Token(Token::TYPE_OPERATOR, 'AND', 76, 'AND'),
            new Token(Token::TYPE_FIELD_NAME, '\\rng\\r', 80, '\\rng\\r'),
            new Token(Token::TYPE_OPERATOR, ':', 86, ':'),
            new Token(Token::TYPE_RANGE_LITERAL, '[qqq to zzz]', 87, '[qqq TO zzz]'),
        );
        $tokens     = $this->lexer->tokenize($inputStr1);
        $this->assertEquals($expectedTokens1, $tokens);
        $tokens     = $this->lexer->tokenize($inputStr2);
        $this->assertEquals($expectedTokens2, $tokens);
    }

}
