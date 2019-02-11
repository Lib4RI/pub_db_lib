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
    
}
