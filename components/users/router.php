<?php

    function routes_users(){


        $routes[] = array(
                            '_uri'  => '/^users\/karma\/plus\/([0-9]+)\/([0-9]+)$/i',
                            'do'    => 'votekarma',
                            'sign'  => 'plus',
                            1       => 'to',
                            2       => 'from'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/karma\/minus\/([0-9]+)\/([0-9]+)$/i',
                            'do'    => 'votekarma',
                            'sign'  => 'minus',
                            1       => 'to',
                            2       => 'from'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/wall\-delete\/(.+)\/([0-9]+)$/i',
                            'do'    => 'wall_delete',
                            1       => 'usertype',
                            2       => 'record_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/wall\-add$/i',
                            'do'    => 'wall_add'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/board.html$/i',
                            'do'    => 'viewboard',
                            1       => 'id',
                            'page'  => '1'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/board([0-9]+).html$/i',
                            'do'    => 'viewboard',
                            1       => 'id',
                            2       => 'page'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/city\/(.+)$/i',
                            'do'    => 'city',
                            1       => 'city'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/hobby\/(.+)$/i',
                            'do'    => 'hobby',
                            1       => 'hobby'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/search.html$/i',
                            'do'    => 'search'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/awardslist.html$/i',
                            'do'    => 'awardslist'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/giveaward.html$/i',
                            'do'    => 'giveaward',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/delaward([0-9]+).html$/i',
                            'do'    => 'delaward',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/friendlist.html$/i',
                            'do'    => 'friendlist',
                            1       => 'id',
                            'page'  => '1'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/friendlist([0-9]+).html$/i',
                            'do'    => 'friendlist',
                            1       => 'id',
                            2       => 'page'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/nofriends.html$/i',
                            'do'    => 'delfriend',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/friendship.html$/i',
                            'do'    => 'addfriend',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/avatar.html$/i',
                            'do'    => 'avatar',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/select\-avatar.html$/i',
                            'do'    => 'select_avatar',
                            1       => 'id',
                            'page'  => '1'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/select\-avatar\-([0-9]+).html$/i',
                            'do'    => 'select_avatar',
                            1       => 'id',
                            2       => 'page'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/select\-avatar\/([0-9]+)$/i',
                            'do'    => 'select_avatar',
                            1       => 'id',
                            2       => 'avatar_id',
                            'set_avatar' => '1'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/photoalbum.html$/i',
                            'do'    => 'viewphotos',
                            1       => 'id',
                            'page'  => '1'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/delalbum([0-9]+).html$/i',
                            'do'    => 'delalbum',
                            1       => 'id',
                            2       => 'album_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/photos\/editlist$/i',
                            'do'    => 'editphotolist',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([a-zA-z0-9\.]+)\/photos\/(public|private)([0-9]+).html$/i',
                            'do'    => 'viewalbum',
                            1       => 'login',
                            2       => 'album_type',
                            3       => 'album_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([a-zA-z0-9\.]+)\/photos\/(public|private)([0-9]+)\-([0-9]+)\.html$/i',
                            'do'    => 'viewalbum',
                            1       => 'login',
                            2       => 'album_type',
                            3       => 'album_id',
                            4       => 'page'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/photos\/upload$/i',
                            'do'    => 'uploadphotos'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([a-zA-z0-9\.]+)\/photos\/submit/i',
                            'do'    => 'submitphotos',
                            1       => 'login'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/photo([0-9]+).html$/i',
                            'do'    => 'viewphoto',
                            1       => 'id',
                            2       => 'photoid'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/editphoto([0-9]+).html$/i',
                            'do'    => 'editphoto',
                            1       => 'id',
                            2       => 'photoid'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/delphoto([0-9]+).html$/i',
                            'do'    => 'delphoto',
                            1       => 'id',
                            2       => 'photoid'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/addphoto(|single).html$/i',
                            'do'    => 'addphoto',
                            1       => 'id',
							2       => 'uload_type'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/comments.html$/i',
                            'do'    => 'comments',
                            1       => 'id',
                            'page'  => '1'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/comments([0-9]+).html$/i',
                            'do'    => 'comments',
                            1       => 'id',
                            2       => 'page'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/forumposts.html$/i',
                            'do'    => 'forumposts',
                            1       => 'id',
                            'page'  => '1'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/forumposts([0-9]+).html$/i',
                            'do'    => 'forumposts',
                            1       => 'id',
                            2       => 'page'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/delprofile.html$/i',
                            'do'    => 'delprofile',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/delprofile\-yes.html$/i',
                            'do'    => 'delprofile',
                            1       => 'id',
                            'confirm' => 'yes'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/restoreprofile([0-9]+).html$/i',
                            'do'    => 'restoreprofile',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/editprofile.html$/i',
                            'do'    => 'editprofile',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/sendmessage.html$/i',
                            'do'    => 'sendmessage',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/delmessages.html$/i',
                            'do'    => 'delmessages',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/messages.html$/i',
                            'do'    => 'messages',
                            1       => 'id',
                            'opt'   => 'in'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/messages([0-9]+).html$/i',
                            'do'    => 'messages',
                            1       => 'id',
                            'opt'   => 'in',
                            2       => 'cpage'
                         );
						 
        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/messages\-sent.html$/i',
                            'do'    => 'messages',
                            1       => 'id',
                            'opt'   => 'out'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/messages\-sent([0-9]+).html$/i',
                            'do'    => 'messages',
                            1       => 'id',
                            'opt'   => 'out',
                            2       => 'cpage'
                         );
						 
        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/messages\-new.html$/i',
                            'do'    => 'messages',
                            1       => 'id',
                            'opt'   => 'new'
                         );


        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/messages\-history([0-9]+).html$/i',
                            'do'    => 'messages',
                            1       => 'id',
                            2       => 'with_id',
                            'opt'   => 'history'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/messages\-history([0-9]+)\-([0-9]+).html$/i',
                            'do'    => 'messages',
                            1       => 'id',
                            2       => 'with_id',
                            'opt'   => 'history',
                            3       => 'cpage'
                         );
						 
        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/reply([0-9]+).html$/i',
                            'do'    => 'sendmessage',
                            1       => 'id',
                            2       => 'replyid'
                         );

        //RewriteRule ^users/delmsg([0-9]*).html$ /index.php?view=users&do=delmessage&id=$1
        $routes[] = array(
                            '_uri'  => '/^users\/delmsg([0-9]+).html$/i',
                            'do'    => 'delmessage',
                            1       => 'id'
                         );

        //RewriteRule ^users/([0-9]*)/karma.html$ /index.php?view=users&do=karma&id=$1
        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/karma.html$/i',
                            'do'    => 'karma',
                            1       => 'id'
                         );

        //RewriteRule ^users/([0-9]*)/files.html$ /index.php?view=users&do=files&id=$1
        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/files.html$/i',
                            'do'    => 'files',
                            1       => 'id'
                         );

        //RewriteRule ^users/([0-9]*)/files([0-9]*).html$ /index.php?view=users&do=files&id=$1&page=$2
        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/files([0-9]+).html$/i',
                            'do'    => 'files',
                            1       => 'id',
                            2       => 'page'
                         );

        //RewriteRule ^users/([0-9]*)/addfile.html$ /index.php?view=users&do=addfile&id=$1
        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/addfile.html$/i',
                            'do'    => 'addfile',
                            1       => 'id'
                         );

        //RewriteRule ^users/([0-9]*)/delfile([0-9]*).html$ /index.php?view=users&do=delfile&id=$1&fileid=$2
        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/delfile([0-9]+).html$/i',
                            'do'    => 'delfile',
                            1       => 'id',
                            2       => 'fileid'
                         );

        //RewriteRule ^users/([0-9]*)/delfilelist.html$ /index.php?view=users&do=delfilelist&id=$1
        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/delfilelist.html$/i',
                            'do'    => 'delfilelist',
                            1       => 'id'
                         );

        //RewriteRule ^users/([0-9]*)/showfilelist.html$ /index.php?view=users&do=pubfilelist&id=$1&allow=all
        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/showfilelist.html$/i',
                            'do'    => 'pubfilelist',
                            1       => 'id',
                            'allow' => 'all'
                         );

        //RewriteRule ^users/([0-9]*)/hidefilelist.html$ /index.php?view=users&do=pubfilelist&id=$1&allow=nobody
        $routes[] = array(
                            '_uri'  => '/^users\/([0-9]+)\/hidefilelist.html$/i',
                            'do'    => 'pubfilelist',
                            1       => 'id',
                            'allow' => 'nobody'
                         );

        //RewriteRule ^users/files/download.html$ /index.php?view=users&do=download
        $routes[] = array(
                            '_uri'  => '/^users\/files\/download.html$/i',
                            'do'    => 'download'
                         );

        //RewriteRule ^users/files/download([0-9]*).html$ /index.php?view=users&do=download&fileid=$1
        $routes[] = array(
                            '_uri'  => '/^users\/files\/download([0-9]+).html$/i',
                            'do'    => 'download',
                            1       => 'fileid'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/latest.html$/i',
                            'orderby' => 'regdate',
                            'orderto' => 'desc'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/latest([0-9]+).html$/i',
                            1       => 'page',
                            'orderby' => 'regdate',
                            'orderto' => 'desc'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/positive.html$/i',
                            'orderby' => 'karma',
                            'orderto' => 'desc'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/positive([0-9]+).html$/i',
                            1       => 'page',
                            'orderby' => 'karma',
                            'orderto' => 'desc'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/rating.html$/i',
                            'orderby' => 'rating',
                            'orderto' => 'desc'
                         );

        $routes[] = array(
                            '_uri'  => '/^users\/rating([0-9]+).html$/i',
                            1       => 'page',
                            'orderby' => 'rating',
                            'orderto' => 'desc'
                         );

        //RewriteRule ^users/online.html$ /index.php?view=users&online=1
        $routes[] = array(
                            '_uri'  => '/^users\/online.html$/i',
                            'online' => '1'
                         );

        //RewriteRule ^users/all.html$ /index.php?view=users&online=0
        $routes[] = array(
                            '_uri'  => '/^users\/all.html$/i',
                            'online' => '0'
                         );

        //RewriteRule ^users/([a-zA-z0-9\.]*)$ /index.php?view=users&do=profile&login=$1
        $routes[] = array(
                            '_uri'  => '/^users\/([a-zA-z0-9\.]+)$/i',
                            'do'    => 'profile',
                            1       => 'login'
                         );

        return $routes;

    }

?>
