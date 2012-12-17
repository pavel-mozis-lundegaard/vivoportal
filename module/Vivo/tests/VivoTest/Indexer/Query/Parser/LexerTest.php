<?php
namespace VivoTest\Indexer\Query\Parser;

use Vivo\Indexer\Query\Parser\Parser;
use Vivo\Indexer\Query;
use Vivo\Indexer\Term as IndexerTerm;
use Vivo\Indexer\Query\Parser\Lexer;

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
        $inputStr   = ' $foo : "abc AND üÜöÖäÄ" AND ( $bar: "def" OR $baz : "bat" ) OR "bazbat" AND $rng:[qqq TO zzz] ';
        $tokens     = $this->lexer->tokenize($inputStr);
        \Zend\Debug\Debug::dump($tokens);
    }


}
