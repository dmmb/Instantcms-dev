<?php

    function routes_content(){

        //RewriteRule ^content/top.html$ /index.php?view=content&do=best
        $routes[] = array(
                            '_uri'  => '/^content\/top.html$/i',
                            'do'    => 'best'
                         );

        //RewriteRule ^content/add.html$ /index.php?view=content&do=addarticle
        $routes[] = array(
                            '_uri'  => '/^content\/add.html$/i',
                            'do'    => 'addarticle'
                         );

        //RewriteRule ^content/edit([0-9]*).html$ /index.php?view=content&do=editarticle&id=$1
        $routes[] = array(
                            '_uri'  => '/^content\/edit([0-9]+).html$/i',
                            'do'    => 'editarticle',
                            1       => 'id'
                         );

        //RewriteRule ^content/delete([0-9]*).html$ /index.php?view=content&do=deletearticle&id=$1
        $routes[] = array(
                            '_uri'  => '/^content\/delete([0-9]+).html$/i',
                            'do'    => 'deletearticle',
                            1       => 'id'
                         );

        //RewriteRule ^content/my.html$ /index.php?view=content&do=my&page=1
        $routes[] = array(
                            '_uri'  => '/^content\/my.html$/i',
                            'do'    => 'my',
                            1       => 'page'
                         );

        //RewriteRule ^content/my([0-9]*).html$ /index.php?view=content&do=my&page=$1
        $routes[] = array(
                            '_uri'  => '/^content\/my([0-9]+).html$/i',
                            'do'    => 'my',
                            1       => 'page'
                         );

        //RewriteRule ^content/(.*)/page-([0-9]*).html$ /index.php?view=content&do=read&seolink=$1&page=$2
        $routes[] = array(
                            '_uri'      => '/^content\/(.+)\/page\-([0-9]+).html$/i',
                            'do'        => 'read',
                            1           => 'seolink',
                            2           => 'page'
                         );

        //RewriteRule ^content/(.*).html$ /index.php?view=content&do=read&seolink=$1
        $routes[] = array(
                            '_uri'      => '/^content\/(.+).html$/i',
                            'do'        => 'read',
                            1           => 'seolink'
                         );

        //RewriteRule ^content/(.*)/page-([0-9]*)$ /index.php?view=content&do=view&seolink=$1&page=$2
        $routes[] = array(
                            '_uri'      => '/^content\/(.+)\/page\-([0-9]+)$/i',
                            'do'        => 'view',
                            1           => 'seolink',
                            2           => 'page'
                         );

        //RewriteRule ^content/(.*)$ /index.php?view=content&do=view&seolink=$1
        $routes[] = array(
                            '_uri'      => '/^content\/(.+)$/i',
                            'do'        => 'view',
                            1           => 'seolink'
                         );

        return $routes;

    }

?>
