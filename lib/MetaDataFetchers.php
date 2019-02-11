<?php

/** 
 * A set of PHP classes to interact with publication databases and services 
 * such us CrossRef, Scopus, Web of Science, Pubmed
 */ 

require 'main.php';

/******************************************************************************
 * Classes for data fetching
 *****************************************************************************/

class MetaDataFetcher extends MetaDataAbstract{
    protected $uri = '';  // To be overridden with defaults in subclasses
    protected $params=[]; // To be overridden with defaults in subclasses
                          // Parameters to construct URI must be in $params['uri_params']
    
    protected $data;
    
    public function __construct() {
        $this->data = new DOMDocument();
    }

    public function set_source_uri($uri){
        $this->uri = $uri;
    }
    
    public function buildUrl(){
        $this->url = $this->uri.'?';
        foreach ($this->params['uri_params'] as $key => $val){
            $this->url.= $key.'='.$val.'&';
        }
        
        return $this;
    }
    
    public function getUrl(){
        return $this->url;
    }
    
    public function fetch(){
        $this->buildUrl();
        $cSession = curl_init();
        
        curl_setopt($cSession,CURLOPT_URL,$this->url);
        curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($cSession,CURLOPT_HEADER, false);
        
        $this->data->loadXML(curl_exec($cSession));
        
        curl_close($cSession);
        
        return $this;
    }
    
    public function getData(){
        return $this->data;
    }
    
    public function getXML(){
        return $this->data->saveXML();
    }
    
    public function getJSON(){
        return json_encode(simplexml_load_string($this->data->saveXML()), JSON_PRETTY_PRINT);
        
    }
    
}

class PubmedFetcher extends MetaDataFetcher{
    protected $uri = "https://www.ncbi.nlm.nih.gov/pmc/utils/idconv/v1.0/";
    protected $params=array('uri_params' => array('tool' => '',
                                                  'email' => '',
                                                  'format' => 'xml'));
    
    public function setTool($tool){
        $this->params['uri_params']['tool'] = $tool;
    }

    public function setEmail($email){
        $this->params['uri_params']['email'] = $email;
    }
    
    public function setDoi($doi){
        $this->params['uri_params']['ids'] = $doi;
    }
    
}

class CrossrefFetcher extends MetaDataFetcher{
    protected $uri = "http://www.crossref.org/openurl";
    protected $params=array('uri_params' => array('pid' => '',
                                                  'noredirect' => 'true',
                                                  'format' => 'unixref'));
            
    public function setPid($pid){ // User's PID
        $this->params['uri_params']['pid'] = $pid;
    }
    
    public function setDoi($doi){
        $this->params['uri_params']['id'] = 'doi%3A'.$doi;
    }
    
}

