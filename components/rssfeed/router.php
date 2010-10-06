<?php

    function routes_rssfeed(){

        $routes[] = array(
                            '_uri'  => '/^rssfeed\/([a-z]+)\/(.+)$/i',
                            1       => 'target',
                            2       => 'item_id'
                         );

        return $routes;

    }

?>
