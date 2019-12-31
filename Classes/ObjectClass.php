<?php 
class ObjectClass {
    protected static $objects;

    private function __construct() {        
    }

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self;   
        }
 
        return self::$_instance;
    }
  
    private function __clone() {
    }

    private function __wakeup() {
    }   
     
    public static function setObjects($name,$value){  
        self::$objects[$name]=$value;
    }
    
    public static function getObject($name){
        if(isset(self::$objects[$name])){
            return self::$objects[$name];
        }else{
            return false;
        }
    }
    
    public static function outObjects(){
        echo '<pre>';
        print_r(self::$objects);
        echo '</pre>';
    }
}