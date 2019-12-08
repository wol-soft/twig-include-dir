<?php

namespace TwigIncludeDir\Tests;

use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;
use TwigIncludeDir\IncludeDirTokenParser;

class IncludeDirTest extends TestCase
{
    /** @var Environment */
    protected $twig;

    public function setUp(): void
    {
        $loader = new FilesystemLoader(__DIR__ . DIRECTORY_SEPARATOR);
        $this->twig = new Environment($loader, ['strict_variables' => true]);
        $this->twig->addTokenParser(new IncludeDirTokenParser());
    }

    public function testIncludeDir(): void
    {
        $output = $this->twig->render('/templates/includeDir.twig');
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/Expectations/includeDir.html', $output);
    }

    public function testIncludeDirRecursive(): void
    {
        $output = $this->twig->render('/templates/includeDirRecursive.twig');
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/Expectations/includeDirRecursive.html', $output);
    }

    public function testIncludeDirWith(): void
    {
        $output = $this->twig->render('/templates/includeDirWith.twig');
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/Expectations/includeDir.html', $output);
    }

    public function testIncludeDirWithRecursive(): void
    {
        $output = $this->twig->render('/templates/includeDirWithRecursive.twig');
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/Expectations/includeDirRecursive.html', $output);
    }

    public function testIncludeDirVariableScope(): void
    {
        $output = $this->twig->render('/templates/includeDirVariableScope.twig');
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/Expectations/includeDir.html', $output);
    }

    public function testIncludeDirVariableScopeMissingVariable(): void
    {
        $this->expectException(RuntimeError::class);
        $this->expectExceptionMessage('Variable "b" does not exist.');
        $this->twig->render('/templates/includeDirVariableScopeMissingVariable.twig');
    }

    public function testIncludeDirVariableScopeOnlyMissingVariable(): void
    {
        $this->expectException(RuntimeError::class);
        $this->expectExceptionMessage('Variable "b" does not exist.');
        $this->twig->render('/templates/includeDirVariableScopeOnlyMissingVariable.twig');
    }

    public function testIncludeDirVariableScopeRecursiveMissingVariable(): void
    {
        $this->expectException(RuntimeError::class);
        $this->expectExceptionMessage('Variable "c" does not exist.');
        $this->twig->render('/templates/includeDirVariableScopeRecursive.twig');
    }

    public function testIncludeDirInvalidDirectory(): void
    {
        $this->expectException(LoaderError::class);
        $this->expectExceptionMessage(
            'Unable to find template "/templates/myFictiveDirectory" (looked into: ' . __DIR__ .
            ') in "/templates/includeDirInvalidDirectory.twig".'
        );
        $this->twig->render('/templates/includeDirInvalidDirectory.twig');
    }

    public function testIncludeDirInvalidLoader(): void
    {
        $this->expectException(LoaderError::class);
        $this->expectExceptionMessage('IncludeDir is only supported for filesystem loader!');

        $loader = new ArrayLoader([
            'template' => file_get_contents(__DIR__ . '/templates/includeDir.twig')
        ]);
        $twig = new Environment($loader, ['strict_variables' => true]);
        $twig->addTokenParser(new IncludeDirTokenParser());
        $twig->render('template');
    }
}