<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2010                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

    function routes_catalog(){


        //RewriteRule ^catalog/([0-9]*)/tag/(.*)$ /index.php?view=catalog&do=tag&cat_id=$1&tag=$2
        $routes[] = array(
                            '_uri'  => '/^catalog\/([0-9]+)\/tag\/(.+)$/i',
                            'do'    => 'tag',
                            1       => 'cat_id',
                            2       => 'tag',
                         );

        //RewriteRule ^catalog/([0-9]*)-([0-9]*)/tag/(.*)$ /index.php?view=catalog&do=tag&cat_id=$1&page=$2&tag=$3
        $routes[] = array(
                            '_uri'  => '/^catalog\/([0-9]+)\-([0-9]*)\/tag\/(.+)$/i',
                            'do'    => 'tag',
                            1       => 'cat_id',
                            2       => 'tag',
                            3       => 'page'
                         );

        //RewriteRule ^catalog/([0-9]*)/find-first/(.*)$ /index.php?view=catalog&do=findfirst&cat_id=$1&text=$2
        $routes[] = array(
                            '_uri'  => '/^catalog\/([0-9]+)\/find\-first\/(.+)$/i',
                            'do'    => 'findfirst',
                            1       => 'cat_id',
                            2       => 'text'
                         );

        //RewriteRule ^catalog/([0-9]*)-([0-9]*)/find-first/(.*)$ /index.php?view=catalog&do=findfirst&cat_id=$1&page=$2&text=$3
        $routes[] = array(
                            '_uri'  => '/^catalog\/([0-9]+)\-([0-9]+)\/find\-first\/(.+)$/i',
                            'do'    => 'findfirst',
                            1       => 'cat_id',
                            2       => 'page',
                            3       => 'text'
                         );

        //RewriteRule ^catalog/([0-9]*)/find/(.*)$ /index.php?view=catalog&do=find&cat_id=$1&text=$2
        $routes[] = array(
                            '_uri'  => '/^catalog\/([0-9]+)\/find\/(.*)$/i',
                            'do'    => 'find',
                            1       => 'cat_id',
                            2       => 'text'
                         );

        //RewriteRule ^catalog/([0-9]*)-([0-9]*)/find/(.*)$ /index.php?view=catalog&do=find&cat_id=$1&page=$2&text=$3
        $routes[] = array(
                            '_uri'  => '/^catalog\/([0-9]+)\-([0-9]+)\/find\/(.+)$/i',
                            'do'    => 'find',
                            1       => 'cat_id',
                            2       => 'page',
                            3       => 'text'
                         );

        //RewriteRule ^catalog/([0-9]*)$ /index.php?view=catalog&do=cat&cat_id=$1
        $routes[] = array(
                            '_uri'  => '/^catalog\/([0-9]+)$/i',
                            'do'    => 'cat',
                            1       => 'cat_id'
                         );

        //RewriteRule ^catalog/([0-9]*)-([0-9]*)$ /index.php?view=catalog&do=cat&cat_id=$1&page=$2
        $routes[] = array(
                            '_uri'  => '/^catalog\/([0-9]+)\-([0-9]+)$/i',
                            'do'    => 'cat',
                            1       => 'cat_id',
                            2       => 'page'
                         );

        //RewriteRule ^catalog/([0-9]*)/add.html$ /index.php?view=catalog&do=add_item&cat_id=$1
        $routes[] = array(
                            '_uri'  => '/^catalog\/([0-9]+)\/add.html$/i',
                            'do'    => 'add_item',
                            1       => 'cat_id'
                         );

        //RewriteRule ^catalog/([0-9]*)/add.html$ /index.php?view=catalog&do=add_item&cat_id=$1
        $routes[] = array(
                            '_uri'  => '/^catalog\/([0-9]+)\/edit([0-9]+).html$/i',
                            'do'    => 'edit_item',
                            1       => 'cat_id',
                            2       => 'item_id'
                         );

        //RewriteRule ^catalog/([0-9]*)/submit.html$ /index.php?view=catalog&do=submit_item&cat_id=$1
        $routes[] = array(
                            '_uri'  => '/^catalog\/([0-9]+)\/submit.html$/i',
                            'do'    => 'submit_item',
                            1       => 'cat_id'
                         );

        //RewriteRule ^catalog/moderation/accept([0-9]*).html$ /index.php?view=catalog&do=accept_item&item_id=$1
        $routes[] = array(
                            '_uri'  => '/^catalog\/moderation\/accept([0-9]+).html$/i',
                            'do'    => 'accept_item',
                            1       => 'item_id'
                         );

        //RewriteRule ^catalog/moderation/reject([0-9]*).html$ /index.php?view=catalog&do=delete_item&item_id=$1
        $routes[] = array(
                            '_uri'  => '/^catalog\/moderation\/reject([0-9]+).html$/i',
                            'do'    => 'delete_item',
                            1       => 'item_id'
                         );

        //RewriteRule ^catalog/([0-9]*)/search.html$ /index.php?view=catalog&do=search&cat_id=$1
        $routes[] = array(
                            '_uri'  => '/^catalog\/([0-9]+)\/search.html$/i',
                            'do'    => 'search',
                            1       => 'cat_id'
                         );

        //RewriteRule ^catalog/item([0-9]*).html$ /index.php?view=catalog&do=item&id=$1
        $routes[] = array(
                            '_uri'  => '/^catalog\/item([0-9]+).html$/i',
                            'do'    => 'item',
                            1       => 'id'
                         );

        //RewriteRule ^catalog/addcart([0-9]*).html$ /index.php?view=catalog&do=addcart&id=$1
        $routes[] = array(
                            '_uri'  => '/^catalog\/addcart([0-9]+).html$/i',
                            'do'    => 'addcart',
                            1       => 'id'
                         );

        //RewriteRule ^catalog/cartremove([0-9]*).html$ /index.php?view=catalog&do=cartremove&id=$1
        $routes[] = array(
                            '_uri'  => '/^catalog\/cartremove([0-9]+).html$/i',
                            'do'    => 'cartremove',
                            1       => 'id'
                         );

        //RewriteRule ^catalog/viewcart.html$ /index.php?view=catalog&do=viewcart
        $routes[] = array(
                            '_uri'  => '/^catalog\/viewcart.html$/i',
                            'do'    => 'viewcart'
                         );

        //RewriteRule ^catalog/clearcart.html$ /index.php?view=catalog&do=clearcart
        $routes[] = array(
                            '_uri'  => '/^catalog\/clearcart.html$/i',
                            'do'    => 'clearcart'
                         );

        //RewriteRule ^catalog/savecart.html$ /index.php?view=catalog&do=savecart
        $routes[] = array(
                            '_uri'  => '/^catalog\/savecart.html$/i',
                            'do'    => 'savecart'
                         );

        //RewriteRule ^catalog/order.html$ /index.php?view=catalog&do=order
        $routes[] = array(
                            '_uri'  => '/^catalog\/order.html$/i',
                            'do'    => 'order'
                         );

        //RewriteRule ^catalog/finish.html$ /index.php?view=catalog&do=finish
        $routes[] = array(
                            '_uri'  => '/^catalog\/finish.html$/i',
                            'do'    => 'finish'
                         );

        return $routes;

    }

?>
