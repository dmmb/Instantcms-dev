<?php

    function routes_comments(){

        $routes[] = array(
                            '_uri'  => '/^comments\/add$/i',
                            'do'    => 'add'
                         );

        $routes[] = array(
                            '_uri'  => '/^comments\/edit$/i',
                            'do'    => 'edit'
                         );

        $routes[] = array(
                            '_uri'  => '/^comments\/delete\/([0-9]+)$/i',
                            'do'    => 'delete',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^comments\/page\-([0-9]+)$/i',
                            'do'    => 'view',
                            1       => 'page'
                         );

        return $routes;

    }

?>
