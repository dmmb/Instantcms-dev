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

    function routes_actions(){

        $routes[] = array(
                            '_uri'  => '/^actions\/delete\/([0-9]+)$/i',
                            'do'    => 'delete',
                            1       => 'id'
                         );

        return $routes;

    }

?>
