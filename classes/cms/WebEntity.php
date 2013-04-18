<?php
/**
 * Класс, реализующий элемент Web-сайта
 *
 * @author andrey
 */
class WebEntity extends WABEntity {
	
    function construct($params) {
        @$this->siteId = @$params[count($params)-2];
        
        array_splice($params,-2,1);
        parent::construct($params);
        $this->persistedFields = $this->explodePersistedFields(array());
        global $Objects;
        if ($this->module_id=="")
            $this->module_id = @$_SERVER["MODULE_ID"];
        if ($this->module_Id=="")
        	$this->module_id = "WebServerApplication_Web";
        if ($this->module_id!="")
            $this->adapter = $Objects->get("SiteDataAdapter_".$this->module_id."_".$this->siteId."_".$this->name);
        else
            $this->adapter = $Objects->get("SiteDataAdapter_".$this->siteId."_".$this->name);
        $this->adapterId = $this->adapter->getId();
        $this->siteName = $this->adapter->site->title;
        $this->cacheDeps = ""; $this->childCacheDeps = "";        
        $arr = $this->getPersistedArray();
        
        $this->presentationField = "title";
        $this->childPersistedFields = $this->persistedFields;
        $this->defaultPersistedFields = $this->persistedFields;
        $this->defaultChildPersistedFields = $this->childPersistedFields;
//        $this->parent = -1;
        $this->handler = "scripts/handlers/cms/WebEntity.js";
        $this->fieldList = 'title Наименование~sortOrder Порядковый номер~isPublic Опубликовано booleans';
        $this->childFieldList = $this->fieldList;
        $this->sortFields = "sortOrder ASC integers";
        $this->childSortFields = $this->sortFields;
        $this->defaultClassName = "";
        $this->date = time()."000";
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->author = $app->User;        
        $this->class = get_class($this);
        $this->childClass = $this->class;
        $this->css_file = "";                               
        
        $this->tabs_string = "text|Текст|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "fieldValues|Дополнительно|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "seo|Продвижение|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "tags|Поля|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "persistedFieldsTable|Структура|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "system|Настройки|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "rights|Права|".$this->skinPath."images/spacer.gif;";
        //        $this->tabs_string.= "descr|Описание|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "cacheDepsTable|Кэширование|".$this->skinPath."images/spacer.gif";
//        $this->tabs_string.= ";submitForm|Форма ввода|".$this->skinPath."images/spacer.gif";
        if ($this->name!="") {
            $this->tabs_string.= ";childrenTable|Элементы|".$this->skinPath."images/spacer.gif";
            $this->active_tab = "childrenTable";
        } else 
            $this->active_tab = "text";
        
        if ($this->module_id!="") {
            $this->system_tabset_id = "WebItemTabset_".$this->module_id."_".str_replace("_","",$this->getId())."SystemTabset";
            $this->systemTabsetName = $this->system_tabset_id;
        }        
        else {
            $this->system_tabset_id = "WebItemTabset_".str_replace("_","",$this->getId())."SystemTabset";
            $this->systemTabsetName = str_replace("_","",$this->getId())."SystemTabset";
        }

        $this->system_tabs_string = "main|Текущий элемент|".$this->skinPath."images/spacer.gif;";
        $this->system_tabs_string.= "childs|Подчиненные элементы|".$this->skinPath."images/spacer.gif";
        $this->system_active_tab = "main";

        if ($this->module_id!="") {
            $this->fields_tabset_id = "WebItemTabset_".$this->module_id."_".str_replace("_","",$this->getId())."FieldsTabset";
            $this->fieldsTabsetName = $this->fields_tabset_id;
        }        
        else {
            $this->fields_tabset_id = "WebItemTabset_".str_replace("_","",$this->getId())."FieldsTabset";
            $this->fieldsTabsetName = str_replace("_","",$this->getId())."FieldsTabset";
        }

        $this->fields_tabs_string = "fieldsMain|Текущий элемент|".$this->skinPath."images/spacer.gif;";
        $this->fields_tabs_string.= "fieldsChilds|Подчиненные элементы|".$this->skinPath."images/spacer.gif";
        $this->fields_active_tab = "fieldsMain";

        if ($this->module_id!="") {
            $this->cachedeps_tabset_id = "WebItemTabset_".$this->module_id."_".str_replace("_","",$this->getId())."CacheDepsTabset";
            $this->cachedepsTabsetName = $this->cachedeps_tabset_id;
        }        
        else {
            $this->cachedeps_tabset_id = "WebItemTabset_".str_replace("_","",$this->getId())."CacheDepsTabset";
            $this->cachedepsTabsetName = str_replace("_","",$this->getId())."CacheDepsTabset";
        }

        if ($this->module_id!="") {
        	$this->rightsTabset_id = "WebItemTabset_".$this->module_id."_".str_replace("_","",$this->getId())."RightsTabset";
        }
        else {
        	$this->rightsTabset_id = "WebItemTabset_".str_replace("_","",$this->getId())."RightsTabset";
        }
        
        $this->cachedeps_tabs_string = "cacheDepsMain|Текущий элемент|".$this->skinPath."images/spacer.gif;";
        $this->cachedeps_tabs_string.= "cacheDepsChilds|Подчиненные элементы|".$this->skinPath."images/spacer.gif";
        $this->cachedeps_active_tab = "cacheDepsMain";

        $this->rights_tabs_string = "usersRights|Пользователи|".$this->skinPath."images/spacer.gif;";
        $this->rights_tabs_string.= "rolesRights|Роли|".$this->skinPath."images/spacer.gif";
        $this->rights_active_tab = "usersRights";
        
        $this->title = "";
        $this->Text = "";
        $this->sysname = "";
        $this->isPublic = 0;
        $this->isPage = 1;
        $this->createdTime = "";
        $this->modifyTimeAuthors = "";
        $this->htmlTitle = "";
        $this->htmlKeywords = "";
        $this->htmlDescription = "";
        $this->htmlEncoding = "UTF-8";
        $this->htmlMeta = "";
        $this->htmlHeader = "";
        $this->entityImage = "";
        $this->groupEntityImage = "";
        $this->childEntityImage = "";
        $this->childGroupEntityImage = "";
        $this->comment = "";
        
        $this->userTemplate = "";
        $this->adminTemplate = "";
        $this->childUserTemplate = "";
        $this->childAdminTemplate = "";
        $this->asAdminTemplate = false;
        
        $this->template = "templates/cms/WebEntity.html";
        $this->clientClass = "WebEntity";
        $this->parentClientClasses = "Entity";        
        $this->icon = $this->skinPath."images/Tree/item.gif";
        
    }

    function getArgs() {
        if (!$this->loaded)
            $this->load();
        if ($this->hasChildren) {
            if ($this->groupEntityImage!="")
                $this->icon = $this->groupEntityImage;
            else
                $this->icon = $this->skinPath."images/Tree/folder.gif";            
        }
        else {
            if ($this->entityImage!="")
                $this->icon = $this->entityImage;
            else
                $this->icon = $this->skinPath."images/Tree/item.gif";
        }
        if ($this->asAdminTemplate)
        	$this->asAdminTemplateStr = "true";
        else
        	$this->asAdminTemplateStr = "false";
        //$parr = explode("_",$this->parent->getId());
        //$parr_class = array_shift($parr);
        //$parr_id = array_pop($parr);
        //$result["{parent}"] = $parr_class."_".$this->module_id."_".$this->siteId."_".$parr_id;
        $result["{module_id}"] = $this->adapter->site->module_id;
        $this->classname = get_class($this);
        
        $this->tagsTableId = "TagsTable_".$this->module_id."_".$this->name;
        if ($this->tagsTableEntity=="")
        	$this->tagsTableCode = '$object->entityObject="'.$this->getId().'";$object->parent_object_id="'.$this->getId().'";';
        else        
        	$this->tagsTableCode = '$object->entityObject="'.$this->tagsTableEntity.'";$object->parent_object_id="'.$this->getId().'";';

        $this->usersRightsTableId = "RightsTable_".$this->module_id."_".$this->name."users";
       	$this->usersRightsTableCode = '$object->entityObject="'.$this->getId().'";$object->parent_object_id="'.$this->getId().'";';

       	$this->rolesRightsTableId = "RightsTable_".$this->module_id."_".$this->name."roles";
       	$this->rolesRightsTableCode = '$object->entityObject="'.$this->getId().'";$object->parent_object_id="'.$this->getId().'";$object->rightsType="roles";';

       	$this->rightsTabsetCode = '$object->module_id="'.$this->module_id.'";
       	$object->item="'.$this->getId().'";
       	$object->window_id="'.$this->window_id.'";
       	$object->tabs_string="'.$this->rights_tabs_string.'";
       	$object->active_tab="'.$this->rights_active_tab.'";';
       		
       	$this->rightsTabsetCode = cleanText($this->rightsTabsetCode);
       	
        $result = parent::getArgs();
        if ($this->parent!="" and is_object($this->parent))
            $result["{parentName}"] = $this->parent->name;
        else
            $result["{parentName}"] = $this->name;
        return $result;
    }
    
    function setTemplates() {
        if (!$this->loaded)
            $this->load();
        global $Objects;     
        if ($this->asAdminTemplate) {
            if ($this->adminTemplate!="" and $this->adminTemplate!="-1" and $this->adminTemplate!="") {
                $tpl = $Objects->get($this->adminTemplate);
            }
        } else if ($this->userTemplate!="" and $this->userTemplate!="-1") {
            $tpl = $Objects->get($this->userTemplate);
        }
        if ($this->userTemplate!="" and $this->userTemplate!="-1") {
            $user_tpl = $Objects->get($this->userTemplate);
            if (!$user_tpl->loaded)
                $user_tpl->load();
            $this->css_file = $user_tpl->css_file;
        }
        if (isset($tpl)) {
            if (!$tpl->loaded)
                $tpl->load();
            if (!$this->reset_template) {
                $this->template = $tpl->template_file;
            }
            if (!$this->reset_css)
                $this->css = $tpl->css_file;
            if (!$this->reset_handler)
                $this->handler = $tpl->handler_file;     
            $class_arr = explode("/",$tpl->class_file);            
            $class = array_pop($class_arr);
            $class = str_replace(".js","",$class);
            $this->template_class = $class;
        }            
    }
    
    function show($instance="",$out=true) {              
        $this->setTemplates();
        return parent::show($instance,$out);
    }
    
    function getId() {     
        if ($this->name=="")
            $this->name = $this->entityId;
        if ($this->module_id!="")
            return get_class($this)."_".$this->module_id."_".$this->siteId."_".$this->name;
        else
           return get_class($this)."_".$this->siteId."_".$this->name;
    }
    
    function checkData() {
        global $Objects;
        if ($this->old_name=="" or ($this->old_parent != $this->parent and $this->parent!="")) {
            if ($this->parent!="-1" and $this->parent!="" and !is_object($this->parent)) {
                $condition = "@parent.@name=".$Objects->get($this->parent)->name;
            }
            else
                $condition = "@parent IS NOT EXISTS";
            if (method_exists($this->adapter,'getMaxValue'))
                $this->sortOrder = $this->adapter->getMaxValue('fields',"sortOrder"," ".$condition." AND @siteId=".$this->siteId);
        }
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->title = trim(strip_tags($this->title));
        if ($this->title=="") {
            $this->reportError("Укажите заголовок !","save");
            return false;
        }            
        if ($this->htmlTitle == "")
            $this->htmlTitle = $this->title;
        if ($this->htmlKeywords == "")
            $this->htmlKeywords = $this->title;
        if ($this->htmlDescription == "")
            $this->htmlDescription = $this->title;
        if ($this->createdTime=="")
            $this->createdTime = time();
        $this->modifyTimeAuthors = date("d.m.Y H:i:s")." - ".$app->User."\n".$this->modifyTimeAuthors;
        if ($this->userTemplate=="-1")
            $this->userTemplate = "";
        if ($this->adminTemplate=="-1")
            $this->adminTemplate = "";
        if ($this->childUserTemplate=="-1")
            $this->childUserTemplate = "";
        if ($this->childAdminTemplate=="-1")
            $this->childAdminTemplate = "";
        return true;
    } 
            
    function getCachedFiles($cached_arr,$dir="") {
        if ($dir=="")
            $dir = "/var/WAB2/cache/".$this->siteId;
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file!="." and $file!="..") {                        
                        if (is_dir($dir."/".$file)) {
                            $this->getCachedFiles($cached_arr,$dir."/".$file);
                        } else {
                            if (preg_match("/^i=".$this->name.".*/", $file))
                                $cached_arr[] = $file;
                            if (preg_match("/^i=".$this->sysname.".*/", $file))
                                $cached_arr[] = $file; 
                            if (!$this->adapter->site->loaded)
                                $this->adapter->site->load();
                            if ($this->adapter->site->mainpage == $this->getId()) {
                                if (preg_match("/^mainpage$/", $file))
                                    $cached_arr[] = $file; 
                            }
                        }
                    }
                }
                closedir($dh);
            }
        }
        return $cached_arr;
    }
    
    function getInnerTemplateFiles($cached_arr,$dir="") {
        if ($dir=="")
            $dir = "/var/WAB2/cache/".$this->siteId."/".$this->getId();
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file!="." and $file!="..") {                        
                        if (is_dir($dir."/".$file)) {
                            $this->getInnerTemplateFiles($cached_arr,$dir."/".$file);
                        } else {
                            $parts = explode(".",$file);
                            $ext = array_pop($parts);
                            if (!isset($cached_arr[$ext]))
                                $cached_arr[$ext] = array();                            
                            $cached_arr[$ext][] = $file; 
                        }
                    }
                }
                closedir($dh);
            }
        }
        return $cached_arr;        
    }
    
    function afterSave($out=true) {
        global $Objects;
        $to_save = false;
        if ($this->name=="") {
            $this->name = $this->entityId;
            $to_save = true;
        }
        if ($this->sysname=="") {
            $this->sysname = "Item".$this->name;
            $to_save = true;
        }
        if ($to_save)
            $this->adapter->save();     
        if (@$_POST["action"]=="submit")
            return 0;
        $cached_arr = array();
        if ($this->isPublic) {
            if ($this->static) {
                $cached_arr = $this->getCachedFiles(array());
                foreach($cached_arr as $link) { 
                    unlink ("/var/WAB2/cache/".$this->siteId."/".$link);
                }
            }
            $cacheDepsArray = explode("~",$this->cacheDeps);
            $objs_array = array();
            $objs_array[] = $this;
            unlink ("/var/WAB2/cache/".$this->siteId."/mainpage");
            foreach($cacheDepsArray as $item) {
                if ($item=="")
                    continue;
                $res = $Objects->query("*WebEntity*_".$this->module_id."_".$this->siteId,"@name=".$item." AND @siteId=".$this->siteId,$this->adapter);
                if (count($res)>0) {
                    $obj = $res[0];
                    if (is_object($obj)) {
                        if (!$obj->loaded)
                            $obj->load();
                        if ($obj->loaded) {
                            $obj->module_id = $this->module_id;
                            $objs_array[] = $obj;
                            $cached_arr = array_merge($cached_arr,$obj->getCachedFiles(array()));
                            foreach($cached_arr as $link) { 
                                @unlink("/var/WAB2/cache/".$this->siteId."/".$link);
                            }                            
                        }
                    }
                }
                $res = null;
            }
            if ($out)
                echo implode(";",$cached_arr)."\n";
            foreach ($objs_array as $obj) {
                $inner_template_arr = $obj->getInnerTemplateFiles(array());
                foreach($inner_template_arr as $link) 
                    foreach ($link as $ext) {
                            unlink ("/var/WAB2/cache/".$this->siteId."/".$obj->getId()."/".$ext);
                    }   
                    continue;
                while (count($inner_template_arr)>0) {                    
                    if (isset($inner_template_arr["html"]) and count($inner_template_arr["html"])>0) {
                        $html = str_replace("~","/",array_shift($inner_template_arr["html"]));
                    } else
                        unset($inner_template_arr["html"]);
                    if (isset($inner_template_arr["css"]) and count($inner_template_arr["css"])>0) {
                        $css = str_replace("~","/",array_shift($inner_template_arr["css"]));
                    } else
                        unset($inner_template_arr["css"]);
                    if (isset($inner_template_arr["js"]) and count($inner_template_arr["js"])>0) {
                        $js = str_replace("~","/",array_shift($inner_template_arr["js"]));
                    } else                
                        unset($inner_template_arr["js"]);

                    if (isset($html)) {
                        $obj->template = $html;
                        $obj->reset_template = true;
                    }
                    if (isset($js)) {
                        $obj->handler = $js;
                        $obj->reset_handler = true;
                    }
                    if (isset($css)) {
                        $obj->css = $css;
                        $obj->reset_css = true;
                    }
                    $obj->inner = true;
                    
                    $inner_result = $obj->show("",false);
                    file_put_contents("/var/WAB2/cache/".$this->siteId."/".$obj->getId()."/".str_replace("/","~",$obj->template), $inner_result["html"]);
                    file_put_contents("/var/WAB2/cache/".$this->siteId."/".$obj->getId()."/".str_replace("/","~",$obj->css), $inner_result["css"]);
                    file_put_contents("/var/WAB2/cache/".$this->siteId."/".$obj->getId()."/".str_replace("/","~",$obj->handler), $inner_result["javascript"]);                                                        
                }
            }
        }
    }
    
    function load($em="",$dbEntity="",$force=false) {
        $module_id = $this->module_id;
        //$arr = explode($this->module_id);
        //if (count($arr)<3)
        	//$this->module_id = $module_id."_".$this->siteId;
        
        parent::load($em,$dbEntity,$force);       
        	 
        $this->module_id = $module_id;
        if ($this->name=="")
            $this->name = $this->entityId;
        foreach ($this->fields as $key=>$value) {
        	if (is_object($value) and strpos(get_class($value),"WebEntity")!==false) {
        		$this->fields[$key]->siteId = $this->siteId;
        		$this->fields[$key]->name = array_pop(explode("_",$this->fields[$key]->name));
        		$this->fields[$key]->adapter->siteId = $this->siteId;
        		$this->fields[$key]->adapter->name = $this->fields[$key]->name;
        		$this->fields[$key]->adapter->init();
        	}
        }        
    }    
    
    function afterInit($parent_id) {
    	parent::afterInit($parent_id);
        global $Objects;
        if (!is_object($this->parent) and $this->parent!="") {
        	$this->parent = $Objects->get($this->parent);
        	if (!$this->parent->loaded)
        		$this->parent->load();
        }
        $this->asAdminTemplate = true;
        if ($this->parent->childUserTemplate!="")
            $this->userTemplate = $this->parent->childUserTemplate;
        if ($this->parent->childAdminTemplate!="")
            $this->adminTemplate = $this->parent->childAdminTemplate;
        if ($this->parent->childSortFields!="")
            $this->sortFields = $this->parent->childSortFields;
        if ($this->parent->childFieldList!="")
            $this->fieldList = $this->parent->childFieldList;
        if ($this->parent->childEntityImage!="")
            $this->entityImage = $this->parent->childEntityImage;
        if ($this->parent->childGroupEntityImage!="")
            $this->groupEntityImage = $this->parent->childGroupEntityImage;
        if ($this->parent->childPersistedFields != "") {
            $this->persistedFields = $this->parent->childPersistedFields;
            //echo $this->persistedFields;
            $this->childPersistedFields = $this->persistedFields;
        }
        if ($this->parent->childCacheDeps != "") {
            $this->cacheDeps = $this->parent->childCacheDeps;
            $this->childCacheDeps = $this->parent->childCacheDeps;
        }        
    }    
}
?>