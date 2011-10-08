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

    function routes_polls(){

        //RewriteRule ^polls/vote$ /index.php?view=polls&do=vote
        $routes[] = array(
                            '_uri'  => '/^polls\/vote$/i',
                            'do'    => 'vote'
                         );

        return $routes;

    }

?>
