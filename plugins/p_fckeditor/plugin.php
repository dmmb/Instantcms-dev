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

        $inUser = cmsUser::getInstance();

        $this->inCore->includeFile('plugins/p_fckeditor/fckeditor/fckeditor.php');

        $oFCKeditor             = new FCKeditor($item['name']) ;
        $oFCKeditor->BasePath   = '/plugins/p_fckeditor/fckeditor/';
        $oFCKeditor->Height     = $item['height'];
        $oFCKeditor->Width      = $item['width'];

        $oFCKeditor->ToolbarSet = ($inUser->is_admin ? 'Admin' : 'Basic');

        $oFCKeditor->Value      = $item['text'];

        if (!$inUser->is_admin){
            $oFCKeditor->Config['ImageBrowser'] = false;
            $oFCKeditor->Config['LinkUpload']   = false;
            $oFCKeditor->Config['LinkBrowser']  = false;
        }

        ob_start();

        $oFCKeditor->Create();

        return ob_get_clean();
        
    }


// ==================================================================== //

}

?>
