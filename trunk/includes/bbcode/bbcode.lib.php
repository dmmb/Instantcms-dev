<?php

/******************************************************************************
 *                                                                            *
 *   bbcode.lib.php, v 0.08 2006/07/27 - Handling of a BBCode                 *
 *   Copyright (C) 2006  Dmitriy Skorobogatov  dima@pc.uz                     *
 *                                                                            *
 *   This program is free software; you can redistribute it and/or modify     *
 *   it under the terms of the GNU General Public License as published by     *
 *   the Free Software Foundation; either version 2 of the License, or        *
 *   (at your option) any later version.                                      *
 *                                                                            *
 *   This program is distributed in the hope that it will be useful,          *
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of           *
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            *
 *   GNU General Public License for more details.                             *
 *                                                                            *
 *   You should have received a copy of the GNU General Public License        *
 *   along with this program; if not, write to the Free Software              *
 *   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA *
 *                                                                            *
 ******************************************************************************/

class bbcode {
    /*
    �������� �����. ������ �������� - ����� �������:
        'handler'  - �������� ������� - ����������� �����.
        'is_close' - true, ���� ��� ������ ��������� �������� (�������� [hr]).
        'lbr'       - ����� ��������� �����, ������� ������� ������������ �����
                     ���������.
        'rbr'      - ����� ��������� �����, ������� ������� ������������ �����
                     ��������.
        'ends'     - ������ �����, ������ ������� ����������� ��������� ������.
        'permission_top_level' - true, ���� ���� ��������� ���������� � �����
                     ������ ���������.
        'children' - ������ �����, ������� ��������� ���� ���������� � ������.
    */
    var $info_about_tags = array(
//            '*' => array(
//                    'handler' => 'star_2html',
//                    'is_close' => false,
//                    'lbr' => 0,
//                    'rbr' => 0,
//                    'ends' => array('*','tr','td','th'),
//                    'permission_top_level' => false,
//                    'children' => array('align','b','code','color','email',
//                        'font','google','h1','h2','h3','hr','i','img','list','nobb',
//                        'php','quote','s','size','sub','sup','table','tt','u','url')
//                ),
            'align' => array(
                    'handler' => 'align_2html',
                    'is_close' => false,
                    'lbr' => 0,
                    'rbr' => 1,
                    'ends' => array('*','tr','td','th'),
                    'permission_top_level' => true,
                    'children' => array('align','b','code', 'video', 'hide','color','email',
                        'font','google','h1','h2','h3','hr','i','img','list',
                        'nobb','php','quote','s','size','sub','sup','table','tt','u','url')
                ),
            'b' => array(
                    'handler' => 'b_2html',
                    'is_close' => false,
                    'lbr' => 0,
                    'rbr' => 0,
                    'ends' => array('*','align','code', 'video','hide','h1','h2','h3','hr',
                        'list','php','quote','table','td','th','tr'),
                    'permission_top_level' => true,
                    'children' => array('b','color','email','font','google','i','img',
                        'nobb','s','size','sub','sup','tt','u','url')
                ),
//            'caption' => array(
//                    'handler' => 'caption_2html',
//                    'is_close' => false,
//                    'lbr' => 0,
//                    'rbr' => 0,
//                    'ends' => array('tr'),
//                    'permission_top_level' => false,
//                    'children' => array('b','color','email','font','google','i','nobb',
//                        's','size','sub','sup','tt','u','url')
//                ),
            'code' => array(
                    'handler' => 'code_2html',
                    'is_close' => false,
                    'lbr' => 0,
                    'rbr' => 2,
                    'ends' => array(),
                    'permission_top_level' => true,
                    'children' => array()
                ),
			'video' => array(
                    'handler' => 'video_2html',
                    'is_close' => false,
                    'lbr' => 0,
                    'rbr' => 2,
                    'ends' => array(),
                    'permission_top_level' => true,
                    'children' => array()
               ),				
            'hide' => array(
                    'handler' => 'hide_2html',
                    'is_close' => false,
                    'lbr' => 0,
                    'rbr' => 2,
                    'ends' => array(),
                    'permission_top_level' => true,
                    'children' => array()
                ),			
            'color' => array(
                    'handler' => 'color_2html',
                    'is_close' => false,
                    'lbr' => 0,
                    'rbr' => 0,
                    'ends' => array('*','align','code','video','hide','h1','h2','h3','hr',
                        'list','php','quote','table','td','th','tr'),
                    'permission_top_level' => true,
                    'children' => array('b','color','email','font','google','i','img',
                        'nobb','s','size','sub','sup','tt','u','url')
                ),
            'email' => array(
                    'handler' => 'email_2html',
                    'is_close' => false,
                    'lbr' => 0,
                    'rbr' => 0,
                    'ends' => array('*','align','code','video','hide','h1','h2','h3','hr',
                        'list','php','quote','table','td','th','tr'),
                    'permission_top_level' => true,
                    'children' => array('b','color','email','font','i','img',
                        'nobb','s','size','sub','sup','tt','u')
                ),
            'font' => array(
                    'handler' => 'font_2html',
                    'is_close' => false,
                    'lbr' => 0,
                    'rbr' => 0,
                    'ends' => array('*','align','code','video','hide','h1','h2','h3','hr',
                        'list','php','quote','table','td','th','tr'),
                    'permission_top_level' => true,
                    'children' => array('b','color','email','font','font','google','i',
                        'img','nobb','s','size','sub','sup','tt','u','url')
                ),
//            'google' => array(
//                    'handler' => 'google_2html',
//                    'is_close' => false,
//                    'lbr' => 0,
//                    'rbr' => 0,
//                    'ends' => array('*','align','code','hide','h1','h2','h3','hr',
//                        'list','php','quote','table','td','th','tr'),
//                    'permission_top_level' => true,
//                    'children' => array('b','color','font','i','img','nobb',
//                        's','size','sub','sup','tt','u')
//                ),
            'h1' => array(
                    'handler' => 'h1_2html',
                    'is_close' => false,
                    'lbr' => 1,
                    'rbr' => 2,
                    'ends' => array('*','align','code','video','hide','h1','h2','h3','hr',
                        'list','php','quote','table','td','th','tr'),
                    'permission_top_level' => true,
                    'children' => array('b','color','email','font','google','i','img',
                        'nobb','s','size','sub','sup','tt','u','url')
                ),
            'h2' => array(
                    'handler' => 'h2_2html',
                    'is_close' => false,
                    'lbr' => 1,
                    'rbr' => 2,
                    'ends' => array('*','align','code','video','hide','h1','h2','h3','hr',
                        'list','php','quote','table','td','th','tr'),
                    'permission_top_level' => true,
                    'children' => array('b','color','email','font','google','i','img',
                        'nobb','s','size','sub','sup','tt','u','url')
                ),
            'h3' => array(
                    'handler' => 'h3_2html',
                    'is_close' => false,
                    'lbr' => 1,
                    'rbr' => 2,
                    'ends' => array('*','align','code','video','hide','h1','h2','h3','hr',
                        'list','php','quote','table','td','th','tr'),
                    'permission_top_level' => true,
                    'children' => array('b','color','email','font','google','i','img',
                        'nobb','s','size','sub','sup','tt','u','url')
                ),
            'hr' => array(
                    'handler' => 'hr_2html',
                    'is_close' => true,
                    'lbr' => 0,
                    'rbr' => 1,
                    'ends' => array(),
                    'permission_top_level' => true,
                    'children' => array()
                ),
            'i' => array(
                    'handler' => 'i_2html',
                    'is_close' => false,
                    'lbr' => 0,
                    'rbr' => 0,
                    'ends' => array('*','align','code','video','hide','h1','h2','h3','hr',
                        'list','php','quote','table','td','th','tr'),
                    'permission_top_level' => true,
                    'children' => array('b','color','email','font','google','i','img',
                        'nobb','s','size','sub','sup','tt','u','url')
                ),
            'img' => array(
                    'handler' => 'img_2html',
                    'is_close' => false,
                    'lbr' => 0,
                    'rbr' => 0,
                    'ends' => array(),
                    'permission_top_level' => true,
                    'children' => array()
                ),
//            'list' => array(
//                    'handler' => 'list_2html',
//                    'is_close' => false,
//                    'lbr' => 0,
//                    'rbr' => 2,
//                    'ends' => array(),
//                    'permission_top_level' => true,
//                    'children' => array('*')
//                ),
//            'nobb' => array(
//                    'handler' => 'nobb_2html',
//                    'is_close' => false,
//                    'lbr' => 0,
//                    'rbr' => 0,
//                    'ends' => array(),
//                    'permission_top_level' => true,
//                    'children' => array()
//                ),
//            'php' => array(
//                    'handler' => 'php_2html',
//                    'is_close' => false,
//                    'lbr' => 0,
//                    'rbr' => 1,
//                    'ends' => array(),
//                    'permission_top_level' => true,
//                    'children' => array()
//                ),
            'quote' => array(
                    'handler' => 'quote_2html',
                    'is_close' => false,
                    'lbr' => 0,
                    'rbr' => 1,
                    'ends' => array(),
                    'permission_top_level' => true,
                    'children' => array('*','align','b','code','video','hide','color','email',
                        'font','google','h1','h2','h3','hr','i','img','list',
                        'nobb','php','quote','s','size','sub','sup','table','tt','u','url')
                ),
//            's' => array(
//                    'handler' => 's_2html',
//                    'is_close' => false,
//                    'lbr' => 0,
//                    'rbr' => 0,
//                    'ends' => array('*','align','code','h1','h2','h3','hr',
//                        'list','php','quote','table','td','th','tr'),
//                    'permission_top_level' => true,
//                    'children' => array('b','color','email','font','google','i','img',
//                        'nobb','s','size','sub','sup','tt','u','url')
//                ),
            'size' => array(
                    'handler' => 'size_2html',
                    'is_close' => false,
                    'lbr' => 0,
                    'rbr' => 0,
                    'ends' => array('*','align','code','video','hide','h1','h2','h3','hr',
                        'list','php','quote','table','td','th','tr'),
                    'permission_top_level' => true,
                    'children' => array('b','color','email','font','google','i','img',
                        'nobb','s','size','sub','sup','tt','u','url')
                ),
//            'sub' => array(
//                    'handler' => 'sub_2html',
//                    'is_close' => false,
//                    'lbr' => 0,
//                    'rbr' => 0,
//                    'ends' => array('*','align','code','h1','h2','h3','hr',
//                        'list','php','quote','table','td','th','tr'),
//                    'permission_top_level' => true,
//                    'children' => array('b','color','email','font','google','i','img',
//                        'nobb','s','size','sub','sup','tt','u','url')
//                ),
//            'sup' => array(
//                    'handler' => 'sup_2html',
//                    'is_close' => false,
//                    'lbr' => 0,
//                    'rbr' => 0,
//                    'ends' => array('*','align','code','h1','h2','h3','hr',
//                        'list','php','quote','table','td','th','tr'),
//                    'permission_top_level' => true,
//                    'children' => array('b','color','email','font','google','i','img',
//                        'nobb','s','size','sub','sup','tt','u','url')
//                ),
//            'table' => array(
//                    'handler' => 'table_2html',
//                    'is_close' => false,
//                    'lbr' => 0,
//                    'rbr' => 1,
//                    'ends' => array('table'),
//                    'permission_top_level' => true,
//                    'children' => array('tr','caption')
//                ),
//            'td' => array(
//                    'handler' => 'td_2html',
//                    'is_close' => false,
//                    'lbr' => 0,
//                    'rbr' => 0,
//                    'ends' => array('td','th','tr'),
//                    'permission_top_level' => false,
//                    'children' => array('align','b','code','color','email',
//                        'font','google','h1','h2','h3','hr','i','img','list','nobb',
//                        'php','quote','s','size','sub','sup','table','tt','u','url')
//                ),
//            'th' => array(
//                    'handler' => 'th_2html',
//                    'is_close' => false,
//                    'lbr' => 0,
//                    'rbr' => 0,
//                    'ends' => array('td','th','tr'),
//                    'permission_top_level' => false,
//                    'children' => array('align','b','code','color','email',
//                        'font','google','h1','h2','h3','hr','i','img','list','nobb',
//                        'php','quote','s','size','sub','sup','table','tt','u','url')
//                ),
//            'tr' => array(
//                    'handler' => 'tr_2html',
//                    'is_close' => false,
//                    'lbr' => 0,
//                    'rbr' => 0,
//                    'ends' => array('tr'),
//                    'permission_top_level' => false,
//                    'children' => array('td','th')
//                ),
//            'tt' => array(
//                    'handler' => 'tt_2html',
//                    'is_close' => false,
//                    'lbr' => 0,
//                    'rbr' => 0,
//                    'ends' => array('*','align','code','h1','h2','h3','hr',
//                        'list','php','quote','table','td','th','tr'),
//                    'permission_top_level' => true,
//                    'children' => array('b','color','email','google','i','img','nobb',
//                        's','size','sub','sup','tt','u','url')
//                ),
            'u' => array(
                    'handler' => 'u_2html',
                    'is_close' => false,
                    'lbr' => 0,
                    'rbr' => 0,
                    'ends' => array('*','align','code','video','hide','h1','h2','h3','hr',
                        'list','php','quote','table','td','th','tr'),
                    'permission_top_level' => true,
                    'children' => array('b','color','email','font','google','i','img',
                        'nobb','s','size','sub','sup','tt','u','url')
                ),
            'url' => array(
                    'handler' => 'url_2html',
                    'is_close' => false,
                    'lbr' => 0,
                    'rbr' => 0,
                    'ends' => array('*','align','code','video','hide','h1','h2','h3','hr',
                        'list','php','quote','table','td','th','tr'),
                    'permission_top_level' => true,
                    'children' => array('b','color','font','i','img','nobb',
                        's','size','sub','sup','tt','u')
                ),
        );
    /*
    ����� �������� - �������� � ������ �������� �����������, ������� ������
    ���������� �� ���-��. ���� - ���������, �������� - �� ��� ����������.
    ��������:
                      ':-)' => '<img src="smile.gif" />'
    */
    var $mnemonics = array();
    // ��� ������������� ������� ������� ���� �������������� ������ ������
    var $syntax = array();
    /*
    ������� ������ BBCode � ���������� ����� ���
    "����� (��� �������) - �������", ��� ���� ������ ����� ���� ���������:
    0 - ���������� ���������� ������ ("[")
    1 - ����������� ���������� c����� ("]")
    2 - ������� ������� ('"')
    3 - �������� ("'")
    4 - ��������� ("=")
    5 - ������ ���� ("/")
    6 - ������������������ ���������� ��������
        (" ", "\t", "\n", "\r", "\0" ��� "\x0B")
    7 - ������������������ ������ ��������, �� ���������� ������ ����
    8 - ��� ����
    */
    function get_array_of_tokens($code) {
        $length = strlen($code);
        $tokens = array();
        $token_key = -1;
        $type_of_char = null;
        for ( $i=0; $i<$length; ++$i ) {
            $previous_type = $type_of_char;
            switch ( $code{$i} ) {
                case '[':
                    $type_of_char = 0;
                    break;
                case ']':
                    $type_of_char = 1;
                    break;
                case '"':
                    $type_of_char = 2;
                    break;
                case "'":
                    $type_of_char = 3;
                    break;
                case "=":
                    $type_of_char = 4;
                    break;
                case '/':
                    $type_of_char = 5;
                    break;
                case ' ':
                    $type_of_char = 6;
                    break;
                case "\t":
                    $type_of_char = 6;
                    break;
                case "\n":
                    $type_of_char = 6;
                    break;
                case "\r":
                    $type_of_char = 6;
                    break;
                case "\0":
                    $type_of_char = 6;
                    break;
                case "\x0B":
                    $type_of_char = 6;
                    break;
                default:
                    $type_of_char = 7;
            }
            if ( 7 == $previous_type && $type_of_char != $previous_type ) {
                $word = strtolower($tokens[$token_key][1]);
                if ( isset($this -> info_about_tags[$word]) ) {
                    $tokens[$token_key][0] = 8;
                }
            }
            switch ( $type_of_char ) {
                case 6:
                    if ( 6 == $previous_type ) {
                        $tokens[$token_key][1] .= $code{$i};
                    } else { $tokens[++$token_key] = array( 6, $code{$i} ); }
                    break;
                case 7:
                    if ( 7 == $previous_type ) {
                        $tokens[$token_key][1] .= $code{$i};
                    } else { $tokens[++$token_key] = array( 7, $code{$i} ); }
                    break;
                default:
                    $tokens[++$token_key] = array( $type_of_char, $code{$i} );
            }
        }
        return $tokens;
    }
    /*
    ����������� ������. ��������� �������������� ������ BBCode � ��������������
    �������� $this -> syntax - ������ ��������� ���������:
    Array
    (
        ...
        [i] => Array  // [i] - ������������� ���� ������� � 0
            (
                [type] => ��� ��������: 'text', 'open', 'close' ��� 'open/close'
                          'text'  - ������� ������������� ������ ����� ������
                          'open'  - ������� ������������� ������������ ����
                          'close' - ������� ������������� ������������ ����
                          'open/close' - ������� ������������� ��������� ����
                                         (�������� ������: [img="..." /])
                [str]  => ��������� ������������� ��������: ����� ����� ������
                          ��� ��� (��������: '[FONT color=red size=+1]')
                [name] => ��� ����. ������ � ������ ��������. ��������: 'color'.
                          �������� [name] ����������� ��� ��������� ���� 'text'
                          � ����� ���� ������ ������� ��� ��������� ����
                          'close'. � ��������� ������ ������� �����
                          ��������������� ���� '[/]', ������� ����� ���������
                          ����������� ��� ���������� ����������� ����� ���.
                [attrib] => Array         // ��� �������� ���������� ������ ���
                    (                     // ��������� ����� 'open' �
                        ...               // 'open/close'
                        ...
                        [��� ��������] => �������� ��������. ��������:
                        ...               [color] => red
                                          ��� �������� ������ � ������ ��������.
                                          �������� �������� ����� ���� ������
                                          �������. ��� ���� ���� ������������ �
                                          ������ ���������. ��� ��� ����, �����
                                          ����� ���� ��������, ��������, �
                                          ������ ������ - [color="#555555"]
                    )
                [layout] => Array                 // ��� �������� ������������
                    (                             // ��� ��������� ���� 'text'.
                        [0] => Array              // ������ �������� ����
                            (                     // ( ��� ������ , ������ )
                                [0] => 0          // ���� ����� ���� ���������:
                                [1] => [          // 0 - ������ ('[' ��� ']')
                            )                     // 1 - ���� '/'
                        ...                       // 2 - ��� ����
                        [i] => Array              //     (�������� - 'FONT')
                            (                     // 3 - ���� '='
                                [0] => ��� ������ // 4 - ������ �� ����������
                                [1] => ������     //     ��������
                            )                     // 5 - ������� ��� ��������,
                        ...                       //     �������������� ��������
                                                  //     ��������
                    )                             // 6 - ��� ��������
            )                                     // 7 - �������� ��������
        ...
    )
    */
    function bbcode($code) {
        /*
        ���������� ����� �������� ���������
        ������ ��������� ��������� ��������:
        0  - ������ ������������ ��� ��������� ��� ����. ������� ��� ������.
        1  - ��������� ������ "[", ������� ������� ������� ����. ������� ���
             ����, ��� ������ "/".
        2  - ����� � ���� ������������� ������ "[". ������� ���������� ������
             �������. ������� ��� ����, ��� ������ "/".
        3  - ����� � ���� �������������� ������. ������� ������ �� �������� "[".
             ������� ��� ������.
        4  - ����� ����� "[" ����� ������ "/". ������������, ��� ������ �
             ����������� ���. ������� ��� ���� ��� ������ "]".
        5  - ����� ����� "[" ����� ��� ����. �������, ��� ��������� �
             ����������� ����. ������� ������ ��� "=" ��� "/" ��� "]".
        6  - ����� ���������� ���� "]". ������� ��� ������.
        7  - ����� ����� "[/" ����� ��� ����. ������� "]".
        8  - � ����������� ���� ����� "=". ������� ������ ��� �������� ��������.
        9  - � ����������� ���� ����� "/", ���������� �������� ����. �������
             "]".
        10 - � ����������� ���� ����� ������ ����� ����� ���� ��� �����
             ��������. ������� "=" ��� ��� ������� �������� ��� "/" ��� "]".
        11 - ����� '"' ���������� �������� ��������, ������������ ���������.
             ������� ��� ������.
        12 - ����� "'" ���������� �������� ��������, ������������ �����������.
             ������� ��� ������.
        13 - ����� ������ ��������������� �������� ��������. ������� ��� ������.
        14 - � ����������� ���� ����� "=" ����� ������. ������� ��������
             ��������.
        15 - ����� ��� ��������. ������� ������ ��� "=" ��� "/" ��� "]".
        16 - ��������� ������ �������� ��������, ������������� ���������.
             ������� ��� ������.
        17 - ���������� �������� ��������. ������� ������ ��� ��� ����������
             �������� ��� "/" ��� "]".
        18 - ��������� ������ �������� ��������, ������������� �����������.
             ������� ��� ������.
        19 - ��������� ������ ��������������� �������� ��������. ������� ���
             ������.
        20 - ����� ������ ����� �������� ��������. ������� ��� ����������
             �������� ��� "/" ��� "]".

        �������� ��������� ��������:
        */
        $finite_automaton = array(
               // ���������� |   ��������� ��� ������� ������� (������)   |
               //  ��������� |  0 |  1 |  2 |  3 |  4 |  5 |  6 |  7 |  8 |
                   0 => array(  1 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 )
                ,  1 => array(  2 ,  3 ,  3 ,  3 ,  3 ,  4 ,  3 ,  3 ,  5 )
                ,  2 => array(  2 ,  3 ,  3 ,  3 ,  3 ,  4 ,  3 ,  3 ,  5 )
                ,  3 => array(  1 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 )
                ,  4 => array(  2 ,  6 ,  3 ,  3 ,  3 ,  3 ,  3 ,  3 ,  7 )
                ,  5 => array(  2 ,  6 ,  3 ,  3 ,  8 ,  9 , 10 ,  3 ,  3 )
                ,  6 => array(  1 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 )
                ,  7 => array(  2 ,  6 ,  3 ,  3 ,  3 ,  3 ,  3 ,  3 ,  3 )
                ,  8 => array( 13 , 13 , 11 , 12 , 13 , 13 , 14 , 13 , 13 )
                ,  9 => array(  2 ,  6 ,  3 ,  3 ,  3 ,  3 ,  3 ,  3 ,  3 )
                , 10 => array(  2 ,  6 ,  3 ,  3 ,  8 ,  9 ,  3 , 15 , 15 )
                , 11 => array( 16 , 16 , 17 , 16 , 16 , 16 , 16 , 16 , 16 )
                , 12 => array( 18 , 18 , 18 , 17 , 18 , 18 , 18 , 18 , 18 )
                , 13 => array( 19 ,  6 , 19 , 19 , 19 , 19 , 17 , 19 , 19 )
                , 14 => array(  2 ,  3 , 11 , 12 , 13 , 13 ,  3 , 13 , 13 )
                , 15 => array(  2 ,  6 ,  3 ,  3 ,  8 ,  9 , 10 ,  3 ,  3 )
                , 16 => array( 16 , 16 , 17 , 16 , 16 , 16 , 16 , 16 , 16 )
                , 17 => array(  2 ,  6 ,  3 ,  3 ,  3 ,  9 , 20 , 15 , 15 )
                , 18 => array( 18 , 18 , 18 , 17 , 18 , 18 , 18 , 18 , 18 )
                , 19 => array( 19 ,  6 , 19 , 19 , 19 , 19 , 20 , 19 , 19 )
                , 20 => array(  2 ,  6 ,  3 ,  3 ,  3 ,  9 ,  3 , 15 , 15 )
            );
        // �������� ������ ������:
        $array_of_tokens = $this -> get_array_of_tokens($code);
        // ��������� ��� � ������� ������������ ��������:
        $mode = 0;
        $result = array();
        $tag_decomposition = array();
        $token_key = -1;
        foreach ( $array_of_tokens as $token ) {
            $previous_mode = $mode;
            $mode = $finite_automaton[$previous_mode][$token[0]];
            switch ( $mode ) {
                case 0:
                    if (-1<$token_key && 'text'==$result[$token_key]['type']) {
                        $result[$token_key]['str'] .= $token[1];
                    } else {
                        $result[++$token_key] = array(
                                'type' => 'text',
                                'str' => $token[1]
                            );
                    }
                    break;
                case 1:
                    $tag_decomposition['name']     = '';
                    $tag_decomposition['type']     = '';
                    $tag_decomposition['str']      = '[';
                    $tag_decomposition['layout'][] = array( 0, '[' );
                    break;
                case 2:
                    if (-1<$token_key && 'text'==$result[$token_key]['type']) {
                        $result[$token_key]['str'] .= $tag_decomposition['str'];
                    } else {
                        $result[++$token_key] = array(
                                'type' => 'text',
                                'str' => $tag_decomposition['str']
                            );
                    }
                    $tag_decomposition = array();
                    $tag_decomposition['name']     = '';
                    $tag_decomposition['type']     = '';
                    $tag_decomposition['str']      = '[';
                    $tag_decomposition['layout'][] = array( 0, '[' );
                    break;
                case 3:
                    if (-1<$token_key && 'text'==$result[$token_key]['type']) {
                        $result[$token_key]['str'] .= $tag_decomposition['str'];
                        $result[$token_key]['str'] .= $token[1];
                    } else {
                        $result[++$token_key] = array(
                                'type' => 'text',
                                'str' => $tag_decomposition['str'].$token[1]
                            );
                    }
                    $tag_decomposition = array();
                    break;
                case 4:
                    $tag_decomposition['type'] = 'close';
                    $tag_decomposition['str'] .= '/';
                    $tag_decomposition['layout'][] = array( 1, '/' );
                    break;
                case 5:
                    $tag_decomposition['type'] = 'open';
                    $name = strtolower($token[1]);
                    $tag_decomposition['name'] = $name;
                    $tag_decomposition['str'] .= $token[1];
                    $tag_decomposition['layout'][] = array( 2, $token[1] );
                    $tag_decomposition['attrib'][$name] = '';
                    break;
                case 6:
                    if ( ! isset($tag_decomposition['name']) ) {
                        $tag_decomposition['name'] = '';
                    }
                    if ( 13 == $previous_mode || 19 == $previous_mode ) {
                        $tag_decomposition['layout'][] = array( 7, $value );
                    }
                    $tag_decomposition['str'] .= ']';
                    $tag_decomposition['layout'][] = array( 0, ']' );
                    $result[++$token_key] = $tag_decomposition;
                    $tag_decomposition = array();
                    break;
                case 7:
                    $tag_decomposition['name'] = strtolower($token[1]);
                    $tag_decomposition['str'] .= $token[1];
                    $tag_decomposition['layout'][] = array( 2, $token[1] );
                    break;
                case 8:
                    $tag_decomposition['str'] .= '=';
                    $tag_decomposition['layout'][] = array( 3, '=' );
                    break;
                case 9:
                    $tag_decomposition['type'] = 'open/close';
                    $tag_decomposition['str'] .= '/';
                    $tag_decomposition['layout'][] = array( 1, '/' );
                    break;
                case 10:
                    $tag_decomposition['str'] .= $token[1];
                    $tag_decomposition['layout'][] = array( 4, $token[1] );
                    break;
                case 11:
                    $tag_decomposition['str'] .= '"';
                    $tag_decomposition['layout'][] = array( 5, '"' );
                    break;
                case 12:
                    $tag_decomposition['str'] .= "'";
                    $tag_decomposition['layout'][] = array( 5, "'" );
                    break;
                case 13:
                    $tag_decomposition['attrib'][$name] = $token[1];
                    $value = $token[1];
                    $tag_decomposition['str'] .= $token[1];
                    break;
                case 14:
                    $tag_decomposition['str'] .= $token[1];
                    $tag_decomposition['layout'][] = array( 4, $token[1] );
                    break;
                case 15:
                    $name = strtolower($token[1]);
                    $tag_decomposition['str'] .= $token[1];
                    $tag_decomposition['layout'][] = array( 6, $token[1] );
                    $tag_decomposition['attrib'][$name] = '';
                    break;
                case 16:
                    $tag_decomposition['str'] .= $token[1];
                    $tag_decomposition['attrib'][$name] .= $token[1];
                    $value .= $token[1];
                    break;
                case 17:
                    $tag_decomposition['str'] .= $token[1];
                    $tag_decomposition['layout'][] = array( 7, $value );
                    $value = '';
                    $tag_decomposition['layout'][] = array( 5, $token[1] );
                    break;
                case 18:
                    $tag_decomposition['str'] .= $token[1];
                    $tag_decomposition['attrib'][$name] .= $token[1];
                    $value .= $token[1];
                    break;
                case 19:
                    $tag_decomposition['str'] .= $token[1];
                    $tag_decomposition['attrib'][$name] .= $token[1];
                    $value .= $token[1];
                    break;
                case 20:
                    $tag_decomposition['str'] .= $token[1];
                    if ( 13 == $previous_mode || 19 == $previous_mode ) {
                        $tag_decomposition['layout'][] = array( 7, $value );
                    }
                    $value = '';
                    $tag_decomposition['layout'][] = array( 4, $token[1] );
                    break;
            }
        }
        if ( count($tag_decomposition) ) {
            if ( -1 < $token_key && 'text' == $result[$token_key]['type'] ) {
                $result[$token_key]['str'] .= $tag_decomposition['str'];
            } else {
                $result[++$token_key] = array(
                        'type' => 'text',
                        'str' => $tag_decomposition['str']
                    );
            }
        }
        $this -> syntax = $result;
    }
    // ������� ���������� ����������� � ���������� ������ ���������
    function get_tree_of_elems() {
        /* ������ ���� ������������: ���������� $this -> syntax � ����������
           ��������� ��������� */
        $structure = array();
        $structure_key = -1;
        $level = 0;
        $open_tags = array();
        foreach ( $this -> syntax as $syntax_key => $val ) {
            unset($val['layout']);
            switch ( $val['type'] ) {
                case 'text':
                    $type = (-1 < $structure_key)
                        ? $structure[$structure_key]['type'] : false;
                    if ( 'text' == $type ) {
                        $structure[$structure_key]['str'] .= $val['str'];
                    } else {
                        $structure[++$structure_key] = $val;
                        $structure[$structure_key]['level'] = $level;
                    }
                    break;
                case 'open/close':
                    foreach (array_reverse($open_tags,true) as $ult_key => $ultimate) {
                        $ends = $this->info_about_tags[$ultimate]['ends'];
                        if ( in_array($val['name'],$ends) ) {
                            $structure[++$structure_key] = array(
                                    'type'  => 'close',
                                    'name'  => $ultimate,
                                    'str'   => '',
                                    'level' => --$level
                                );
                            unset($open_tags[$ult_key]);
                        } else { break; }
                    }
                    $structure[++$structure_key] = $val;
                    $structure[$structure_key]['level'] = $level;
                    break;
                case 'open':
                    foreach (array_reverse($open_tags,true) as $ult_key => $ultimate) {
                        $ends = $this->info_about_tags[$ultimate]['ends'];
                        if ( in_array($val['name'],$ends) ) {
                            $structure[++$structure_key] = array(
                                    'type'  => 'close',
                                    'name'  => $ultimate,
                                    'str'   => '',
                                    'level' => --$level
                                );
                            unset($open_tags[$ult_key]);
                        } else { break; }
                    }
                    if ( $this->info_about_tags[$val['name']]['is_close'] ) {
                        $val['type'] = 'open/close';
                        $structure[++$structure_key] = $val;
                        $structure[$structure_key]['level'] = $level;
                    } else {
                        $structure[++$structure_key] = $val;
                        $structure[$structure_key]['level'] = $level++;
                        $open_tags[] = $val['name'];
                    }
                    break;
                case 'close':
                    if ( ! count($open_tags) ) {
                        $type = (-1 < $structure_key)
                            ? $structure[$structure_key]['type'] : false;
                        if ( 'text' == $type ) {
                            $structure[$structure_key]['str'] .= $val['str'];
                        } else {
                            $structure[++$structure_key] = array(
                                    'type'  => 'text',
                                    'str'   => $val['str'],
                                    'level' => 0
                                );
                        }
                        break;
                    }
                    if ( ! $val['name'] ) {
                        end($open_tags);
                        list($ult_key, $ultimate) = each($open_tags);
                        $val['name'] = $ultimate;
                        $structure[++$structure_key] = $val;
                        $structure[$structure_key]['level'] = --$level;
                        unset($open_tags[$ult_key]);
                        break;
                    }
                    if ( ! in_array($val['name'],$open_tags) ) {
                        $type = (-1 < $structure_key)
                            ? $structure[$structure_key]['type'] : false;
                        if ( 'text' == $type ) {
                            $structure[$structure_key]['str'] .= $val['str'];
                        } else {
                            $structure[++$structure_key] = array(
                                    'type'  => 'text',
                                    'str'   => $val['str'],
                                    'level' => $level
                                );
                        }
                        break;
                    }
                    foreach (array_reverse($open_tags,true) as $ult_key => $ultimate) {
                        if ( $ultimate != $val['name'] ) {
                            $structure[++$structure_key] = array(
                                    'type'  => 'close',
                                    'name'  => $ultimate,
                                    'str'   => '',
                                    'level' => --$level
                                );
                            unset($open_tags[$ult_key]);
                        } else { break; }
                    }
                    $structure[++$structure_key] = $val;
                    $structure[$structure_key]['level'] = --$level;
                    unset($open_tags[$ult_key]);
            }
        }
        foreach (array_reverse($open_tags,true) as $ult_key => $ultimate) {
            $structure[++$structure_key] = array(
                    'type'  => 'close',
                    'name'  => $ultimate,
                    'str'   => '',
                    'level' => --$level
                );
            unset($open_tags[$ult_key]);
        }
        /* ������ ���� ������������: �����������, ����� �� ��������
           ������������� �����������. �������������� ����� ����������
           $structure. */
        $normalized = array();
        $normal_key = -1;
        $level = 0;
        $open_tags = array();
        $not_tags = array();
        foreach ( $structure as $structure_key => $val ) {
            switch ( $val['type'] ) {
                case 'text':
                    $type = (-1 < $normal_key)
                        ? $normalized[$normal_key]['type'] : false;
                    if ( 'text' == $type ) {
                        $normalized[$normal_key]['str'] .= $val['str'];
                    } else {
                        $normalized[++$normal_key] = $val;
                        $normalized[$normal_key]['level'] = $level;
                    }
                    break;
                case 'open/close':
                    $is_open = count($open_tags);
                    end($open_tags);
                    $info = $this->info_about_tags[$val['name']];
                    $children = $is_open
                        ? $this->info_about_tags[current($open_tags)]['children']
                        : array();
                    $not_normal = ! $level && ! $info['permission_top_level']
                        || $is_open && ! in_array($val['name'],$children);
                    if ( $not_normal ) {
                        $type = (-1 < $normal_key)
                            ? $normalized[$normal_key]['type'] : false;
                        if ( 'text' == $type ) {
                            $normalized[$normal_key]['str'] .= $val['str'];
                        } else {
                            $normalized[++$normal_key] = array(
                                    'type'  => 'text',
                                    'str'   => $val['str'],
                                    'level' => $level
                                );
                        }
                        break;
                    }
                    $normalized[++$normal_key] = $val;
                    $normalized[$normal_key]['level'] = $level;
                    break;
                case 'open':
                    $is_open = count($open_tags);
                    end($open_tags);
                    $info = $this->info_about_tags[$val['name']];
                    $children = $is_open
                        ? $this->info_about_tags[current($open_tags)]['children']
                        : array();
                    $not_normal = ! $level && ! $info['permission_top_level']
                        || $is_open && ! in_array($val['name'],$children);
                    if ( $not_normal ) {
                        $not_tags[$val['level']] = $val['name'];
                        $type = (-1 < $normal_key)
                            ? $normalized[$normal_key]['type'] : false;
                        if ( 'text' == $type ) {
                            $normalized[$normal_key]['str'] .= $val['str'];
                        } else {
                            $normalized[++$normal_key] = array(
                                    'type'  => 'text',
                                    'str'   => $val['str'],
                                    'level' => $level
                                );
                        }
                        break;
                    }
                    $normalized[++$normal_key] = $val;
                    $normalized[$normal_key]['level'] = $level++;
                    $ult_key = count($open_tags);
                    $open_tags[$ult_key] = $val['name'];
                    break;
                case 'close':
                    $not_normal = isset($not_tags[$val['level']])
                        && $not_tags[$val['level']] = $val['name'];
                    if ( $not_normal ) {
                        unset($not_tags[$val['level']]);
                        $type = (-1 < $normal_key)
                            ? $normalized[$normal_key]['type'] : false;
                        if ( 'text' == $type ) {
                            $normalized[$normal_key]['str'] .= $val['str'];
                        } else {
                            $normalized[++$normal_key] = array(
                                    'type'  => 'text',
                                    'str'   => $val['str'],
                                    'level' => $level
                                );
                        }
                        break;
                    }
                    $normalized[++$normal_key] = $val;
                    $normalized[$normal_key]['level'] = --$level;
                    $ult_key = count($open_tags) - 1;
                    unset($open_tags[$ult_key]);
                    break;
            }
        }
        // ��������� ������ ���������
        $result = array();
        $result_key = -1;
        $open_tags = array();
        $val_key = -1;
        foreach ( $normalized as $normal_key => $val ) {
            switch ( $val['type'] ) {
                case 'text':
                    if ( ! $val['level'] ) {
                        $result[++$result_key] = array(
                                'type' => 'text',
                                'str' => $val['str']
                            );
                        break;
                    }
                    $open_tags[$val['level']-1]['val'][] = array(
                            'type' => 'text',
                            'str' => $val['str']
                        );
                    break;
                case 'open/close':
                    if ( ! $val['level'] ) {
                        $result[++$result_key] = array(
                                'type'   => 'item',
                                'name'   => $val['name'],
                                'attrib' => $val['attrib'],
                                'val'    => array()
                            );
                        break;
                    }
                    $open_tags[$val['level']-1]['val'][] = array(
                            'type'   => 'item',
                            'name'   => $val['name'],
                            'attrib' => $val['attrib'],
                            'val'    => array()
                        );
                    break;
                case 'open':
                    $open_tags[$val['level']] = array(
                            'type'   => 'item',
                            'name'   => $val['name'],
                            'attrib' => $val['attrib'],
                            'val'    => array()
                        );
                    break;
                case 'close':
                    if ( ! $val['level'] ) {
                        $result[++$result_key] = $open_tags[0];
                        unset($open_tags[0]);
                        break;
                    }
                    $open_tags[$val['level']-1]['val'][] = $open_tags[$val['level']];
                    unset($open_tags[$val['level']]);
                    break;
            }
        }
        return $result;
    }
    /*
    ������� ������������� HTML-���, ��������� � ����� ������� <br />, �������� �
    "�������������� ������".
    */
    function insert_smiles($text) {
        $text = nl2br(htmlspecialchars($text,ENT_NOQUOTES));
        $text = str_replace('  ', '&nbsp;&nbsp;', $text);
        $search = array(
                "'(.)((http|https|ftp)://[\w\d-]+\.[\w\d-]+[^\s<\"\']*[^.,;\s<\"\'\)]+)'si",
                "'([^/])(www\.[\w\d-]+\.[\w\d-]+[^\s<\"\']*[^.,;\s<\"\'\)]+)'si",
                "'([^\w\d-\.])([\w\d-\.]+@[\w\d-\.]+\.[\w]+[^.,;\s<\"\'\)]+)'si"
            );
        $replace = array(
                '$1<a href="$2" target="_blank">$2</a>',
                '$1<a href="http://$2" target="_blank">$2</a>',
                '$1<a href="mailto:$2">$2</a>'
            );
        $text = preg_replace($search, $replace, $text);
        foreach ($this -> mnemonics as $mnemonic => $value) {
            $text = str_replace($mnemonic, $value, $text);
        }
        return $text;
    }
    // ������� ��������� ������ ��������� BBCode � HTML � ���������� ���������
    function get_html($tree_of_elems=false) {
        if (! is_array($tree_of_elems)) {
            $tree_of_elems = $this -> get_tree_of_elems();
        }
        $result = '';
        $lbr = 0;
        $rbr = 0;
        foreach ( $tree_of_elems as $elem ) {
            if ('text'==$elem['type']) {
                $elem['str'] = $this -> insert_smiles($elem['str']);
                for ($i=0; $i<$rbr; ++$i) {
                    $elem['str'] = ltrim($elem['str']);
                    if ('<br />' == substr($elem['str'], 0, 6)) {
                        $elem['str'] = substr_replace($elem['str'], '', 0, 6);
                    }
                }
                $result .= $elem['str'];
            } else {
                $lbr = $this -> info_about_tags[$elem['name']]['lbr'];
                $rbr = $this -> info_about_tags[$elem['name']]['rbr'];
                for ($i=0; $i<$lbr; ++$i) {
                    $result = rtrim($result);
                    if ('<br />' == substr($result, -6)) {
                        $result = substr_replace($result, '', -6, 6);
                    }
                }
                $func_name = $this -> info_about_tags[$elem['name']]['handler'];
                $result .= call_user_func(array(&$this,$func_name), $elem);
            }
        }
        return $result;
    }
    // ������� - ���������� ���� [*]
    function star_2html($elem) {
        return '<li class="bb">'.$this -> get_html($elem['val']).'</li>';
    }
    // ������� - ���������� ���� [align]
    function align_2html($elem) {
        $align = htmlspecialchars($elem['attrib']['align']);
        return '<div align="'.$align.'">'.$this -> get_html($elem['val']).'</div>';
    }
    // ������� - ���������� ���� [b]
    function b_2html($elem) {
        return '<b>'.$this -> get_html($elem['val']).'</b>';
    }
    // ������� - ���������� ���� [caption]
    function caption_2html($elem) {
        return '<caption class="bb">'.$this -> get_html($elem['val']).'</caption>';
    }
    // ������� - ���������� ���� [code]
    function code_2html($elem) {
        $lang = $elem['attrib']['code'];
        if(!$lang){ $lang = 'php'; }
        $str = '<div class="bb_tag_code">';
		$str .= '<b>��� '.strtoupper($lang).':</b><br/>';
		$str .= '<pre class="brush: '.strtolower($lang).';">';
        $inPage = cmsPage::getInstance();
        if (!isset($_SESSION['bbcode']['code_js_added']['main'])){
            $inPage->addHeadCSS('includes/jquery/syntax/styles/shCore.css');
            $inPage->addHeadCSS('includes/jquery/syntax/styles/shThemeDefault.css');
            $inPage->addHeadJS('includes/jquery/syntax/src/shCore.js');
            $_SESSION['bbcode']['code_js_added']['main'] = 1;
        }
        if (!isset($_SESSION['bbcode']['code_js_added'][$lang])){
            $inPage->addHeadJS('includes/jquery/syntax/scripts/shBrush'.ucfirst(strtolower($lang)).'.js');
            $_SESSION['bbcode']['code_js_added'][$lang] = 1;
        }
        foreach ($elem['val'] as $item) {
            if ('item'==$item['type']) { continue; }
            $item['str'] = str_replace('&#8217;', "'", $item['str']);
            $item['str'] = str_replace('�', "'", $item['str']);
            $str .= htmlspecialchars($item['str']);
        }
        $str .= '</pre></div>';
        $str .= '<script type="text/javascript">
                    SyntaxHighlighter.config.clipboardSwf = \'/includes/jquery/syntax/scripts/clipboard.swf\';
                    SyntaxHighlighter.all();
                 </script>';
        return $str;
    }
    // ������� - ���������� ���� [video]
    function video_2html($elem) {
        $str = '<div class="bb_tag_video">';
        foreach ($elem['val'] as $item) {
            if ('item'==$item['type']) { continue; }
            $str .= strip_tags($item['str'], '<object><param><embed>');
        }
        $str .= '</div>';
        return $str;
    }	
    // ������� - ���������� ���� [hide]
    function hide_2html($elem) {
        $inUser = cmsUser::getInstance();
        $str = '<div class="bb_tag_hide">';
		if ($inUser->id){
			$hide = '';
			foreach ($elem['val'] as $item) {
				if ('item'==$item['type']) { continue; }
				$hide .= nl2br($item['str']);
			}
			if (substr($hide, 0, 6)=='<br />') { $hide = substr($hide, 6, strlen($hide)-5); }
			$str .= $hide;
		} else {
			$str .= '������� ����� ����� ������ <a href="/registration">������������������</a> �������������';
		}
        $str .= '</div>';
        return $str;
    }
    // ������� - ���������� ���� [color]
    function color_2html($elem) {
        $color = htmlspecialchars($elem['attrib']['color']);
        return '<font color="'.$color.'">'.$this -> get_html($elem['val'])
            .'</font>';
    }
    // ������� - ���������� ���� [email]
    function email_2html($elem) {
        $attr = ' class="bb_email"';
        $href = $elem['attrib']['email'];
        if ( ! $href ) {
            foreach ($elem['val'] as $text) {
                if ('text'==$text['type']) { $href .= $text['str']; }
            }
        }
        $protocols = array('mailto:');
        $is_http = false;
        foreach ($protocols as $val) {
            if ($val==substr($href,0,strlen($val))) {
                $is_http = true;
                break;
            }
        }
        if (! $is_http) { $href = 'mailto:'.$href; }
        if ($href) { $attr .= ' href="'.htmlspecialchars($href).'"'; }
        $title = isset($elem['attrib']['title']) ? $elem['attrib']['title'] : '';
        if ($title) { $attr .= ' title="'.htmlspecialchars($title).'"'; }
        $name = isset($elem['attrib']['name']) ? $elem['attrib']['name'] : '';
        if ($name) { $attr .= ' name="'.htmlspecialchars($name).'"'; }
        $target = isset($elem['attrib']['target']) ? $elem['attrib']['target'] : '';
        if ($target) { $attr .= ' target="'.htmlspecialchars($target).'"'; }
        return '<a'.$attr.'>'.$this -> get_html($elem['val']).'</a>';
    }
    // ������� - ���������� ���� [font]
    function font_2html($elem) {
        $face = $elem['attrib']['font'];
        $attr = ' face="'.htmlspecialchars($face).'"';
        $color = isset($elem['attrib']['color']) ? $elem['attrib']['color'] : '';
        if ($color) { $attr .= ' color="'.htmlspecialchars($color).'"'; }
        $size = isset($elem['attrib']['size']) ? $elem['attrib']['size'] : '';
        if ($size) { $attr .= ' size="'.htmlspecialchars($size).'"'; }
        return '<font'.$attr.'>'.$this -> get_html($elem['val']).'</font>';
    }
    // ������� - ���������� ���� [google]
    function google_2html($elem) {
        $attr = htmlspecialchars(urlencode($elem['attrib']['google']));
        $attr = ' href="http://www.google.com/search?q='.$attr.'"';
        $title = isset($elem['attrib']['title']) ? $elem['attrib']['title'] : '';
        if ($title) { $attr .= ' title="'.htmlspecialchars($title).'"'; }
        $name = isset($elem['attrib']['name']) ? $elem['attrib']['name'] : '';
        if ($name) { $attr .= ' name="'.htmlspecialchars($name).'"'; }
        $target = isset($elem['attrib']['target']) ? $elem['attrib']['target'] : '';
        if ($target) { $attr .= ' target="'.htmlspecialchars($target).'"'; }
        return '<a class="bb_google" '.$attr.'>'.$this -> get_html($elem['val']).'</a>';
    }
    // ������� - ���������� ���� [h1]
    function h1_2html($elem) {
        $attr = ' class="bb_tag_h1"';
        $align = isset($elem['attrib']['align']) ? $elem['attrib']['align'] : '';
        if ( $align ) { $attr .= ' align="'.htmlspecialchars($align).'"'; }
        return '<h1'.$attr.'>'.$this -> get_html($elem['val']).'</h1>';
    }
    // ������� - ���������� ���� [h2]
    function h2_2html($elem) {
        $attr = ' class="bb_tag_h2"';
        $align = isset($elem['attrib']['align']) ? $elem['attrib']['align'] : '';
        if ( $align ) { $attr .= ' align="'.htmlspecialchars($align).'"'; }
        return '<h2'.$attr.'>'.$this -> get_html($elem['val']).'</h2>';
    }
    // ������� - ���������� ���� [h3]
    function h3_2html($elem) {
        $attr = ' class="bb_tag_h3"';
        $align = isset($elem['attrib']['align']) ? $elem['attrib']['align'] : '';
        if ( $align ) { $attr .= ' align="'.htmlspecialchars($align).'"'; }
        return '<h3'.$attr.'>'.$this -> get_html($elem['val']).'</h3>';
    }
    // ������� - ���������� ���� [hr]
    function hr_2html($elem) {
        return '<hr class="bb" />';
    }
    // ������� - ���������� ���� [i]
    function i_2html($elem) {
        return '<i>'.$this -> get_html($elem['val']).'</i>';
    }
    // ������� - ���������� ���� [img]
    function img_2html($elem) {
        $attr = 'alt=""';
//        $width = isset($elem['attrib']['width']) ? (int) $elem['attrib']['width'] : 0;
//        $attr .= $width ? ' width="'.$width.'"' : '';
//        $height = isset($elem['attrib']['height']) ? (int) $elem['attrib']['height'] : 0;
//        $attr .= $height ? ' height="'.$height.'"' : '';
        if (''!==$border) { $attr .= ' border="'.htmlspecialchars($border).'"'; };
      
	    $src = '';
        foreach ($elem['val'] as $text) {
            if ('text'==$text['type']) { $src .= $text['str']; }
        }
		
		$width = '';
		$hegiht = '';
		$zoom = false;
					
		if (!strstr($src, 'http://')){				
			if(file_exists($_SERVER['DOCUMENT_ROOT'].$src)){
				if (function_exists('getimagesize')){
					$size = getimagesize($_SERVER['DOCUMENT_ROOT'].$src);
					$width = $size[0];
					$height = $size[1];
					while ($width > 640 || $height > 640){
						$width  = round($width*0.9);
						$height = round($height*0.9);
						$zoom   = true;
					}			
				} 		
				if (!$zoom){
					return '<img src="'.htmlspecialchars($src).'" '.$attr.' />';
				} else {
					$html = '<div class="forum_zoom" style="width:'.$width.'px">'."\n";
						$html .= '<div><a href="'.htmlspecialchars($src).'" target="_blank"><img src="'.htmlspecialchars($src).'" '.$attr.' width="'.$width.'" height="'.$height.'" border="0"/></a></div>'."\n";
						$html .= '<div class="forum_zoom_text">����������� ���������. ��������, ����� ������� ��������.</div>'."\n";
					$html .= '</div>';
					return $html;
				}		
			} else {
				return '<div class="forum_lostimg">���� "'.$src.'" �� ������!</div>';
			}
		} else {
			return '<img src="'.htmlspecialchars($src).'" '.$attr.' />';
		}
    }
    // ������� - ���������� ���� [list]
    function list_2html($elem) {
        $attr = ' class="bb"';
        $list_attr = $elem['attrib']['list'];
        switch ($list_attr) {
            case '1':
                $tag_name = 'ol';
                $attr .= ' type="1"';
                break;
            case 'a':
                $tag_name = 'ol';
                $attr .= ' type="a"';
                break;
            default:
                $tag_name = 'ul';
        }
        $str = '<'.$tag_name.$attr.'>';
        foreach ($elem['val'] as $item) {
            if ('text'==$item['type']) { continue; }
            $func_name = $this -> info_about_tags[$item['name']]['handler'];
            $str .= call_user_func(array(&$this,$func_name), $item);
        }
        $str .= '</'.$tag_name.'>';
        return $str;
    }
    // ������� - ���������� ���� [nobb]
    function nobb_2html($elem) {
        $str = '';
        foreach ($elem['val'] as $item) {
            if ('text'==$item['type']) {
                $str .= nl2br(htmlspecialchars($item['str']));
            }
        }
        $str = str_replace('  ', '&nbsp;&nbsp;', $str);
        return $str;
    }
    // ������� - ���������� ���� [php]
    function php_2html($elem) {
        $str = '';
        foreach ($elem['val'] as $item) {
            if ('text'==$item['type']) { $str .= $item['str']; }
        }
        if (false !== strpos($str,'<?')) {
            $str = highlight_string($str, true);
        } else {
            $str = '<?php '.$str.' ?>';
            $str = highlight_string($str, true);
            $str = str_replace('&lt;?php ', '', $str);
            $str = str_replace('&lt;?php&nbsp;', '', $str);
            $str = str_replace('<font color="#0000BB">?&gt;</font>', '', $str);
        }
        return '<div class="php">'.$str.'</div>';
    }
    // ������� - ���������� ���� [quote]
    function quote_2html($elem) {
        $author = htmlspecialchars($elem['attrib']['quote']);
        $author = $author
            ? '<tr><td class="author"><b>'.$author.' �����(�):</b></td></tr>'
            : '';
        return '<table width="90%" border="0" align="center" class="bb_quote">'
            .$author.'<tr><td class="quote">'.$this -> get_html($elem['val'])
            .'</td></tr></table>';
    }
    // ������� - ���������� ���� [s]
    function s_2html($elem) {
        return '<s>'.$this -> get_html($elem['val']).'</s>';
    }
    // ������� - ���������� ���� [size]
    function size_2html($elem) {
        $size = isset($elem['attrib']['size']) ? (int) $elem['attrib']['size'] : 0;
        return '<font size="'.$size.'">'.$this -> get_html($elem['val']).'</font>';
    }
    // ������� - ���������� ���� [sub]
    function sub_2html($elem) {
        return '<sub>'.$this -> get_html($elem['val']).'</sub>';
    }
    // ������� - ���������� ���� [sup]
    function sup_2html($elem) {
        return '<sup>'.$this -> get_html($elem['val']).'</sup>';
    }
    // ������� - ���������� ���� [table]
    function table_2html($elem) {
        $attr = ' class="bb"';
        $border = isset($elem['attrib']['border'])
            ? (int) $elem['attrib']['border']
            : null;
        if (null !== $border) { $attr .= ' border="'.$border.'"'; }
        $width = isset($elem['attrib']['width']) ? $elem['attrib']['width'] : '';
        if ($width) { $attr .= ' width="'.htmlspecialchars($width).'"'; }
        $cellspacing = isset($elem['attrib']['cellspacing'])
            ? (int) $elem['attrib']['cellspacing']
            : null;
        if (null !== $cellspacing) { $attr .= ' cellspacing="'.$cellspacing.'"'; }
        $cellpadding = isset($elem['attrib']['cellpadding'])
            ? (int) $elem['attrib']['cellpadding']
            : null;
        if (null !== $cellpadding) { $attr .= ' cellpadding="'.$cellpadding.'"'; }
        $align = isset($elem['attrib']['align']) ? $elem['attrib']['align'] : '';
        if ($align) { $attr .= ' align="'.htmlspecialchars($align).'"'; }
        $str = '<table'.$attr.'>';
        foreach ($elem['val'] as $item) {
            if ('text'==$item['type']) { continue; }
            $func_name = $this -> info_about_tags[$item['name']]['handler'];
            $str .= call_user_func(array(&$this,$func_name), $item);
        }
        $str .= '</table>';
        return $str;
    }
    // ������� - ���������� ���� [td]
    function td_2html($elem) {
        $attr = ' class="bb"';
        $width = isset($elem['attrib']['width']) ? $elem['attrib']['width'] : '';
        if ($width) { $attr .= ' width="'.htmlspecialchars($width).'"'; }
        $height = isset($elem['attrib']['height']) ? $elem['attrib']['height'] : '';
        if ($height) { $attr .= ' height="'.htmlspecialchars($height).'"'; }
        $align = isset($elem['attrib']['align']) ? $elem['attrib']['align'] : '';
        if ($align) { $attr .= ' align="'.htmlspecialchars($align).'"'; }
        $valign = isset($elem['attrib']['valign']) ? $elem['attrib']['valign'] : '';
        if ($valign) { $attr .= ' valign="'.htmlspecialchars($valign).'"'; }
        $colspan
            = isset($elem['attrib']['colspan']) ? (int) $elem['attrib']['colspan'] : 0;
        if ($colspan) { $attr .= ' colspan="'.$colspan.'"'; }
        return '<td'.$attr.'>'.$this -> get_html($elem['val']).'</td>';
    }
    // ������� - ���������� ���� [th]
    function th_2html($elem) {
        $attr = ' class="bb"';
        $width = isset($elem['attrib']['width']) ? $elem['attrib']['width'] : '';
        if ($width) { $attr .= ' width="'.htmlspecialchars($width).'"'; }
        $height = isset($elem['attrib']['height']) ? $elem['attrib']['height'] : '';
        if ($height) { $attr .= ' height="'.htmlspecialchars($height).'"'; }
        $align = isset($elem['attrib']['align']) ? $elem['attrib']['align'] : '';
        if ($align) { $attr .= ' align="'.htmlspecialchars($align).'"'; }
        $valign = isset($elem['attrib']['valign']) ? $elem['attrib']['valign'] : '';
        if ($valign) { $attr .= ' valign="'.htmlspecialchars($valign).'"'; }
        $colspan
            = isset($elem['attrib']['colspan']) ? (int) $elem['attrib']['colspan'] : 0;
        if ($colspan) { $attr .= ' colspan="'.$colspan.'"'; }
        return '<th'.$attr.'>'.$this -> get_html($elem['val']).'</th>';
    }
    // ������� - ���������� ���� [tr]
    function tr_2html($elem) {
        $str = '<tr class="bb">';
        foreach ($elem['val'] as $item) {
            if ('text'==$item['type']) { continue; }
            $func_name = $this -> info_about_tags[$item['name']]['handler'];
            $str .= call_user_func(array(&$this,$func_name), $item);
        }
        $str .= '</tr>';
        return $str;
    }
    // ������� - ���������� ���� [tt]
    function tt_2html($elem) {
        return '<tt>'.$this -> get_html($elem['val']).'</tt>';
    }
    // ������� - ���������� ���� [u]
    function u_2html($elem) {
        return '<u>'.$this -> get_html($elem['val']).'</u>';
    }
    // ������� - ���������� ���� [url]
    function url_2html($elem) {
        $attr = '';
        $href = $elem['attrib']['url'];
        if ( ! $href ) {
            foreach ($elem['val'] as $text) {
                if ('text'==$text['type']) { $href .= $text['str']; }
            }
        }
        $protocols = array(
                'http://','https://','ftp://','file://','#','/','?','./','../'
            );
        $is_http = false;
        foreach ($protocols as $val) {
            if ($val==substr($href,0,strlen($val))) {
                $is_http = true;
                break;
            }
        }
        if (! $is_http) { $href = 'http://'.$href; }
        if ($href) {
            if (preg_match('/^http:\/\/'.$_SERVER['HTTP_HOST'].'/', $href)){
                $url = $href;
                $local = true;
            } else {
                $url = '/go/url='.htmlspecialchars($this->process_url($href));
                $local = false;
            }
            $attr .= ' href="'.$url.'"';
        }
        $title = isset($elem['attrib']['title']) ? $elem['attrib']['title'] : '';
        if ($title) { $attr .= ' title="'.htmlspecialchars($title).'"'; }
        $name = isset($elem['attrib']['name']) ? $elem['attrib']['name'] : '';
        if ($name) { $attr .= ' name="'.htmlspecialchars($name).'"'; }
        $target = isset($elem['attrib']['target']) ? $elem['attrib']['target'] : '';
        if ($target) { $attr .= ' target="'.htmlspecialchars($target).'"'; } 
        //���� �������� target �� ������, �������, ��� ������ ���� ��������� � ����� ����. 
        	elseif (!$local)
        	{ $attr .= ' target="_blank"'; }
        return '<a'.$attr.'>'.$this -> get_html($elem['val']).'</a>';
    }

  private function process_url($href){
  	return str_replace('?', '--q--', $href);
  }
}

?>