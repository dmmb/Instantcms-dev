<?php

// ========================================================================== //

    function info_component(){

        //�������� ����������

        $_component['title']        = '������� - ���� ���������'; //��������
        $_component['description']  = '��������� ��������� ����������� �� ����� ������� �����������, �������� �� ���������'; //��������
        $_component['link']         = 'meetings'; //������ (�������������)
        $_component['author']       = 'r2';      //�����
        $_component['internal']     = '0';       //���������� (������ ��� �������)? 1-��, 0-���
        $_component['version']      = '1.0';     //������� ������

        //������� ��� .htaccess
        
        $_component['htaccess'][]   = 'RewriteRule ^new_com/([0-9]*)/([0-9]*)$ /index.php?view=new_com&id=$2&menuid=$1';
        $_component['htaccess'][]   = 'RewriteRule ^new_com/([0-9]*)/read([0-9]*).html$ /index.php?view=new_com&do=read&id=$2&menuid=$1';

        //��������� ��-���������

        $_component['config']['param1'] = 1;
        $_component['config']['param2'] = 2;

        return $_component;

    }

// ========================================================================== //

    /*
     * ������� ��������� ����������.
     * ��� ������� ����� ������� � ������ ���������.
     * ����� ����� ������� ����� ������ ���������������� ������.
     */
    function install_component(){

        $inCore     = cmsCore::getInstance();       //���������� ����
        $inDB       = cmsDatabase::getInstance();   //���������� ���� ������

        //��� ��������� ���������� ����� ������� ������������ �� ������� � ��

        //������� ������� � �����������, ���� ��� ��� ����
        $sql = "DROP TABLE IF EXISTS `cms_meet_category`";
        $inDB->query($sql);

        //������� ������� � �����������
        $sql = "CREATE TABLE `cms_meet_category` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `published` int(11) NOT NULL,
                  `title` varchar(255) NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `published` (`published`)
                ) ENGINE=MyISAM DEFAULT CHARSET=cp1251";
        $inDB->query($sql);

        //������� ������� � �������������, ���� ��� ��� ����
        $sql = "DROP TABLE IF EXISTS `cms_meet_items`";
        $inDB->query($sql);

        //������� ������� � �������������
        $sql = "CREATE TABLE `cms_meet_items` (
                `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                `category_id` INT NOT NULL ,
                `pubdate` DATETIME NOT NULL ,
                `title` VARCHAR( 255 ) NOT NULL ,
                `published` INT NOT NULL ,
                INDEX ( `category_id` , `published` )
                ) ENGINE = MYISAM DEFAULT CHARSET=cp1251";
        $inDB->query($sql);

        //���� ��������� ��������� �������, ���������� true
        //� ��������� ������ ����� ������� ������, ����� ������� �������
        //������������ � ��� ��� �������� ������ ��� ��������� � �������
        //���������� ������ ���������
        return true; 

    }

// ========================================================================== //

    /*
     * ������� ���������� ����������.
     * ��� ������� ����� ������� � ������ ����������.
     * ����� ����� ������� ������ ���������������� ������, ��������
     * ��������� ��������� ������ � ����
     */
    function upgrade_component(){

        $inCore     = cmsCore::getInstance();       //���������� ����
        $inDB       = cmsDatabase::getInstance();   //���������� ���� ������

        // ... ������ ���-����, ���� ��������� ...

        //���� ���������� ��������� �������, ���������� true
        //� ��������� ������ ����� ������� ������, ����� ������� �������
        //������������ � ��� ��� �������� ������ ��� ���������� � �������
        //���������� ������ ���������
        return true;
        
    }

// ========================================================================== //

?>