<?php

// ========================================================================== //

    function info_component(){

        //Описание компонента

        $_component['title']        = 'Встречи - демо компонент'; //название
        $_component['description']  = 'Компонент позволяет публиковать на сайте графики мероприятий, разбитые на категории'; //описание
        $_component['link']         = 'meetings'; //ссылка (идентификатор)
        $_component['author']       = 'r2';      //автор
        $_component['internal']     = '0';       //внутренний (только для админки)? 1-Да, 0-Нет
        $_component['version']      = '1.0';     //текущая версия

        //Правила для .htaccess
        
        $_component['htaccess'][]   = 'RewriteRule ^new_com/([0-9]*)/([0-9]*)$ /index.php?view=new_com&id=$2&menuid=$1';
        $_component['htaccess'][]   = 'RewriteRule ^new_com/([0-9]*)/read([0-9]*).html$ /index.php?view=new_com&do=read&id=$2&menuid=$1';

        //Настройки по-умолчанию

        $_component['config']['param1'] = 1;
        $_component['config']['param2'] = 2;

        return $_component;

    }

// ========================================================================== //

    /*
     * Функция установки компонента.
     * Эта функция будет вызвана в момент установки.
     * Здесь можно сделать любую нужную подготовительную работу.
     */
    function install_component(){

        $inCore     = cmsCore::getInstance();       //подключаем ядро
        $inDB       = cmsDatabase::getInstance();   //подключаем базу данных

        //При установке компонента нужно создать используемые им таблицы в БД

        //Удаляем таблицу с категориями, если она уже была
        $sql = "DROP TABLE IF EXISTS `cms_meet_category`";
        $inDB->query($sql);

        //Создаем таблицу с категориями
        $sql = "CREATE TABLE `cms_meet_category` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `published` int(11) NOT NULL,
                  `title` varchar(255) NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `published` (`published`)
                ) ENGINE=MyISAM DEFAULT CHARSET=cp1251";
        $inDB->query($sql);

        //Удаляем таблицу с мероприятиями, если она уже была
        $sql = "DROP TABLE IF EXISTS `cms_meet_items`";
        $inDB->query($sql);

        //Создаем таблицу с мероприятиями
        $sql = "CREATE TABLE `cms_meet_items` (
                `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                `category_id` INT NOT NULL ,
                `pubdate` DATETIME NOT NULL ,
                `title` VARCHAR( 255 ) NOT NULL ,
                `published` INT NOT NULL ,
                INDEX ( `category_id` , `published` )
                ) ENGINE = MYISAM DEFAULT CHARSET=cp1251";
        $inDB->query($sql);

        //если установка считается удачной, возвращаем true
        //в противном случае можно вернуть строку, тогда система сообщит
        //пользователю о том что возникла ошибка при установке и покажет
        //полученное отсюда сообщение
        return true; 

    }

// ========================================================================== //

    /*
     * Функция обновления компонента.
     * Эта функция будет вызвана в момент обновления.
     * Здесь можно сделать нужную подготовительную работу, например
     * исправить структуру таблиц в базе
     */
    function upgrade_component(){

        $inCore     = cmsCore::getInstance();       //подключаем ядро
        $inDB       = cmsDatabase::getInstance();   //подключаем базу данных

        // ... делаем что-либо, если требуется ...

        //если обновление считается удачным, возвращаем true
        //в противном случае можно вернуть строку, тогда система сообщит
        //пользователю о том что возникла ошибка при обновлении и покажет
        //полученное отсюда сообщение
        return true;
        
    }

// ========================================================================== //

?>