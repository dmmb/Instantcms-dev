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

    function routes_board(){

        $routes[] = array(
                            '_uri'  => '/^board\/([0-9]+)$/i',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^board\/([0-9]+)\/type\/(.+)$/i',
                            1       => 'id',
                            2       => 'obtype'
                         );

        $routes[] = array(
                            '_uri'  => '/^board\/([0-9]+)\-([0-9]+)$/i',
                            1       => 'id',
                            2       => 'page'
                         );

        $routes[] = array(
                            '_uri'  => '/^board\/latest.html$/i',
                            'do'    => 'latest'
                         );

        $routes[] = array(
                            '_uri'  => '/^board\/([0-9]+)\/add.html$/i',
                            'do'    => 'additem',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^board\/edit([0-9]+).html$/i',
                            'do'    => 'edititem',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^board\/moveitem([0-9]+).html$/i',
                            'do'    => 'moveitem',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^board\/delete([0-9]+).html$/i',
                            'do'    => 'delete',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^board\/read([0-9]+).html$/i',
                            'do'    => 'read',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^board\/city\/(.+)$/i',
                            'do'    => 'city',
                            1       => 'city'
                         );

        $routes[] = array(
                            '_uri'      => '/^board\/(.*)$/i',
                            1           => 'is_404'
                         );

        return $routes;

    }

?>
