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

    function routes_photos(){
        
        $routes[] = array(
                            '_uri'  => '/^photos\/([0-9]+)$/i',
                            1    => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^photos\/([0-9]+)\/byuser([0-9]+).html$/i',
                            1       => 'id',
                            2       => 'userid'
                         );

        $routes[] = array(
                            '_uri'  => '/^photos\/([0-9]+)\-([0-9]+)$/i',
                            1       => 'id',
                            2       => 'page'
                         );

        $routes[] = array(
                            '_uri'  => '/^photos\/latest.html$/i',
                            'do'    => 'latest'
                         );

        $routes[] = array(
                            '_uri'  => '/^photos\/top.html$/i',
                            'do'    => 'best'
                         );

        $routes[] = array(
                            '_uri'  => '/^photos\/([0-9]+)\/addphoto.html$/i',
                            'do'    => 'addphoto',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^photos\/([0-9]+)\/submit_photo.html$/i',
                            'do'    => 'submit_photo',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^photos\/([0-9]+)\/upload$/i',
                            'do'    => 'uploadphotos',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^photos\/([0-9]+)\/uploaded.html$/i',
                            'do'    => 'uploaded',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^photos\/editphoto([0-9]+).html$/i',
                            'do'    => 'editphoto',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^photos\/movephoto([0-9]+).html$/i',
                            'do'    => 'movephoto',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^photos\/delphoto([0-9]+).html$/i',
                            'do'    => 'delphoto',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^photos\/publish([0-9]+).html$/i',
                            'do'    => 'pubphoto',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^photos\/photo([0-9]+).html$/i',
                            'do'    => 'viewphoto',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'      => '/^photos\/(.*)$/i',
                            1           => 'is_404'
                         );

        return $routes;

    }

?>
