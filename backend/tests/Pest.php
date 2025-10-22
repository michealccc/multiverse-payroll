<?php

/*
|--------------------------------------------------------------------------
| Pest Configuration
|--------------------------------------------------------------------------
|
| This file configures Pest PHP for the Multiverse Payroll backend tests.
|
*/

uses()->in('Unit', 'Feature');

/*
|--------------------------------------------------------------------------
| Custom Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeValidEmail', function () {
    return $this->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');
});

expect()->extend('toBePositiveNumber', function () {
    return $this->toBeNumeric()->toBeGreaterThan(0);
});
