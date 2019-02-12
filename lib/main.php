<?php

/** 
 * A set of PHP classes to interact with publication databases and services 
 * such us CrossRef, Scopus, Web of Science, Pubmed
 */ 


/******************************************************************************
 * Abstract classes
 *****************************************************************************/
abstract class MetaDataAbstract{
    protected $dom;
    
    public function __construct() {
        
    }
    
    public function set_params($params){
        $this->params = $params;
    }

    public function getDom(){
        return $this->dom;
    }
    
    public function getXML(){
        return $this->getDom()->saveXML();
    }
    
    public function getJSON(){
        return json_encode(simplexml_load_string($this->getXML()), JSON_PRETTY_PRINT);
        
    }

    public function getArray(){
        return json_decode($this->getJSON(),TRUE);
        
    }
    
}
