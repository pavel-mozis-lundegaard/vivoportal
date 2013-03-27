<?php
namespace VivoTest\Indexer\Query\Parser;

use Vivo\Indexer\Query\Parser\ShuntingYard;
use Vivo\Indexer\Query\Parser\Token;
use Vivo\Indexer\Query\Parser\TokenInterface;

/**
 * ShuntingYardTest
 */
class ShuntingYardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ShuntingYard
     */
    protected $shuntingYard;

    protected function setUp()
    {
        $this->shuntingYard = new ShuntingYard();
    }

    public function testGetRpn()
    {
        $tokenFieldA    = new Token(TokenInterface::TYPE_FIELD_NAME, 'field_a', null, 'field_a');
        $tokenFieldB    = new Token(TokenInterface::TYPE_FIELD_NAME, 'field_b', null, 'field_b');
        $tokenFieldLike = new Token(TokenInterface::TYPE_OPERATOR, ':', null, ':');
        $tokenStrLitFoo = new Token(TokenInterface::TYPE_STRING_LITERAL, '"foo"', null, 'foo');
        $tokenStrLitBar = new Token(TokenInterface::TYPE_STRING_LITERAL, '"bar"', null, 'bar');
        $tokenAnd       = new Token(TokenInterface::TYPE_OPERATOR, 'and', null, 'AND');
        $tokenOr        = new Token(TokenInterface::TYPE_OPERATOR, 'or', null, 'OR');
        $tokenNot       = new Token(TokenInterface::TYPE_OPERATOR, 'not', null, 'NOT');
        $tokenRange     = new Token(TokenInterface::TYPE_RANGE_LITERAL, '[100 to 200]', null, '[100 TO 200]');
        $tokenLp        = new Token(TokenInterface::TYPE_LEFT_PARENTHESIS, '(', null, '(');
        $tokenRp        = new Token(TokenInterface::TYPE_RIGHT_PARENTHESIS, ')', null, ')');
        $tokens         = array(
            $tokenLp,
            $tokenFieldA,
            $tokenFieldLike,
            $tokenStrLitFoo,
            $tokenOr,
            $tokenNot,
            $tokenFieldB,
            $tokenFieldLike,
            $tokenStrLitBar,
            $tokenRp,
            $tokenAnd,
            $tokenFieldA,
            $tokenFieldLike,
            $tokenRange,
        );
        $expectedRpn    = array(
            $tokenFieldA,
            $tokenStrLitFoo,
            $tokenFieldLike,
            $tokenFieldB,
            $tokenStrLitBar,
            $tokenFieldLike,
            $tokenNot,
            $tokenOr,
            $tokenFieldA,
            $tokenRange,
            $tokenFieldLike,
            $tokenAnd,
        );
        $rpn    = $this->shuntingYard->getRpn($tokens);
        $this->assertEquals($expectedRpn, $rpn);
    }

    public function testParenthesesMismatch1()
    {
        $tokenFieldA    = new Token(TokenInterface::TYPE_FIELD_NAME, 'field_a', null, 'field_a');
        $tokenFieldB    = new Token(TokenInterface::TYPE_FIELD_NAME, 'field_b', null, 'field_b');
        $tokenFieldLike = new Token(TokenInterface::TYPE_OPERATOR, ':', null, ':');
        $tokenStrLitFoo = new Token(TokenInterface::TYPE_STRING_LITERAL, '"foo"', null, 'foo');
        $tokenStrLitBar = new Token(TokenInterface::TYPE_STRING_LITERAL, '"bar"', null, 'bar');
        $tokenAnd       = new Token(TokenInterface::TYPE_OPERATOR, 'and', null, 'AND');
        $tokenOr        = new Token(TokenInterface::TYPE_OPERATOR, 'or', null, 'OR');
        $tokenNot       = new Token(TokenInterface::TYPE_OPERATOR, 'not', null, 'NOT');
        $tokenRange     = new Token(TokenInterface::TYPE_RANGE_LITERAL, '[100 to 200]', null, '[100 TO 200]');
        $tokenLp        = new Token(TokenInterface::TYPE_LEFT_PARENTHESIS, '(', null, '(');
        $tokenRp        = new Token(TokenInterface::TYPE_RIGHT_PARENTHESIS, ')', null, ')');
        $tokens         = array(
            $tokenLp,
            $tokenFieldA,
            $tokenFieldLike,
            $tokenStrLitFoo,
            $tokenOr,
            $tokenNot,
            $tokenLp,
            $tokenFieldB,
            $tokenFieldLike,
            $tokenStrLitBar,
            $tokenRp,
            $tokenAnd,
            $tokenFieldA,
            $tokenFieldLike,
            $tokenRange,
        );
        $this->setExpectedException('Vivo\Indexer\Query\Parser\Exception\ParenthesesMismatchException');
        $rpn    = $this->shuntingYard->getRpn($tokens);
    }

    public function testParenthesesMismatch2()
    {
        $tokenFieldA    = new Token(TokenInterface::TYPE_FIELD_NAME, 'field_a', null, 'field_a');
        $tokenFieldB    = new Token(TokenInterface::TYPE_FIELD_NAME, 'field_b', null, 'field_b');
        $tokenFieldLike = new Token(TokenInterface::TYPE_OPERATOR, ':', null, ':');
        $tokenStrLitFoo = new Token(TokenInterface::TYPE_STRING_LITERAL, '"foo"', null, 'foo');
        $tokenStrLitBar = new Token(TokenInterface::TYPE_STRING_LITERAL, '"bar"', null, 'bar');
        $tokenAnd       = new Token(TokenInterface::TYPE_OPERATOR, 'and', null, 'AND');
        $tokenOr        = new Token(TokenInterface::TYPE_OPERATOR, 'or', null, 'OR');
        $tokenNot       = new Token(TokenInterface::TYPE_OPERATOR, 'not', null, 'NOT');
        $tokenRange     = new Token(TokenInterface::TYPE_RANGE_LITERAL, '[100 to 200]', null, '[100 TO 200]');
        $tokenLp        = new Token(TokenInterface::TYPE_LEFT_PARENTHESIS, '(', null, '(');
        $tokenRp        = new Token(TokenInterface::TYPE_RIGHT_PARENTHESIS, ')', null, ')');
        $tokens         = array(
            $tokenLp,
            $tokenFieldA,
            $tokenFieldLike,
            $tokenStrLitFoo,
            $tokenOr,
            $tokenNot,
            $tokenFieldB,
            $tokenFieldLike,
            $tokenStrLitBar,
            $tokenRp,
            $tokenAnd,
            $tokenFieldA,
            $tokenFieldLike,
            $tokenRange,
            $tokenRp,
        );
        $this->setExpectedException('Vivo\Indexer\Query\Parser\Exception\ParenthesesMismatchException');
        $rpn    = $this->shuntingYard->getRpn($tokens);
    }
}