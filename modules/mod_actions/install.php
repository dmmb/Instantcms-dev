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

    function info_module_mod_actions(){

        //
        // �������� ������
        //

        //��������� (�� �����)
        $_module['title']        = '����� ����������';

        //�������� (� �������)
        $_module['name']         = '����� ����������';

        //��������
        $_module['description']  = '���������� ������ ��������� �������� ������������� �� �����';
        
        //������ (�������������)
        $_module['link']         = 'mod_actions';
        
        //�������
        $_module['position']     = 'maintop';

        //�����
        $_module['author']       = 'InstantCMS Team';

        //������� ������
        $_module['version']      = '1.7';

        //
        // ��������� ��-���������
        //
        $_module['config'] = array();

        return $_module;

    }

// ========================================================================== //

    function install_module_mod_actions(){

        return true;

    }

// ========================================================================== //

    function upgrade_module_mod_actions(){

        return true;
        
    }

// ========================================================================== //

?>