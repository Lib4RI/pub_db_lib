<?php

/** 
 * A set of PHP classes to interact with publication databases and services 
 * such us CrossRef, Scopus, Web of Science, Pubmed
 */ 

require_once 'MetaDataAbstract.php';

/******************************************************************************
 * Classes for data fetching
 *****************************************************************************/

/**
 * Generic class to fetch data from metadta providers web services
 */
class MetaDataFetcher extends MetaDataAbstract{
    
    /**
     * Service's base URL (To be overridden with defaults in subclasses or set with setUri())
     */
    protected $uri = '';
    
    /**
     * URL parameters specific to the service (To be overridden with defaults in subclasses or set with setParams())
     * Parameters to construct URI must be in $params['uri_params']
     */
    protected $params = '';
                          
    /**
     * Error check parameters (To be overridden with defaults in subclasses or set with setErrorsParams())
     *
     * query: Xpath query to extract error string from the response
     * check: string to check for the error
     * code: error code to write in the error status array
     * message: error message to write in the error status array
     */
    protected $error_queries = array(array('query' => '', 
                                           'check' => '', 
                                           'code' => '', 
                                           'message' => ''));
    
    /**
     * Error status array
     * 
     * status: TRUE or FALSE
     * code: string contaoning the error code
     * message: string containing the error message
     */
    private $error = array('status' => FALSE, 
                           'code' => '', 
                           'message' => '');
    
    /**
     * Constructor 
     */
    public function __construct() {
        $this->dom = new DOMDocument();
        $this->dom->formatOutput = true;
    }

    /**
     * Set the service's base URL.
     *
     * @param array $uri
     *   A string containing the service's base URL
     *
     * @return MetaDataFetcher
     *   The instatiated class.
     */
    public function setUri($uri){
        $this->uri = $uri;
        return $this;
    }
  
    /**
     * Build the full URL to fetch metadata
     *
     * @return MetaDataFetcher
     *   The instatiated class.
     */
    public function buildUrl(){
        $this->url = $this->uri.'?'.http_build_query($this->params['uri_params']);
        return $this;
    }

    /**
     * Return the full URL
     *
     * @return string
     *   A string containing the full URL.
     */
    public function getUrl(){
        return $this->url;
    }
    
    /**
     * Build the HTTP header to submit the request
     *
     * @return MetaDataFetcher
     *   The instatiated class.
     */
    public function buildHeaders(){
        if (empty($this->params['headers_params'])){
            $this->headers = FALSE;
        }
        else{
            $this->headers = array();
            foreach ($this->params['headers_params'] as $key => $value){
                array_push($this->headers, "$key: $value");
            }
        }
        
        return $this;
    }
    
    /**
     * Return the HTTP headers
     *
     * @return string
     *   An array containing the HTTP headers or FALSE
     */
    public function getHeaders(){
        return $this->headers;
    }

    /**
     * Set options for the curl library
     *
     */
    private function setCurlOpt(){
        curl_setopt($this->cSession,CURLOPT_URL,$this->url);
        curl_setopt($this->cSession,CURLOPT_RETURNTRANSFER,TRUE);
        if (!empty($this->getHeaders())){
            curl_setopt($this->cSession,CURLOPT_HTTPHEADER, $this->headers);
        }
        curl_setopt($this->cSession,CURLOPT_HEADER, FALSE);
    }

    /**
     * Fetch data from the selected web service
     *
     * @return MetaDataFetcher
     *   The instatiated class.
     */
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

    /**
     * Check the service's response for errors
     */
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
 
    /**
     * Convenient function to set the error status 
     */    
    protected function setErrosStatus($status, $code, $message){
        $this->error['status'] = $status;
        $this->error['code'] = $code;
        $this->error['message'] = $message;
    }

    /**
     * Return the current error status
     *
     * @return array
     *   The array containing the error status, code and message.
     */
    public function getErrorStatus(){
        return $this->error;
    }
 
    /**
     * Set error check parameters.
     *
     * @param array $error_queries
     *   An associative array containing the error check parameters.
     *
     * @return MetaDataAbstract
     *   The instatiated class.
     */
    public function setErrorParams($error_queries){
        $this->error_queries = $error_queries;
        return $this;
    }
}

/**
 * Class to fetch Pubmed identifiers
 */
class PubmedFetcher extends MetaDataFetcher{
    /**
     * Service's base URL
     */
    protected $uri = "https://www.ncbi.nlm.nih.gov/pmc/utils/idconv/v1.0/";
    
    /**
     * URL parameters specific to the service
     */
    protected $params=array('uri_params' => array('tool' => '',
                                                  'email' => '',
                                                  'format' => 'xml'));
    
    /**
     * Error check parameters
     * 
     * query: Xpath query to extract error string from the response
     * check: string to check for the error
     * code: error code to write in the error status array
     * message: error message to write in the error status array
     */
    protected $error_queries = array(array('query' => '//errmsg', 
                                           'check' => 'invalid article id', 
                                           'code' => '', 
                                           'message' => 'Invalid ID'),
    );
    
    /**
     * Convenience method to set the class specific URL parameter 'tool'
     *
     * @return PubmedFetcher
     *   The instatiated class.
     */
    public function setTool($tool){
        $this->params['uri_params']['tool'] = $tool;
        return $this;
    }

    /**
     * Convenience method to set the class specific URL parameter 'email'
     *
     * @return PubmedFetcher
     *   The instatiated class.
     */
    public function setEmail($email){
        $this->params['uri_params']['email'] = $email;
        return $this;
    }
    
    /**
     * Convenience method to set the class specific URL parameter 'doi'
     *
     * @return PubmedFetcher
     *   The instatiated class.
     */
    public function setDoi($doi){
        $this->params['uri_params']['ids'] = $doi;
        return $this;
    }
    
}

/**
 * Class to fetch Crossref metadata
 */
class CrossrefFetcher extends MetaDataFetcher{
    
    /**
     * Service's base URL
     */
    protected $uri = "http://www.crossref.org/openurl";

    /**
     * URL parameters specific to the service
     */
    protected $params=array('uri_params' => array('pid' => '',
                                                  'noredirect' => 'true',
                                                  'format' => 'unixref'));

    /**
     * Convenience method to set the class specific URL parameter 'pid' (the user's id)
     *
     * @return CrossrefFetcher
     *   The instatiated class.
     */
    public function setPid($pid){
        $this->params['uri_params']['pid'] = $pid;
        return $this;
    }

    /**
     * Convenience method to set the class specific URL parameter 'doi'
     *
     * @return CrossrefFetcher
     *   The instatiated class.
     */
    public function setDoi($doi){
        $this->params['uri_params']['id'] = 'doi:'.$doi;
        return $this;
    }

    /**
     * Check the service's response for errors
     */
    protected function checkError(){
        $xpath = new DOMXPath($this->dom);
        $entries = $xpath->query('//error');
        foreach ($entries as $entry) {
            if ($entry->nodeValue == $this->params['uri_params']['id']){
                $this->setErrosStatus(TRUE, '', 'DOI not found');
                return;
            }
        }
    }
}

/**
 * Class to fetch Scopus search metadata
 */
class ScopusSearchFetcher extends  MetaDataFetcher{
    
    /**
     * Service's base URL
     */
    protected $uri = "https://api.elsevier.com/content/search/scopus";
    
    /**
     * URL parameters specific to the service
     */
    protected $params=array('uri_params' => array('query' => ''),
                            'headers_params' => array('Accept' => 'application/xml')
     );
    
    /**
     * Error check parameters
     *
     * query: Xpath query to extract error string from the response
     * check: string to check for the error
     * code: error code to write in the error status array
     * message: error message to write in the error status array
     */
    protected $error_queries = array(array('query' => '//atom:error', 'check' => 'Result set was empty', 'code' => '', 'message' => 'Result set was empty'),
                               array('query' => '//statusText', 'check' => 'Invalid API Key', 'code' => 'Authentication error', 'message' => 'Invalid API Key'),
    );

    /**
     * Convenience method to set the class specific URL parameter 'doi'
     *
     * @return ScopusSearchFetcher
     *   The instatiated class.
     */
    public function setDoi($doi){
        if (!empty($this->params['uri_params']['query'])){
            $this->params['uri_params']['query'] .= '&';
        }
        $this->params['uri_params']['query'] .= "DOI($doi)";
    }

    /**
     * Convenience method to set the class specific URL parameter 'key' (User specific)
     *
     * @return ScopusSearchFetcher
     *   The instatiated class.
     */
    public function setKey($key){
        $this->params['headers_params']['X-ELS-APIKey'] = $key;
//        $this->params['uri_params']['apiKey'] = $key; //alternative configuration
    }
}

/**
 * lass to fetch WoS redirect url
 */
class WosRedirectFetcher extends  MetaDataFetcher{
    
    /**
     * Service's base URL
     */
    protected $uri = "http://ws.isiknowledge.com/cps/openurl/service";
 
    /**
     * URL parameters specific to the service
     */
    protected $params=array('uri_params' => array('url_ver' => 'Z39.88-2004'));
       
    /**
     * Convenience method to set the class specific URL parameter 'doi'
     *
     * @return WosRedirectFetcher
     *   The instatiated class.
     */
    public function setDoi($doi){
        $this->params['uri_params']['rft_id'] = "info:doi/$doi";
    }
    
    /**
     * Set the curl options.
     * Need to override the parent's method as the fetching strategy does not fit with the main implementation. 
     */
    private function setCurlOpt(){
        curl_setopt($this->cSession, CURLOPT_URL,$this->url);
        curl_setopt($this->cSession, CURLOPT_HEADER, 1);
        curl_setopt($this->cSession, CURLOPT_NOBODY, 1);
        curl_setopt($this->cSession, CURLOPT_RETURNTRANSFER, 1);
    }

    /**
     * Fetch data from the selected web service
     * Need to override the parent's method as the fetching strategy does not fit with the main implementation.
     *
     * @return MetaDataFetcher
     *   The instatiated class.
     */
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
    
    
    /**
     * Build DOM from array. 
     */
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