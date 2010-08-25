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

class cmsFormGen {

    private $xml;
    private $html;
    private $default_cfg;

    private $params;

//============================================================================//
//============================================================================//

    public function __construct($xml_file, $default_cfg) {

        $this->xml          = simplexml_load_file($xml_file);
        $this->default_cfg  = $default_cfg;

        $this->parseParams();

    }

//============================================================================//
//============================================================================//

    private function parseParams(){

        $inDB = cmsDatabase::getInstance();

        foreach($this->xml->params->param as $param){

            $param['value'] = $this->getParamValue($param['name'], (isset($param['default']) ? $param['default'] : ''));

            $param['html']  = $this->getParamHTML($param);

            $this->params[] = $param;

        }

        return;

    }

//============================================================================//
//============================================================================//

    private function getParamValue($param_name, $default){

        $param_name = (string)$param_name;
        $default    = (string)$default;
        $value      = '';

        if (isset($this->default_cfg[$param_name])){

            $value = $this->default_cfg[$param_name];

        } else {

            $value = $default;

        }

        if ($value === 'on') { $value = 1; }
        if ($value === 'off') { $value = 0; }

        return $value;

    }

//============================================================================//
//============================================================================//

    public function getHTML(){

        $inPage = cmsPage::getInstance();

        ob_start();

            global $tpl_data;

            $tpl_data['module'] = $this->xml->module;
            $tpl_data['fields'] = $this->params;

            $inPage->includeTemplateFile('admin/autoform.php');

        $this->html = ob_get_clean();

        return iconv('utf-8//IGNORE', 'cp1251//IGNORE', $this->html);

    }


//============================================================================//
//============================================================================//

    private function getParamHTML($param) {

        $type = (string)$param['type'];

        switch ($type){

            case 'number':  return $this->renderNumber($param);
                            break;

            case 'flag':    return $this->renderFlag($param);
                            break;

            case 'list_db': return $this->renderListDB($param);
                            break;

        }

        return;

    }

//============================================================================//
//============================================================================//

    private function renderNumber($param) {

        $name   = (string)$param['name'];
        $value  = (string)$param['value'];
        $units  = isset($param['units']) ? (string)$param['units'] : '';

        return '<input type="text" id="'.$name.'" name="'.$name.'" value="'.$value.'" class="param-number" /> '. $units;

    }

    private function renderFlag($param) {

        $name       = (string)$param['name'];
        $value      = (string)$param['value'];
        $checked    = ($value ? 'checked="checked"' : '');

        return '<input type="checkbox" id="'.$name.'" name="'.$name.'" value="1" '.$checked.' />';

    }

    private function renderListDB($param) {

        $inDB = cmsDatabase::getInstance();

        $html       = '';

        $name       = (string)$param['name'];
        $value      = (string)$param['value'];

        $src        = (string)$param['src'];
        $src_title  = isset($param['src_title']) ? (string)$param['src_title'] : 'title';
        $src_id     = isset($param['src_value']) ? (string)$param['src_value'] : 'id';

        $tree       = isset($param['tree']) ? (int)$param['tree'] : 0;
        $order_by   = ($tree ? 'NSLeft' : $src_title);
        $select     = "{$src_id} as value, {$src_title} as title";

        if ($tree) { $select .= ", NSLevel as level"; }
        
        $sql        = "SELECT {$select}
                       FROM {$src}
                       ORDER BY {$order_by}
                       LIMIT 100";

        $result = $inDB->query($sql);

        $html = '<select id="'.$name.'" name="'.$name.'" class="param-list">' . "\n";

        if ($inDB->num_rows($result)){
            while($option = $inDB->fetch_assoc($result)){
                $option['title'] = iconv('cp1251', 'utf-8', $option['title']);
                $html .= "\t" . '<option value="'.$option['value'].'">'.$option['title'].'</option>' . "\n";
            }
        }

        $html .= '</select>' . "\n";

        return $html;

    }


//============================================================================//
//============================================================================//


}

?>
