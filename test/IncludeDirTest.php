<?php

namespace TwigIncludeDir\Tests;

use PHPUnit\Framework\TestCase;
use Twig_Environment;
use Twig_Loader_Filesystem;
use TwigIncludeDir\IncludeDirTokenParser;

class IncludeDirTest extends TestCase
{
    /** @var Twig_Environment */
    protected $twig;

    public function setUp()
    {
        $loader = new Twig_Loader_Filesystem(__DIR__ . DIRECTORY_SEPARATOR);
        $this->twig = new Twig_Environment($loader);
        $this->twig->addTokenParser(new IncludeDirTokenParser());
    }

    public function testIncludeDir()
    {
        $output = $this->twig->render('/templates/includeDirTest.twig');
        echo $output;
    }
}