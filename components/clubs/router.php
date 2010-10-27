<?php

    function routes_clubs(){

        //RewriteRule ^clubs/page-([0-9]*)$ /index.php?view=clubs&do=view&page=$1
        $routes[] = array(
                            '_uri'  => '/^clubs\/page\-([0-9]+)$/i',
                            'do'    => 'view',
                            1       => 'page'
                         );

        //RewriteRule ^clubs/create.html$ /index.php?view=clubs&do=create
        $routes[] = array(
                            '_uri'  => '/^clubs\/create.html$/i',
                            'do'    => 'create'
                         );

        //RewriteRule ^clubs/([0-9]*)$ /index.php?view=clubs&do=club&id=$1
        $routes[] = array(
                            '_uri'  => '/^clubs\/([0-9]+)$/i',
                            'do'    => 'club',
                            1       => 'id'
                         );

        //RewriteRule ^clubs/([0-9]*)/albums$ /index.php?view=clubs&do=albums&id=$1
        $routes[] = array(
                            '_uri'  => '/^clubs\/([0-9]+)\/albums$/i',
                            'do'    => 'albums',
                            1       => 'id'
                         );

        //RewriteRule ^clubs/([0-9]*)/albums/([0-9]*)$ /index.php?view=clubs&do=album&id=$1&album_id=$2
        $routes[] = array(
                            '_uri'  => '/^clubs\/([0-9]+)\/albums\/([0-9]+)$/i',
                            'do'    => 'album',
                            1       => 'id',
                            2       => 'album_id'
                         );

        //RewriteRule ^clubs/([0-9]*)/leave.html$ /index.php?view=clubs&do=leave&id=$1
        $routes[] = array(
                            '_uri'  => '/^clubs\/([0-9]+)\/leave.html$/i',
                            'do'    => 'leave',
                            1       => 'id'
                         );

        //RewriteRule ^clubs/([0-9]*)/join.html$ /index.php?view=clubs&do=join&id=$1
        $routes[] = array(
                            '_uri'  => '/^clubs\/([0-9]+)\/join.html$/i',
                            'do'    => 'join',
                            1       => 'id'
                         );

        //RewriteRule ^clubs/([0-9]*)/config.html$ /index.php?view=clubs&do=config&id=$1
        $routes[] = array(
                            '_uri'  => '/^clubs\/([0-9]+)\/config.html$/i',
                            'do'    => 'config',
                            1       => 'id'
                         );

        return $routes;

    }

?>
