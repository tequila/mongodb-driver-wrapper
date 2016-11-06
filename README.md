# Tequila PHP MongoDB Driver Wrapper

This library provides a thin wrapper around the new official [PHP MongoDB driver](https://github.com/mongodb/mongo-php-driver).
It can be helpful for you: 
- If you don't want to use high level abstractions around PHP MongoDB Driver, such as 
[Tequila MongoDB PHP Library](https://github.com/tequila/mongodb-php-lib) or official
[MongoDB PHP Library](https://github.com/mongodb/mongo-php-library) and you are going to use native driver classes.
Read below to know, what benefits could you gain by using this library.
- If you're writing abstraction around the new PHP MongoDB driver, such as alternative high-level driver library.

In case this is not about you - you can try high-level tools, for example 
[Tequila MongoDB PHP Library](https://github.com/tequila/mongodb-php-lib), which is based on this lib.

By wrapping driver classes this library allows you to do some cool things, that could not be done when using just native driver.
These cool things are explained below.

## Installation
This library requires PHP 5.6 or higher, PHP 7.0 or higher.
It may work with the MongoDB 2.4+, but support will be provided for the MongoDB 3.0+.
Since this library wraps official [PHP MongoDB driver](https://github.com/mongodb/mongo-php-driver), it requires
this driver to be installed:
```bash
$ pecl install mongodb
```

The library should be installed with [Composer](https://getcomposer.org):
```bash
$ composer require tequila/mongodb-driver-wrapper
```

## Why to use this library

### Your code becomes testable:
Suppose you are writing a code, that uses MongoDB driver classes to communicate with MongoDB server.
You need to write tests for your code. Since MongoDB driver has its own tests, 
you don't have to repeat them by making real queries to the database.
What you wanna do, is to write tests for your code, and check that your code makes proper calls to `MongoDB\Driver\Manager` methods,
since this class is the only entry point to the communication with MongoDB from PHP.

Let's imagine you wanna test the following code:

```php
<?php 

class Database
{
    public function __construct(\MongoDB\Driver\Manager $manager, $databaseName)
    {
        // ...
    }
    
    public function createCollection($collectionName, array $options = [])
    {
        // ...
    }
}

$db = new Database(new \MongoDB\Driver\Manager(), 'myapp');
$db->createCollection('logs', ['capped' => true, 'size' => 1000000]);
```

To check that this code works properly, you actually need to check that `MongoDB\Driver\Manager::executeCommand()`
is called with the proper arguments. For such cases you'll need to use **mocks**. But here is the problem - you cannot
mock `MongoDB\Driver\Manager` to check that its method `executeCommand()` is called, because this class is final.
Also, you cannot mock `MongoDB\Driver\Command`, and any other driver's native class.
Because of that, you have two ways to test the code, which depends on the driver:
- Create functional tests, which actually make calls to MongoDB, and then check the results on MongoDB server.
This is not the best solution, because your tests will depend on a MongoDB server to be installed and active.
Also you will be doing an extra work - testing the whole chain instead of just testing your code.
- Do not test the behavior of your code - for example test that your `Database` instance does not throw exception 
when passing proper arguments to its constructor, but NOT test it's `createCollection()` method, which actually does something.
But that's not cool also, because you will not be able to tell that your code is really tested and stable.

Here's where this library can help: it defines classes, that has almost the same interfaces that driver classes do.
You can use this classes instead of native driver's classes, mock them, and so to make your code testable.
For example, `Tequila\MongoDB\Manager` class wraps the native `MongoDB\Driver\Manager` class, 
and `Tequila\MongoDB\Manager::executeQuery()` accepts `Tequila\MongoDB\Query` instance instead of `MongoDB\Driver\Query`.
This allows you to call `Tequila\MongoDB\Query::getFilter()` method and check that you're executing the query 
you are expecting to execute. This allows your code to be more testable and stable.

### Your code becomes more flexible:
This library is written with the high-level code in mind. Currently, every application needs to have an ability to profile,
what requests are sent to the database server. And one of the main goals of the profiling tools is to intercept 
requests to the database at the lowest possible level. Suppose you're using the official [MongoDB PHP Library](https://github.com/mongodb/mongo-php-library).
To profile the requests, you can extend the `MongoDB\Collection` class and decorate it's calls to the `MongoDB\Driver\Manager`
instance like so:

```php
<?php

namespace MyApplication;

use MongoDB\Collection;

class ProfilerAwareCollection extends Collection
{
    private $profiler;
    
    public function setProfiler(Profiler $profiler)
    {
        $this->profiler = $profiler;
    }
    
    public function findOneAndUpdate($filter, $update, array $options = [])
    {
        $profilerEntry = [
             'command' => 'findOneAndUpdate',
             'filter' => $filter,
             'update' => $update,
             'options' => $options,
        ];
        
        $response = parent::findOneAndUpdate($filter, $update, $options);
        
        $profilerEntry['response'] = $response;
        $this->profiler->addEntry($profilerEntry);
        
        return $response;
    }
}
```

Looks good, but the problem is that `$options` array, saved with the profiler entry, is not the options, that were
actually sent to the MongoDB server. That's because the `FindOneAndReplace` command translated it's input options
to the format, acceptable by the `FindAndModify` command, and `FindAndModify` command translated it's input options
to the format, acceptable by the MongoDB server. As a result - your profiler will save just input options of the command,
and you will not know, what request has been actually sent to the MongoDB server.

Off course, you can decorate every single command or write your own commands. But it doesn't make sense to use the library,
if you need to write everything by yourself. It's too hard and it leads to bugs: if you change your profiler, you must
fix it's usages in every single command.
Therefore, the best solution is to have ability to add your logic right before the request is sent to the database.
By accepting `Tequila\MongoDB\ManagerInterface` in your methods, you can achieve this goal very easy:
just write your own `Manager` class, which implements this interface and has some custom logic, or just extend
`Tequila\MongoDB\Manager` class and decorate one of it's three main methods to intercept database request:

```php
<?php

namespace MyApplication;

use MongoDB\Driver\ReadPreference;
use Tequila\MongoDB\Manager;
use Tequila\MongoDB\CommandInterface;

class ProfilerAwareManager extends Manager
{
    private $profiler;
        
    public function setProfiler(Profiler $profiler)
    {
        $this->profiler = $profiler;
    }
    
    public function executeCommand($databaseName, CommandInterface $command, ReadPreference $readPreference)
    {
        $server = $this->selectServer($readPreference);
        $profilerEntry = $command->getOptions($server);
        $response = parent::executeCommand($databaseName, $command, $readPreference);
        
        $profilerEntry['response'] = $response;
        $this->profiler->addEntry($profilerEntry);
        
        return $response;
    }
}
```

The library is MIT-licensed. Please create an issue if you think that this library can be improved for your needs.
Contributions are appreciated.