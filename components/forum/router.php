<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8.1                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

    function routes_forum(){

        //RewriteRule ^forum/unpinthread([0-9]*).html$ /index.php?view=forum&do=pin&id=$1&pinned=0
        $routes[] = array(
                            '_uri'  => '/^forum\/unpinthread([0-9]+).html$/i',
                            'do'    => 'pin',
                            1       => 'id',
                            'pinned'=> 0
                         );

        //RewriteRule ^forum/closethread([0-9]*).html$ /index.php?view=forum&do=close&id=$1&closed=1
        $routes[] = array(
                            '_uri'  => '/^forum\/closethread([0-9]+).html$/i',
                            'do'    => 'close',
                            1       => 'id',
                            'closed'=> 1
                         );

        //RewriteRule ^forum/openthread([0-9]*).html$ /index.php?view=forum&do=close&id=$1&closed=0
        $routes[] = array(
                            '_uri'  => '/^forum\/openthread([0-9]+).html$/i',
                            'do'    => 'close',
                            1       => 'id',
                            'closed'=> 0
                         );

        //RewriteRule ^forum/viewpoll([0-9]*).html$ /index.php?view=forum&do=thread&id=$1&viewpoll=1
        $routes[] = array(
                            '_uri'  => '/^forum\/viewpoll([0-9]+).html$/i',
                            'do'    => 'thread',
                            1       => 'id',
                            'viewpoll' => 1
                         );

        //RewriteRule ^forum/revote([0-9]*).html$ /index.php?view=forum&do=thread&id=$1&revote=1
        $routes[] = array(
                            '_uri'  => '/^forum\/revote([0-9]+).html$/i',
                            'do'    => 'thread',
                            1       => 'id',
                            'revote' => 1
                         );

        //RewriteRule ^forum/thread([0-9]*).html$ /index.php?view=forum&do=thread&id=$1
        $routes[] = array(
                            '_uri'  => '/^forum\/thread-last([0-9]+).html$/i',
                            'do'    => 'thread',
                            1       => 'id',
                            'go_last_post' => 1
                         );

        //RewriteRule ^forum/thread([0-9]*).html$ /index.php?view=forum&do=thread&id=$1
        $routes[] = array(
                            '_uri'  => '/^forum\/thread([0-9]+).html$/i',
                            'do'    => 'thread',
                            1       => 'id'
                         );

        //RewriteRule ^forum/thread([0-9]*)-([0-9]*).html$ /index.php?view=forum&do=thread&id=$1&page=$2
        $routes[] = array(
                            '_uri'  => '/^forum\/thread([0-9]+)\-([0-9]+).html$/i',
                            'do'    => 'thread',
                            1       => 'id',
                            2       => 'page'
                         );

        //RewriteRule ^forum/([0-9]*)/newthread.html$ /index.php?view=forum&do=newthread&id=$1
        $routes[] = array(
                            '_uri'  => '/^forum\/([0-9]+)\/newthread.html$/i',
                            'do'    => 'newthread',
                            1       => 'id'
                         );

        //RewriteRule ^forum/movethread([0-9]*).html$ /index.php?view=forum&do=movethread&id=$1
        $routes[] = array(
                            '_uri'  => '/^forum\/movethread([0-9]+).html$/i',
                            'do'    => 'movethread',
                            1       => 'id'
                         );

        //RewriteRule ^forum/renamethread([0-9]*).html$ /index.php?view=forum&do=renamethread&id=$1
        $routes[] = array(
                            '_uri'  => '/^forum\/renamethread([0-9]+).html$/i',
                            'do'    => 'renamethread',
                            1       => 'id'
                         );

        //RewriteRule ^forum/deletethread([0-9]*).html$ /index.php?view=forum&do=deletethread&id=$1
        $routes[] = array(
                            '_uri'  => '/^forum\/deletethread([0-9]+).html$/i',
                            'do'    => 'deletethread',
                            1       => 'id'
                         );

        //RewriteRule ^forum/pinthread([0-9]*).html$ /index.php?view=forum&do=pin&id=$1&pinned=1
        $routes[] = array(
                            '_uri'  => '/^forum\/pinthread([0-9]+).html$/i',
                            'do'    => 'pin',
                            1       => 'id',
                            'pinned'=> '1'
                         );

        //RewriteRule ^forum/reply([0-9]*).html$ /index.php?view=forum&do=newpost&id=$1
        $routes[] = array(
                            '_uri'  => '/^forum\/reply([0-9]+).html$/i',
                            'do'    => 'newpost',
                            1       => 'id'
                         );

        //RewriteRule ^forum/thread([0-9]*)-quote([0-9]*).html$ /index.php?view=forum&do=newpost&id=$1&replyid=$2
        $routes[] = array(
                            '_uri'  => '/^forum\/thread([0-9]+)\-quote([0-9]+).html$/i',
                            'do'    => 'newpost',
                            1       => 'id',
                            2       => 'replyid'
                         );

        //RewriteRule ^forum/download([0-9]*).html$ /index.php?view=forum&do=download&id=$1
        $routes[] = array(
                            '_uri'  => '/^forum\/download([0-9]+).html$/i',
                            'do'    => 'download',
                            1       => 'id'
                         );

        //RewriteRule ^forum/delfile([0-9]*).html$ /index.php?view=forum&do=delfile&id=$1
        $routes[] = array(
                            '_uri'  => '/^forum\/delfile([0-9]+).html$/i',
                            'do'    => 'delfile',
                            1       => 'id'
                         );

        //RewriteRule ^forum/reloadfile([0-9]*).html$ /index.php?view=forum&do=reloadfile&id=$1
        $routes[] = array(
                            '_uri'  => '/^forum\/reloadfile([0-9]+).html$/i',
                            'do'    => 'reloadfile',
                            1       => 'id'
                         );

        //RewriteRule ^forum/deletepost([0-9]*).html$ /index.php?view=forum&do=deletepost&id=$1
        $routes[] = array(
                            '_uri'  => '/^forum\/deletepost([0-9]+).html$/i',
                            'do'    => 'deletepost',
                            1       => 'id'
                         );

        //RewriteRule ^forum/editpost([0-9]*).html$ /index.php?view=forum&do=editpost&id=$1
        $routes[] = array(
                            '_uri'  => '/^forum\/editpost([0-9]+).html$/i',
                            'do'    => 'editpost',
                            1       => 'id'
                         );

        //RewriteRule ^forum/([0-9]+)$ /index.php?view=forum&do=forum&id=$1&page=1
        $routes[] = array(
                            '_uri'  => '/^forum\/([0-9]+)$/i',
                            'do'    => 'forum',
                            1       => 'id'
                         );

        //RewriteRule ^forum/([0-9]*)-([0-9]*)$ /index.php?view=forum&do=forum&id=$1&page=$2
        $routes[] = array(
                            '_uri'  => '/^forum\/([0-9]*)\-([0-9]*)$/i',
                            'do'    => 'forum',
                            1       => 'id',
                            2       => 'page'
                         );

        $routes[] = array(
                            '_uri'      => '/^forum\/(.*)$/i',
                            1           => 'is_404'
                         );

        return $routes;

    }

?>
