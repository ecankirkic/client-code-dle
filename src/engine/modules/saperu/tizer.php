<?php
if( ! defined( 'DATALIFEENGINE' ) ) {
    die( "Hacking attempt!" );
}

@include_once ROOT_DIR.DIRECTORY_SEPARATOR.'.sape'.DIRECTORY_SEPARATOR.'load.php';

if(_SAPE_MODULE_ARTICLES) {
    if (isset($id)) {
        echo SAPE_Worker_loader::getTizer((int)$id);
    }
}