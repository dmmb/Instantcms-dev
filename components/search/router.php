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

    function routes_search(){

        //RewriteRule ^search/tag/(.*)/page([0-9]*).html$ /index.php?view=search&mode=tag&query=$1&page=$2
        $routes[] = array(
                            '_uri'  => '/^search\/tag\/(.+)\/page([0-9]+).html$/i',
                            'mode'  => 'tag',
                            1       => 'query',
                            2       => 'page'
                         );

        //RewriteRule ^search/tag/(.*)$ /index.php?view=search&mode=tag&query=$1
        $routes[] = array(
                            '_uri'  => '/^search\/tag\/(.+)$/i',
                            'mode'  => 'tag',
                            1       => 'query'
                         );

        return $routes;

    }

?>
