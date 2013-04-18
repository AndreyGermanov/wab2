<?
class Chapter extends Doctrine_Record {
    public function setTableDefinition() {
        $this->setTableName("lvacompany_chapters");
        $this->hasColumn('id','integer');
        $this->hasColumn('name','string',255);
    }
}
?>