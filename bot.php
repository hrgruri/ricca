<?php
require 'vendor/autoload.php';

$app = new Hrgruri\Ricca\Application(__DIR__.'/data', 'general');
$app->run();
