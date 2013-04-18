<?php
class Users extends WABEntity {
    public $users;
    
    function construct() {        
        $this->load();
        $this->clientClass = "Users";
        $this->parentClientClasses = "Entity";        
    }
    
    function addUser($name) {
        if (!isset($this->users[$name]))
        {
            $user = new User($name);
            $user->collection = $this;
            $this->users[$name] = $user;
            return $user;
        }
        else
            return 0;
    }

    function removeUser($name) {
        if (isset($this->users[$name]))
        {
            $mboxes = $this->users[$name]->mailboxes;
            foreach ($mboxes as $value)
                    $this->users[$name]->removeMailbox($value->name, $value->domain);

            $this->users[$name]->collection = null;
            unset($this->users[$name]);
        }
    }

    function showUsers() {
        foreach ($this->users as $user)
        {
            echo $user->name." - ".$user->description."<br>";
        }
    }

    function load() {
        global $Objects;
        $app = $Objects->get($this->module_id);
        
        $f = file($app->remotePath."/etc/shadow");
  
        for ($counter=0;$counter<count($f);$counter++)
        {
            $user_string = explode(":",$f[$counter]);
            if ($user_string[1]!="!!" and $user_string[1]!="*")
            {
                $this->users[$user_string[0]] = new User($user_string[0]);
            }
        }
        
        $f = file($app->remotePath."/etc/passwd");
        for ($counter=0;$counter<count($f);$counter++)
        {
            $user_string = explode(":",$f[$counter]);
            if (isset($this->users[$user_string[0]]))
            {
                $this->users[$user_string[0]]->description = $user_string[4];
                $this->users[$user_string[0]]->collection = $this;
            }
        }
    }

    function getUser($name) {
        return $this->users[$name];
    }
}
?>