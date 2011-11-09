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

    function routes_clubs(){

        $routes[] = array(
                            '_uri'  => '/^clubs\/page\-([0-9]+)$/i',
                            'do'    => 'view',
                            1       => 'page'
                         );

        $routes[] = array(
                            '_uri'  => '/^clubs\/create.html$/i',
                            'do'    => 'create'
                         );

        $routes[] = array(
                            '_uri'  => '/^clubs\/([0-9]+)\/message\-members.html$/i',
                            'do'    => 'send_message',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^clubs\/([0-9]+)\/join_member.html$/i',
                            'do'    => 'join_member',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^clubs\/([0-9]+)$/i',
                            'do'    => 'club',
                            1       => 'id'
                         );


        $routes[] = array(
                            '_uri'  => '/^clubs\/([0-9]+)\/leave.html$/i',
                            'do'    => 'leave',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^clubs\/([0-9]+)\/join.html$/i',
                            'do'    => 'join',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^clubs\/([0-9]+)\/config.html$/i',
                            'do'    => 'config',
                            1       => 'id'
                         );

        return $routes;

    }

?>
