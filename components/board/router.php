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

    function routes_board(){

        //RewriteRule ^board/([0-9]*)$ /index.php?view=board&id=$1
        $routes[] = array(
                            '_uri'  => '/^board\/([0-9]+)$/i',
                            1       => 'id'
                         );

        //RewriteRule ^board/([0-9]*)/type/(.*)$ /index.php?view=board&id=$1&obtype=$2
        $routes[] = array(
                            '_uri'  => '/^board\/([0-9]+)\/type\/(.+)$/i',
                            1       => 'id',
                            2       => 'obtype'
                         );

        //RewriteRule ^board/([0-9]*)-([0-9]*)$ /index.php?view=board&id=$1&page=$2
        $routes[] = array(
                            '_uri'  => '/^board\/([0-9]+)\-([0-9]+)$/i',
                            1       => 'id',
                            2       => 'page'
                         );

        //RewriteRule ^board/latest.html$ /index.php?view=board&do=latest
        $routes[] = array(
                            '_uri'  => '/^board\/latest.html$/i',
                            'do'    => 'latest'
                         );

        //RewriteRule ^board/([0-9]+)/add.html$ /index.php?view=board&id=$1&do=additem
        $routes[] = array(
                            '_uri'  => '/^board\/([0-9]+)\/add.html$/i',
                            'do'    => 'additem',
                            1       => 'id'
                         );

        //RewriteRule ^board/([0-9]*)/edit([0-9]*).html$ /index.php?view=board&id=$1&do=edititem
        $routes[] = array(
                            '_uri'  => '/^board\/edit([0-9]+).html$/i',
                            'do'    => 'edititem',
                            1       => 'id'
                         );

        //RewriteRule ^board/moveitem([0-9]*).html$ /index.php?view=board&id=$1&do=moveitem
        $routes[] = array(
                            '_uri'  => '/^board\/moveitem([0-9]+).html$/i',
                            'do'    => 'moveitem',
                            1       => 'id'
                         );

        //RewriteRule ^board/delete([0-9]*).html$ /index.php?view=board&id=$1&do=delete
        $routes[] = array(
                            '_uri'  => '/^board\/delete([0-9]+).html$/i',
                            'do'    => 'delete',
                            1       => 'id'
                         );

        //RewriteRule ^board/read([0-9]*).html$ /index.php?view=board&do=read&id=$1
        $routes[] = array(
                            '_uri'  => '/^board\/read([0-9]+).html$/i',
                            'do'    => 'read',
                            1       => 'id'
                         );

        //RewriteRule ^board/city/(.*)$ /index.php?view=board&do=city&city=$1
        $routes[] = array(
                            '_uri'  => '/^board\/city\/(.+)$/i',
                            'do'    => 'city',
                            1       => 'city'
                         );

        return $routes;

    }

?>
