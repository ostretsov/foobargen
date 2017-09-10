<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 09.09.17 19:54.
 */

require __DIR__.'/../vendor/autoload.php';

(new \Foobargen\Generator('data', 'web', 'deadsimple'))->generate();