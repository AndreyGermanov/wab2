<?php
/*
 * Абстрактный класс, предназначенный для управления коллекцией элементов типа
 * $itemClass, которые хранятся в базе данных. От него наследуются классы управления
 * конкретными видами элементов.
 *
 * Тип элемента может передаваться самому этому классу в конструктор в виде параметра
 * или напрямую указываться в качестве значения свойства $itemClass.
 *
 * Класс содержит стандартные методы для управления коллекцией:
 *
 * connect() - подключается к базе данных, в которой расположена коллекция и возвращает
 * ссылку на нее. В данном абстрактном классе этот метод пуст. Конкретные классы сами
 * для себя устанавливают методы подключения
 *
 * Методы contains(), contains_id() и contains_title() проверяют, существует ли
 * элемент с указанным значением указанного поля в базе данных
 *
 * Метод load() загружает список элементов из базы и возвращает в качестве массива.
 * В качестве параметра передается идентификатор родительского элемента, хранящийся
 * в свойстве parent_id. Это позволяет строить иерархические структуры и получать
 * дочерние элементы указанного родителя.
 *
 * Метод remove() удаляет элемент с указанным идентификатором и все его дочерние
 * элементы, если таковые имеются и класс элемента настроен на каскадное удаление.
 */

/**
 * Description of ItemCollection
 *
 * @author andrey
 */
class ItemCollection extends WABEntity {

    public $itemClass;

    function connect() {
    }

    function construct($params) {
		if (count($params)>=2) { 
			$this->module_id = $params[0]."_".$params[1];
			$this->itemClass = @$params[2];
		}
		else {
			$this->module_id = "";
			$this->itemClass = @$params[0];			
		}
    }

    function contains($field,$value) {
       $this->connect();
       $q = Doctrine_Query::create()
            ->select("id")
            ->from($this->itemClass."Record")
            ->where("$field = ?",$value);
       $result = $q->fetchArray();
       if (count($result)>0)
            return true;
       else
           return false;
    }

    function contains_id($id) {
        return $this->contains("id",$id);
    }

    function contains_title($title) {
        return $this->contains("title",$title);
    }

    function load($parent_id="") {
        $this->connect();
        
        if ($parent_id!="") {
            $q = Doctrine_Query::create()
                 ->from($this->itemClass."Record")
                 ->where("parent_id = ?",$parent_id);
        } else {
            $q = Doctrine_Query::create()
                 ->from($this->itemClass."Record");
        }
       
        $result = $q->fetchArray();
        if (count($result)>0)
            return $result;
        else
            return 0;
    }

    function remove($id) {
        global $Objects;
        if ($this->contains_id($id)) {
			if ($this->module_id!="")
				$item = $Objects->get($this->itemClass."_".$this->module_id."_".$id);
			else
				$item = $Objects->get($this->itemClass."_".$id);
            $item->remove();
        }
        else
            reportError("Указанный шаблон не существует !","remove");
    }
}
?>
