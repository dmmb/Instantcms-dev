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

    function routes_subscribes(){

        $routes[] = array(
                            '_uri'  => '/^subscribes\/([a-z]+)\/([0-9]+)\/([0-9]+)$/i',
                            1       => 'target',
                            2       => 'target_id',
                            3       => 'subscribe'
                         );

        return $routes;

    }

?>
