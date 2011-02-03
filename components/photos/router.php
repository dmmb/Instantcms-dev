<?php

    function routes_photos(){
        
        //RewriteRule ^photos/([0-9]*)$ /index.php?view=photos&id=$1
        $routes[] = array(
                            '_uri'  => '/^photos\/([0-9]+)$/i',
                            1    => 'id'
                         );

        //RewriteRule ^photos/([0-9]*)/byuser([0-9]*).html$ /index.php?view=photos&id=$1&userid=$2
        $routes[] = array(
                            '_uri'  => '/^photos\/([0-9]+)\/byuser([0-9]+).html$/i',
                            1       => 'id',
                            2       => 'userid'
                         );

        //RewriteRule ^photos/([0-9]*)/([0-9]*)-([0-9]*)$ /index.php?view=photos&id=$1&page=$2
        $routes[] = array(
                            '_uri'  => '/^photos\/([0-9]+)\-([0-9]+)$/i',
                            1       => 'id',
                            2       => 'page'
                         );

        //RewriteRule ^photos/latest.html$ /index.php?view=photos&do=latest
        $routes[] = array(
                            '_uri'  => '/^photos\/latest.html$/i',
                            'do'    => 'latest'
                         );

        //RewriteRule ^photos/top.html$ /index.php?view=photos&do=best
        $routes[] = array(
                            '_uri'  => '/^photos\/top.html$/i',
                            'do'    => 'best'
                         );

        //RewriteRule ^photos/([0-9]*)/addphoto.html$ /index.php?view=photos&id=$1&do=addphoto
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

        //RewriteRule ^photos/([0-9]*)/uploaded.html$ /index.php?view=photos&id=$1&do=uploaded
        $routes[] = array(
                            '_uri'  => '/^photos\/([0-9]+)\/uploaded.html$/i',
                            'do'    => 'uploaded',
                            1       => 'id'
                         );

        //RewriteRule ^photos/editphoto([0-9]*).html$ /index.php?view=photos&id=$1&do=editphoto
        $routes[] = array(
                            '_uri'  => '/^photos\/editphoto([0-9]+).html$/i',
                            'do'    => 'editphoto',
                            1       => 'id'
                         );

        //RewriteRule ^photos/movephoto([0-9]*).html$ /index.php?view=photos&id=$1&do=movephoto
        $routes[] = array(
                            '_uri'  => '/^photos\/movephoto([0-9]+).html$/i',
                            'do'    => 'movephoto',
                            1       => 'id'
                         );

        //RewriteRule ^photos/delphoto([0-9]*).html$ /index.php?view=photos&id=$1&do=delphoto
        $routes[] = array(
                            '_uri'  => '/^photos\/delphoto([0-9]+).html$/i',
                            'do'    => 'delphoto',
                            1       => 'id'
                         );

        //RewriteRule ^photos/publish([0-9]*).html$ /index.php?view=photos&id=$1&do=pubphoto
        $routes[] = array(
                            '_uri'  => '/^photos\/publish([0-9]+).html$/i',
                            'do'    => 'pubphoto',
                            1       => 'id'
                         );

        //RewriteRule ^photos/photo([0-9]*).html$ /index.php?view=photos&do=viewphoto&id=$1
        $routes[] = array(
                            '_uri'  => '/^photos\/photo([0-9]+).html$/i',
                            'do'    => 'viewphoto',
                            1       => 'id'
                         );

        return $routes;

    }

?>
