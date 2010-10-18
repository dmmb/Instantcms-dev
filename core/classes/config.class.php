<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
//                                   LICENSED BY GNU/GPL v2                                  //
//                                                                                           //
/*********************************************************************************************/

class cmsConfig {

    private static $instance;

    private function __construct(){
        
        $cfg_file = PATH.'/includes/config.inc.php';

        $this->homecom = '';

        if (file_exists($cfg_file)){
        
            include($cfg_file);

            foreach ($_CFG as $id=>$value) {
                $this->{$id} = $value;
            }

        }

        if (!$this->db_prefix){ $this->db_prefix = 'cms'; }
        if (!$this->homecom){ $this->homecom = ''; }

        return true;
	}

    private function __clone() {}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }        
        return self::$instance;
    }


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Сохраняет массив в файл конфигурации
     * @param array $_CFG
     */
    public function saveToFile($_CFG, $file='config.inc.php'){
        
        $filepath = PATH.'/includes/'.$file;

        if (file_exists($filepath)){
            if (!@is_writable($filepath)){ die('Файл <strong>'.$filepath.'</strong> недоступен для записи!'); }
        } else {
            if (!@is_writable(dirname($filepath))){ die('Папка <strong>'.dirname($filepath).'</strong> недоступна для записи!'); }
        }

        $cfg_file = fopen($filepath, 'w+');

        fputs($cfg_file, "<?php \n");
        fputs($cfg_file, "if(!defined('VALID_CMS')) { die('ACCESS DENIED'); } \n");
        fputs($cfg_file, '$_CFG = array();'."\n");

        foreach($_CFG as $key=>$value){
            if (is_int($value)){
                $s = '$_CFG' . "['$key'] \t= $value;\n";
            } else {
                $s = '$_CFG' . "['$key'] \t= '$value';\n";
            }
            fwrite($cfg_file, $s);
        }

        fwrite($cfg_file, "?>");
        fclose($cfg_file);

        return true;
        
    }
    	
}

?>