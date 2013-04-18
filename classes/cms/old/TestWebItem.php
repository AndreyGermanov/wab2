<?php
/**
 * Класс описывает элемент Web-сайта, который реализует модуль тестирования
 *
 */
class TestWebItem extends WebItem{

    function getResultRatio() {
        $result = 0;
        if (!$this->loaded)
            $this->load();
        if (isset($this->data["clob"]["answers_string"]))
            $this->answersString = $this->data["clob"]["answers_string"]->value;
        if ($this->answersString!="") {
            $question_pairs = explode("|",$this->answersString);
            foreach($question_pairs as $question) {
                $qa = explode("#",$question);
                $result = $result + $qa[1];
            }
        }
        return $result;
    }

    function getResultText() {
        global $Objects;
        if (!$this->loaded)
            $this->load();
        $ratio = $this->getResultRatio();
        if (isset($this->data["item"]["results"]) and $this->data["item"]["results"]->value!="" )
            $results = $Objects->get($this->data["item"]["results"]->value);
        else {
            if (isset($this->data["item"]["test"]) and $this->data["item"]["test"]->value!="") {
                $test = $Objects->get($this->data["item"]["test"]->value);
                if (isset($test->data["item"]["results"]) and $test->data["item"]["results"]->value!="") {
                    $results = $Objects->get($test->data["item"]["results"]->value);
                }
            }
        }
        if (isset($results) and $results!="") {
            $childs = $results->getItems();
            foreach($childs as $child) {
                if (!$child->loaded)
                    $child->load();
                if ($ratio >= $child->data["integer"]["result_from"]->value and $ratio <= $child->data["integer"]["result_to"]->value) {
                    return $child->data["clob"]["text"]->value;
                }
            }
        }

        return "";
    }

    function postResult($fields,$test) {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        if (!$this->loaded)
            $this->load();
        $people = $Objects->get($this->data["item"]["people"]->value);
        if (!$people->loaded)
            $people->load();
        if ($this->module_id!="") {        
			$new_result = $Objects->get($people->class."_".$this->module_id."_".$people->site->name."__".$people->base_id);
		}
		else {
			$new_result = $Objects->get($people->class."_".$people->site->name."__".$people->base_id);
		}
        $new_result->setDataField("answers_string",$this->answersString);
        $new_result->setDataField("test",$test);
        $fields = explode("|",$fields);
        $values = array();
        foreach($fields as $field) {
            $field_name_value = explode("#",$field);
            $new_result->setDataField(strip_tags($field_name_value[0]),strip_tags($field_name_value[1]));
            $values[count($values)] = strip_tags($field_name_value[1]);
        }
        $new_result->title = implode(",",$values);
        $new_result->icon = $app->skinPath."images/Tree/item.gif";
        $new_result->item_icon = $app->skinPath."images/Tree/item.gif";
        $new_result->save();
    }

    function getArgs() {
        $result = parent::getArgs();       
        $result["{answers_string}"] = $this->answersString;
        $result["{result_ratio}"] = $this->getResultRatio();
        $result["{result_text}"] = strip_tags($this->getResultText());

        global $Objects;
        if (isset($this->parent->class)) {
            if ($this->module_id!="")
                            $parent = $Objects->get($this->parent->class."_".$this->module_id."_".$this->site->name."_".$this->parent->id);
                    else
                            $parent = $Objects->get($this->parent->class."_".$this->site->name."_".$this->parent->id);
            if (!$parent->loaded)
                $parent->load();

            if (ceil(@$parent->data["integer"]["default"]->value)==ceil(@$this->data["integer"]["weight"]->value)) {
                $result["{checked}"] = "checked";
            } else {
                $result["{checked}"] = "";
            }
        }
        return $result;
    }

    function getAnswersTable() {
        global $Objects;
        $result = array();
        if (!$this->loaded)
            $this->load();
        if (isset($this->data["clob"]["answers_string"])) {
            $this->answersString = $this->data["clob"]["answers_string"]->value;
            $answers = explode("|",$this->answersString);
            foreach($answers as $answer_text) {
                $answer_array = explode("#",$answer_text);
                if (count($answer_array)>1) {
                    $question = $Objects->get($answer_array[0])->data["integer"]["sort_ord"]->value.". ".$Objects->get($answer_array[0])->title;
                    $answer = $answer_array[2];
                    $ratio = $answer_array[1];
                    $result[count($result)] = $question."#".$answer."#".$ratio;
                }
            }            
        }
        return implode("|",$result);
    }
}
?>
