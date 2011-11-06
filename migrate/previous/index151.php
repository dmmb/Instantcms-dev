<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

    session_start();

    define('VALID_CMS', 1);
    
    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

    require(PATH."/core/cms.php");

    $inCore     = cmsCore::getInstance();

    $inCore->loadClass('config');       //конфигурация
    $inCore->loadClass('db');           //база данных
    $inCore->loadClass('user');

    $inConf     = cmsConfig::getInstance();
    $inDB       = cmsDatabase::getInstance();

// ========================================================================================== //
// ========================================================================================== //

    if (!$inDB->isFieldExists('cms_comments', 'parent_id')){

        $sql = "ALTER TABLE `cms_comments` ADD `parent_id` INT NOT NULL DEFAULT '0' AFTER `id`";

        $result = $inDB->query($sql);

        echo '<p>Таблица <strong>cms_comments</strong> обновлена...</p>';

    }

// ========================================================================================== //
// ========================================================================================== //

    if (!$inDB->isTableExists('cms_comments_votes')){

        $sql = "CREATE TABLE `cms_comments_votes` (
                  `comment_id` int(11) NOT NULL,
                  `comment_type` varchar(3) NOT NULL,
                  `vote` smallint(6) NOT NULL,
                  `user_id` int(11) NOT NULL,
                  KEY `comment_id` (`comment_id`,`comment_type`)
                ) ENGINE=MyISAM DEFAULT CHARSET=cp1251";

        $result = $inDB->query($sql);

        echo '<p>Таблица <strong>cms_comments_votes</strong> создана...</p>';

    }

// ========================================================================================== //
// ========================================================================================== //

    if (!$inDB->isTableExists('cms_event_hooks')){

        $sql = "CREATE TABLE `cms_event_hooks` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `event` varchar(50) NOT NULL,
                      `plugin_id` varchar(30) NOT NULL,
                      PRIMARY KEY (`id`),
                      KEY `event` (`event`,`plugin_id`)
                    ) ENGINE=MyISAM  DEFAULT CHARSET=cp1251";

        $result = $inDB->query($sql);

        echo '<p>Таблица <strong>cms_event_hooks</strong> создана...</p>';

        $sql = "INSERT INTO `cms_event_hooks` (`id`, `event`, `plugin_id`)
                VALUES ('6','GET_ARTICLE','5'),
                        ('3','INSERT_WYSIWYG','3'),
                        ('7','USER_PROFILE','6'),
						(8, 'PRINT_PAGE_HEAD', '7')";
        $result = $inDB->query($sql);

        echo '<p>Таблица <strong>cms_event_hooks</strong> заполнена...</p>';

    }

// ========================================================================================== //
// ========================================================================================== //

    $sql = "UPDATE cms_components SET internal = 1 WHERE link = 'statistics' LIMIT 1" ;
    $result = $inDB->query($sql);

    echo '<p>Компонент <Strong>Статистика</strong> преобразован...</p>';

// ========================================================================================== //
// ========================================================================================== //

    if (!$inDB->isTableExists('cms_plugins')){

        $sql = "CREATE TABLE `cms_plugins` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `plugin` varchar(30) NOT NULL,
                  `title` varchar(255) NOT NULL,
                  `description` text NOT NULL,
                  `author` varchar(255) NOT NULL,
                  `version` varchar(15) NOT NULL,
                  `plugin_type` varchar(10) NOT NULL,
                  `published` int(11) NOT NULL,
                  `config` text NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=cp1251";

        $result = $inDB->query($sql);

        echo '<p>Таблица <strong>cms_plugins</strong> создана...</p>';

        $sql = "INSERT INTO `cms_plugins` (`id`, `plugin`, `title`, `description`, `author`, `version`, `plugin_type`, `published`, `config`)
                VALUES ('6','p_usertab','Demo Profile Plugin','Пример плагина - Добавляет вкладку \"Статьи\" в профили всех пользователей','InstantCMS Team','1.0','plugin','0','---\nКоличество статей: 10\n'),
                       ('3','p_fckeditor','FCKEditor','Визуальный редактор','F. C. Knabben','2.63','wysiwyg','1','---\n'),
                       ('5','p_demo','Demo Plugin','Пример плагина - Добавляет текст в конец каждой статьи на сайте','InstantCMS Team','1.0','plugin','0','---\ntext: Added By Plugin From Parameter\ncolor: blue\ncounter: 1\n'),
					   ('7', 'p_swftags', 'SWF облако тэгов', 'Является частью модуля mod_swftags. Добавляет подключение скрипта', 'InstantCMS Team (Konstantin Maximchik) smart.maxx@gmail.com', '1.0', 'plugin', 0, '')";
        $result = $inDB->query($sql);

        echo '<p>Плагин <strong>FCKEditor</strong> установлен...</p>';
        echo '<p>Плагин <strong>SWF облако тэгов</strong> установлен...</p>';
        
    }

// ========================================================================================== //
// ========================================================================================== //

    if (!$inDB->isFieldExists('cms_modules', 'template')){

        $sql = "ALTER TABLE `cms_modules` ADD `template` VARCHAR( 35 ) NOT NULL DEFAULT 'module.tpl'";
        $result = $inDB->query($sql);

        echo '<p>Таблица <strong>cms_modules</strong> обновлена...</p>';

    }

// ========================================================================================== //
// ========================================================================================== //

    if (!$inDB->rows_count('cms_modules', "content = 'mod_respect'", 1)){

        $sql = "INSERT INTO `cms_modules` (`id`, `position`, `name`, `title`, `is_external`, `content`, `ordering`, `showtitle`, `published` ,
                                            `user`, `config`, `original`, `css_prefix`, `allow_group`, `cache`, `cachetime`, `cacheint`, `template`)
                VALUES (NULL, 'left', 'Доска почета', 'Доска почета', '1', 'mod_respect', '1', '1', '0', '0', '', '1', '', '-1', '', '1', 'HOUR', 'module.tpl')";
        $result = $inDB->query($sql);

        echo '<p>Модуль <strong>Доска почета</strong> установлен...</p>';

    }

// ========================================================================================== //
// ========================================================================================== //

    if (!$inDB->rows_count('cms_modules', "content = 'mod_swftags'", 1)){

        $sql = "INSERT INTO `cms_modules` (`position`, `name`, `title`, `is_external`, `content`, `ordering`, `showtitle`, `published`, `user`, `config`, `original`, `css_prefix`, `allow_group`, `cache`, `cachetime`, `cacheint`, `template`) VALUES
				('right', 'SWF Облако тегов', 'SWF Облако тегов', 1, 'mod_swftags', 99, 0, 0, 0, '---\ncat_id: \nsortby: tag\nmenuid: \nminfreq: 0\nminlen: 3\ntargets: \n  content: content\n  photo: photo\n  blogpost: blog\n  catalog: catalog\n  userphoto: userphoto\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl')";
        $result = $inDB->query($sql);

        echo '<p>Модуль <strong>SWF Облако тегов</strong> установлен...</p>';

    }

// ========================================================================================== //
// ========================================================================================== //

    if (!$inDB->rows_count('cms_modules', "content = 'mod_userfiles'", 1)){

        $sql = "INSERT INTO `cms_modules` (`position`, `name`, `title`, `is_external`, `content`, `ordering`, `showtitle`, `published`, `user`, `config`, `original`, `css_prefix`, `allow_group`, `cache`, `cachetime`, `cacheint`, `template`)
                VALUES ('right', 'Файлы пользователей', 'Файлы пользователей', 1, 'mod_userfiles', 1, 1, 1, 0, '---\nmenuid: 0\nsw_stats: 1\nsw_latest: 1\nsw_popular: 1\nnum_latest: 5\nnum_popular: 5\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl')";

        $result = $inDB->query($sql);

        echo '<p>Модуль <strong>Файлы пользователей</strong> добавлен...</p>';

    }

// ========================================================================================== //
// ========================================================================================== //

    //конверт настроек компонентов в YAML

    $sql = "SELECT id, config FROM cms_components";

    $result = $inDB->query($sql);

    $config = '';

    if ($inDB->num_rows($result)){
        while($component = $inDB->fetch_assoc($result)){
            $config         = @unserialize($component['config']);
            if (is_array($config)){
                $config_yaml    = ($config) ? $inCore->arrayToYaml($config) : "---\n";
                $inDB->query("UPDATE cms_components SET config = '{$config_yaml}' WHERE id={$component['id']}");
            }
        }
    }

    echo '<p>Настройки компонентов переведены в формат YAML...</p>';

// ========================================================================================== //

    //конверт настроек модулей в YAML

    $sql = "SELECT id, config FROM cms_modules";

    $result = $inDB->query($sql);

    $config = '';

    if ($inDB->num_rows($result)){
        while($module = $inDB->fetch_assoc($result)){
            $config         = @unserialize($module['config']);
            if (is_array($config)){
                $config_yaml    = ($config) ? $inCore->arrayToYaml($config) : "---\n";
                $inDB->query("UPDATE cms_modules SET config = '{$config_yaml}' WHERE id={$module['id']}");
            }
        }
    }

    echo '<p>Настройки модулей переведены в формат YAML...</p>';

// ========================================================================================== //

    //конверт форм пользователей в YAML

    $sql = "SELECT id, formsdata FROM cms_user_profiles";

    $result = $inDB->query($sql);

    $config = '';

    if ($inDB->num_rows($result)){
        while($data = $inDB->fetch_assoc($result)){
            $config         = @unserialize($data['formsdata']);
            if (is_array($config)){
                $config_yaml    = ($config) ? $inCore->arrayToYaml($config) : "---\n";
                $inDB->query("UPDATE cms_user_profiles SET formsdata = '{$config_yaml}' WHERE id={$data['id']}");
            }
        }
    }

    echo '<p>Формы профилей переведены в формат YAML...</p>';

// ========================================================================================== //

    if (!$inDB->isFieldExists('cms_user_profiles', 'stats')){

        $sql = "ALTER TABLE `cms_user_profiles` ADD `stats` TEXT NOT NULL";

        $result = $inDB->query($sql);

        echo '<p>Таблица <strong>cms_user_profiles</strong> преобразована...</p>';

    }

// ========================================================================================== //

    //конверт статистики пользователей в YAML

    $sql = "SELECT id FROM cms_users";

    $result = $inDB->query($sql);

    if ($inDB->num_rows($result)){
        while($user = $inDB->fetch_assoc($result)){

            cmsUser::updateStats($user['id']);
            
        }
    }

    echo '<p>Статистика пользователей переведена в формат YAML...</p>';

// ========================================================================================== //
// ========================================================================================== //

    //COMPLETED
	echo '<div style="margin:15px 0px 15px 0px;font-weight:bold">Миграция завершена. Обязательно удалите папку /migrate/ прежде чем продолжить!</div>';
	echo '<a href="/">Перейти на сайт</a>';
?>