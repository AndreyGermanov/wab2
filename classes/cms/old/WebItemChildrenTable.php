<?php
/*
 * Класс управляет
 */
class WebItemChildrenTable extends Table {

    public $item_object;
    public $site_object;

    function construct($params) {
        $this->object_id = implode("_",$params);
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();

        $this->template = "templates/WebItemChildrenTable.html";
        $this->css = $app->skinPath."styles/Table.css";
        $this->handler = "scripts/handlers/WebItemChildrenTable.js";
        $this->page_number = 1;
        $this->row_count = 10;
        $p = implode("_",$params);
        $p =explode("_",$p);
        $this->module_id = @$p[0]."_".$p[1];
        $this->name = @$p[2];
        $this->site = @$p[3];
        $this->class = @$p[4];
        $this->item = @$p[5];
        $this->parent = @$p[6];
        $this->rows = 1;
        $this->cols = 4;
        $this->width= "100%";
        //$this->init();
    }

    function getId() {
        return "WebItemChildrenTable_".$this->module_id."_".$this->name."_".$this->site."_".$this->class."_".$this->item."_".$this->parent;
    }

    function init() {
        if ($this->display_fields=="")
            $this->display_fields = "title~Заголовок~main~string";
        //$this->page_number = $this->current_page;
        
        $fields_arr = explode("|",$this->display_fields);
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
       $this->item_object = $Objects->get($this->class."_".$this->module_id."_".$this->site."_".$this->item."_".$this->parent);
        if (!$this->item_object->loaded)
            $this->item_object->load();
        $this->openAs = $this->item_object->openAs;
        $this->item_object_id = $this->item_object->id;
        $this->site_object = $Objects->get("WebSite_".$this->module_id."_".$this->site);
        $this->site_object->load();
        $this->cell_properties = array();
        $this->row_properties = array();
        $this->row_properties[0] = "id:row0";
        $this->cell_properties[0][0] = "id:col0_0|class:header|innerHTML: |editable:false|unique:false|must_set:false|width:5%~";
        $this->cell_properties[0][0].= "id:col0_1|class:header|innerHTML: |editable:false|unique:false|must_set:false|width:5%~";
        foreach ($fields_arr as $field) {
            $field_parts = explode("~",$field);
            $this->cell_properties[0][0].= "id:col0_2|class:header|innerHTML:".$field_parts[1]."|editable:false|unique:false|must_set:false~";
        }
        $this->cell_properties[0][0].= "id:col0_3|class:header|innerHTML: |editable:false|unique:false|must_set:false|width:5%~";
        $this->cell_properties[0][0].= "id:col0_4|class:header|innerHTML: |editable:false|unique:false|must_set:false|width:5%~";
        $children_count = $this->item_object->getChildrenCount();
        $this->num_pages = ceil($children_count/$this->row_count);
        $this->id = $this->item_object->base_id;
        $childs = $this->item_object->getItems(0,0,$this->item_object->sort_order);
        $this->site_object->connect();
        $counter = -1;
//        if ($this->page_number<1)
//                $this->page_number=1;
        if ($childs!=FALSE) {
            foreach($childs as $value) {
                $counter++;
//                if (($this->page_number-1) < 0)
//                        $page_number = 0;
//                else
//                    $page_number = $this->page_number-1;
                if ($counter<($this->page_number-1)*$this->row_count or $counter>=(($this->page_number-1)*$this->row_count+$this->row_count))
                    continue;

            
                if (!$value->loaded)
                    $value->load();
                if ($value->record->getNode()->isLeaf()) {
                    $cell_class = "cell";
                    $expandable = "false";
                }
                else {
                    $cell_class = "expandable_cell";
                    $expandable = "true";
                }
                $cnt = count($this->row_properties);
                $this->row_properties[$cnt] = "id:row".$cnt;
;
                $id_col = "id:col".$cnt."_";
                $id_col1 = "col".$cnt."_";

                $par = $value->record->getNode()->getParent();
                $parent_path = array();                
                $parent_path[0] = $par->id;
                while (isset($par->getNode()->getParent()->id)) {
                    $par = $par->getNode()->getParent();
                    $parent_path[count($parent_path)] = $par->id;
                }
                $parent_path = implode("_",$parent_path);

                if ($value->is_public==true)
                    $checked = "checked";
                else
                    $checked = "";
                $cnt = count($this->cell_properties);
                $this->cell_properties[$cnt][0] = $id_col."0|class:".$cell_class."|innerHTML:<div align='center'><input type='checkbox' id='del_".$value->record->id."'></div>|editable:false|unique:false|must_set:false~";
                $this->cell_properties[$cnt][0].= $id_col."1|class:".$cell_class."|innerHTML:<div align='center'><img src='".$value->icon."' border='0' onclick=\"\$O('".$this->id."','').cellClick(event)\"></div>|editable:false|unique:false|must_set:false|";
                $this->cell_properties[$cnt][0].= "parent_id:".@$value->record->getNode()->getParent()->id."|parent_path:".$parent_path."|expandable:".$expandable."|expanded:false|loaded:false|item_id:".$value->id."~";
                foreach ($fields_arr as $field) {
                    $field_parts = explode("~",$field);
                    $fld = $field_parts[0];
                    if ($field_parts[2]=="main")
                        $text = $value->$fld;
                    if ($field_parts[2]=="data") {
                        if (!$value->loaded)
                            $value->load();
                         $text = $value->data[$field_parts[3]][$fld]->value;
                    }

                    if ($field_parts[3]=="date") {
                        $fld_arr = explode(" ",$text);
                        array_pop($fld_arr);
                        $text = implode(".",array_reverse(explode("-",$fld_arr[0])));
                    }
                    $this->cell_properties[$cnt][0].= $id_col."2|class:".$cell_class."|innerHTML:".$text."<input type='hidden' id='".$id_col1."5_id' class_name='".$value->class."' value='".$value->record->id."'><input type='hidden' id='".$id_col1."5_id' value='".@$value->record->getNode()->getParent()->id."' class_name='".@$value->record->getNode()->getParent()->class."'><input type='hidden' id='".$id_col1."5_id' value='".@$value->class."'>|editable:false|unique:false|must_set:false~";
                }
                $this->cell_properties[$cnt][0].= $id_col."4|class:".$cell_class."|innerHTML:<div align='center'><input type='checkbox' id='public_".$value->id."' ".$checked." onclick='\$O(\"".$this->getId()."\",\"\").publishItem(event,\"".$value->getId()."\")'></div>|editable:true|unique:false|must_set:false~";
                $this->cell_properties[$cnt][0].= $id_col."5|class:".$cell_class."|innerHTML:<div align='center'><a href='http://".@$this->site_object->domain_name."/?i=".$value->record->id."' target='_blank'><img border=0 border='0' src='".$app->skinPath."images/Table/preview.gif'></a>";
                $this->cell_properties[$cnt][0].= " </div>|editable:false|unique:false|must_set:false";
            }
        }
    }

    function getItems($parent_id) {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $row_properties = array();
        $cell_properties = array();
        $this->site_object = $Objects->get("WebSite_".$this->module_id."_".$this->site);
        $this->site_object->connect();
        
        if ($this->display_fields=="")
            $this->display_fields = "title~Заголовок~main~string";

        $fields_arr = explode("|",$this->display_fields);

        $item_object = $Objects->get($parent_id);//$this->class."_".$this->module_id."_".$this->site."_".$parent_id);
        if (!$item_object->loaded)
            $item_object->load();
        $counter = 0;
        $childs = $item_object->getItems(0,0,$item_object->sort_order);
        if ($childs!=FALSE) {
            $counter=-1;
            foreach($childs as $value) {
                $counter++;
//                if (($this->page_number-1) < 0)
//                        $page_number = 0;
//                else
//                    $page_number = $this->page_number-1;
//                if ($counter<($this->page_number-1)*$this->row_count or $counter>=(($this->page_number-1)*$this->row_count+$this->row_count))
//                    continue;
                if (!$value->loaded)
                    $value->load();
                $this->site_object->connect();
                if ($value->record->getNode()->isLeaf()) {
                    $cell_class = "cell";
                    $expandable = "false";
                }
                else {
                    $cell_class = "expandable_cell";
                    $expandable = "true";
                }
                $cnt = count($row_properties);
                $row_properties[$cnt] = "id:row".$cnt;

                if ($value->is_public==true)
                    $checked = "checked";
                else
                    $checked = "";
                $par = $value->record->getNode()->getParent();
                $parent_path = array();
                $parent_path[0] = $par->id;
                while (isset($par->getNode()->getParent()->id)) {
                    $par = $par->getNode()->getParent();
                    $parent_path[count($parent_path)] = $par->id;
                }
                $parent_path = implode("_",$parent_path);
                $id_col = "id:col".$counter."_".$parent_path."_";
                $id_col1 = "col".$counter."_".$parent_path."_";
                $cnt=count($cell_properties);
                $cell_properties[$cnt][0] = $id_col."0|class:".$cell_class."|innerHTML:<div align='center'><input type='checkbox' id='del_".$value->record->id."'></div>|editable:false|unique:false|must_set:false~";
                $cell_properties[$cnt][0].= $id_col."1|class:".$cell_class."|innerHTML:<div align='center'><img src='".$value->icon."' border='0' onclick=\"\$O('".$this->id."','').cellClick(event)\"></div>|editable:false|unique:false|must_set:false|";
                $cell_properties[$cnt][0].= "parent_id:".@$value->record->getNode()->getParent()->id."|parent_path:".$parent_path."|expandable:".$expandable."|expanded:false|loaded:false|item_id:".$value->id."~";
                foreach ($fields_arr as $field) {
                    $field_parts = explode("~",$field);
                    $fld = $field_parts[0];
                    if (@$field_parts[2]=="main")
                        $text = $value->$fld;
                    if (@$field_parts[2]=="data") {
                        if (!$value->loaded)
                            $value->load();
                         $text = $value->data[$field_parts[3]][$fld]->value;
                    }

                    if (@$field_parts[3]=="date") {
                        $fld_arr = explode(" ",$text);
                        array_pop($fld_arr);
                        $text = implode(".",array_reverse(explode("-",$fld_arr[0])));
                    }
                    $cell_properties[$cnt][0].= $id_col."2|class:".$cell_class."|innerHTML:".@$text."<input type='hidden' id='".$id_col1."5_id' class_name='".$value->class."' value='".$value->record->id."'><input type='hidden' id='".$id_col1."5_id' value='".@$value->record->getNode()->getParent()->id."' class_name='".@$value->record->getNode()->getParent()->class."'><input type='hidden' id='".$id_col1."5_id' value='".@$value->class."'>|editable:false|unique:false|must_set:false~";
                }
                $cell_properties[$cnt][0].= $id_col."4|class:".$cell_class."|innerHTML:<div align='center'><input type='checkbox' id='public_".$value->record->id."' ".$checked."></div>|editable:true|unique:false|must_set:false~";
                $cell_properties[$cnt][0].= $id_col."5|class:".$cell_class."|innerHTML:<div align='center'><img border='0' src='".$app->skinPath."images/Table/preview.gif'>";
                $cell_properties[$cnt][0].= " </div>|editable:false|unique:false|must_set:false";
            }
        }
        $row_properties = str_replace("'","\'",implode("~",$row_properties))."!!!".$this->page_number;;
        $res = array();
        for ($counter=0;$counter<count($cell_properties);$counter++) {
            $res[count($res)] = $counter."#".implode("~",$cell_properties[$counter]);
        }
        $cell_properties = str_replace("'","\'",implode("&",$res));
        return $row_properties."^".$cell_properties;
    }
}
?>