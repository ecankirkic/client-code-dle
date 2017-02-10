<?php
if( ! defined( 'DATALIFEENGINE' ) ) {
    die( "Hacking attempt!" );
}

@include_once ROOT_DIR.DIRECTORY_SEPARATOR.'.sape'.DIRECTORY_SEPARATOR.'config.php';
@include_once ROOT_DIR.DIRECTORY_SEPARATOR.'.sape'.DIRECTORY_SEPARATOR.'sape.php';

function getDefaultConfig(){

    return array(
        'users_id' => "",
        'links' => "0",
        'context' => "0",
        'articles' => "0",
        'tizer' => "0",
        'tizer_image' => "0",
        'rtb' => "0",
        'fsc' => "0",
        'chr_set' => 'UTF-8'
    );
}


if(!isset($sape_config) || !is_array($sape_config)){
    $sape_config = getDefaultConfig();
}

$sape_config = array_merge(getDefaultConfig(),$sape_config);

$params = array(
    'users_id' => '_SAPE_USER',
    'links' => '_SAPE_MODULE_LINKS',
    'context' => '_SAPE_MODULE_CONTEXT',
    'articles' => '_SAPE_MODULE_ARTICLES',
    'tizer' => '_SAPE_MODULE_TIZER',
    'rtb' => '_SAPE_MODULE_RTB',
    'fsc' => '_SAPE_CONFIG_FSC',
    'chr_set' => '_SAPE_CONFIG_CHR'
);

foreach($params as $v=>$k){
    if(!defined($k)){
        define($k, $sape_config[$v]);
    }
}


class SAPE_Worker_loader
{

    private static $_sape_client = null;
    private static $_sape_client_articles = null;
    private static $_sape_client_context = null;

    private static function getSapeOptions()
    {
        return array(
            'charset'                 => _SAPE_CONFIG_CHR,
            'multi_site'              => true,
            'show_counter_separately' => true,
            'force_show_code' => _SAPE_CONFIG_FSC
        );
    }

    private static function _getSapeClient() {
        if ( self::$_sape_client === null ) {
            self::$_sape_client = new SAPE_client( self::getSapeOptions() );
        }

        return self::$_sape_client;
    }

    private static function _getSapeArticles(){

        if ( self::$_sape_client_articles === null ) {
            self::$_sape_client_articles = new SAPE_articles( self::getSapeOptions() );
        }

        return self::$_sape_client_articles;
    }

    private static function _getSapeContext(){
        if ( self::$_sape_client_context === null ) {
            self::$_sape_client_context = new SAPE_context( self::getSapeOptions() );
        }

        return self::$_sape_client_context;
    }

    public static function getLinks($count, $options){
        return self::_getSapeClient()->return_links( $count, $options );
    }

    public static function getHeadScript(){
        return self::_getSapeClient()->return_teasers_block( 0 );
    }

    public static function getArticles($count){
        return self::_getSapeArticles()->return_announcements( $count );
    }

    public static function getTizer($ID){
        return self::_getSapeClient()->return_teasers_block( (int)$ID );
    }

    public static function getContextlink($text){
        return self::_getSapeContext()->replace_in_text_segment($text);
    }


}