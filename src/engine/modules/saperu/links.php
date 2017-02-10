<?php
if( ! defined( 'DATALIFEENGINE' ) ) {
    die( "Hacking attempt!" );
}





@include_once ROOT_DIR.DIRECTORY_SEPARATOR.'.sape'.DIRECTORY_SEPARATOR.'load.php';
if(_SAPE_MODULE_LINKS) {

    if(!isset($count)){
        $count = 0;
    }

    if(!isset($orientation)){
        $orientation = 0;
    }

    if(!isset($block)){
        $block = 0;
    }

    echo SAPE_Worker_loader::getLinks((int)$count, array(
        'as_block' => ((int)$block == 1) ? true : false,
        'block_orientation' => ((int)$orientation == 1) ? true : false
    ));
}