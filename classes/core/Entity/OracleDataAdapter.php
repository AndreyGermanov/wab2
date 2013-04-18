<?php
class OracleDataAdapter extends CacheDataAdapter {
    public $classes,$field_types,$type_classes,$type_arrays;
    function construct($params) {

        if (count($params)>2) {
            $this->module_id = $params[0]."_".$params[1];
            $this->name = $params[2];
        }
        else
            $this->name = $params[0];

        $this->driver = "oci8";
        $this->user = "SYSTEM";
        $this->password = "111111";
        $this->dbname = "XE";
        $this->host = "localhost";
        $this->port = 1521;
        $this->classes = array();
        $this->classes[count($this->classes)] = "DbEntity";
        $this->classes[count($this->classes)] = "StringField";
        $this->classes[count($this->classes)] = "IntegerField";
        $this->classes[count($this->classes)] = "BooleanField";
        $this->classes[count($this->classes)] = "EntityField";
        $this->classes[count($this->classes)] = "ArrayField";
        $this->classes[count($this->classes)] = "DecimalField";
        $this->classes[count($this->classes)] = "TextField";

        $this->field_types = array();
        $this->field_types[count($this->field_types)] = "strings";
        $this->field_types[count($this->field_types)] = "integers";
        $this->field_types[count($this->field_types)] = "booleans";
        $this->field_types[count($this->field_types)] = "entities";
        $this->field_types[count($this->field_types)] = "arrays";
        $this->field_types[count($this->field_types)] = "decimals";
        $this->field_types[count($this->field_types)] = "texts";

        $this->type_classes = array();
        $this->type_classes["strings"] = "StringField";
        $this->type_classes["integers"] = "IntegerField";
        $this->type_classes["booleans"] = "BooleanField";
        $this->type_classes["entities"] = "EntityField";
        $this->type_classes["arrays"] = "ArrayField";
        $this->type_classes["decimals"] = "DecimalField";
        $this->type_classes["texts"] = "TextField";

        $this->type_arrays["string"] = "strings";
        $this->type_arrays["integer"] = "integers";
        $this->type_arrays["boolean"] = "booleans";
        $this->type_arrays["decimal"] = "decimals";
        $this->type_arrays["text"] = "texts";
        $this->type_arrays["entity"] = "entities";
        $this->type_arrays["array"] = "arrays";

        $this->entities_path = "classes/dbClasses/Entities";
        $this->proxies_path = "classes/dbClasses/Proxies";
        $this->loaded = false;

        $this->query = "SELECT e,s,i,b,et,d,t,a,ast,ain,ab,ad,at,ent{sortFields} FROM DBEntity e LEFT JOIN e.strings s WITH s.arrayItem IS NULL INDEX BY s.name LEFT JOIN e.integers i WITH i.arrayItem IS NULL INDEX BY i.name".
                       " LEFT JOIN e.booleans b WITH b.arrayItem IS NULL INDEX BY b.name LEFT JOIN e.entities et WITH et.arrayItem IS NULL INDEX BY et.name LEFT JOIN e.decimals d WITH d.arrayItem IS NULL INDEX BY d.name".
                       " LEFT JOIN e.texts t WITH t.arrayItem IS NULL INDEX BY t.name LEFT JOIN e.arrays a INDEX BY a.name LEFT JOIN a.strings ast WITH ast.arrayItem IS NOT NULL INDEX BY ast.name".
                       " LEFT JOIN a.integers ain WITH ain.arrayItem IS NOT NULL INDEX BY ain.name LEFT JOIN a.booleans ab WITH ab.arrayItem IS NOT NULL INDEX BY ab.name LEFT JOIN a.decimals ad WITH ad.arrayItem IS NOT NULL INDEX BY ad.name".
                       " LEFT JOIN a.texts at WITH at.arrayItem IS NOT NULL INDEX BY at.name LEFT JOIN a.entities ent WITH ent.arrayItem IS NOT NULL INDEX BY ent.name {sortJoins} WHERE ";

        $this->sortFields="";
        $this->sortJoins="";
        $this->sortDirections = "";
        $this->clientClass = "OracleDataAdapter";
        $this->parentClientClasses = "CacheDataAdapter~DataAdapter~Entity";        
    }
}
 ?>