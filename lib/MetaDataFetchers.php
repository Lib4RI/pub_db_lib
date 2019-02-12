<?php

/** 
 * A set of PHP classes to interact with publication databases and services 
 * such us CrossRef, Scopus, Web of Science, Pubmed
 */ 

require_once 'main.php';

/******************************************************************************
 * Classes for data fetching
 *****************************************************************************/

class MetaDataFetcher extends MetaDataAbstract{
    protected $uri = '';  // To be overridden with defaults in subclasses
    protected $params = ''; // To be overridden with defaults in subclasses
                          // Parameters to construct URI must be in $params['uri_params']
    
    
    public function __construct() {
        $this->dom = new DOMDocument();
    }

    public function set_source_uri($uri){
        $this->uri = $uri;
    }
    
    public function buildUrl(){
//         $this->url = $this->uri.'?';
//         foreach ($this->params['uri_params'] as $key => $val){
//             $this->url.= $key.'='.$val.'&';
//         }
        $this->url = $this->uri.'?'.http_build_query($this->params['uri_params']);
        return $this;
    }
    
    public function getUrl(){
        return $this->url;
    }
    
    public function buildHeaders(){
        if (empty($this->params['headers_params'])){
            $this->headers = false;
        }
        else{
            $this->headers = array();
            foreach ($this->params['headers_params'] as $key => $value){
                array_push($this->headers, "$key: $value");
            }
        }
        
        return $this;
    }

    public function getHeaders(){
        return $this->headers;
    }
    
    public function fetch(){
        $this->buildUrl();
        $this->buildHeaders();
        $cSession = curl_init();
        
        curl_setopt($cSession,CURLOPT_URL,$this->url);
        curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($cSession,CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($cSession,CURLOPT_HEADER, false); 
        
        $this->dom->loadXML(curl_exec($cSession));
        
        curl_close($cSession);

        return $this;
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

class ScopusSearchFetcher extends  MetaDataFetcher{
    protected $uri = "https://api.elsevier.com/content/search/scopus";
    protected $params=array('uri_params' => array('query' => ''),
                            'headers_params' => array('Accept' => 'application/xml')
    );

    public function setDoi($doi){
        if (!empty($this->params['uri_params']['query'])){
            $this->params['uri_params']['query'] .= '&';
        }
        $this->params['uri_params']['query'] .= "DOI($doi)";
    }

    public function setKey($key){
        $this->params['headers_params']['X-ELS-APIKey'] = $key;
    }
}

