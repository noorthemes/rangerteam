<?php
/**
 * The control file of doc module of Ranzhi.
 *
 * @copyright   Copyright 2013-2014 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     LGPL
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     doc 
 * @version     $Id $
 * @link        http://www.ranzhi.org
 */
class doc extends control
{
    /**
     * Construct function, load user, tree, action auto.
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->loadModel('user');
        $this->loadModel('tree');
        $this->loadModel('action');
        $this->loadModel('product', 'crm');
        //$this->loadModel('project');
        $this->libs = $this->doc->getLibs();

        $libMenu = array();
        foreach($this->libs as $id => $libName)
        {
            $libID = isset($this->lang->doc->systemLibs[$id]) ? $id : 'lib' . $id;
            $libMenu[$libID] = "$libName|doc|browse|libID=$id";
        }
        $libMenu += (array)$this->lang->doc->menu;
        $this->lang->doc->menu = (object)$libMenu;

    }

    /**
     * Go to browse page.
     * 
     * @access public
     * @return void
     */
    public function index()
    {
        $this->locate(inlink('browse'));
    }

    /**
     * Browse docs.
     * 
     * @param  string|int $libID    product|project or the int id of custom library
     * @param  int    $moduleID 
     * @param  int    $productID 
     * @param  int    $projectID 
     * @param  string $orderBy 
     * @param  int    $recTotal 
     * @param  int    $recPerPage 
     * @param  int    $pageID 
     * @access public
     * @return void
     */
    public function browse($libID = 'product', $moduleID = 0, $productID = 0, $projectID = 0, $browseType = 'byModule', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {  
        /* Set browseType.*/ 
        $browseType = strtolower($browseType);
        $queryID    = ($browseType == 'bysearch') ? (int)$param : 0;

        /* Set menu, save session. */
        $this->session->set('docList',   $this->app->getURI(true));

        /* Set header and position. */
        $this->view->title      = $this->lang->doc->common . $this->lang->colon . $this->libs[$libID];
        $this->view->position[] = $this->libs[$libID];

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
 
        /* Get docs. */
        $modules = 0;
        $docs=array();
        if($browseType == "bymodule")
        {
            $type = is_numeric($libID) ? 'customerdoc' : $libID . 'doc';
            if($moduleID) $modules = $this->tree->getFamily($moduleID, $type, (int)$libID);
            $docs = $this->doc->getDocs($libID, $productID, $projectID, $modules, $orderBy, $pager);
        }

        /* Get the tree menu. */
        if($libID == 'product')
        {
            $moduleTree = $this->tree->getProductDocTreeMenu();
        }
        elseif($libID == 'project')
        {
            $moduleTree = $this->tree->getProjectDocTreeMenu();
        }
        else
        {
            $moduleTree = $this->tree->getTreeMenu($type = 'customdoc', $startModuleID = 0, array('treeModel', 'createDocLink'), $libID);
        }
       
        $this->view->libID         = $libID;
        $this->view->libName       = $this->libs[$libID];
        $this->view->moduleID      = $moduleID;
        $this->view->moduleTree    = $moduleTree;
        $this->view->parentModules = $this->tree->getFamily($moduleID);
        $this->view->docs          = $docs;
        $this->view->pager         = $pager;
        $this->view->users         = $this->loadModel('user')->getPairs('noletter');
        $this->view->orderBy       = $orderBy;
        $this->view->productID     = $productID;
        $this->view->projectID     = $projectID;
        $this->view->browseType    = $browseType;
        $this->view->param         = $param;

        $this->display();
    }

    /**
     * Create a library.
     * 
     * @access public
     * @return void
     */
    public function createLib()
    {
        if(!empty($_POST))
        {
            $libID = $this->doc->createLib();
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action')->create('docLib', $libID, 'Created');
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('browse', "libID=$libID")));
        }

        $this->view->title = $this->lang->doc->createLib;
        $this->display();
    }

    /**
     * Edit a library.
     * 
     * @param  int    $libID 
     * @access public
     * @return void
     */
    public function editLib($libID)
    {
        if(!empty($_POST))
        {
            $changes = $this->doc->updateLib($libID); 
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            if($changes)
            {
                $actionID = $this->loadModel('action')->create('docLib', $libID, 'edited');
                $this->action->logHistory($actionID, $changes);
            }
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('browse', "libID=$libID")));
        }
        
        $lib = $this->doc->getLibByID($libID);
        $this->view->libName = empty($lib) ? $libID : $lib->name;
        $this->view->libID   = $libID;
        $this->view->title   = $this->lang->doc->editLib;
        
        $this->display();
    }

    /**
     * Delete a library.
     * 
     * @param  int    $libID 
     * @access public
     * @return void
     */
    public function deleteLib($libID)
    {
        if($libID == 'product' or $libID == 'project') die();

        $this->doc->delete(TABLE_DOCLIB, $libID);
        if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

        $this->send(array('result' => 'success', 'locate' => inlink('browse')));
    }
    
    /**
     * Create a doc.
     * 
     * @param  int|string   $libID 
     * @param  int          $moduleID 
     * @param  int          $productID 
     * @param  int          $projectID 
     * @param  string       $from 
     * @access public
     * @return void
     */
    public function create($libID, $moduleID = 0, $productID = 0, $projectID = 0, $from = 'doc')
    {
        $projectID = (int)$projectID;
        if(!empty($_POST))
        {
            $docID = $this->doc->create();
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->action->create('doc', $docID, 'Created');

            if($from == 'product') $link = $this->createLink('product', 'doc', "productID={$this->post->product}");
            if($from == 'project') $link = $this->createLink('project', 'doc', "projectID={$this->post->project}");
            if($from == 'doc')
            {
                $productID = intval($this->post->product);
                $projectID = intval($this->post->project);
                $vars = "libID=$libID&moduleID={$this->post->module}&productID=$productID&projectID=$projectID";
                $link = $this->createLink('doc', 'browse', $vars);
            }
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $link));
        }

        /* Get the modules. */
        if($libID == 'product' or $libID == 'project')
        {
            $moduleOptionMenu = $this->tree->getOptionMenu($libID . 'doc', $startModuleID = 0);
        }
        else
        {
            $moduleOptionMenu = $this->tree->getOptionMenu('customdoc', $startModuleID = 0, false, $libID);
        }

        $this->view->title      = $this->libs[$libID] . $this->lang->colon . $this->lang->doc->create;
        $this->view->position[] = html::a($this->createLink('doc', 'browse', "libID=$libID"), $this->libs[$libID]);
        $this->view->position[] = $this->lang->doc->create;

        $this->view->libID            = $libID;
        $this->view->moduleOptionMenu = $moduleOptionMenu;
        $this->view->moduleID         = $moduleID;
        $this->view->productID        = $productID;
        $this->view->projectID        = $projectID;
        $this->view->products         = $projectID == 0 ? $this->product->getPairs() : $this->project->getProducts($projectID);
        $this->view->projects         = array();
        //$this->view->projects         = $this->project->getPairs('all');

        $this->display();
    }

    /**
     * Edit a doc.
     * 
     * @param  int    $docID 
     * @access public
     * @return void
     */
    public function edit($docID)
    {
        if(!empty($_POST))
        {
            $changes  = $this->doc->update($docID);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $files = $this->loadModel('file')->saveUpload('doc', $docID);
            if($this->post->comment != '' or !empty($changes) or !empty($files))
            {
                $action = !empty($changes) ? 'Edited' : 'Commented';
                $fileAction = '';
                if(!empty($files)) $fileAction = $this->lang->addFiles . join(',', $files) . "\n" ;
                $actionID = $this->action->create('doc', $docID, $action, $fileAction . $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('doc', 'view', "docID=$docID")));
        }

        /* Get doc and set menu. */
        $doc = $this->doc->getById($docID);
        $libID = $doc->lib;

        /* Get modules. */
        if($libID == 'product' or $libID == 'project')
        {
            $moduleOptionMenu = $this->tree->getOptionMenu(0, $libID . 'doc', $startModuleID = 0);
        }
        else
        {
            $moduleOptionMenu = $this->tree->getOptionMenu($libID, 'customdoc', $startModuleID = 0);
        }

        $this->view->title      = $this->libs[$libID] . $this->lang->colon . $this->lang->doc->edit;
        $this->view->position[] = html::a($this->createLink('doc', 'browse', "libID=$libID"), $this->libs[$libID]);
        $this->view->position[] = $this->lang->doc->edit;

        $this->view->doc              = $doc;
        $this->view->libID            = $libID;
        $this->view->moduleOptionMenu = $moduleOptionMenu;
        $this->display();
    }

    /**
     * View a doc.
     * 
     * @param  int    $docID 
     * @access public
     * @return void
     */
    public function view($docID)
    {
        /* Get doc. */
        $doc = $this->doc->getById($docID, true);
        if(!$doc) die(js::error($this->lang->notFound) . js::locate('back'));
        if($doc->project != 0 and !$this->project->checkPriv($this->project->getById($doc->project)))
        {
            echo(js::alert($this->lang->error->accessDenied));
            die(js::locate('back'));
        }

        /* Get library. */
        $lib = $doc->libName;
        if($doc->lib == 'product') $lib = $doc->productName;
        if($doc->lib == 'project') $lib = $doc->productName . $this->lang->arrow . $doc->projectName;

        $this->view->title      = "DOC #$doc->id $doc->title - " . $this->libs[$doc->lib];
        $this->view->position[] = html::a($this->createLink('doc', 'browse', "libID=$doc->lib"), $this->libs[$doc->lib]);
        $this->view->position[] = $this->lang->doc->view;

        $this->view->doc        = $doc;
        $this->view->lib        = $lib;
        $this->view->actions    = $this->loadModel('action')->getList('doc', $docID);
        $this->view->users      = $this->user->getPairs('noclosed,noletter');
        $this->view->keTableCSS = $this->doc->extractKETableCSS($doc->content);

        $this->display();
    }

    /**
     * Delete a doc.
     * 
     * @param  int    $docID 
     * @param  string $confirm  yes|no
     * @access public
     * @return void
     */
    public function delete($docID)
    {
        $this->doc->delete(TABLE_DOC, $docID);
        if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

        $this->send(array('result' => 'success', 'locate' => inlink('browse')));
    }
}
