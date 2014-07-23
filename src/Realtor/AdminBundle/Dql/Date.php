<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 23.07.14
 * Time: 0:08
 */

namespace Realtor\AdminBundle\Dql;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class Date extends FunctionNode
{
    public $datetimeExpression;

    public function getSql(SqlWalker $sqlWalker)
    {
        return 'DATE('.$this->datetimeExpression->dispatch($sqlWalker).')';
    }

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->datetimeExpression = $parser->SimpleSelectExpression();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
} 