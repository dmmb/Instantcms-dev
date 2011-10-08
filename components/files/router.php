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

    function routes_files(){

        $routes[] = array(
                            '_uri'  => '/^files\/go\/(.+)/i',
                            'do'    => 'redirect',
                            1       => 'url'
                         );

        $routes[] = array(
                            '_uri'  => '/^files\/load\/(.+)/i',
                            'do'    => 'download',
                            1       => 'fileurl'
                         );

        return $routes;

    }

?>
