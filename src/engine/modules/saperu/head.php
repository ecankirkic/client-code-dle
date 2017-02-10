<?php
if( ! defined( 'DATALIFEENGINE' ) ) {
    die( "Hacking attempt!" );
}

@include_once ROOT_DIR.DIRECTORY_SEPARATOR.'.sape'.DIRECTORY_SEPARATOR.'load.php';

echo SAPE_Worker_loader::getHeadScript();