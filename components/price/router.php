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

    function routes_price(){

        //RewriteRule ^price/([0-9]*)$ /index.php?view=price&id=$1
        $routes[] = array(
                            '_uri'  => '/^price\/([0-9]+)$/i',
                            1       => 'id'
                         );

        //RewriteRule ^price/([0-9]*)-([0-9]*)$ /index.php?view=price&id=$1&page=$2
        $routes[] = array(
                            '_uri'  => '/^price\/([0-9]+)\-([0-9]+)$/i',
                            1       => 'id',
                            2       => 'page'
                         );

        //RewriteRule ^price/cart.html$ /index.php?view=price&do=cart
        $routes[] = array(
                            '_uri'  => '/^price\/cart.html$/i',
                            'do'    => 'cart'
                         );

        //RewriteRule ^price/cart.html$ /index.php?view=price&do=cart
        $routes[] = array(
                            '_uri'  => '/^price\/cart\/removeitem([0-9]+).html$/i',
                            'do'    => 'removeitem',
                            1       => 'id'
                         );

        //RewriteRule ^price/order.html$ /index.php?view=price&do=order
        $routes[] = array(
                            '_uri'  => '/^price\/order.html$/i',
                            'do'    => 'order'
                         );

        //RewriteRule ^price/finish.html$ /index.php?view=price&do=finish
        $routes[] = array(
                            '_uri'  => '/^price\/finish.html$/i',
                            'do'    => 'finish'
                         );

        return $routes;

    }

?>
