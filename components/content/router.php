<?php

    $request_uri = $_GET['q'];

    echo '<pre>'.$request_uri.'</pre>';

    $routes[] = array(
                        '_uri'  => '/^\/content\/top.html$/i',
                        'do'    => 'best'
                     );

    $routes[] = array(
                        '_uri'  => '/^\/content\/add.html$/i',
                        'do'    => 'addarticle'
                     );

    $routes[] = array(
                        '_uri'  => '/^\/content\/edit([0-9]+).html$/i',
                        'do'    => 'editarticle',
                        'id'    => '$1'
                     );

    $routes[] = array(
                        '_uri'  => '/^\/content\/delete([0-9]+).html$/i',
                        'do'    => 'deletearticle',
                        'id'    => '$1'
                     );

    $routes[] = array(
                        '_uri'  => '/^\/content\/my.html$/i',
                        'do'    => 'my',
                        'page'  => 1
                     );

    $routes[] = array(
                        '_uri'  => '/^\/content\/my([0-9]+).html$/i',
                        'do'    => 'my',
                        'page'  => '$1'
                     );

    $routes[] = array(
                        '_uri'      => '/^\/content\/(.+)\/page\-([0-9]+).html$/i',
                        'do'        => 'read',
                        'seolink'   => '$1',
                        'page'      => '$2'
                     );

    $routes[] = array(
                        '_uri'      => '/^\/content\/(.+).html$/i',
                        'do'        => 'read',
                        'seolink'   => '$1'
                     );

    $routes[] = array(
                        '_uri'      => '/^\/content\/(.+)\/page\-([0-9]+)$/i',
                        'do'        => 'view',
                        'seolink'   => '$1',
                        'page'      => '$2'
                     );

    $routes[] = array(
                        '_uri'      => '/^\/content\/(.+)$/i',
                        'do'        => 'view',
                        'seolink'   => '$1'
                     );

    foreach($routes as $route){

        preg_match($route['_uri'], $request_uri, $matches);

        if ($matches){
            echo '<pre>'; print_r($matches); echo '</pre>';
            break;
        }

    }

?>
