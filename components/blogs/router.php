<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8.1                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

    function routes_blogs(){

        //RewriteRule ^blogs/createblog.html$ /index.php?view=blog&do=create
        $routes[] = array(
                            '_uri'  => '/^blogs\/createblog.html$/i',
                            'do'    => 'create'
                         );

        //RewriteRule ^blogs/latest.html$ /index.php?view=blog&do=latest
        $routes[] = array(
                            '_uri'  => '/^blogs\/latest.html$/i',
                            'do'    => 'latest'
                         );

        //RewriteRule ^blogs/latest-([0-9]*).html$ /index.php?view=blog&do=latest&page=$1
        $routes[] = array(
                            '_uri'  => '/^blogs\/latest\-([0-9]+).html$/i',
                            'do'    => 'latest',
                            1       => 'page'
                         );

        //RewriteRule ^blogs/popular.html$ /index.php?view=blog&do=best
        $routes[] = array(
                            '_uri'  => '/^blogs\/popular.html$/i',
                            'do'    => 'best'
                         );

        //RewriteRule ^blogs/popular-([0-9]*).html$ /index.php?view=blog&do=best&page=$1
        $routes[] = array(
                            '_uri'  => '/^blogs\/popular\-([0-9]+).html$/i',
                            'do'    => 'best',
                            1       => 'page'
                         );

        //RewriteRule ^blogs/all.html$ /index.php?view=blog&do=view&ownertype=all
        $routes[] = array(
                            '_uri'      => '/^blogs\/all.html$/i',
                            'do'        => 'view',
                            'ownertype' => 'all'
                         );

        $routes[] = array(
                            '_uri'  	=> '/^blogs\/all\-([0-9]+).html$/i',
                            'do'    	=> 'view',
                            'ownertype' => 'all',
                            1       	=> 'page'
                         );
						 
        //RewriteRule ^blogs/single.html$ /index.php?view=blog&do=view&ownertype=single
        $routes[] = array(
                            '_uri'      => '/^blogs\/single.html$/i',
                            'do'        => 'view',
                            'ownertype' => 'single'
                         );

        $routes[] = array(
                            '_uri'  	=> '/^blogs\/single\-([0-9]+).html$/i',
                            'do'    	=> 'view',
                            'ownertype' => 'single',
                            1       	=> 'page'
                         );
						 
        //RewriteRule ^blogs/multi.html$ /index.php?view=blog&do=view&ownertype=multi
        $routes[] = array(
                            '_uri'      => '/^blogs\/multi.html$/i',
                            'do'        => 'view',
                            'ownertype' => 'multi'
                         );

        $routes[] = array(
                            '_uri'  	=> '/^blogs\/multi\-([0-9]+).html$/i',
                            'do'    	=> 'view',
                            'ownertype' => 'multi',
                            1       	=> 'page'
                         );

        //RewriteRule ^blogs/[0-9]*)/moderate.html$ /index.php?view=blog&do=moderate&id=$1
        $routes[] = array(
                            '_uri'  => '/^blogs\/([0-9]+)\/moderate.html$/i',
                            'do'    => 'moderate',
                            1       => 'id'
                         );

        //RewriteRule ^blogs/([0-9]*)/publishpost([0-9]*).html$ /index.php?view=blog&do=publishpost&id=$1&post_id=$2
        $routes[] = array(
                            '_uri'  => '/^blogs\/([0-9]+)\/publishpost([0-9]+).html$/i',
                            'do'    => 'publishpost',
                            1       => 'id',
                            2       => 'post_id'
                         );

        //RewriteRule ^blogs/([0-9]*)/authors.html$ /index.php?view=blog&do=authors&id=$1
        $routes[] = array(
                            '_uri'  => '/^blogs\/([0-9]+)\/authors.html$/i',
                            'do'    => 'authors',
                            1       => 'id'
                         );

        //RewriteRule ^blogs/([0-9]*)/editblog.html$ /index.php?view=blog&do=config&id=$1
        $routes[] = array(
                            '_uri'  => '/^blogs\/([0-9]+)\/editblog.html$/i',
                            'do'    => 'config',
                            1       => 'id'
                         );

        //RewriteRule ^blogs/([0-9]*)/delblog.html$ /index.php?view=blog&do=delblog&id=$1
        $routes[] = array(
                            '_uri'  => '/^blogs\/([0-9]+)\/delblog.html$/i',
                            'do'    => 'delblog',
                            1       => 'id'
                         );

        //RewriteRule ^blogs/([0-9]*)/delblog-yes.html$ /index.php?view=blog&do=delblog&id=$1&confirm=yes
        $routes[] = array(
                            '_uri'      => '/^blogs\/([0-9]+)\/delblog-yes.html$/i',
                            'do'        => 'delblog',
                            1           => 'id',
                            'confirm'   => 'yes'
                         );

        //RewriteRule ^blogs/([0-9]*)/delpost([0-9]*).html$ /index.php?view=blog&do=delpost&id=$1&post_id=$2
        $routes[] = array(
                            '_uri'  => '/^blogs\/([0-9]+)\/delpost([0-9]+).html$/i',
                            'do'    => 'delpost',
                            1       => 'id',
                            2       => 'post_id'
                         );

        //RewriteRule ^blogs/([0-9]*)/delpost([0-9]*)-yes.html$ /index.php?view=blog&do=delpost&id=$1&post_id=$2&confirm=yes
        $routes[] = array(
                            '_uri'      => '/^blogs\/([0-9]+)\/delpost([0-9]+)\-yes.html$/i',
                            'do'        => 'delpost',
                            1           => 'id',
                            2           => 'post_id',
                            'confirm'   => 'yes'
                         );

        //RewriteRule ^blogs/([0-9]*)/delcat([0-9]*).html$ /index.php?view=blog&do=delcat&id=$1&cat_id=$2
        $routes[] = array(
                            '_uri'  => '/^blogs\/([0-9]+)\/delcat([0-9]+).html$/i',
                            'do'    => 'delcat',
                            1       => 'id',
                            2       => 'cat_id'
                         );

        //RewriteRule ^blogs/([0-9]*)/delcat([0-9]*)-yes.html$ /index.php?view=blog&do=delcat&id=$1&cat_id=$2&confirm=yes
        $routes[] = array(
                            '_uri'      => '/^blogs\/([0-9]+)\/delcat([0-9]+)\-yes.html$/i',
                            'do'        => 'delcat', 
                            1           => 'id',
                            2           => 'cat_id',
                            'confirm'   => 'yes'
                         );

        //RewriteRule ^blogs/([0-9]*)/newpost.html$ /index.php?view=blog&do=newpost&id=$1
        $routes[] = array(
                            '_uri'  => '/^blogs\/([0-9]+)\/newpost.html$/i',
                            'do'    => 'newpost',
                            1       => 'id'
                         );

        //RewriteRule ^blogs/([0-9]*)/newcat.html$ /index.php?view=blog&do=newcat&id=$1
        $routes[] = array(
                            '_uri'  => '/^blogs\/([0-9]+)\/newcat.html$/i',
                            'do'    => 'newcat',
                            1       => 'id'
                         );

        //RewriteRule ^blogs/([0-9]*)/editpost([0-9]*).html$ /index.php?view=blog&do=editpost&id=$1&post_id=$2
        $routes[] = array(
                            '_uri'  => '/^blogs\/([0-9]+)\/editpost([0-9]+).html$/i',
                            'do'    => 'editpost', 
                            1       => 'id',
                            2       => 'post_id'
                         );

        //RewriteRule ^blogs/([0-9]*)/editcat([0-9]*).html$ /index.php?view=blog&do=editcat&id=$1&cat_id=$2
        $routes[] = array(
                            '_uri'  => '/^blogs\/([0-9]+)\/editcat([0-9]+).html$/i',
                            'do'    => 'editcat',
                            1       => 'id',
                            2       => 'cat_id'
                         );

        //RewriteRule ^blogs/([a-zA-Z0-9\-]*)$ /index.php?view=blog&do=blog&bloglink=$1
        $routes[] = array(
                            '_uri'  => '/^blogs\/([a-zA-Z0-9\-]+)$/i',
                            'do'    => 'blog',
                            1       => 'bloglink'
                         );

        //RewriteRule ^blogs/([a-zA-Z0-9\-]*)$ /index.php?view=blog&do=blog&bloglink=$1
        $routes[] = array(
                            '_uri'  => '/^blogs\/([a-zA-Z0-9\-]+)\/$/i',
                            'do'    => 'blog',
                            1       => 'bloglink'
                         );

        //RewriteRule ^blogs/([a-zA-Z0-9\-]*)/page-([0-9]*)$ /index.php?view=blog&do=blog&bloglink=$1&page=$2
        $routes[] = array(
                            '_uri'  => '/^blogs\/([a-zA-Z0-9\-]+)\/page\-([0-9]+)$/i',
                            'do'    => 'blog',
                            1       => 'bloglink',
                            2       => 'page'
                         );

        //RewriteRule ^blogs/([a-zA-Z0-9\-]*)/cat-([0-9]*)$ /index.php?view=blog&do=blog&bloglink=$1&cat_id=$2
        $routes[] = array(
                            '_uri'  => '/^blogs\/([a-zA-Z0-9\-]+)\/cat\-([0-9]+)$/i',
                            'do'    => 'blog',
                            1       => 'bloglink',
                            2       => 'cat_id'
                         );

        //RewriteRule ^blogs/([a-zA-Z0-9\-]*)/page-([0-9]*)/cat-([0-9]*)$ /index.php?view=blog&do=blog&bloglink=$1&page=$2&cat_id=$3
        $routes[] = array(
                            '_uri'  => '/^blogs\/([a-zA-Z0-9\-]+)\/page\-([0-9]+)\/cat\-([0-9]+)$/i',
                            'do'    => 'blog',
                            1       => 'bloglink',
                            2       => 'page',
                            3       => 'cat_id'
                         );

        //RewriteRule ^blogs/([a-zA-Z0-9\-]*)/([a-zA-Z0-9\-]*).html$ /index.php?view=blog&do=post&bloglink=$1&seolink=$2
        $routes[] = array(
                            '_uri'  => '/^blogs\/([a-zA-Z0-9\-]+)\/([a-zA-Z0-9\-]+).html$/i',
                            'do'    => 'post',
                            1       => 'bloglink',
                            2       => 'seolink'
                         );

        return $routes;

    }

?>
