<?php

namespace TwigIncludeDir;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;
use Twig\Compiler;
use Twig\Error\LoaderError;
use Twig\Loader\FilesystemLoader;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\IncludeNode;
use Twig\Node\Node;
use Twig\Node\NodeOutputInterface;

/**
 * Class IncludeDirNode
 *
 * @package TwigIncludeDir
 */
class IncludeDirNode extends Node implements NodeOutputInterface
{
    public function __construct(
        AbstractExpression $expr,
        AbstractExpression $variables = null,
        bool $recursive = false,
        bool $only = false,
        int $lineno = 0,
        string $tag = ''
    ) {
        $nodes = ['expr' => $expr];
        if (null !== $variables) {
            $nodes['variables'] = $variables;
        }

        parent::__construct(
            $nodes,
            [
                'recursive' => $recursive,
                'only' => $only
            ],
            $lineno,
            $tag
        );
    }

    /**
     * @param Compiler $compiler
     *
     * @throws LoaderError
     */
    public function compile(Compiler $compiler): void
    {
        $loader = $compiler->getEnvironment()->getLoader();

        if (!$loader instanceof FilesystemLoader) {
            throw new LoaderError('IncludeDir is only supported for filesystem loader!');
        }

        $includePath = '';
        $loaderPath  = '';
        foreach ($loader->getPaths() as $path) {
            if (is_dir($path . $this->getNode('expr')->getAttribute('value'))) {
                $includePath = $path . $this->getNode('expr')->getAttribute('value');
                $loaderPath  = $path;
            }
        }

        if (empty($includePath)) {
            throw new LoaderError(
                sprintf(
                    'Unable to find template "%s" (looked into: %s).',
                    $this->getNode('expr')->getAttribute('value'),
                    implode(', ', $loader->getPaths())
                )
            );
        }

        if ($this->getAttribute('recursive')) {
            $directory = new RecursiveDirectoryIterator($includePath);
            $iterator = new RecursiveIteratorIterator($directory);
            $foundFiles = new RegexIterator($iterator, '/^.+\.twig$/i', RecursiveRegexIterator::GET_MATCH);

            $files = [];
            foreach ($foundFiles as $file) {
                $files[] = $file[0];
            }
        } else {
            $files = glob($includePath . '/*.twig');
        }

        sort($files);

        foreach ($files as $file) {
            $file = str_replace(DIRECTORY_SEPARATOR, '/', str_replace($loaderPath, '', $file));
            $template = new IncludeNode(
                new ConstantExpression($file, $this->lineno),
                $this->hasNode('variables') ? $this->getNode('variables') : null,
                $this->getAttribute('only'),
                false,
                $this->lineno
            );

            $template->compile($compiler);
        }
    }
}
