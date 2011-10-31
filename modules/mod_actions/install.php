<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.9                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

    function info_module_mod_actions(){

        //
        // Описание модуля
        //

        //Заголовок (на сайте)
        $_module['title']        = 'Лента активности';

        //Название (в админке)
        $_module['name']         = 'Лента активности';

        //описание
        $_module['description']  = 'Показывает список последних действий пользователей на сайте';
        
        //ссылка (идентификатор)
        $_module['link']         = 'mod_actions';
        
        //позиция
        $_module['position']     = 'maintop';

        //автор
        $_module['author']       = 'InstantCMS Team';

        //текущая версия
        $_module['version']      = '1.7';

        //
        // Настройки по-умолчанию
        //
        $_module['config'] = array();

        return $_module;

    }

// ========================================================================== //

    function install_module_mod_actions(){

        return true;

    }

// ========================================================================== //

    function upgrade_module_mod_actions(){

        return true;
        
    }

// ========================================================================== //

?>