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

// ========================================================================== //

    function info_component_arhive(){

        //�������� ����������
        $_component['title']        = '����� ����������';
        $_component['description']  = '��������� ���������� �������� ������ �� �����';
        $_component['link']         = 'arhive';
        $_component['author']       = 'InstantCMS Team';
        $_component['internal']     = '0';
        $_component['version']      = '1.7';

        //��������� ��-���������
        $_component['config']       = array();

        //��������� ������
        $_component['modules']      = array(
                                             'mod_arhive' => '����� ������'
                                           );

        return $_component;

    }

// ========================================================================== //

    function install_component_arhive(){

        $inCore     = cmsCore::getInstance();       //���������� ����
        $inDB       = cmsDatabase::getInstance();   //���������� ���� ������

        if (!$inCore->isModuleInstalled('mod_arhive')){
            $sql = "INSERT INTO `cms_modules` (`position`, `name`, `title`, `is_external`, `content`, `ordering`, `showtitle`, `published`, `user`, `config`, `original`, `css_prefix`, `allow_group`, `cache`, `cachetime`, `cacheint`, `template`, `is_strict_bind`, `version`) 
                    VALUES ('sidebar', '����� ������', '����� ������', 1, 'mod_arhive', 27, 1, 0, 0, '---\nsource: both\ncat_id: 6\n', 1, '', 1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0')";
            $inDB->query($sql);
        }

        return true;

    }

// ========================================================================== //

    function upgrade_component_arhive(){

        return true;
        
    }

// ========================================================================== //

?>