<?php

/** 
 * A set of PHP classes to interact with publication databases and services 
 * such us CrossRef, Scopus, Web of Science, Pubmed
 */ 


/******************************************************************************
 * Abstract classes
 *****************************************************************************/
abstract class MetaDataAbstract{
    protected $params=[]; //To be overridden with defaults in subclasses

    public function __construct() {
        
    }
    
    public function set_params($params){
        $this->params = $params;
    }

    public function getData(){
        return $this->doc;
    }
    
    public function getXML(){
        return $this->doc->saveXML();
    }
    
    public function getJSON(){
        return json_encode(simplexml_load_string($this->doc->saveXML()), JSON_PRETTY_PRINT);
        
    }
    
}
