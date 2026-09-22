<?php

require_once __DIR__.'/../vendor/autoload.php';

use Dotenv\Dotenv;
use Illuminate\Filesystem\Filesystem;

// Load DB connection settings from an optional .env file (see .env.example).
// Variables already set in the environment (e.g. by CI) take precedence.
Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

// Clear out the compiled views from Orchestra Testbench
$file = new Filesystem;
if($file->isDirectory('vendor/orchestra/testbench-core/laravel/storage/framework/views')){
    $file->cleanDirectory('vendor/orchestra/testbench-core/laravel/storage/framework/views');
}
