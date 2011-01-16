<?php

error_reporting(E_ALL | E_STRICT);

set_include_path(realpath(dirname(__FILE__) . '/../src') . PATH_SEPARATOR .
                 realpath(dirname(__FILE__) . '/../vendor/lime') . PATH_SEPARATOR .
                 realpath(dirname(__FILE__) . '/../vendor/Lime-Test-Pluggable/lib') . PATH_SEPARATOR .
                 get_include_path()
);

require_once 'lime/pluggable.php';
