<?php
/**
 * Класс управляет справочной системой, состоящей из HTML-страниц.
 * HTML-страницы хранятся в каталоге, определяемом в переменной doc_path.
 *
 * В этом же каталоге хранится файл оглавления, который называется toc. Он состоит
 * из строк следующего формата:
 *
 * level|id|parent_id|Наименование|Ссылка
 *
 * level - уровень вложенности
 * id - идентификатор раздела
 * parent_id - идентификатор родительского раздела
 * Наименование - наименование раздела в оглавлении
 * Ссылка - HTML-ссылка на файл с разделом
 * @author andrey
 */
class HTMLBook extends WABEntity {

    public $toc_by_id,$toc_by_name,$toc_by_link,$toc_by_parent,$toc_by_level;
    function construct($params) {
        $this->module_id = $params[0]."_".$params[1];
        $this->name = $params[2];
        $this->current_id = $params[3];

        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();

        $this->doc_path = "documents/".$this->name;

        $this->template = "templates/interface/HTMLBook.html";
        $this->css = $app->skinPath."styles/Mailbox.css";
        $this->handler = "scripts/handlers/interface/HTMLBook.js";
        $this->icon = $app->skinPath."images/Tree/docs.png";
        $this->skinPath = $app->skinPath;
        $this->width = "800";
        $this->height = "480";
        $this->overrided = "width,height";
        $this->docroot = $_SERVER["DOCUMENT_ROOT"];
        $this->loaded = false;
        $this->clientClass = "HTMLBook";
        $this->parentClientClasses = "Entity";        
        $this->classTitle = "Справочная система";
        $this->classListTitle = "Справочная система";
    }

    function load() {

        $strings = file($this->doc_path."/toc");
        $this->toc_by_id = array();
        $this->toc_by_link = array();
        $this->toc_by_name = array();
        $this->toc_by_parent = array();
        $this->toc_by_level = array();
        $this->toc_js = "obj.toc_by_id = new Array; obj.toc_by_name = new Array; obj.toc_by_link = new Array; obj.toc_by_parent = new Array; obj.toc_by_level = new Array;\n";
        foreach($strings as $line) {
            $arr = explode("|",$line);
            if ($this->current_id==0 or $this->current_id=="")
                $this->current_id = $arr[1];
            $arr[3] = str_replace('"',"`",$arr[3]);
            $this->toc_js .= "obj.toc_by_id['".$arr[1]."']=new Array;\n";
            $this->toc_by_id[$arr[1]]["level"] = $arr[0]; $this->toc_js .= "obj.toc_by_id['".$arr[1]."']['level']=".$arr[0].";\n";
            $this->toc_by_id[$arr[1]]["id"] = $arr[1]; $this->toc_js .= "obj.toc_by_id['".$arr[1]."']['id']='".$arr[1]."';\n";
            $this->toc_by_id[$arr[1]]["parent_id"] = $arr[2]; $this->toc_js .= "obj.toc_by_id['".$arr[1]."']['parent_id']='".$arr[2]."';\n";
            $this->toc_by_id[$arr[1]]["name"] = $arr[3];$this->toc_js .= "obj.toc_by_id['".$arr[1]."']['name']='".$arr[3]."';\n";
            $this->toc_by_id[$arr[1]]["link"] = trim($arr[4]);$this->toc_js .= "obj.toc_by_id['".$arr[1]."']['link']='".trim($arr[4])."';\n";

            $this->toc_js .= "obj.toc_by_name['".$arr[3]."']=new Array;\n";
            $this->toc_by_name[$arr[3]]["level"] = $arr[0]; $this->toc_js .= "obj.toc_by_name['".$arr[3]."']['level']='".$arr[0]."';\n";
            $this->toc_by_name[$arr[3]]["id"] = $arr[1]; $this->toc_js .= "obj.toc_by_name['".$arr[3]."']['id']='".$arr[1]."';\n";
            $this->toc_by_name[$arr[3]]["parent_id"] = $arr[2];$this->toc_js .= "obj.toc_by_name['".$arr[3]."']['parent_id']='".$arr[2]."';\n";
            $this->toc_by_name[$arr[3]]["name"] = $arr[3]; $this->toc_js .= "obj.toc_by_name['".$arr[3]."']['name']='".$arr[3]."';\n";
            $this->toc_by_name[$arr[3]]["link"] = trim($arr[4]); $this->toc_js .= "obj.toc_by_name['".$arr[3]."']['link']='".trim($arr[4])."';\n";

            $this->toc_js .= "obj.toc_by_link['".trim($arr[4])."']=new Array;\n";
            $this->toc_by_link[trim($arr[4])]["level"] = $arr[0];$this->toc_js .= "obj.toc_by_link['".trim($arr[4])."']['level']='".$arr[0]."';\n";
            $this->toc_by_link[trim($arr[4])]["id"] = $arr[1];$this->toc_js .= "obj.toc_by_link['".trim($arr[4])."']['id']='".$arr[1]."';\n";
            $this->toc_by_link[trim($arr[4])]["parent_id"] = $arr[2];$this->toc_js .= "obj.toc_by_link['".trim($arr[4])."']['parent_id']='".$arr[2]."';\n";
            $this->toc_by_link[trim($arr[4])]["name"] = $arr[3];$this->toc_js .= "obj.toc_by_link['".trim($arr[4])."']['name']='".$arr[3]."';\n";
            $this->toc_by_link[trim($arr[4])]["link"] = trim($arr[4]);$this->toc_js .= "obj.toc_by_link['".trim($arr[4])."']['link']='".trim($arr[4])."';\n";

            if (!isset($this->toc_by_parent[$arr[2]])) {
                $this->toc_by_parent[$arr[2]] = array();
                $this->toc_js .= "obj.toc_by_parent['".$arr[2]."'] = new Array;\n";
            }
            $this->toc_by_parent[$arr[2]][count($this->toc_by_parent[$arr[2]])] = $this->toc_by_id[$arr[1]];
            $this->toc_js .= "obj.toc_by_parent['".$arr[2]."'][obj.toc_by_parent.length]=obj.toc_by_id['".$arr[1]."'];\n";
            if (!isset($this->toc_by_level[$arr[0]])) {
                $this->toc_by_level[$arr[0]] = array();
                $this->toc_js .= "obj.toc_by_level['".$arr[0]."'] = new Array;\n";
            }
            $this->toc_by_level[$arr[0]][count($this->toc_by_level[$arr[0]])] = $this->toc_by_id[$arr[1]];
            $this->toc_js .= "obj.toc_by_level['".$arr[0]."'][obj.toc_by_level.length]=obj.toc_by_id['".$arr[1]."'];\n";

        }
        $this->loaded = true;
    }
    
    function make_spaces($count) {
        $res = "";
        for ($c=0;$c<$count*4;$c++) {
            $res = $res."-";
        }
        return $res;
    }

    function getArgs() {
        if (!$this->loaded)
            $this->load();
        $result = parent::getArgs();
        $ids = array_keys($this->toc_by_id);
        $names = array();
        foreach($this->toc_by_id as $item) {
            $names[count($names)] = $this->make_spaces($item["level"]).$item["name"];
        }
        $result["{toc_list}"] = implode(",",$ids)."|".implode(",",$names);
        $result["{current_id}"] = $this->current_id;
        $result["{frame_src}"] = $this->doc_path."/".$this->toc_by_id[$this->current_id]["link"];
        return $result;
    }

    function getId() {
        return "HTMLBook_".$this->module_id."_".$this->name."_".$this->current_id;
    }

    function getPresentation() {
        if (!$this->loaded)
            $this->load();
        return $this->toc_by_id[$this->current_id]["name"];
    }
}
?>