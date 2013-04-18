<?
class ItemTemplateRecord extends Doctrine_Record {

    public function setTableDefinition() {
        parent::setTableDefinition();
        $this->setTableName("item_templates");
        $this->hasColumn("title",'string',100);
        $this->hasColumn("template_file",'string',100);
        $this->hasColumn("css_file",'string',100);
        $this->hasColumn("handler_file","string",100);
        $this->hasColumn("class_file","string",100);
        $this->hasColumn("parent_id","integer");
    }

    public function setUp() {
        $this->hasOne("ItemTemplateRecord as parent",array(
            'local' => "parent_id",
            'foreign' => "id",
            'onDelete' => "CASCADE"
            )
        );
        $this->hasMany("ItemTemplateRecord as children",array(
            'local' => "id",
            'foreign' => "parent_id",
            'cascade' => array("delete")
            )
        );
    }
}
?>
