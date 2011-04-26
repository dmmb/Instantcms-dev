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

    //
    // ��������! ���� �� ������ �������� ����������� �������, �� ��������
    //           ���� custom_rewrite.php � �������� � ��� �������
    //           custom_rewrite_rules() �� �������� � ������� ������!
    //
    // � ���� ����� ���������� ��������� ������� ��� ��������� � ������� �������
    //
    //      source          : ���������� ���������, ��� ��������� � ������� URI
    //      target          : URI ��� ���������������, ��� ���������� source
    //      action          : �������� ��� ���������� source
    //
    // ��������� �������� ��� action:
    //
    //      rewrite         : ��������� URI ����� ������������ ����������
    //      redirect        : �������� �� target � ����� 303 See Other
    //      redirect-301    : �������� �� target � ����� 301 Moved Permanently
    //      alias           : ����������� ���� target � ���������� ������
    //

    function rewrite_rules(){

        //
        // ���� / �����
        //


        $rules[] = array(
                            'source'  => '/^admin$/i',
                            'target'  => '/admin/index.php',
                            'action'  => 'redirect'
                         );

        $rules[] = array(
                            'source'  => '/^login$/i',
                            'target'  => 'registration/login',
                            'action'  => 'rewrite'
                         );

        $rules[] = array(
                            'source'  => '/^logout$/i',
                            'target'  => 'registration/logout',
                            'action'  => 'rewrite'
                         );

        $rules[] = array(
                            'source'  => '/^auth\/error.html$/i',
                            'target'  => 'registration/autherror',
                            'action'  => 'rewrite'
                         );

        $rules[] = array(
                            'source'  => '/^go\/url=(.+)$/i',
                            'target'  => 'files/go/{1}',
                            'action'  => 'rewrite'
                         );

        $rules[] = array(
                            'source'  => '/^load\/url=(.+)$/i',
                            'target'  => 'files/load/{1}',
                            'action'  => 'rewrite'
                         );

        //
        // ����������� / ���������
        //

        $rules[] = array(
                            'source'  => '/^registration\/complete.html$/i',
                            'target'  => '/core/auth/regcomplete.html',
                            'action'  => 'alias'
                         );

        $rules[] = array(
                            'source'  => '/^registration\/activate.html$/i',
                            'target'  => '/core/auth/regactivate.html',
                            'action'  => 'alias'
                         );

        $rules[] = array(
                            'source'  => '/^activate\/(.+)$/i',
                            'target'  => 'registration/activate/{1}',
                            'action'  => 'rewrite'
                         );

        $rules[] = array(
                            'source'  => '/^passremind.html$/i',
                            'target'  => 'registration/passremind',
                            'action'  => 'rewrite'
                         );

        //
        // RSS
        //

        $rules[] = array(
                            'source'  => '/^rss\/([a-z]+)\/(.+)\/feed.rss$/i',
                            'target'  => 'rssfeed/{1}/{2}',
                            'action'  => 'rewrite'
                         );

        //
        // ������� ������
        //

        $rules[] = array(
                            'source'  => '/^go\/url=(.+)$/i',
                            'target'  => 'files/go/{1}',
                            'action'  => 'rewrite'
                         );

        $rules[] = array(
                            'source'  => '/^load\/url=(.+)$/i',
                            'target'  => 'files/load/{1}',
                            'action'  => 'rewrite'
                         );

        $rules[] = array(
                            'source'  => '/^r([0-9]+)$/i',
                            'target'  => 'billing/ref_link/{1}',
                            'action'  => 'rewrite'
                         );
        
        //
        // �������
        //

        $rules[] = array(
                            'source'  => '/^gobanner([0-9]+)$/i',
                            'target'  => 'banners/{1}',
                            'action'  => 'rewrite'
                         );

        //
        // ��������
        //

        $rules[] = array(
                            'source'  => '/^subscribe\/([a-z]+)\/([0-9]+)$/i',
                            'target'  => 'subscribes/{1}/{2}/1',
                            'action'  => 'rewrite'
                         );

        $rules[] = array(
                            'source'  => '/^unsubscribe\/([a-z]+)\/([0-9]+)$/i',
                            'target'  => 'subscribes/{1}/{2}/0',
                            'action'  => 'rewrite'
                         );

        $rules[] = array(
                            'source'  => '/^forum\/subscribe([0-9]+).html$/i',
                            'target'  => 'subscribes/forum/{1}/1',
                            'action'  => 'rewrite'
                         );

        $rules[] = array(
                            'source'  => '/^forum\/unsubscribe([0-9]+).html$/i',
                            'target'  => 'subscribes/forum/{1}/0',
                            'action'  => 'rewrite'
                         );


        //
        // ������ ������ ������ � �������� (1.5.x)
        //

        $rules[] = array(
                            'source'  => '/^content\/(.+)\/page\-([0-9]+).html$/i',
                            'target'  => '/{1}/page-{2}.html',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^content\/(.+)\/page\-([0-9]+)$/i',
                            'target'  => '/{1}/page-{2}',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^content\/([0-9]+)\/(.+).html$/i',
                            'target'  => '/{2}.html',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^content\/([0-9]+)\/(.+)$/i',
                            'target'  => '/{2}',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^content\/(.+)$/i',
                            'target'  => '/{1}',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^content\/([0-9]+)\/(.+)\/page\-([0-9]+).html$/i',
                            'target'  => '/{2}/page-{3}.html',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^content\/([0-9]+)\/(.+)\/page\-([0-9]+)$/i',
                            'target'  => '/{2}/page-{3}',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^(.+)\/$/i',
                            'target'  => '/{1}',
                            'action'  => 'redirect-301'
                         );

        //
        // ������ ������ ������ � ������ (1.5.x)
        //

        $rules[] = array(
                            'source'  => '/^blogs\/([0-9]+)\/([a-zA-Z0-9\-]+)$/i',
                            'target'  => '/blogs/{2}',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^blogs\/([0-9]+)\/([a-zA-Z0-9\-]+)\/$/i',
                            'target'  => '/blogs/{2}',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^blogs\/([0-9]+)\/([a-zA-Z0-9\-]+)\/page\-([0-9]+)$/i',
                            'target'  => '/blogs/{2}/page-{3}',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^blogs\/([0-9]+)\/([a-zA-Z0-9\-]+)\/cat\-([0-9]+)$/i',
                            'target'  => '/blogs/{2}/cat-{3}',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^blogs\/([0-9]+)\/([a-zA-Z0-9\-]+)\/page\-([0-9]+)\/cat\-([0-9]+)$/i',
                            'target'  => '/blogs/{2}/page-{3}/cat-{4}',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^blogs\/([0-9]+)\/([a-zA-Z0-9\-]+)\/([a-zA-Z0-9\-]+).html$/i',
                            'target'  => '/blogs/{2}/{3}.html',
                            'action'  => 'redirect-301'
                         );

        //
        // ������ ������ ������ (1.5.x)
        //

        $rules[] = array(
                            'source'  => '/^forum\/([0-9]+)\/([0-9]+)$/i',
                            'target'  => '/forum/{2}',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^forum\/([0-9]*)\/([0-9]*)-([0-9]*)$/i',
                            'target'  => '/forum/{2}-{3}',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^forum\/([0-9]*)\/thread([0-9]+).html$/i',
                            'target'  => '/forum/thread{2}.html',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^forum\/([0-9]*)\/thread([0-9]+)\-([0-9]+).html$/i',
                            'target'  => '/forum/thread{2}-{3}.html',
                            'action'  => 'redirect-301'
                         );

        //
        // ������ ������ �������� (1.5.x)
        //

        $rules[] = array(
                            'source'  => '/^catalog\/([0-9]+)\/([0-9]+)$/i',
                            'target'  => '/catalog/{2}',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^catalog\/([0-9]*)\/([0-9]*)-([0-9]*)$/i',
                            'target'  => '/catalog/{2}-{3}',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^catalog\/([0-9]*)\/item([0-9]+).html$/i',
                            'target'  => '/catalog/item{2}.html',
                            'action'  => 'redirect-301'
                         );

        return $rules;

    }

?>
