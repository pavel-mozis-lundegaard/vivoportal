<?php
namespace Vivo\Indexer\Query\Parser;

/**
 * Lexer
 */
class Lexer implements LexerInterface
{
    /**
     * Tokenizes input text (UTF-8 encoded)
     * @param $text
     * @return TokenInterface[]
     */
    public function tokenize($text)
    {
        $mbIntEnc   = mb_internal_encoding();
        mb_internal_encoding('utf-8');
        $tokens = $this->scan($text);
        $this->evaluate($tokens);
        mb_internal_encoding($mbIntEnc);
        return $tokens;
    }

    /**
     * Scans the input text and creates an array of tokens from it
     * @param string $inputStr
     * @return TokenInterface[]
     */
    protected function scan($inputStr)
    {
        $strLen     = mb_strlen($inputStr);
        $pos        = 0;
        $tokens     = array();
        while ($pos < $strLen) {
            $char   = mb_substr($inputStr, $pos, 1, 'utf-8');
            if ($char == '(') {
                $token      = new Token(TokenInterface::TYPE_LEFT_PARENTHESIS, $char, $pos);
                $tokens[]   = $token;
                $pos++;
            } elseif ($char == ')') {
                $token      = new Token(TokenInterface::TYPE_RIGHT_PARENTHESIS, $char, $pos);
                $tokens[]   = $token;
                $pos++;
            } elseif ($char == ':') {
                $token      = new Token(TokenInterface::TYPE_FIELD_LIKE, $char, $pos);
                $tokens[]   = $token;
                $pos++;
            } elseif ($char == ' ') {
                //Skip whitespace
                $pos++;
            } elseif ($char == '$') {
                $lexemePos  = $pos;
                $fieldName  = $this->readFieldName($inputStr, $strLen, $pos);
                $token      = new Token(TokenInterface::TYPE_FIELD_NAME, $fieldName, $lexemePos);
                $tokens[]   = $token;
            } elseif ($char == '"') {
                $lexemePos  = $pos;
                $stringLit  = $this->readStringLiteral($inputStr, $strLen, $pos);
                $token      = new Token(TokenInterface::TYPE_STRING_LITERAL, $stringLit, $lexemePos);
                $tokens[]   = $token;
            } elseif ($char == '[') {
                $lexemePos  = $pos;
                $range      = $this->readRange($inputStr, $strLen, $pos);
                $token      = new Token(TokenInterface::TYPE_RANGE, $range, $lexemePos);
                $tokens[]   = $token;
            } else {
                $lexemePos  = $pos;
                $operator   = $this->readOperator($inputStr, $strLen, $pos);
                $token      = new Token(TokenInterface::TYPE_OPERATOR, $operator, $lexemePos);
                $tokens[]   = $token;
            }
        }
        return $tokens;
    }

    protected function readFieldName($inputStr, $len, &$pos)
    {
        $cont       = true;
        $fieldName  = '';
        while ($cont) {
            $char  = $this->getCharAt($inputStr, $len, $pos);
            if (!is_null($char) && $char != ' ' && $char != ':') {
                $fieldName  .= $char;
                $pos++;
            } else {
                $cont   = false;
            }
        }
        return $fieldName;
    }

    protected function readStringLiteral($inputStr, $len, &$pos)
    {
        $cont       = true;
        //Store the opening quote
        $stringLit  = $this->getCharAt($inputStr, $len, $pos);
        while ($cont) {
            $pos++;
            $char  = $this->getCharAt($inputStr, $len, $pos);
            if (!is_null($char) && $char != '"') {
                $stringLit  .= $char;
            } else {
                $cont   = false;
                if ($char == '"') {
                    //Store the closing quote
                    $stringLit  .= $char;
                    $pos++;
                }
            }
        }
        return $stringLit;
    }

    protected function readRange($inputStr, $len, &$pos)
    {
        $cont       = true;
        //Store the opening bracket [
        $range      = $this->getCharAt($inputStr, $len, $pos);
        while ($cont) {
            $pos++;
            $char  = $this->getCharAt($inputStr, $len, $pos);
            if (!is_null($char) && $char != ']') {
                $range  .= $char;
            } else {
                $cont   = false;
                if ($char == ']') {
                    //Store the closing bracket
                    $range  .= $char;
                    $pos++;
                }
            }
        }
        return $range;
    }

    protected function readOperator($inputStr, $len, &$pos)
    {
        $cont       = true;
        $operator   = '';
        while ($cont) {
            $char  = $this->getCharAt($inputStr, $len, $pos);
            if (is_null($char) || $char == ' ' || $char == '(' || $char == ')') {
                $cont   = false;
            } else {
                $operator  .= $char;
                $pos++;
            }
        }
        return $operator;
    }

    protected function getCharAt($inputStr, $len, $pos)
    {
        if ($pos >= $len) {
            $char  = null;
        } else {
            $char  = mb_substr($inputStr, $pos, 1);
        }
        return $char;
    }

    /**
     * Evaluates tokens and sets their value
     * @param TokenInterface[] $tokens
     * @throws Exception\UnsupportedTokenTypeException
     * @return void
     */
    protected function evaluate(array &$tokens)
    {
        /** @var $tokens TokenInterface[] */
        foreach ($tokens as $key => $token) {
           switch ($token->getType()) {
               case TokenInterface::TYPE_FIELD_LIKE:
                   $tokens[$key]->setValue($token->getLexeme());
                   break;
               case TokenInterface::TYPE_FIELD_NAME:
                   $fieldName   = mb_substr($token->getLexeme(), 1);
                   $tokens[$key]->setValue($fieldName);
                   break;
               case TokenInterface::TYPE_LEFT_PARENTHESIS:
                   $tokens[$key]->setValue($token->getLexeme());
                   break;
               case TokenInterface::TYPE_OPERATOR:
                   $tokens[$key]->setValue($token->getLexeme());
                   break;
               case TokenInterface::TYPE_RANGE:
                   $tokens[$key]->setValue($token->getLexeme());
                   break;
               case TokenInterface::TYPE_RIGHT_PARENTHESIS:
                   $tokens[$key]->setValue($token->getLexeme());
                   break;
               case TokenInterface::TYPE_STRING_LITERAL:
                   $len     = mb_strlen($token->getLexeme());
                   $value   = mb_substr($token->getLexeme(), 1, $len-2);
                   $tokens[$key]->setValue($value);
                   break;
               default:
                   throw new Exception\UnsupportedTokenTypeException(
                       sprintf("%s: Unsupported token type ''", __METHOD__, $token->getType()));
                   break;
           }
       }
    }
}