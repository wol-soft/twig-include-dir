<?php

declare(strict_types = 1);

namespace TwigIncludeDir;

use Twig\Error\SyntaxError;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * Class IncludeDirTokenParser
 *
 * @package TwigIncludeDir
 */
class IncludeDirTokenParser extends AbstractTokenParser
{
    /**
     * Parses a token and returns a node.
     *
     * @param Token $token
     *
     * @return Node
     *
     * @throws SyntaxError
     */
    public function parse(Token $token): Node
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

    /**
     * @return array
     *
     * @throws SyntaxError
     */
    protected function parseArguments()
    {
        $stream = $this->parser->getStream();

        $recursive = false;
        if ($stream->nextIf(Token::NAME_TYPE, 'recursive')) {
            $recursive = true;
        }

        $variables = null;
        if ($stream->nextIf(Token::NAME_TYPE, 'with')) {
            $variables = $this->parser->getExpressionParser()->parseExpression();
        }

        $only = false;
        if ($stream->nextIf(Token::NAME_TYPE, 'only')) {
            $only = true;
        }

        $stream->expect(Token::BLOCK_END_TYPE);

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
