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

    function routes_comments(){

        $routes[] = array(
                            '_uri'  => '/^comments\/add$/i',
                            'do'    => 'add'
                         );

        $routes[] = array(
                            '_uri'  => '/^comments\/edit$/i',
                            'do'    => 'edit'
                         );

        $routes[] = array(
                            '_uri'  => '/^comments\/delete\/([0-9]+)$/i',
                            'do'    => 'delete',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^comments\/page\-([0-9]+)$/i',
                            'do'    => 'view',
                            1       => 'page'
                         );

        return $routes;

    }

?>
