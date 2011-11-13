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

    function routes_board(){

        $routes[] = array(
                            '_uri'  => '/^board\/([0-9]+)$/i',
                            1       => 'category_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^board\/([0-9]+)\/type\/(.+)$/i',
                            1       => 'category_id',
                            2       => 'obtype'
                         );

        $routes[] = array(
                            '_uri'  => '/^board\/type\/(.+)$/i',
                               1       => 'obtype'
                         );

        $routes[] = array(
                            '_uri'  => '/^board\/([0-9]+)\-([0-9]+)$/i',
                            1       => 'category_id',
                            2       => 'page'
                         );

        $routes[] = array(
                            '_uri'  => '/^board\/([0-9]+)\/add.html$/i',
                            'do'    => 'additem',
                            1       => 'category_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^board\/add.html$/i',
                            'do'    => 'additem'
                         );

        $routes[] = array(
                            '_uri'  => '/^board\/edit([0-9]+).html$/i',
                            'do'    => 'edititem',
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
                            1       => 'city'
                         );

        return $routes;

    }

?>
