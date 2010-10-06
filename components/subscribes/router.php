<?php

    function routes_subscribes(){

        $routes[] = array(
                            '_uri'  => '/^subscribes\/([a-z]+)\/([0-9]+)\/([0-9]+)$/i',
                            1       => 'target',
                            2       => 'target_id',
                            3       => 'subscribe'
                         );

        return $routes;

    }

?>
