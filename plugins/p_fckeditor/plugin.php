<?php

class p_fckeditor extends cmsPlugin {

// ==================================================================== //

    public function __construct(){
        
        parent::__construct();

        $this->info['plugin']           = 'p_fckeditor';
        $this->info['title']            = 'FCKEditor';
        $this->info['description']      = 'Визуальный редактор';
        $this->info['author']           = 'F. C. Knabben';
        $this->info['version']          = '2.63';
        $this->info['type']             = 'wysiwyg';

        $this->events[]                 = 'INSERT_WYSIWYG';

    }

// ==================================================================== //

    public function install(){

        return parent::install();

    }

// ==================================================================== //

    public function upgrade(){

        return parent::upgrade();

    }

// ==================================================================== //

    public function execute($event, $item){

        parent::execute();

        $this->inCore->includeFile('wysiwyg/fckeditor.php');
        
        $oFCKeditor             = new FCKeditor($item['name']) ;
        $oFCKeditor->BasePath   = '/wysiwyg/';
        $oFCKeditor->Height     = $item['height'];
        $oFCKeditor->Width      = $item['width'];
        $oFCKeditor->ToolbarSet = 'Admin';

        $oFCKeditor->Value      = $item['text'];

        ob_start();

        $oFCKeditor->Create();

        return ob_get_clean();
        
    }


// ==================================================================== //

}

?>
