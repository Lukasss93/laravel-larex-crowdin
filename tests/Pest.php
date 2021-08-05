<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use Illuminate\Support\Facades\File;
use Lukasss93\LarexCrowdin\Support\Crowdin\Crowdin;
use Lukasss93\LarexCrowdin\Tests\TestCase;
use Mockery\MockInterface;

uses(TestCase::class)
    ->beforeEach(function () {
        //clear lang folder
        $items = glob(resource_path('lang/*'));
        foreach ($items as $item) {
            if (is_dir($item)) {
                File::deleteDirectory($item);
            } else {
                File::delete($item);
            }
        }
    })
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('fileContent', fn () => $this->and(File::get($this->value)));
expect()->extend('toEqualStub', fn ($name) => $this->toEqual(getStub($name)));

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function mockCrowdin(array $properties)
{
    test()->mock(Crowdin::class, function (MockInterface $mock) use ($properties) {
        foreach ($properties as $property => $mockLogic) {
            $mock->{$property} = $mockLogic;
        }
    });
}

function getStub($name, bool $asPHP = false)
{
    if (is_string($name)) {

        if($asPHP){
            return include(__DIR__."/Stubs/$name");
        }

        return file_get_contents(__DIR__."/Stubs/$name");
    }

    if (is_array($name)) {
        return array_map(function ($item) use ($asPHP) {
            return getStub($item, $asPHP);
        }, $name);
    }

    throw new InvalidArgumentException('The $name parameter must be a string or array of strings');
}

function initFromTestStub(string $name)
{
    File::put(base_path(config('larex.csv.path')), getStub($name));
}
