<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2010                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

    //
    // ВНИМАНИЕ! Если вы хотите добавить собственное правило, то создайте
    //           файл custom_rewrite.php и объявите в нем функцию
    //           custom_rewrite_rules() по аналогии с текущим файлом!
    //
    // В этом файле определены системные правила для редиректа и подмены адресов
    //
    //      source          : регулярное выражение, для сравнения с текущим URI
    //      target          : URI для перенаправления, при совпадении source
    //      action          : действие при совпадении source
    //
    // Возможные значения для action:
    //
    //      rewrite         : подменить URI перед определением компонента
    //      redirect        : редирект на target с кодом 303 See Other
    //      redirect-301    : редирект на target с кодом 301 Moved Permanently
    //      alias           : заинклудить файл target и остановить скрипт
    //

    function rewrite_rules(){

        //
        // Вход / Выход
        //


        $rules[] = array(
                            'source'  => '/^admin$/i',
                            'target'  => '/admin/index.php',
                            'action'  => 'redirect'
                         );

        $rules[] = array(
                            'source'  => '/^login$/i',
                            'target'  => 'registration/login',
                            'action'  => 'rewrite'
                         );

        $rules[] = array(
                            'source'  => '/^logout$/i',
                            'target'  => 'registration/logout',
                            'action'  => 'rewrite'
                         );

        $rules[] = array(
                            'source'  => '/^auth\/error.html$/i',
                            'target'  => 'registration/autherror',
                            'action'  => 'rewrite'
                         );

        $rules[] = array(
                            'source'  => '/^go\/url=(.+)$/i',
                            'target'  => 'files/go/{1}',
                            'action'  => 'rewrite'
                         );

        $rules[] = array(
                            'source'  => '/^load\/url=(.+)$/i',
                            'target'  => 'files/load/{1}',
                            'action'  => 'rewrite'
                         );

        //
        // Регистрация / Активация
        //

        $rules[] = array(
                            'source'  => '/^registration\/complete.html$/i',
                            'target'  => '/core/auth/regcomplete.html',
                            'action'  => 'alias'
                         );

        $rules[] = array(
                            'source'  => '/^registration\/activate.html$/i',
                            'target'  => '/core/auth/regactivate.html',
                            'action'  => 'alias'
                         );

        $rules[] = array(
                            'source'  => '/^activate\/(.+)$/i',
                            'target'  => 'registration/activate/{1}',
                            'action'  => 'rewrite'
                         );

        $rules[] = array(
                            'source'  => '/^passremind.html$/i',
                            'target'  => 'registration/passremind',
                            'action'  => 'rewrite'
                         );

        //
        // RSS
        //

        $rules[] = array(
                            'source'  => '/^rss\/([a-z]+)\/(.+)\/feed.rss$/i',
                            'target'  => 'rssfeed/{1}/{2}',
                            'action'  => 'rewrite'
                         );

        //
        // Внешние ссылки
        //

        $rules[] = array(
                            'source'  => '/^go\/url=(.+)$/i',
                            'target'  => 'files/go/{1}',
                            'action'  => 'rewrite'
                         );

        $rules[] = array(
                            'source'  => '/^load\/url=(.+)$/i',
                            'target'  => 'files/load/{1}',
                            'action'  => 'rewrite'
                         );

        $rules[] = array(
                            'source'  => '/^r([0-9]+)$/i',
                            'target'  => 'billing/ref_link/{1}',
                            'action'  => 'rewrite'
                         );
        
        //
        // Баннеры
        //

        $rules[] = array(
                            'source'  => '/^gobanner([0-9]+)$/i',
                            'target'  => 'banners/{1}',
                            'action'  => 'rewrite'
                         );

        //
        // Подписка
        //

        $rules[] = array(
                            'source'  => '/^subscribe\/([a-z]+)\/([0-9]+)$/i',
                            'target'  => 'subscribes/{1}/{2}/1',
                            'action'  => 'rewrite'
                         );

        $rules[] = array(
                            'source'  => '/^unsubscribe\/([a-z]+)\/([0-9]+)$/i',
                            'target'  => 'subscribes/{1}/{2}/0',
                            'action'  => 'rewrite'
                         );

        $rules[] = array(
                            'source'  => '/^forum\/subscribe([0-9]+).html$/i',
                            'target'  => 'subscribes/forum/{1}/1',
                            'action'  => 'rewrite'
                         );

        $rules[] = array(
                            'source'  => '/^forum\/unsubscribe([0-9]+).html$/i',
                            'target'  => 'subscribes/forum/{1}/0',
                            'action'  => 'rewrite'
                         );


        //
        // Старые адреса статей и разделов (1.5.x)
        //

        $rules[] = array(
                            'source'  => '/^content\/(.+)\/page\-([0-9]+).html$/i',
                            'target'  => '/{1}/page-{2}.html',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^content\/(.+)\/page\-([0-9]+)$/i',
                            'target'  => '/{1}/page-{2}',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^content\/([0-9]+)\/(.+).html$/i',
                            'target'  => '/{2}.html',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^content\/([0-9]+)\/(.+)$/i',
                            'target'  => '/{2}',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^content\/(.+)$/i',
                            'target'  => '/{1}',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^content\/([0-9]+)\/(.+)\/page\-([0-9]+).html$/i',
                            'target'  => '/{2}/page-{3}.html',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^content\/([0-9]+)\/(.+)\/page\-([0-9]+)$/i',
                            'target'  => '/{2}/page-{3}',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^(.+)\/$/i',
                            'target'  => '/{1}',
                            'action'  => 'redirect-301'
                         );

        //
        // Старые адреса постов и блогов (1.5.x)
        //

        $rules[] = array(
                            'source'  => '/^blogs\/([0-9]+)\/([a-zA-Z0-9\-]+)$/i',
                            'target'  => '/blogs/{2}',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^blogs\/([0-9]+)\/([a-zA-Z0-9\-]+)\/$/i',
                            'target'  => '/blogs/{2}',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^blogs\/([0-9]+)\/([a-zA-Z0-9\-]+)\/page\-([0-9]+)$/i',
                            'target'  => '/blogs/{2}/page-{3}',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^blogs\/([0-9]+)\/([a-zA-Z0-9\-]+)\/cat\-([0-9]+)$/i',
                            'target'  => '/blogs/{2}/cat-{3}',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^blogs\/([0-9]+)\/([a-zA-Z0-9\-]+)\/page\-([0-9]+)\/cat\-([0-9]+)$/i',
                            'target'  => '/blogs/{2}/page-{3}/cat-{4}',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^blogs\/([0-9]+)\/([a-zA-Z0-9\-]+)\/([a-zA-Z0-9\-]+).html$/i',
                            'target'  => '/blogs/{2}/{3}.html',
                            'action'  => 'redirect-301'
                         );

        //
        // Старые адреса форума (1.5.x)
        //

        $rules[] = array(
                            'source'  => '/^forum\/([0-9]+)\/([0-9]+)$/i',
                            'target'  => '/forum/{2}',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^forum\/([0-9]*)\/([0-9]*)-([0-9]*)$/i',
                            'target'  => '/forum/{2}-{3}',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^forum\/([0-9]*)\/thread([0-9]+).html$/i',
                            'target'  => '/forum/thread{2}.html',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^forum\/([0-9]*)\/thread([0-9]+)\-([0-9]+).html$/i',
                            'target'  => '/forum/thread{2}-{3}.html',
                            'action'  => 'redirect-301'
                         );

        //
        // Старые адреса каталога (1.5.x)
        //

        $rules[] = array(
                            'source'  => '/^catalog\/([0-9]+)\/([0-9]+)$/i',
                            'target'  => '/catalog/{2}',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^catalog\/([0-9]*)\/([0-9]*)-([0-9]*)$/i',
                            'target'  => '/catalog/{2}-{3}',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^catalog\/([0-9]*)\/item([0-9]+).html$/i',
                            'target'  => '/catalog/item{2}.html',
                            'action'  => 'redirect-301'
                         );

        return $rules;

    }

?>
