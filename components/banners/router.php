<?php

    function routes_banners(){

        $routes[] = array(
                            '_uri'  => '/^banners\/([0-9]+)$/i',
                            'do'    => 'click',
                            1       => 'id'
                         );

        return $routes;

    }

?>
