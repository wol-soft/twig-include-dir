<?php

namespace TwigIncludeDir;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;
use Twig_Compiler;
use Twig_Error_Loader;
use Twig_Loader_Filesystem;
use Twig_Node;
use Twig_Node_Expression;
use Twig_Node_Expression_Constant;
use Twig_Node_Include;
use Twig_NodeOutputInterface;

/**
 * Class IncludeDirNode
 *
 * @package TwigIncludeDir
 */
class IncludeDirNode extends Twig_Node implements Twig_NodeOutputInterface
{
    public function __construct(
        Twig_Node_Expression $expr,
        Twig_Node_Expression $variables = null,
        $recursive = false,
        $only = false,
        $lineno, $tag = null
    ) {
        $nodes = ['expr' => $expr];
        if (null !== $variables) {
            $nodes['variables'] = $variables;
        }

        parent::__construct(
            $nodes,
            [
                'recursive' => (bool) $recursive,
                'only' => (bool) $only
            ],
            $lineno,
            $tag
        );
    }

    public function compile(Twig_Compiler $compiler)
    {
        $loader = $compiler->getEnvironment()->getLoader();

        if (!$loader instanceof Twig_Loader_Filesystem) {
            throw new Twig_Error_Loader('IncludeDir is only supported for filesystem loader!');
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
            throw new Twig_Error_Loader(
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

        foreach ($files as $file) {
            $file = str_replace(DIRECTORY_SEPARATOR, '/', str_replace($loaderPath, '', $file));
            $template = new Twig_Node_Include(
                new Twig_Node_Expression_Constant($file, $this->lineno),
                $this->hasNode('variables') ? $this->getNode('variables') : null,
                $this->getAttribute('only'),
                false,
                $this->lineno
            );

            $template->compile($compiler);
        }
    }
}
