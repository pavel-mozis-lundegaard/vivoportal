<?php
namespace Vivo\Indexer\Query\Parser;

use Vivo\Indexer\Query;
use Vivo\Indexer\Term as IndexerTerm;
use Vivo\Indexer\QueryBuilder;

/**
 * Parser
 */
class Parser implements ParserInterface
{
    /**
     * Lexer
     * @var LexerInterface
     */
    protected $lexer;

    /**
     * RPN convertor
     * @var RpnConvertorInterface
     */
    protected $rpnConvertor;

    /**
     * Query Builder
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * Constructor
     * @param LexerInterface $lexer
     * @param RpnConvertorInterface $rpnConvertor
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(LexerInterface $lexer, RpnConvertorInterface $rpnConvertor, QueryBuilder $queryBuilder)
    {
        $this->lexer        = $lexer;
        $this->rpnConvertor = $rpnConvertor;
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * Unserializes string representation of query into the Query object
     * @param $string
     * @return Query\QueryInterface
     */
    public function stringToQuery($string)
    {
        $tokens     = $this->lexer->tokenize($string);
        $tokensRpn  = $this->rpnConvertor->getRpn($tokens);
        $query      = $this->rpnToQuery($tokensRpn);
        return $query;
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
                $string = sprintf('%s:"%s"', $term->getField(), $term->getText());
            } else {
                //Field not specified
                $string = sprintf('"%s"', $term->getText());
            }
        } elseif ($query instanceof Query\WildcardInterface) {
            //Wildcard query
            /** @var $query \Vivo\Indexer\Query\WildcardInterface */
            $term   = $query->getPattern();
            if ($term->getField()) {
                //Field specified
                $string = sprintf('%s:"%s"', $term->getField(), $term->getText());
            } else {
                //Field not specified
                $string = sprintf('"%s"', $term->getText());
            }
        } elseif ($query instanceof Query\RangeInterface) {
            //Range query
            /** @var $query \Vivo\Indexer\Query\RangeInterface */
            $leftBracket    = $query->isLowerLimitInclusive() ? '[' : '{';
            $rightBracket   = $query->isUpperLimitInclusive() ? ']' : '}';
            $lowerLimit     = is_null($query->getLowerLimit()) ? '*' : $query->getLowerLimit();
            $upperLimit     = is_null($query->getUpperLimit()) ? '*' : $query->getUpperLimit();
            $string         = sprintf('%s:%s"%s" TO "%s"%s', $query->getField(),
                $leftBracket, $lowerLimit, $upperLimit, $rightBracket);
        } elseif ($query instanceof Query\BooleanAnd) {
            //BooleanAnd query
            /** @var $query \Vivo\Indexer\Query\BooleanAnd */
            $stringLeft     = $this->queryToString($query->getQueryLeft());
            $stringRight    = $this->queryToString($query->getQueryRight());
            $string         = sprintf('(%s) AND (%s)', $stringLeft, $stringRight);
        } elseif ($query instanceof Query\BooleanOr) {
            //BooleanOr query
            /** @var $query \Vivo\Indexer\Query\BooleanOr */
            $stringLeft     = $this->queryToString($query->getQueryLeft());
            $stringRight    = $this->queryToString($query->getQueryRight());
            $string         = sprintf('(%s) OR (%s)', $stringLeft, $stringRight);
        } elseif ($query instanceof Query\BooleanNot) {
            //BooleanNot query
            /** @var $query \Vivo\Indexer\Query\BooleanNot */
            $str            = $this->queryToString($query->getQuery());
            $string         = sprintf('NOT (%s)', $str);
        } else {
            //Unsupported query type
            throw new Exception\UnsupportedQueryTypeException(sprintf('%s: Unsupported query type', __METHOD__));
        }
        return $string;
    }

    /**
     * Converts stream of tokens in RPN to a Query object
     * @param TokenInterface[] $tokens
     * @throws Exception\IllegalSyntaxException
     * @throws Exception\FieldNameExpectedException
     * @throws Exception\UnsupportedOperatorException
     * @return Query\QueryInterface
     */
    protected function rpnToQuery(array $tokens)
    {
        $stack  = array();
        /** @var $token TokenInterface */
        while ($token = array_shift($tokens)) {
            if ($token->getType() == TokenInterface::TYPE_OPERATOR) {
                //Operator
                switch ($token->getValue()) {
                    case ':':
                        /** @var $fieldCondTok TokenInterface */
                        $fieldCondTok   = $this->popFromStack($stack);
                        /** @var $fieldNameTok TokenInterface */
                        $fieldNameTok   = $this->popFromStack($stack);
                        if ($fieldNameTok->getType() != TokenInterface::TYPE_FIELD_NAME) {
                            throw new Exception\FieldNameExpectedException(
                                sprintf('%s: A field name expected', __METHOD__));
                        }
                        $fieldCondVal   = $fieldCondTok->getValue();
                        $query          = $this->queryBuilder->cond($fieldCondVal, $fieldNameTok->getValue());
                        $stack[]        = $query;
                        break;
                    case 'AND':
                        $right      = $this->getLogicalOperatorOperand($stack);
                        $left       = $this->getLogicalOperatorOperand($stack);
                        $query      = new Query\BooleanAnd($left, $right);
                        $stack[]    = $query;
                        break;
                    case 'OR':
                        $right      = $this->getLogicalOperatorOperand($stack);
                        $left       = $this->getLogicalOperatorOperand($stack);
                        $query      = new Query\BooleanOr($left, $right);
                        $stack[]    = $query;
                        break;
                    case 'NOT':
                        $operand    = $this->getLogicalOperatorOperand($stack);
                        $query      = new Query\BooleanNot($operand);
                        $stack[]    = $query;
                        break;
                    default:
                        throw new Exception\UnsupportedOperatorException(
                            sprintf("%s: Unsupported operator '%s'", __METHOD__, $token->getValue()));
                        break;
                }
            } else {
                //Operand
                $stack[]    = $token;
            }
        }
        $query  = $this->popFromStack($stack);
        if (!$query instanceof Query\QueryInterface || count($stack)) {
            //Either something else than QueryInterface was on stack or stack contains more items
            throw new Exception\IllegalSyntaxException(sprintf('%s: Illegal syntax', __METHOD__));
        }
        return $query;
    }

    /**
     * Pops an item from stack, if the stack is empty, throws an exception
     * @param array $stack
     * @return mixed
     * @throws Exception\IllegalSyntaxException
     */
    protected function popFromStack(array &$stack)
    {
        $pop    = array_pop($stack);
        if (is_null($pop)) {
            throw new Exception\IllegalSyntaxException(sprintf('%s: Illegal syntax', __METHOD__));
        }
        return $pop;
    }

    /**
     * Pops a logical operator operand from the stack and returns it
     * Such operand may be either a QueryInterface object or a string literal Token
     * If a string literal Token is popped from the stack, a query is constructed from it
     * @param array $stack
     * @return Query\QueryInterface
     * @throws Exception\IllegalSyntaxException
     */
    protected function getLogicalOperatorOperand(array &$stack)
    {
        $operand    = $this->popFromStack($stack);
        if (($operand instanceof TokenInterface)
            && ($operand->getType() == TokenInterface::TYPE_STRING_LITERAL)) {
            //We have a string literal as an operand, i.e. field name is not specified
            //Create a query from the string literal
            $val    = $operand->getValue();
            $operand    = $this->queryBuilder->cond($val);
        }
        if (!$operand instanceof Query\QueryInterface) {
            throw new Exception\IllegalSyntaxException(sprintf('%s: Illegal syntax', __METHOD__));
        }
        return $operand;
    }
}
