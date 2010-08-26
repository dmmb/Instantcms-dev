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

            if ($param['hint']) {
                $param['hint'] = iconv('utf-8', 'cp1251', (string)$param['hint']);
            }

            //получаем значение параметра
            $value = $this->getParamValue($param['name'], (isset($param['default']) ? $param['default'] : ''));
            //если это массив, склеиваем в строку
            if (is_array($value)){ $value = implode('|', $value); }

            $param['value'] = $value;

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

        $this->xml->module->title = iconv('utf-8', 'cp1251', $this->xml->module->title);

        foreach($this->params as $key=>$param){
            $this->params[$key]['title']    = iconv('utf-8', 'cp1251', $param['title']);
            $this->params[$key]['html']     = iconv('utf-8', 'cp1251', $param['html']);
        }

        ob_start();

            global $tpl_data;

            $tpl_data['module'] = $this->xml->module;
            $tpl_data['fields'] = $this->params;

            $inPage->includeTemplateFile('admin/autoform.php');

        $this->html = ob_get_clean();

        return $this->html;

    }


//============================================================================//
//============================================================================//

    private function getParamHTML($param) {

        $type = (string)$param['type'];

        switch ($type){

            case 'number':  return $this->renderNumber($param);
                            break;

            case 'string':  return $this->renderString($param);
                            break;

            case 'flag':    return $this->renderFlag($param);
                            break;

            case 'list':    return $this->renderList($param);
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

    private function renderString($param) {

        $name   = (string)$param['name'];
        $value  = (string)$param['value'];

        return '<input type="text" id="'.$name.'" name="'.$name.'" value="'.$value.'" class="param-string" /> ';

    }

    private function renderFlag($param) {

        $html       = '';

        $name       = (string)$param['name'];
        $value      = (string)$param['value'];

        $html = '<input type="checkbox" '.($value==1 ? 'checked="checked"' : '').' onclick="$(\'#'.$name.'\').val(1-$(\'#'.$name.'\').val())" />' . "\n" .
                '<input type="hidden" id="'.$name.'" name="'.$name.'" value="'.$value.'" />';

        return iconv('cp1251', 'utf-8', $html);

    }

    private function renderList($param){
        
        $html       = '';
        $name       = (string)$param['name'];
        $value      = (string)$param['value'];

        $html = '<select id="'.$name.'" name="'.$name.'" class="param-list">' . "\n";

            foreach($param->option as $option){
        
                $option['title'] = (string)$option['title'];
                $option['value'] = (string)$option['value'];

                $html .= "\t" . '<option value="'.$option['value'].'" '.($value == $option['value'] ? 'selected="selected"' : '').'>'.$option['title'].'</option>' . "\n";
                
            }

        $html .= '</select>' . "\n";

        return $html;
        
        
    }


    private function renderListDB($param) {

        $inDB = cmsDatabase::getInstance();

        $html       = '';

        $name       = (string)$param['name'];
        $value      = (string)$param['value'];

        $multiple   = isset($param['multiple']) ? 1 : 0;

        $src        = (string)$param['src'];
        $src_title  = isset($param['src_title']) ? (string)$param['src_title'] : 'title';
        $src_id     = isset($param['src_value']) ? (string)$param['src_value'] : 'id';
        $src_where  = isset($param['src_where']) ? (string)$param['src_where'] : '';

        $tree       = isset($param['tree']) ? (int)$param['tree'] : 0;
        $order_by   = ($tree ? 'NSLeft' : $src_title);
        $select     = "{$src_id} as value, {$src_title} as title";

        if ($tree) { $select .= ", NSLevel as level"; }

        $where      = ($src_where) ? "WHERE {$src_where}" : '';
        
        $sql        = "SELECT {$select}
                       FROM {$src}
                       {$where}
                       ORDER BY {$order_by}
                       LIMIT 100";

        $result = $inDB->query($sql);


        // ------------------------------------------------------------- //
        // ------------------------------------------------------------- //

        if (!$multiple){

            $html = '<select id="'.$name.'" name="'.$name.'" class="param-list">' . "\n";

            if (isset($param->option)){
                foreach($param->option as $option){

                    $option['title'] = (string)$option['title'];
                    $option['value'] = (string)$option['value'];

                    $html .= "\t" . '<option value="'.$option['value'].'" '.($value == $option['value'] ? 'selected="selected"' : '').'>'.$option['title'].'</option>' . "\n";

                }
            }

            if ($inDB->num_rows($result)){
                while($option = $inDB->fetch_assoc($result)){
                    $option['title'] = iconv('cp1251', 'utf-8', $option['title']);
                    if ($option['level'] >= 1){
                        $option['title'] = str_repeat('--', $option['level']-1) . ' ' . $option['title'];
                    }
                    $html .= "\t" . '<option value="'.$option['value'].'" '.($value == $option['value'] ? 'selected="selected"' : '').'>'.$option['title'].'</option>' . "\n";
                }
            }

            $html .= '</select>' . "\n";

        }

        // ------------------------------------------------------------- //
        // ------------------------------------------------------------- //

        if ($multiple){

            $values = explode('|', $value);

            $html = '<table cellpadding="0" cellspacing="0">' . "\n";

            if ($inDB->num_rows($result)){
                while($option = $inDB->fetch_assoc($result)){
                    $option['title'] = iconv('cp1251', 'utf-8', $option['title']);
                    if ($option['level'] >= 1){
                        $option['title'] = str_repeat('--', $option['level']-1) . ' ' . $option['title'];
                    }
                    
                    $html .= '<tr>' . "\n" .
                                "\t" . '<td><input type="checkbox" id="'.$name.'_'.$option['value'].'" name="'.$name.'['.$option['value'].']" value="'.$option['value'].'" '.(in_array($option['value'], $values) ? 'checked="checked"' : '').' />' . "\n" .
                                "\t" . '<td><label for="'.$name.'_'.$option['value'].'">'.$option['title'].'</label></td>' . "\n" .
                             '</tr>';
                }
            }

            $html .= '</table>' . "\n";

        }

        return $html;

    }


//============================================================================//
//============================================================================//


}

?>
