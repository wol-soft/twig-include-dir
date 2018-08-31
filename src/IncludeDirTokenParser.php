<?php

namespace TwigIncludeDir;

use Twig_Error_Syntax;
use Twig_Node;
use Twig_Token;
use Twig_TokenParser;

/**
 * Class IncludeDirTokenParser
 *
 * @package TwigIncludeDir
 */
class IncludeDirTokenParser extends Twig_TokenParser
{
    /**
     * Parses a token and returns a node.
     *
     * @param Twig_Token $token
     *
     * @return Twig_Node A Twig_Node instance
     *
     * @throws Twig_Error_Syntax
     */
    public function parse(Twig_Token $token)
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();

        list($recursive, $variables, $only) = $this->parseArguments();

        return new IncludeDirNode(
            $expr,
            $variables,
            $recursive,
            $only,
            $token->getLine(),
            $this->getTag()
        );
    }

    protected function parseArguments()
    {
        $stream = $this->parser->getStream();

        $recursive = false;
        if ($stream->nextIf(Twig_Token::NAME_TYPE, 'recursive')) {
            $recursive = true;
        }

        $variables = null;
        if ($stream->nextIf(Twig_Token::NAME_TYPE, 'with')) {
            $variables = $this->parser->getExpressionParser()->parseExpression();
        }

        $only = false;
        if ($stream->nextIf(Twig_Token::NAME_TYPE, 'only')) {
            $only = true;
        }

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return array($recursive, $variables, $only);
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag(): string
    {
        return 'includeDir';
    }
}
