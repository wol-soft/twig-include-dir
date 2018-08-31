<?php

namespace TwigIncludeDir\Tests;

use PHPUnit\Framework\TestCase;
use Twig_Environment;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Loader_Array;
use Twig_Loader_Filesystem;
use TwigIncludeDir\IncludeDirTokenParser;

class IncludeDirTest extends TestCase
{
    /** @var Twig_Environment */
    protected $twig;

    public function setUp()
    {
        $loader = new Twig_Loader_Filesystem(__DIR__ . DIRECTORY_SEPARATOR);
        $this->twig = new Twig_Environment($loader, ['strict_variables' => true]);
        $this->twig->addTokenParser(new IncludeDirTokenParser());
    }

    public function testIncludeDir()
    {
        $output = $this->twig->render('/templates/includeDir.twig');
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/Expectations/includeDir.html', $output);
    }

    public function testIncludeDirRecursive()
    {
        $output = $this->twig->render('/templates/includeDirRecursive.twig');
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/Expectations/includeDirRecursive.html', $output);
    }

    public function testIncludeDirWith()
    {
        $output = $this->twig->render('/templates/includeDirWith.twig');
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/Expectations/includeDir.html', $output);
    }

    public function testIncludeDirWithRecursive()
    {
        $output = $this->twig->render('/templates/includeDirWithRecursive.twig');
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/Expectations/includeDirRecursive.html', $output);
    }

    public function testIncludeDirVariableScope()
    {
        $output = $this->twig->render('/templates/includeDirVariableScope.twig');
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/Expectations/includeDir.html', $output);
    }

    public function testIncludeDirVariableScopeMissingVariable()
    {
        $this->expectException(Twig_Error_Runtime::class);
        $this->expectExceptionMessage('Variable "b" does not exist.');
        $this->twig->render('/templates/includeDirVariableScopeMissingVariable.twig');
    }

    public function testIncludeDirVariableScopeOnlyMissingVariable()
    {
        $this->expectException(Twig_Error_Runtime::class);
        $this->expectExceptionMessage('Variable "b" does not exist.');
        $this->twig->render('/templates/includeDirVariableScopeOnlyMissingVariable.twig');
    }

    public function testIncludeDirVariableScopeRecursiveMissingVariable()
    {
        $this->expectException(Twig_Error_Runtime::class);
        $this->expectExceptionMessage('Variable "c" does not exist.');
        $this->twig->render('/templates/includeDirVariableScopeRecursive.twig');
    }

    public function testIncludeDirInvalidDirectory()
    {
        $this->expectException(Twig_Error_Loader::class);
        $this->expectExceptionMessage(
            'Unable to find template "/templates/myFictiveDirectory" (looked into: ' . __DIR__ .
            ') in "/templates/includeDirInvalidDirectory.twig".'
        );
        $this->twig->render('/templates/includeDirInvalidDirectory.twig');
    }

    public function testIncludeDirInvalidLoader()
    {
        $this->expectException(Twig_Error_Loader::class);
        $this->expectExceptionMessage('IncludeDir is only supported for filesystem loader!');

        $loader = new Twig_Loader_Array([
            'template' => file_get_contents(__DIR__ . '/templates/includeDir.twig')
        ]);
        $twig = new Twig_Environment($loader, ['strict_variables' => true]);
        $twig->addTokenParser(new IncludeDirTokenParser());
        $twig->render('template');
    }
}