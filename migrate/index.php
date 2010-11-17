<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

    session_start();

    define('VALID_CMS', 1);
    
    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

    require(PATH."/core/cms.php");
    include(PATH."/includes/config.inc.php");

    $inCore     = cmsCore::getInstance();

    $inCore->loadClass('config');       //������������
    $inCore->loadClass('db');           //���� ������
    $inCore->loadClass('user');

    $inConf     = cmsConfig::getInstance();
    $inDB       = cmsDatabase::getInstance();

    $steps_count = 6;

    $version_prev = '1.6';
    $version_next = '1.7';

    $step   = $inCore->request('step', 'int', 0);

    $locker = file_exists('locker');

// ========================================================================== //
// ========================================================================== //

    echo '<style type="text/css">
            body { font-family:Arial; font-size:14px; }

            a { color: #0099CC; }
            a:hover { color: #375E93; }
            h2 { color: #375E93; }

            #wrapper { padding:10px 30px; }
            #wrapper p{ line-height: 20px; }

            .migrate p { 
                           line-height:16px;
                           padding-left:20px;
                           margin:2px;
                           margin-left:20px;                           
                           background:url(/admin/images/actions/on.gif) no-repeat;
                       }
            .important {
                           margin:20px;
                           margin-left:0px;
                           border:solid 1px silver;
                           padding:15px;
                           padding-left:65px;
                           background:url(important.png) no-repeat 15px 15px;
                       }
             .nextlink {
                           margin-top:15px;
                           font-size:18px;
             }
          </style>';

    echo '<div id="wrapper">';

    echo "<h2>�������� InstantCMS {$version_prev} &rarr; {$version_next}</h2>";

    if ($step){

        echo "<h4>��� {$step} �� {$steps_count}</h4>";
        echo '<div class="migrate">'; include "step{$step}.php"; echo '</div>';

        if ($step < $steps_count){
            echo '<div class="nextlink"><a href="?step='.($step+1).'>����� &rarr;</a></div>';
        } else {
            //COMPLETED
            echo '<p><strong>�������� ������� ��� CRON!</strong></p>';

            echo '<p>
                        �������� ���� <strong>/cron.php</strong> � ���������� ������� CRON � ������ ������ ��������.<br/>
                        �������� ���������� � 24 ����. ��� �������� ������� ��������� ������������� ��������� ������.
                  </p>';

            echo '<div style="margin:15px 0px 15px 0px;font-weight:bold">�������� ���������. ������� ����� /migrate/ ������ ��� ����������!</div>';
            echo '<div class="nextlink"><a href="/">������� �� ����</a></div>';
        }

    } else {

        echo "<p>
                ���� ������ ����� ����� ��������� �� ����� ������.<br/>
                ����� �� ��������� ������������ ����� ���������� �������<br/>
                ������� �������� ������ �� {$steps_count} �����.
              </p>";
                
        echo '<p>�� ��� ��������� ������ �������� ������ "�����" ����� ������� ����.</p>';

        echo '<p>
                ������ ��� ������ �������� ����� ���� ������ � ���������<br/>
                ��� ���� ���������� ��� ����������� �� ����� ��������.
              </p>';

        if (is_writable(dirname(__FILE__))){
            echo '<div class="nextlink"><a href="?step=1">������ ��������</a></div>';
        } else {
            echo '<p style="color:red">����� /migrate/ �� �������� ��� ������</p>';
        }

    }

    echo '</div>';

    
?>