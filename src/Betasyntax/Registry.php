<?php

namespace Betasyntax;

Class Registry 
{
    /*
    **
    *** The $objects variable will contain all the classes/variables/data
    **
    */
    var $objects = array();

    /*
    **
    *** The __constructor method will run when the class is first created
    *** Please not that in the constructor it should take 0 args if posible
    **
    */
    public function __construct(){
    }
    
    /*
    **
    *** The __set magic method will be used to add new objects to the $objects
    *** (http://www.php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members)
    **
    */
    
    public function __set($index,$value){
        $this->objects[$index] = $value;
    }
    
    /*
    **
    *** The magic method __get will be used when were trying to pull objects from the storage variable
    **
    */
    
    public function __get($index){
        return $this->objects[$index];
    }
    
    /*
    **
    *** The magic methos sleep and wake are used to compress the data when there not being used, this helps save system
    *** Resources if your Registry gets on the larger side.
    **
    */
    
    function __sleep(){ /*serialize on sleep*/
        $this->objects = serialize($this->objects);
    }
    function __wake(){ /*un serialize on wake*/
        $this->$objects = unserialize($this->objects);
    }
}

