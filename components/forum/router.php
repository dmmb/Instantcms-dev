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

    function routes_forum(){

        $routes[] = array(
                            '_uri'  => '/^forum\/latest_posts$/i',
                            'do'    => 'latest_posts'
                         );

        $routes[] = array(
                            '_uri'  => '/^forum\/latest_posts\/page\-([0-9]+)$/i',
                            'do'    => 'latest_posts',
                            1       => 'page'
                         );

        $routes[] = array(
                            '_uri'  => '/^forum\/latest_thread$/i',
                            'do'    => 'latest_thread'
                         );

        $routes[] = array(
                            '_uri'  => '/^forum\/latest_thread\/page\-([0-9]+)$/i',
                            'do'    => 'latest_thread',
                            1       => 'page'
                         );

        $routes[] = array(
                            '_uri'  => '/^forum\/unpinthread([0-9]+).html$/i',
                            'do'    => 'pin',
                            1       => 'id',
                            'pinned'=> 0
                         );

        $routes[] = array(
                            '_uri'  => '/^forum\/closethread([0-9]+).html$/i',
                            'do'    => 'close',
                            1       => 'id',
                            'closed'=> 1
                         );

        $routes[] = array(
                            '_uri'  => '/^forum\/openthread([0-9]+).html$/i',
                            'do'    => 'close',
                            1       => 'id',
                            'closed'=> 0
                         );

        $routes[] = array(
                            '_uri'  => '/^forum\/viewpoll([0-9]+).html$/i',
                            'do'    => 'thread',
                            1       => 'id',
                            'viewpoll' => 1
                         );

        $routes[] = array(
                            '_uri'  => '/^forum\/revote([0-9]+).html$/i',
                            'do'    => 'thread',
                            1       => 'id',
                            'revote' => 1
                         );

        $routes[] = array(
                            '_uri'  => '/^forum\/thread-last([0-9]+).html$/i',
                            'do'    => 'thread',
                            1       => 'id',
                            'go_last_post' => 1
                         );

        $routes[] = array(
                            '_uri'  => '/^forum\/thread([0-9]+).html$/i',
                            'do'    => 'thread',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^forum\/thread([0-9]+)\-([0-9]+).html$/i',
                            'do'    => 'thread',
                            1       => 'id',
                            2       => 'page'
                         );

        $routes[] = array(
                            '_uri'  => '/^forum\/([0-9]+)\/newthread.html$/i',
                            'do'    => 'newthread',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^forum\/movethread([0-9]+).html$/i',
                            'do'    => 'movethread',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^forum\/renamethread([0-9]+).html$/i',
                            'do'    => 'renamethread',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^forum\/deletethread([0-9]+).html$/i',
                            'do'    => 'deletethread',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^forum\/pinthread([0-9]+).html$/i',
                            'do'    => 'pin',
                            1       => 'id',
                            'pinned'=> '1'
                         );

        $routes[] = array(
                            '_uri'  => '/^forum\/reply([0-9]+).html$/i',
                            'do'    => 'newpost',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^forum\/thread([0-9]+)\-quote([0-9]+).html$/i',
                            'do'    => 'newpost',
                            1       => 'id',
                            2       => 'replyid'
                         );

        $routes[] = array(
                            '_uri'  => '/^forum\/download([0-9]+).html$/i',
                            'do'    => 'download',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^forum\/delfile([0-9]+).html$/i',
                            'do'    => 'delfile',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^forum\/reloadfile([0-9]+).html$/i',
                            'do'    => 'reloadfile',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^forum\/deletepost([0-9]+).html$/i',
                            'do'    => 'deletepost',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^forum\/editpost([0-9]+).html$/i',
                            'do'    => 'editpost',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^forum\/([0-9]+)$/i',
                            'do'    => 'forum',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^forum\/([0-9]*)\-([0-9]*)$/i',
                            'do'    => 'forum',
                            1       => 'id',
                            2       => 'page'
                         );

        $routes[] = array(
                            '_uri'  => '/^forum\/([a-zA-Z0-9\-]+)$/i',
                            'do'    => 'view_cat',
                            1       => 'seolink'
                         );

        $routes[] = array(
                            '_uri'      => '/^forum\/(.*)$/i',
                            1           => 'is_404'
                         );

        return $routes;

    }

?>
