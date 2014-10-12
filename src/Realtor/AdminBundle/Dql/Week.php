<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 11.10.14
 * Time: 20:30
 */

namespace Realtor\AdminBundle\Dql;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class Week extends FunctionNode
{
    public $expression;

    public function getSql(SqlWalker $sqlWalker)
    {
        return 'concat(date_part(\'week\', '.$this->expression->dispatch($sqlWalker).'), \'-неделя-\', date_part(\'year\', '.$this->expression->dispatch($sqlWalker).'))';
    }

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->expression = $parser->SimpleSelectExpression();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
} 