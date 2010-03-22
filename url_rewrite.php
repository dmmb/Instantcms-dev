<?php

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
    //      redirect-303    : �������� �� target � ����� 301 Moved Permanently
    //      alias           : ����������� ���� target � ���������� ������
    //

    function rewrite_rules(){

        //
        // ���� / �����
        //

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
                            'target'  => '/core/auth/autherror.html',
                            'action'  => 'alias'
                         );

        //
        // ����������� / ���������
        //

        $rules[] = array(
                            'source'  => '/^registration\/complete.html$/i',
                            'target'  => '/core/auth/regcomplete.html',
                            'action'  => 'alias'
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
                            'source'  => '/^go\/url=(.*)$/i',
                            'target'  => 'files/go/{1}',
                            'action'  => 'rewrite'
                         );

        $rules[] = array(
                            'source'  => '/^load\/url=(.*)$/i',
                            'target'  => 'files/load/{1}',
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
                            'source'  => '/^content\/([0-9]+)\/(.+).html$/i',
                            'target'  => '/content/{2}.html',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^content\/([0-9]+)\/(.+)\/page\-([0-9]+).html$/i',
                            'target'  => '/content/{2}/page-{3}.html',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^content\/([0-9]+)\/(.+)$/i',
                            'target'  => '/content/{2}',
                            'action'  => 'redirect-301'
                         );

        $rules[] = array(
                            'source'  => '/^content\/([0-9]+)\/(.+)\/page\-([0-9]+)$/i',
                            'target'  => '/content/{2}/page-{3}',
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

        return $rules;

    }

?>
