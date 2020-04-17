<?php

/** 
 * A set of PHP classes to interact with publication databases and services 
 * such us CrossRef, Scopus, Web of Science, Pubmed
 */ 

require_once dirname(__FILE__).'/MetaDataAbstract.php';

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
     * Array containing a list of http response code that will not trigger an error 
     * Can be overridden in subclasses
     */
    protected $allowed_http_response_code = array(200);
   

    /**
     * Error status array
     * 
     * status: TRUE or FALSE
     * code: string containing the error code
     * message: string containing the error message
     */
    private $error = array('status' => FALSE, 
                           'connection' => array('err_no' => NULL, 'error' => NULL),
                           'http_response' => array('code' => NULL),
                           'content' => array('code' => '', 'message' => ''));
    
    /**
     * Steps done flag (TRUE or FALSE)
     */
    protected $fetch_steps_done = FALSE;
    
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
     *   The instantiated class.
     */
    public function setUri($uri){
        $this->uri = $uri;
        return $this;
    }
  
    /**
     * Set URI parameter
     *
     * @param string $key
     *   A string containing parameter's key
     *   
     * @param string $value
     *   A string containing parameter's value
     *
     * @return MetaDataFetcher
     *   The instantiated class.
     */
    public function setUriParam($key, $value){
        $this->params['uri_params'][$key] = $value;
        return $this;
    }
    
    /**
     * Set URI parameters
     *
     * @param array $params
     *   An array containing parameters in the form $key => $value
     *
     * @return MetaDataFetcher
     *   The instantiated class.
     */
    public function setUriParams($params){
        array_push($this->params['uri_params'], $params);
        return $this;
    }
    
    /**
     * Build the full URL to fetch metadata
     *
     * @return MetaDataFetcher
     *   The instantiated class.
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
     *   The instantiated class.
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
    public function getHttpHeaders(){
        return $this->headers;
    }

    /**
     * Return the response header
     *
     * @return string
     *   A strin containing the response header
     */
    public function getResponseHeader(){
        return $this->response_header;
    }

    /**
     * Return the parsed response header
     *
     * @return array
     *   An array containing the parsed version of the response header
     */
    public function getParsedHeader(){
        $s = $this->getResponseHeader();
        $s = str_replace("\n", '&', $s);
        $s = str_replace("\r", '', $s);
        $s = str_replace(': ', '=', $s);
        
        parse_str($s,$out);
        return $out;
    }
    
    /**
     * Set options for the curl library
     *
     */
    protected function setCurlOpt(){
        curl_setopt($this->cSession,CURLOPT_URL,$this->url);
        curl_setopt($this->cSession,CURLOPT_RETURNTRANSFER,TRUE);
        if (!empty($this->getHttpHeaders())){
            curl_setopt($this->cSession,CURLOPT_HTTPHEADER, $this->getHttpHeaders());
        }
//        curl_setopt($this->cSession,CURLOPT_VERBOSE, TRUE);
        curl_setopt($this->cSession,CURLOPT_HEADER, TRUE);
    }

    /**
     * Fetch data from the selected web service
     *
     * @return MetaDataFetcher
     *   The instantiated class.
     */
    public function fetch(){
        $this->buildUrl(); //echo $this->getUrl(); exit;
        $this->buildHeaders();
        $this->cSession = curl_init();
        $this->setCurlOpt();
        $response = curl_exec($this->cSession);
        $header_size = curl_getinfo($this->cSession, CURLINFO_HEADER_SIZE);
        $this->response_header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        
        $this->dom->loadXML($body);
        $this->checkError();
        
        curl_close($this->cSession);
        
        
        return $this;
    }

    /**
     * Check the service's response for errors
     */
    protected function checkError(){
        $this->error['connection']['err_no'] = curl_errno($this->cSession);
        $this->error['connection']['error'] = curl_error($this->cSession);
        if ($this->error['connection']['err_no']){
            $this->error['status'] = TRUE;
        }
            
        $this->error['http_response']['code'] = curl_getinfo($this->cSession, CURLINFO_RESPONSE_CODE);
        if (!in_array($this->error['http_response']['code'], $this->allowed_http_response_code)){
            $this->error['status'] = TRUE;
        }
        
        $xpath = new DOMXPath($this->dom);
        foreach ($this->error_queries as $key => $error_query){
            $entries = @$xpath->query($error_query['query']);
            if (!empty($entries)){
                foreach ($entries as $entry) {
                    if ($entry->nodeValue == $error_query['check']){
                        $this->setErrosStatus(TRUE, $error_query['code'], $error_query['message']);
                        return;
                    }
                }
            }
        }
    }
 
    /**
     * Convenient function to set the error status 
     */    
    protected function setErrosStatus($status, $code, $message){
        $this->error['status'] = $status;
        $this->error['content']['code'] = $code;
        $this->error['content']['message'] = $message;
    }

    /**
     * Return the current error details
     *
     * @return array
     *   The array containing the error status and details
     */
    public function getError(){
        return $this->error;
    }

    /**
     * Return the current error status
     *
     * @return bool
     *   The error status
     */
    public function getErrorStatus(){
        return $this->getError()['status'];
    }

    /**
     * Return the current error code
     *
     * @return string
     *   The error code
     */
    public function getErrorCode(){
        return $this->getError()['content']['code'];
    }

    /**
     * Return the current error message
     *
     * @return string
     *   The error message
     */
    public function getErrorMessage(){
        return $this->getError()['content']['message'];
    }
    
    /**
     * Set error check parameters.
     *
     * @param array $error_queries
     *   An associative array containing the error check parameters.
     *
     * @return MetaDataFetcher
     *   The instantiated class.
     */
    public function setErrorParams($error_queries){
        $this->error_queries = $error_queries;
        return $this;
    }

    /**
     * Steps Generator
     *
     * @return MetaDataFetcher
     *   The instantiated class.
     */
    public function steps(){
        while (!$this->fetch_steps_done){
            if ($this->getErrorStatus()){
                $this->fetch_steps_done = TRUE;
                break;
            }
            $this->fetch()->nextStep();
            yield $this;
        }
    }
    
    /**
     * Steps "iterator"
     *
     * @return MetaDataFetcher
     *   The instantiated class.
     */
    public function nextStep(){
        $this->fetch_steps_done = TRUE;
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
     *   The instantiated class.
     */
    public function setTool($tool){
        $this->params['uri_params']['tool'] = $tool;
        return $this;
    }

    /**
     * Convenience method to set the class specific URL parameter 'email'
     *
     * @return PubmedFetcher
     *   The instantiated class.
     */
    public function setEmail($email){
        $this->params['uri_params']['email'] = $email;
        return $this;
    }
    
    /**
     * Convenience method to set the class specific URL parameter 'doi'
     *
     * @return PubmedFetcher
     *   The instantiated class.
     */
    public function setDoi($doi){
        $this->params['uri_params']['ids'] = $doi;
        $this->params['uri_params']['idtype'] = 'doi';
        return $this;
    }

    /**
     * Convenience method to set the class specific URL parameter 'pmid'
     *
     * @return PubmedFetcher
     *   The instantiated class.
     */
    public function setPmid($pmid){
        $this->params['uri_params']['ids'] = $pmid;
        $this->params['uri_params']['idtype'] = 'pmid';
        return $this;
    }
    
}

/**
 * Class to fetch DOI from PubmedID
 */
class PubmedWebFetcher extends MetaDataFetcher{
    /**
     * Service's base URL
     */
    protected $uri = "https://www.ncbi.nlm.nih.gov/pubmed/";
    protected $baseuri = "https://www.ncbi.nlm.nih.gov/pubmed/";
    
    /**
     * URL parameters specific to the service
     */
    protected $params=array('uri_params' =>[]);
    
    public function setPmid($pmid){
        $this->uri = $this->baseuri.$pmid;
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
     *   The instantiated class.
     */
    public function setPid($pid){
        $this->params['uri_params']['pid'] = $pid;
        return $this;
    }

    /**
     * Convenience method to set the class specific URL parameter 'doi'
     *
     * @return CrossrefFetcher
     *   The instantiated class.
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
        
        if($xpath->query('//doi_records')->length == 0){
            $this->setErrosStatus(TRUE, '', 'DOI not found');
            return;
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
     * Convenience method to set the URL query
     *
     * @return ScopusSearchFetcher
     *   The instantiated class.
     */
    public function setQuery($query){
        $this->params['uri_params']['query'] .= $query;
        return $this;
    }
    
    /**
     * Convenience method to set the class specific URL parameter 'doi'
     *
     * @return ScopusSearchFetcher
     *   The instantiated class.
     */
    public function setDoi($doi){
        if (!empty($this->params['uri_params']['query'])){
            $this->params['uri_params']['query'] .= 'AND';
        }
        $this->params['uri_params']['query'] .= "DOI($doi)";
        return $this;
    }

    /**
     * Convenience method to set the class specific URL parameter 'title'
     *
     * @return ScopusSearchFetcher
     *   The instantiated class.
     */
    public function setTitle($title){
        if (!empty($this->params['uri_params']['query'])){
            $this->params['uri_params']['query'] .= 'AND';
        }
        $this->params['uri_params']['query'] .= "TITLE($title)";
        return $this;
    }
    
    /**
     * Convenience method to set the class specific URL parameter 'key' (User specific)
     *
     * @return ScopusSearchFetcher
     *   The instantiated class.
     */
    public function setKey($key){
        $this->params['headers_params']['X-ELS-APIKey'] = $key;
//        $this->params['uri_params']['apiKey'] = $key; //alternative configuration
        return $this;
    }


    /**
     * Convenience internal method to get relevant values from DOM
     *
     * @return String
     *   The value of the relevant node
     */
    private function getValueFromDom($query){
        $xpath = new DOMXPath($this->getDom());
        $results = @$xpath->query($query);
        if (!empty($results)){
            return $results[0]->nodeValue;
        }
    }
        
    /**
     * Steps "iterator"
     *
     * @return MetaDataFetcher
     *   The instantiated class.
     */
    public function nextStep(){
        $total_results = $this->getValueFromDom("//opensearch:totalResults");
        $start_index = $this->getValueFromDom("opensearch:startIndex");
        $items_par_page = $this->getValueFromDom("opensearch:itemsPerPage");
        
        if (($start_index + $items_par_page) >= $total_results){
            $this->fetch_steps_done = TRUE;
        }
        else{
            $this->setUriParam('start', $start_index + $items_par_page);
        }
        
        return $this;
    }
}

/**
 * Class to fetch some Elsevier and Scopus metadata
 */
class ElsevierScopusFetcher extends  MetaDataFetcher{
    
    /**
     * Service's base URL (to be overridden in subclasses)
     */
    protected $baseuri; 
    protected $uri;
    
    /**
     * URL parameters specific to the service
     */
    protected $params=array('uri_params' => array('query' => ''));
    
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
                                     array('query' => '//error-response/error-code', 'check' => 'QUOTAEXCEEDED', 'code' => 'Quota Exceeded', 'message' => 'Quota Excedeed'),
    );
    

    /**
     * Get the request number limit allowed by the current key
     *
     * @return string
     *   A string containing the number of the request allowed with the current key
     */
    public function getRequestLimit(){
        if (isset($this->getParsedHeader()['X-RateLimit-Limit'])){
            return $this->getParsedHeader()['X-RateLimit-Limit'];
        }
        else{
            return null;
        }
        
    }

    /**
     * Get the number of the request left before reset
     *
     * @return string
     *   A string containing the number of the request left before reset
     */
    public function getRequestRemaining(){
        if (isset($this->getParsedHeader()['X-RateLimit-Remaining'])){
            return $this->getParsedHeader()['X-RateLimit-Remaining'];
        }
        else{
            return null;
        }
    }
    
    /**
     * Get the request reset date
     *
     * @return string
     *   A string containing the request reset date
     */
    public function getRequestReset(){
        if (isset($this->getParsedHeader()['X-RateLimit-Reset'])){
            return $this->getParsedHeader()['X-RateLimit-Reset'];
        }
        else{
            return null;
        }
    }
    
    /**
     * Overridding parent's method to add info about key reset time
     *
     * @return array
     *   The array containing the error status and details
     */
    public function getError(){
        $error = parent::getError();
        if ($error['content']['code'] == "Quota Exceeded"){
            if (null !== $this->getRequestReset()){
                $error['content']['message'] .=  '. Reset: '.$this->getRequestReset();
            }
        }
        
        return $error;
    }
    
    /**
     * Convenience method to set the class specific URL parameter 'doi'
     *
     * @return ElsevierScopusFetcher
     *   The instantiated class.
     */
    public function setDoi($doi){
        $this->uri = $this->baseuri.'/doi/'.$doi;
        return $this;
    }

    /**
     * Convenience method to set the class specific URL parameter 'eid'
     *
     * @return ElsevierScopusFetcher
     *   The instantiated class.
     */
    public function setEid($eid){
        $this->uri = $this->baseuri.'/eid/'.$eid;
        return $this;
    }
    
    /**
     * Convenience method to set the class specific URL parameter 'pii'
     *
     * @return ElsevierScopusFetcher
     *   The instantiated class.
     */
    public function setPii($pii){
        $this->uri = $this->baseuri.'/pii/'.$pii;
        return $this;
    }
    
    /**
     * Convenience method to set the class specific URL parameter 'pmid'
     *
     * @return ElsevierScopusFetcher
     *   The instantiated class.
     */
    public function setPmid($pmid){
        $this->uri = $this->baseuri.'/pubmed_id/'.$pmid;
        return $this;
    }
    
    /**
     * Convenience method to set the class specific URL parameter 'key' (User specific)
     *
     * @return ElsevierScopusFetcher
     *   The instantiated class.
     */
    public function setKey($key){
        $this->params['uri_params']['apiKey'] = $key;
    }
}

/**
 * Class to fetch Elsevier article metadata
 */
class ElsevierArticleFetcher extends ElsevierScopusFetcher{
    /**
     * Service's base URL
     */
    protected $baseuri = "https://api.elsevier.com/content/article";
    protected $uri = "https://api.elsevier.com/content/article";
}

/**
 * Class to fetch Scopus abstract metadata
 */
class ScopusAbstractFetcher extends ElsevierScopusFetcher{
    /**
     * Service's base URL
     */
    protected $baseuri = "https://api.elsevier.com/content/abstract";
    protected $uri = "https://api.elsevier.com/content/abstract";
    
    /**
     * Convenience method to set the class specific URL parameter 'pui'
     *
     * @return ScopusAbstractFetcher
     *   The instantiated class.
     */
    public function setPui($pui){
        $this->uri = $this->baseuri.'/pui/'.$pui;
        return $this;
    }
    
    /**
     * Convenience method to set the class specific URL parameter 'scopus_id'
     *
     * @return ScopusAbstractFetcher
     *   The instantiated class.
     */
    public function setScopusId($scopus_id){
        $this->uri = $this->baseuri.'/scopus_id/'.$scopus_id;
        return $this;
    }
}

/**
 * Class to fetch WoS redirect url
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
     *   The instantiated class.
     */
    public function setDoi($doi){
        $this->params['uri_params']['rft_id'] = "info:doi/$doi";
        return $this;
    }
    
    /**
     * Set the curl options.
     * Need to override the parent's method as the fetching strategy does not fit with the main implementation. 
     */
    protected function setCurlOpt(){
        curl_setopt($this->cSession, CURLOPT_URL,$this->url);
        curl_setopt($this->cSession, CURLOPT_HEADER, 1);
        curl_setopt($this->cSession, CURLOPT_NOBODY, 1);
        curl_setopt($this->cSession, CURLOPT_RETURNTRANSFER, 1);
    }

    /**
     * Fetch data from the selected web service
     * Need to override the parent's method as the fetching strategy does not fit with the main implementation.
     *
     * @return WosRedirectFetcher
     *   The instantiated class.
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
     * 
     * @param DOMDocument $dom
     * 
     * @param array $array
     *   An array to convert to DOM
     *   
     * @param DOMNode $node
     *   The DOMNode to append the DOM representation of the array to
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

class PdbBiosyncFetcher extends MetaDataFetcher{
    
    /**
     * Service's base URL
     */
    protected $baseuri = "http://biosync.sbkb.org/biosync_pdbtext/";
    protected $uri = "http://biosync.sbkb.org/biosync_pdbtext/";
    protected $params=array('uri_params' => []);
        
    public function setSource($source){
        $this->uri = $this->baseuri.$source;
        return $this;
    }
    
    /**
     * Fetch data from the selected web service
     *
     * @return MetaDataFetcher
     *   The instantiated class.
     */
    public function fetch(){
        $this->buildUrl(); //echo $this->getUrl(); exit;
        $this->buildHeaders();
        $this->cSession = curl_init();
        $this->setCurlOpt();
        $response = curl_exec($this->cSession);
        $header_size = curl_getinfo($this->cSession, CURLINFO_HEADER_SIZE);
        $this->response_header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        $body=preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $body);
        $this->dom->loadXML('<response>'.$body.'</response>');
        $this->checkError();
        
        curl_close($this->cSession);
        
        
        return $this;
    }
}

class FileFetcher extends MetaDataFetcher{
    
    public function setSource($path){
        $this->uri = $path;
    }
    
    public function fetch(){
        $body = file_get_contents($this->uri);
        $this->dom->loadXML('<response>'.$body.'</response>');
        
        return $this;
    }
}

class PdbFetcher extends MetaDataFetcher{
    public function fetch(){
        $this->buildUrl(); //echo $this->getUrl(); exit;
        $this->buildHeaders();
        $this->cSession = curl_init();
        $this->setCurlOpt();
        
        $response = curl_exec($this->cSession);
        $header_size = curl_getinfo($this->cSession, CURLINFO_HEADER_SIZE);
        $this->response_header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        
        $body = arrayToXml(json_decode($body,TRUE),$rootElement='<response/>');
        $body = str_replace ( "<?xml version=\"1.0\"?>\n", '' , $body);
        $body = trim ( $body, $character_mask = " \t\n\r\0\x0B");
        
        $this->dom->loadXML($body);
        $this->checkError();
        
        curl_close($this->cSession);
        
        
        return $this;
    }
        
}
    
class PdbSearchFetcher extends PdbFetcher{
    
    /**
     * Service's base URL
     */
    protected $baseuri = "http://search.rcsb.org/rcsbsearch/v1/query";
    protected $uri = "http://search.rcsb.org/rcsbsearch/v1/query";
    protected $range = array('start' => 0, 'rows' => 1);
    protected $beamline;
    protected $f_count = FALSE;
    
    protected $query = array(
        'query' => array(
            'type' => "terminal",
            'node_id' => 0,
            'service' => "text",
            'parameters' => array(
                'attribute' => "diffrn_source.pdbx_synchrotron_beamline",
                'operator' => "exact_match",
                'value' => "",//$this->beamline,
            ),
        ),
        'return_type' => "entry",
        'request_options' => array(
            'pager' => array(),//$this->range,
            'return_counts' => FALSE,
        ),
    );
    
    
    
    
    
    public function buildUrl(){
        
        $query = array(
            'query' => array(
                'type' => "terminal",
                'node_id' => 0,
                'service' => "text",
                'parameters' => array(
                    'attribute' => "diffrn_source.pdbx_synchrotron_beamline",
                    'operator' => "exact_match",
                    'value' => $this->beamline,
                ),
            ),
            'return_type' => "entry",
            'request_options' => array(
                'pager' => $this->range,
                'return_counts' => $this->f_count,
            ),
        );
        
        $query['query']['parameters']['value'] = $this->beamline;
        $query['query']['request_options']['pager'] = $this->range;
        
        $this->url = $this->uri.'?json='.urlencode(json_encode($query));
//        echo $this->url;
        return $this;
    }
    
    public function setBeamline($beamline){
        $this->beamline = $beamline;
        return $this;
    }
    
    public function returnCount($f_count){
        $this->f_count = $f_count;
        return $this;
    }
    
    public function setRange($range){
        $this->range['start'] = $range[0];
        $this->range['rows']  = $range[1];
        return $this;
    }
    
}


class PdbEntryFetcher extends PdbFetcher{
    protected $baseuri = "https://data.rcsb.org/rest/v1/core/entry";
    protected $uri = "https://data.rcsb.org/rest/v1/core/entry";
    protected $params=array('uri_params' => []);
    
    public function setEntry($entry){
        $this->uri = $this->baseuri.'/'.$entry;
        return $this;
    }
}

function arrayToXml($array, $rootElement = null, $xml = null) {
    $_xml = $xml;
    
    // If there is no Root Element then insert root
    if ($_xml === null) {
        $_xml = new SimpleXMLElement($rootElement !== null ? $rootElement : '<root/>');
    }
    
    // Visit all key value pair
    foreach ($array as $k => $v) {
        
        // If there is nested array then
        if (is_array($v)) {
            if (is_numeric($k)){
                $elem = $_xml->addChild('item');
                $elem->addAttribute('id', $k);
            }
            else{
                $elem = $_xml->addChild($k);
            }
            // Call function for nested array
            arrayToXml($v, $k, $elem);
        }
        
        else {
            if (is_numeric($k)){
                $elem = $_xml->addChild('item', $v);
                $elem->addAttribute('id', $k);
            }
            else{
                $_xml->addChild($k, $v);
            }
            
            // Simply add child element.
            //$_xml->addChild($k, $v);
        }
    }
    
    return $_xml->asXML();
}

