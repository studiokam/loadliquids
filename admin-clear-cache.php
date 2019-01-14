<?php

require_once 'core/init.php';

$dir = 'cache/';
foreach(glob($dir.'*.*') as $v){
    unlink($v);
}
Session::flash('cache-deleted', 'Poprawnie usunięto pliki cache.');
Redirect::to('admin-settings.php');