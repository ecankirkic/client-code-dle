<?php
if( ! defined( 'DATALIFEENGINE' ) ) {
    die( "Hacking attempt!" );
}

/**
 * Сохранение настроек модуля
 */
include ('engine/api/api.class.php');

$dle_api->install_admin_module ( 'saperu', 'saperu', 'saperu', 'saperu.png' );

$chrSet = $dle_api->dle_config['charset'];


$loadPath = sprintf('%s%s.sape%sconfig.php', ROOT_DIR, DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR);



if(!is_writable_f($loadPath)){
    msg("error", 'Админпанель модуля ингерации Sape.ru', 'Для продолжения работы необходимы разрешения на запись в '.$loadPath, 'admin.php');
}else {

    if ($action == "dosave") {
        $find[] = "'\r'";
        $replace[] = "";
        $find[] = "'\n'";
        $replace[] = "";

        $handler = @fopen($loadPath, "wb");
        fwrite($handler, "<?php \n\n//SAPE config
                         \n\n\$sape_config = array(
                         \n\n'version' => \"v.1.0\",\n\n");

        foreach ($_POST['save_con'] as $name => $value) {
            $value = trim(stripslashes($value));
            $value = htmlspecialchars($value, ENT_QUOTES);
            $value = preg_replace($find, $replace, $value);
            fwrite($handler, "'{$name}' => \"{$value}\",\n\n");

        }
        fwrite($handler, "'chr_set' => \"{$chrSet}\",\n\n");

        fwrite($handler, ");\n\n?>");
        fclose($handler);


        @include_once ($loadPath);

        if(!isset($sape_config) || !is_array($sape_config)){
            $sape_config = array();
        }

        $sape_config = array_merge(getDefaultConfig(),$sape_config);

        $is_ok = true;
        if($sape_config['tizer']){

            $file_name = _getTizerImageOptions($sape_config['tizer_image']);

            $write_file = sprintf('%s%s%s', ROOT_DIR, DIRECTORY_SEPARATOR,$file_name);

            if(!is_writable_f($write_file)){
                msg("error", 'Админпанель модуля ингерации Sape.ru', 'Для продолжения работы с тизерами необходимы разрешения на запись в '.$write_file, 'admin.php');
                $is_ok = false;
            }else{

                $content = sprintf(
                    '<?php define(\'_SAPE_USER\', \'%s\');require_once(\'%s/.sape/sape.php\');$sape = new SAPE_client(array(\'charset\' => \'%s\'));$sape->show_image();',
                    $sape_config['users_id'],
                    ROOT_DIR,
                    $sape_config['chr_set']
                );

                file_put_contents($write_file, $content);
            }

            $write_file = sprintf('%s%s%s.php', ROOT_DIR, DIRECTORY_SEPARATOR,$sape_config['users_id']);

            if(!is_writable_f($write_file)){
                msg("error", 'Админпанель модуля ингерации Sape.ru', 'Для продолжения работы с тизерами необходимы разрешения на запись в '.$write_file, 'admin.php');
                $is_ok = false;
            }else{

                $content = sprintf(
                    '<?php define(\'_SAPE_USER\', \'%s\');require_once(\'%s/.sape/sape.php\');$sape = new SAPE_articles(array(\'charset\' => \'%s\'));echo $sape->process_request();',
                    $sape_config['users_id'],
                    ROOT_DIR,
                    $sape_config['chr_set']
                );

                file_put_contents($write_file, $content);
            }


        }



        if($is_ok) {
            msg("info", "Строка изменена", "{$lang['opt_sysok_1']}<br /><br /><a href=\"{$PHP_SELF}?mod=saperu\">{$lang['db_prev']}</a>", '?mod=saperu');
        }
    }


    @include_once ($loadPath);

    if(!isset($sape_config) || !is_array($sape_config)){
        $sape_config = array();
    }

    $sape_config = array_merge(getDefaultConfig(),$sape_config);



    /**
     * Вывод header
     */

    echoheader("Saperu", "Админпанель модуля ингерации Sape.ru");
    /**
     * Вывод блока настроек
     */
    echo('<form action="" method="POST">');

    opentable();
    tableheader('Идентификационная часть');
    printFieldsStart();
    printField('users_id', '_SAPE_USER', $sape_config['users_id'], 'text', 'Это ваш уникальный идентификатор (хеш).<br/>Можете найти его на сайте <a href="http://www.sape.ru" target="_blank">sape.ru</a> кликнув по кнопке "добавить площадку".<br/>Будет похож на что-то вроде <strong>d12d0d074c7ba7f6f78d60e2bb560e3f</strong>.');
    printFieldsEnd();
    closetable();

    opentable();
    tableheader('Системы монетизации');
    printFieldsStart();
    printField('links', 'Простые ссылки', $sape_config['links'], 'checkbox', 'Текстовые и блочные ссылки.<br/>Доступен для вставки в шаблон сайта как <strong>{include file="engine/modules/saperu/links.php?count=X&block=0&orientation=0"}</strong>, где <strong>count</strong> - количество выводимых ссылок, <strong>block</strong> - формат вывода (0 - текст, 1 - блок), <strong>orientation</strong> - ориентация блока(0 - вертикально, 1 - горизонтально)');
    printField('context', 'Контекстные ссылки', $sape_config['context'], 'checkbox', 'Ссылки внутри записей. <br/>Доступен для вставки в шаблон сайта как <strong>{include file="engine/modules/saperu/context.php?data={DATA_CONTEXT}"}</strong>, где DATA_CONTEXT - имя переменной, содержащей тело статьи');
    printField('articles', 'Размещение статей', $sape_config['articles'], 'checkbox', 'Текстовые и блочные ссылки на статьи.<br />Доступен для вставки в шаблон сайта как <strong>{include file="engine/modules/saperu/articles.php?count=X"}</strong>, где <strong>count</strong> - количество выводимых ссылок на статьи');
    printField('tizer', 'Размещение тизеров', $sape_config['tizer'], 'checkbox', 'Тизерные блоки.<br />Доступен для вставки в шаблон сайта как <strong>{include file="engine/modules/saperu/tizer.php?id=000"}</strong>, где <strong>id</strong> - ID тизерного блока в системе sape.ru.');
    printField('tizer_image', 'Файл изображения тизеров', $sape_config['tizer_image'], 'select', 'Имя файла, показывающего картинки тизеров', _getTizerImageOptions());
    printField('rtb', 'Размещение RTB блоков', $sape_config['rtb'], 'checkbox', 'RTB блоки.<br/>Для вставки кода блока, вставте <strong>код</strong>, полученные в <strong>RTB.Sape</strong> в необходимое место шаблона сайта.');
    printField('fsc', 'Режим отладки', $sape_config['fsc'], 'checkbox', 'Выводить коды отладки<br/>Подробности: <a href=\'http://help.sape.ru/sape/faq/270\' target=\'_blank\'>faq</a>.');

    printText('Важно!', 'Для нормальной работы RTB блоков, тизерный ссылок и счётчиков необходимо вставить в head шаблон сайта: <strong>{include file="engine/modules/saperu/head.php"}</strong>.<br/>Обычно, для этого необходимо отредактиорвать шаблон <strong>main.tpl</strong>');

    printFieldsEnd();

    closetable();

    ?>
    <div style="margin-bottom:30px;">
        <input type="hidden" name="mod" value="saperu">
        <input type="hidden" name="action" value="dosave">
        <input type="submit" class="btn btn-lg btn-green" value="Сохранить">
    </div>
    <?php

    echo "</form>";
    /**
     * Вывод footer
     */



}

echofooter();


function opentable()
{
    echo "<div class=\"box\">";
}

function closetable()
{
    echo "</div>";
}

function tableheader($value)
{
    printf('<div class="box-header"><div class="title">%s</div></div>', $value);
}

function printField($field, $name, $value, $type, $desctiption, $options = null)
{
    switch ($type) {

        case 'checkbox':
            printf(
                '<tr>%s<td class="col-xs-6 col-sm-6 col-md-7 white-line"><input type="hidden" name="save_con[%s]" value="0"/><input class="iButton-icons-tab" type="checkbox" name="save_con[%s]" value="1" %s></td></tr>',
                getFieldHeader($name, $desctiption),
                $field, $field,
                $value ? 'checked' : ''
            );
            break;

        case 'select':
            printf(
                '<tr>%s<td class="col-xs-6 col-sm-6 col-md-7 white-line">%s</tr>',
                getFieldHeader($name, $desctiption),
                getFieldSelect($field, $value, $options)
            );
            break;
        case 'text':
        default:
            printf(
                '<tr>%s<td class="col-xs-6 col-sm-6 col-md-7 white-line"><input type="text" style="width:100%%;" name="save_con[%s]" value="%s"></td></tr>',
                getFieldHeader($name, $desctiption),
                $field,
                $value
            );
    }
}

function printText($name, $text){
    printf('<tr><td colspan="2"><h3>%s</h3><span class="large">%s</span></td></tr>', $name,$text);
}

function getFieldSelect($field, $value, $options)
{
    $option = "";
    foreach ($options as $k => $v) {
        $option .= sprintf('<option value="%s" %s>%s</option>', $k, $value == $k ? 'selected' : '', $v);
    }
    return sprintf('<select class="uniform" style="min-width:100px;" name="save_con[%s]">%s</select>', $field, $option);
}

function getFieldHeader($name, $desctiption)
{
    return sprintf('<td class="col-xs-6 col-sm-6 col-md-7 white-line"><h6> %s:</h6><span class="large">%s</span>', $name, $desctiption);
}

function printFieldsStart()
{
    echo('<div class="box-content"><table class="table table-normal"><tbody>');
}

function printFieldsEnd()
{
    echo "</tbody></table></div>";
}

function _getTizerImageOptions($id = null)
{
    if ($id != null) {
        $data = _getTizerImageOptions();
        return isset($data[$id]) ? $data[$id] : null;
    }
    return array('img.php', 'image.php', 'photo.php', 'wp-img.php', 'wp-image.php', 'wp-photo.php');
}

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

function is_writable_f($file_name)
{
    if(file_exists($file_name)){
        if(is_writable($file_name)){
            return true;
        }else{
            if(!@chmod($file_name, "u+w")){
                return false;
            }
        }
    }else{
        if(!file_put_contents($file_name, 'init')){
            return false;
        }
    }

    return true;
}