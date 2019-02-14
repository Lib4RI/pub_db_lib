<?php

/** 
 * A set of PHP classes to interact with publication databases and services 
 * such us CrossRef, Scopus, Web of Science, Pubmed
 */ 

require_once 'MetaDataAbstract.php';

/******************************************************************************
 * Classes for data fetching
 *****************************************************************************/

class MetaDataFetcher extends MetaDataAbstract{
    protected $uri = '';  // To be overridden with defaults in subclasses
    protected $params = ''; // To be overridden with defaults in subclasses
                          // Parameters to construct URI must be in $params['uri_params']
    protected $error_queries = array(array('query' => '', 'check' => '', 'code' => '', 'message' => ''));
    private $error = array('status' => FALSE, 'code' => '', 'message' => '');
    
    
    public function __construct() {
        $this->dom = new DOMDocument();
        $this->dom->formatOutput = true;
    }

    public function set_source_uri($uri){
        $this->uri = $uri;
    }
    
    public function buildUrl(){
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
    
    private function setCurlOpt(){
        curl_setopt($this->cSession,CURLOPT_URL,$this->url);
        curl_setopt($this->cSession,CURLOPT_RETURNTRANSFER,true);
        if (!empty($this->getHeaders())){
            curl_setopt($this->cSession,CURLOPT_HTTPHEADER, $this->headers);
        }
        curl_setopt($this->cSession,CURLOPT_HEADER, false);
    }
    
    public function fetch(){
        $this->buildUrl(); //echo $this->getUrl(); exit;
        $this->buildHeaders();
        $this->cSession = curl_init();
        $this->setCurlOpt();
        
        $this->dom->loadXML(curl_exec($this->cSession));
        
        curl_close($this->cSession);
        
        $this->checkError();
        
        return $this;
    }
    
    protected function checkError(){
        $xpath = new DOMXPath($this->dom);
        foreach ($this->error_queries as $key => $error_query){
            $entries = $xpath->query($error_query['query']);
            foreach ($entries as $entry) {
                if ($entry->nodeValue == $error_query['check']){
                    $this->setErrosStatus(TRUE, $error_query['code'], $error_query['message']);
                    return;
                }
            }
        }
    }
    
    protected function setErrosStatus($status, $code, $message){
        $this->error['status'] = $status;
        $this->error['code'] = $code;
        $this->error['message'] = $message;
    }
    
    public function getErrorStatus(){
        return $this->error;
    }
}

class PubmedFetcher extends MetaDataFetcher{
    protected $uri = "https://www.ncbi.nlm.nih.gov/pmc/utils/idconv/v1.0/";
    protected $params=array('uri_params' => array('tool' => '',
                                                  'email' => '',
                                                  'format' => 'xml'));
    protected $error_queries = array(array('query' => '//errmsg', 'check' => 'invalid article id', 'code' => '', 'message' => 'Invalid ID'),
    );
    
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
        $this->params['uri_params']['id'] = 'doi:'.$doi;
    }
    
}

class ScopusSearchFetcher extends  MetaDataFetcher{
    protected $uri = "https://api.elsevier.com/content/search/scopus";
    protected $params=array('uri_params' => array('query' => ''),
                            'headers_params' => array('Accept' => 'application/xml')
     );
    protected $error_queries = array(array('query' => '//atom:error', 'check' => 'Result set was empty', 'code' => '', 'message' => 'Result set was empty'),
                               array('query' => '//statusText', 'check' => 'Invalid API Key', 'code' => 'Authentication error', 'message' => 'Invalid API Key'),
    );
    
    public function setDoi($doi){
        if (!empty($this->params['uri_params']['query'])){
            $this->params['uri_params']['query'] .= '&';
        }
        $this->params['uri_params']['query'] .= "DOI($doi)";
    }

    public function setKey($key){
        $this->params['headers_params']['X-ELS-APIKey'] = $key;
//        $this->params['uri_params']['apiKey'] = $key;
    }
    
}

class WosRedirectFetcher extends  MetaDataFetcher{
    protected $uri = "http://ws.isiknowledge.com/cps/openurl/service";
    protected $params=array('uri_params' => array('url_ver' => 'Z39.88-2004'));
        
    public function setDoi($doi){
        $this->params['uri_params']['rft_id'] = "info:doi/$doi";
    }
    
    // Need to override the follwing two functions as the fetching strategy does not match with the main implementation.
    private function setCurlOpt(){
        curl_setopt($this->cSession, CURLOPT_URL,$this->url);
        curl_setopt($this->cSession, CURLOPT_HEADER, 1);
        curl_setopt($this->cSession, CURLOPT_NOBODY, 1);
        curl_setopt($this->cSession, CURLOPT_RETURNTRANSFER, 1);
    }
    
    public function fetch(){
        $this->buildUrl(); 
        
        $this->cSession = curl_init();
        $this->setCurlOpt();
        
        $red_url = curl_exec($this->cSession);
        curl_close($this->cSession);

        $element = $this->dom->createElement('wos_redirect_url');
        // Check if there's a Location: header (redirect)
        if (preg_match('/^Location: (.+)$/im', $red_url, $matches)){
            $url_array = parse_url(trim($matches[1]));
            parse_str($url_array['query'],$url_array['query']); 
            $this->array2dom($this->dom, $url_array, $element);
            if(empty($url_array['query'])){
                $this->setErrosStatus(TRUE, '', 'No results');
            }
        }
        else{
            $this->setErrosStatus(TRUE, '', 'No results');
        }
            
        $this->dom->appendChild($element);
        
            
        return $this;
        
    }
    
    private function array2dom($dom, $array, $node){
        
        foreach ($array as $key => $val){
            $element = $dom->createElement($key, (is_array($val) ? null : htmlspecialchars($val)));
            $node->appendChild($element);
            
            if(is_array($val)){
                $this->array2dom($dom, $val, $element);
            }
        }
    }
}