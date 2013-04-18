<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AddressBookTable
 *
 * @author andrey
 */
class WebItemMetadataTable extends Table {

    public $item_object;

    function construct($params) {
        $this->object_id = implode("_",$params);
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;
        $this->template = "templates/Table.html";
        $this->css = $app->skinPath."styles/Table.css";
        $this->handler = "scripts/handlers/WebItemMetadataTable.js";
        $this->init($params);
    }

    function getId() {
        return "WebItemMetadataTable_".$this->module_id."_".$this->name."_".$this->site."_".$this->class."_".$this->item."_".$this->parent;
    }

    function init($params) {

        $p = implode("_",$params);
        $p =explode("_",$p);
        $this->module_id = @$p[0]."_".$p[1];
        $this->name = @$p[2];
        $this->site = @$p[3];
        $this->class = @$p[4];
        $this->item = @$p[5];
        $this->parent = @$p[6];
        $this->rows = 1;
        $this->cols = 5;
        $this->width= "100%";
        $this->height = "100%";

        global $Objects;
        $this->item_object = $Objects->get($this->class."_".$this->module_id."_".$this->site."_".$this->item."_".$this->parent);
            $this->item_object->load();

        $this->metadata_types = implode(",",array_values($this->item_object->metadata_types));
        $counter = 1;
        $this->row_properties[0] = "id:row0";
        $this->cell_properties[0][0] = "id:col0_0|class:header|innerHTML:Системное имя|editable:false|unique:false|must_set:false|width:20%~";
        $this->cell_properties[0][0].= "id:col0_1|class:header|innerHTML:Название|editable:false|unique:false|must_set:false|width:20%~";
        $this->cell_properties[0][0].= "id:col0_2|class:header|innerHTML:Тип|editable:false|unique:false|must_set:false|width:20%~";
        $this->cell_properties[0][0].= "id:col0_3|class:header|innerHTML:Группа|editable:false|unique:false|must_set:false|width:20%~";
        $this->cell_properties[0][0].= "id:col0_4|class:header|innerHTML:По умолчанию|editable:false|unique:false|must_set:false|width:20%~";
        $this->cell_properties[0][0].= "id:col0_5|class:header|innerHTML:Параметры|editable:false|unique:false|must_set:false|width:20%";
        if (count($this->item_object->metadata)>0) {
            $counter=1;
            foreach($this->item_object->metadata as $value) {
                $this->row_properties[$counter] = "id:row".$counter;
                $id_col = "id:col".$counter."_";
                $id_col1 = "col".$counter."_";

                if ($value->is_group=="true")
                    $checked = "checked";
                else
                    $checked = "";
                $this->cell_properties[$counter][0] = $id_col."0|class:cell|innerHTML:".$value->name."|editable:true|unique:true|must_set:true~";
                $this->cell_properties[$counter][0].= $id_col."1|class:cell|innerHTML:".$value->title."|editable:true|unique:true|must_set:true~";
                $this->cell_properties[$counter][0].= $id_col."2|class:cell|collection:".$this->metadata_types."|innerHTML:".$this->item_object->metadata_types[$value->type]."|editable:true|unique:false|must_set:true~";
                $this->cell_properties[$counter][0].= $id_col."3|class:cell|innerHTML:<div align='center'><input type='checkbox' ".$checked." id='col1_3_groupbox' onclick='\$O(\"".$this->id."\",\"\").onGroupboxClick(event)'></div>|editable:false|unique:false|must_set:false~";
                $this->cell_properties[$counter][0].= $id_col."4|class:cell|innerHTML:".$value->default_value."|editable:true|unique:false|must_set:false~";
                $this->cell_properties[$counter][0].= $id_col."5|class:cell|innerHTML:<div align='center'>";
                $this->cell_properties[$counter][0].= "<input type='button' value='...' id='col".$counter."_5_settingsButton' onclick='\$O(\"".$this->id."\",\"\").onSettingsButtonClick(event)'>";
                $this->cell_properties[$counter][0].= "<input type='hidden' id='".$id_col1."5_mustunique' value='".$value->must_unique."'>";
                $this->cell_properties[$counter][0].= "<input type='hidden' id='".$id_col1."5_mustset' value='".$value->must_set."'>";
                $this->cell_properties[$counter][0].= "<input type='hidden' id='".$id_col1."5_checkregexp' value='".$value->check_regexp."'>";
                $this->cell_properties[$counter][0].= "<input type='hidden' id='".$id_col1."5_min' value='".$value->min."'>";
                $this->cell_properties[$counter][0].= "<input type='hidden' id='".$id_col1."5_max' value='".$value->max."'>";
                $this->cell_properties[$counter][0].= "<input type='hidden' id='".$id_col1."5_accuracy' value='".$value->accuracy."'>";
                $this->cell_properties[$counter][0].= "<input type='hidden' id='".$id_col1."5_wherequery' value='".$value->where_query."'>";
                $this->cell_properties[$counter][0].= "<input type='hidden' id='".$id_col1."5_id' value='".$value->id."'>";
                $this->cell_properties[$counter][0].= " </div> |editable:false|unique:false|must_set:false";
                $counter++;
            }
        } else {
            $this->row_properties[1] = "id:row1";
            $this->cell_properties[1][0] = "id:col1_0|class:cell|innerHTML:|editable:true|unique:true|must_set:true~";
            $this->cell_properties[1][0].= "id:col1_1|class:cell|innerHTML:|editable:true|unique:true|must_set:true~";
            $this->cell_properties[1][0].= "id:col1_2|class:cell|collection:".$this->metadata_types."|innerHTML:Число|editable:true|unique:false|must_set:true~";
            $this->cell_properties[1][0].= "id:col1_3|class:cell|innerHTML:<div align='center'><input type='checkbox' id='col1_3_groupbox' onclick='\$O(\"".$this->id."\",\"\").onGroupboxClick(event)'></div>|editable:false|unique:false|must_set:false~";
            $this->cell_properties[1][0].= "id:col1_4|class:cell|innerHTML:|editable:true|unique:false|must_set:false~";
            $this->cell_properties[1][0].= "id:col1_5|class:cell|innerHTML:<div align='center'>";
            $this->cell_properties[1][0].= "<input type='button' value='...' id='col1_5_settingsButton' onclick='\$O(\"".$this->id."\",\"\").onSettingsButtonClick(event)'>";
            $this->cell_properties[1][0].= "<input type='hidden' id='col1_5_mustunique' value='0'>";
            $this->cell_properties[1][0].= "<input type='hidden' id='col1_5_mustset' value='false'>";
            $this->cell_properties[1][0].= "<input type='hidden' id='col1_5_checkregexp' value=''>";
            $this->cell_properties[1][0].= "<input type='hidden' id='col1_5_min' value='0'>";
            $this->cell_properties[1][0].= "<input type='hidden' id='col1_5_max' value='0'>";
            $this->cell_properties[1][0].= "<input type='hidden' id='col1_5_accuracy' value='2'>";
            $this->cell_properties[1][0].= "<input type='hidden' id='col1_5_wherequery' value=''>";
            $this->cell_properties[1][0].= "<input type='hidden' id='col1_5_id' value=''>";
            $this->cell_properties[1][0].= " </div> |editable:false|unique:false|must_set:false";
        }
    }
}
?>