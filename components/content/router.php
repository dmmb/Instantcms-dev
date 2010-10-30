<?php

    function routes_content(){

        $routes[] = array(
                            '_uri'  => '/^content\/top.html$/i',
                            'do'    => 'best'
                         );

        $routes[] = array(
                            '_uri'  => '/^content\/add.html$/i',
                            'do'    => 'addarticle'
                         );

        $routes[] = array(
                            '_uri'  => '/^content\/edit([0-9]+).html$/i',
                            'do'    => 'editarticle',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^content\/delete([0-9]+).html$/i',
                            'do'    => 'deletearticle',
                            1       => 'id'
                         );
						 
        $routes[] = array(
                            '_uri'  => '/^content\/publish([0-9]+).html$/i',
                            'do'    => 'publisharticle',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^content\/my.html$/i',
                            'do'    => 'my',
                            1       => 'page'
                         );

        $routes[] = array(
                            '_uri'  => '/^content\/my([0-9]+).html$/i',
                            'do'    => 'my',
                            1       => 'page'
                         );

        $routes[] = array(
                            '_uri'      => '/^content\/(.+)\/page\-([0-9]+).html$/i',
                            'do'        => 'read',
                            1           => 'seolink',
                            2           => 'page'
                         );

        $routes[] = array(
                            '_uri'      => '/^content\/(.+).html$/i',
                            'do'        => 'read',
                            1           => 'seolink'
                         );

        $routes[] = array(
                            '_uri'      => '/^content\/(.+)\/page\-([0-9]+)$/i',
                            'do'        => 'view',
                            1           => 'seolink',
                            2           => 'page'
                         );

        $routes[] = array(
                            '_uri'      => '/^content\/(.*)$/i',
                            'do'        => 'view',
                            1           => 'seolink'
                         );

        return $routes;

    }

?>
