[![Latest Version](https://img.shields.io/packagist/v/wol-soft/twig-include-dir.svg)](https://packagist.org/packages/wol-soft/twig-include-dir)
[![Build Status](https://travis-ci.org/wol-soft/twig-include-dir.svg?branch=master)](https://travis-ci.org/wol-soft/twig-include-dir)
[![Coverage Status](https://coveralls.io/repos/github/wol-soft/twig-include-dir/badge.svg?branch=master)](https://coveralls.io/github/wol-soft/twig-include-dir?branch=master)
[![MIT License](https://img.shields.io/packagist/l/wol-soft/twig-include-dir.svg)](https://github.com/wol-soft/twig-include-dir/blob/master/LICENSE)

# twig-include-dir
Include all twig templates within a directory

## Features ##

- Provide a directory with multiple .twig templates and all templates will be included
- Add *recursive* keyword to include all templates within a directory recursive
- known variable handling as known from *include* using the keywords *only* and *with*

## Requirements ##

- Requires Twig > 2.7
- Requires PHP > 7.2.9

## Installation ##

The recommended way to install twig-include-dir is through [Composer](http://getcomposer.org):
```
$ composer require wol-soft/twig-include-dir
```

## Why? ##

An example use case could be: you set up a site using bootstrap with many modals. Now you don't need to throw all your modals together in a file or include each modal manually but instead you can separate your modals by using one file for each modal. Throw all modals in a modal-directory and simply include the whole directory. Adding a new modal? No problem, just create a new template file in your modal-directory.

## Getting started ##

To use twig-include-dir you need to add the include-dir token parser to your Twig Environment first:

```php
<?php

/* ... */

$loader = new FilesystemLoader(__DIR__ . DIRECTORY_SEPARATOR);
$twig = new Environment($loader);
$twig->addTokenParser(new \TwigIncludeDir\IncludeDirTokenParser());

/* ... */
```

Afterwards you can start using the added token *includeDir* in your templates:

```twig
<div class="modal-container">
    {% includeDir '/modals' %}
</div>
```

The files in the directory will be included alphabetically.

### Recursive usage

To include all files within a given directory recursive simply add the keyword *recursive* to your include statement:

```twig
<div class="modal-container">
    {% includeDir '/modals' recursive %}
</div>
```

Now also the modals from the directories */modals/user* and */modals/system* etc. will be included.

__Caution:__ The templates will be included alphabetically as well, including the directories. Thus the template */modals/footer.twig* will be included before the templates from the directory */modals/system* followed by */modals/user* followed by a possible */modals/zebraHeader.twig*. It is recommended to use twig-include-dir only for templates which do __not__ require a specific order.

### Variables

As known from the Twig Core *include* you can control the available variables with the keywords *with* and *only* (compare: [include](https://twig.symfony.com/doc/2.x/tags/include.html))

```twig
<div class="modal-container">
    {# only the foo variable will be accessible #}
    {% includeDir '/modals' recursive with {'foo': 'bar'} only %}
</div>
```
