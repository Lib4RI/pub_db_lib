<?php

/** 
 * A set of PHP classes to interact with publication databases and services 
 * such us CrossRef, Scopus, Web of Science, Pubmed
 */ 

require 'main.php';

/******************************************************************************
 * Classes for data manipulation
 *****************************************************************************/

class MetaDataFetcher extends MetaDataAbstract{
    protected $uri = ''; //To be overridden with defaults in subclasses
    protected $params=[]; //To be overridden with defaults in subclasses
    
    public function __construct() {
        
    }

    public function set_source_uri($uri){
        $this->uri = $uri;
    }
    
    
    public function fetch(){
        
    }
    
}
