#!/bin/bash
./phpunit --bootstrap tests/autoload.php --coverage-html ./coverage --coverage-filter ./src tests
