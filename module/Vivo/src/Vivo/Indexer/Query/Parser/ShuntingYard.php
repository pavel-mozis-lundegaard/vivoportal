<?php
namespace Vivo\Indexer\Query\Parser;

/**
 * ShuntingYard
 * RPN (Reverse Polish Notation) conversion
 * @see http://en.wikipedia.org/wiki/Shunting-yard_algorithm
 */
class ShuntingYard implements RpnConvertorInterface
{
    /**
     * Operator precedence and associativity
     * rAssoc = Right Associative
     * @var array
     */
    protected $operatorPrec = array(
        ':'     => array(
            'prec'      => 10,
            'rAssoc'    => true,
        ),
        'NOT'   => array(
            'prec'      => 8,
            'rAssoc'    => true,
        ),
        'AND'   => array(
            'prec'      => 6,
            'rAssoc'    => false,
        ),
        'OR'   => array(
            'prec'      => 4,
            'rAssoc'    => false,
        ),
    );

    /**
     * Converts the stream of tokens into RPN (ReversePolishNotation)
     * @param TokenInterface[] $tokens
     * @throws Exception\UnsupportedTokenTypeException
     * @throws Exception\ParenthesesMismatchException
     * @return TokenInterface[]
     */
    public function getRpn(array $tokens)
    {
        $output = array();
        $stack  = array();
        /** @var $token TokenInterface */
        while ($token = array_shift($tokens))
        {
            switch ($token->getType()) {
                case TokenInterface::TYPE_OPERATOR:
                    //Operators
                    while ($this->shouldPopOpFromStack($token, $stack)) {
                        $output[]   = array_pop($stack);
                    }
                    $stack[]    = $token;
                    break;
                case TokenInterface::TYPE_FIELD_NAME:
                case TokenInterface::TYPE_RANGE_LITERAL:
                case TokenInterface::TYPE_STRING_LITERAL:
                    //Operands -> put them to the output queue
                    $output[]   = $token;
                    break;
                case TokenInterface::TYPE_LEFT_PARENTHESIS:
                    //Left parenthesis
                    $stack[]    = $token;
                    break;
                case TokenInterface::TYPE_RIGHT_PARENTHESIS:
                    //Right parenthesis
                    /** @var $stackTop TokenInterface */
                    $stackTop   = array_pop($stack);
                    while ($stackTop && ($stackTop->getType() != TokenInterface::TYPE_LEFT_PARENTHESIS)) {
                        $output[]   = $stackTop;
                        $stackTop   = array_pop($stack);
                    }
                    if (!$stackTop) {
                        throw new Exception\ParenthesesMismatchException(
                            sprintf('%s: Mismatched parentheses', __METHOD__));
                    }
                    break;
                default:
                    throw new Exception\UnsupportedTokenTypeException(
                        sprintf("%s: Unsupported token type '%s'", __METHOD__, $token->getType()));
                    break;
            }
        }
        /** @var $token TokenInterface */
        while ($token = array_pop($stack)) {
            if ($token->getType() == TokenInterface::TYPE_LEFT_PARENTHESIS) {
                throw new Exception\ParenthesesMismatchException(sprintf('%s: Mismatched parentheses', __METHOD__));
            }
            $output[]   = $token;
        }
        return $output;
    }

    /**
     * Returns the top token from a stack
     * If the stack is empty, returns false
     * This method does not pop any tokens from the stack
     * @param TokenInterface[] $stack
     * @return TokenInterface|bool
     */
    protected function getFromStack(array &$stack)
    {
        $len    = count($stack);
        if ($len > 0) {
            $token  = $stack[$len-1];
        } else {
            $token  = false;
        }
        return $token;
    }

    /**
     * Returns if an operator token should be popped form the stack
     * This is a convenience function which just isolates the logical condition
     * @param TokenInterface $operator
     * @param TokenInterface[] $stack
     * @return bool
     */
    protected function shouldPopOpFromStack(TokenInterface $operator, array &$stack)
    {
        $stackTop   = $this->getFromStack($stack);
        if ($stackTop) {
            //Stack is not empty
            if ($stackTop->getType() != TokenInterface::TYPE_OPERATOR) {
                //The token at the top of the stack is not an operator
                return false;
            }
            //The token at the top of the stack is an operator
            $o1Props    = $this->operatorPrec[$operator->getValue()];
            $o2Props    = $this->operatorPrec[$stackTop->getValue()];
            if (((!$o1Props['rAssoc']) && ($o1Props['prec'] <= $o2Props['prec']))
                || ($o1Props['prec'] < $o2Props['prec'])) {
                return true;
            }
            return false;
        }
        return false;
    }
}
