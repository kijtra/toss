# Toss
Pass the message in background


[![Build Status](https://travis-ci.org/kijtra/toss.svg?branch=master)](https://travis-ci.org/kijtra/toss)
[![Coverage Status](https://coveralls.io/repos/github/kijtra/toss/badge.svg)](https://coveralls.io/github/kijtra/toss)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/kijtra/toss/blob/master/LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/kijtra/toss.svg?style=flat-square)](https://packagist.org/packages/kijtra/toss)



## Installation

Using Composer

```php
composer.phar require kijtra/toss
```

## Requires

PHP >= 5.5


## Basic Usage

```php
<?php
use \Kijtra\Toss;

$toss = new Toss;

// ... something ...

$toss->error('Oops!');

// ... something ...

if ($toss->hasError()) {
    echo $toss->getMessage(); // 'Oops!'
}
```

Or global(singleton) method

```php
<?php
use \Kijtra\Toss;

Toss::getGlobal()->success('Year!');

// ... something ...

if (Toss::getGlobal()->hasSuccess()) {
    echo Toss::getGlobal()->getMessage(); // 'Year!'
}
```

## Geting message(s)

```php
<?php
use \Kijtra\Toss;

$toss = new Toss;

// Add message s
$toss->warning('Warn!');
$toss->info('Information');

// Get LATEST message
$latest = $toss->getMessage();
var_dump($latest->type); // 'info'
var_dump($latest->message); // 'Information'

// Get latest message of type
$messages = $toss->getMessages('warning');
var_dump($messages[0]->type); // 'warning'
var_dump($messages[0]->message); // 'Warn!'

/*
- ATTENTION -

'getMessages()' is need argument.

$messages = $toss->getMessages();
var_dump($messages); // null
*/
```


## Add globally after added

```php
<?php
use \Kijtra\Toss;

// Add message
$toss = new Toss('You Correct', 'success');

// Get latest message
$latest = $toss->getMessage();

// Sync to Global instance
$latest->toGlobal();

var_dump(Toss::getGlobal()->hasSuccess()); // true
```

## Clear messages

```php
<?php
use \Kijtra\Toss;

// Add 'error' type message
$toss = new Toss('Oh no..', 'error');

// Add 'notice' type message
$toss->notice('Really?');

// If message is not empty
if (false === $toss->isNothing()) {
    // Clear 'error' type only
    $toss->clear('error');
}

var_dump($toss->hasError()); // false
var_dump($toss->hasNotice()); // true

// If message is not empty
if (false === $toss->isNothing()) {
    // Clear all message
    $toss->clear();
}

var_dump($toss->hasError()); // false
var_dump($toss->hasNotice()); // false
var_dump($toss->isNothing()); // true
```


## Bundled Types

```php
<?php
use \Kijtra\Toss;

$toss = new Toss;

var_dump($toss->getDefaultType());
/*
'info'
*/

var_dump($toss->getAvailableTypes());
/*
array(
    'error',
    'warning',
    'notice',
    'info',
    'success',
    'invalid',
    'valid',
)
*/
```


## Add Custom type

```php
use \Kijtra\Toss;

// MUST extending Kijtra\Toss\Type class
class MyType extends Toss\Type
{
    // You do not need
}


$toss = new Toss;

$toss->addtype(MyType::class);
// Or $toss->addtype('MyType');

var_dump($toss->getAvailableTypes());
/*
array(
    'error',
    'warning',
    'notice',
    'info',
    'success',
    'invalid',
    'valid',
    'mytype', <- Added
)
*/

$toss->MyType('My Added Type!');

if ($toss->hasMyType()) {
    echo $toss->getMessage(); // 'My Added Type!'
}
```
