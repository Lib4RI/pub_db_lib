<?php

/** 
 * A set of PHP classes to interact with publication databases and services 
 * such us CrossRef, Scopus, Web of Science, Pubmed
 */ 

include_once '../lib/MetaDataFetchers.php';
include_once '../lib/MetaDataProcessors.php';
include_once '../callbacks/callbacks.php';


/******************************************************************************
 * Convenience classes for data retrieve
 *****************************************************************************/

/**
 * Generic class to fetch data from metadata providers web services and transforming it
 */
class MetaDataServant{
    protected $fetcher;
    protected $processor;
    private $fetched = FALSE;
    private $processed = FALSE;
    private $fetched_stack = array();
    private $processed_stack = array();
    
    /**
     * Constructor. To be implemented in subclasses
     */
    public function __construct() {
        $this->fetcher = new MetaDataFetcher();
        $this->cruncher = new MetaDataProcessor(null);
    }
    
    /**
     * Return TRUE if MetaData is fetched
     */
    public function isFetched(){
        return $this->fetched;        
    }

    /**
     * Return TRUE if MetaData is processed
     */
    public function isProcessed(){
        return $this->processed;
    }
    
    /**
     * Convenience method to set the class specific URL parameter 'doi'
     *
     * @return MetaDataServant
     *   The instatiated class.
     */
    public function setDoi($doi){
        $this->fetcher->setDoi($doi);
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
     * @return MetaDataServant
     *   The instantiated class.
     */
    public function setUriParam($key, $value){
        $this->fetcher->setUriParam($key, $value);
        return $this;
    }
    
    /**
     * Set URI parameters
     *
     * @param array $params
     *   An array containing parameters in the form $key => $value
     *
     * @return MetaDataServant
     *   The instantiated class.
     */
    public function setUriParams($params){
        $this->fetcher->setUriParams($params);
        return $this;
    }
    
    /**
     * Return the current fetcher error status
     *
     * @return array
     *   The array containing the error status, code and message.
     */
    public function getFetcherErrorStatus(){
        return $this->fetcher->getErrorStatus();
    }
    
    /**
     * Fetch data from the selected web service
     *
     * @return MetaDataServant
     *   The instatiated class.
     */
    public function fetch(){
        foreach ($this->fetcher->steps() as $step){
            $dom = new DOMDocument( "1.0", "ISO-8859-15" );
            $dom->loadXML($step->getXML()); //This must be improved!!!
            array_push($this->fetched_stack, $dom);
            if($this->getFetcherErrorStatus()){
                $this->fetched = FALSE;
                return $this;
            }
        }
        $this->fetched = TRUE;
        return $this;
    }
    
    /**
     * Perform the transformation chain
     *
     * @return MetaDataServant
     *   The instatiated class.
     */
    public function process(){
        if ($this->isfetched()){
            foreach ($this->fetched_stack as $step){
                $this->processor->loadDom($step);
                $dom = new DOMDocument( "1.0", "ISO-8859-15" );
                $dom->loadXML($this->processor->process()->getXML()); //This must be improved!!!
                array_push($this->processed_stack, $dom);
            }
            $this->processed = TRUE;
        }
        return $this;
    }
    
    /**
     * Fetch data from the selected web service and perform the transformation chain
     *
     * @return MetaDataServant
     *   The instatiated class.
     */
    public function serve(){
        $this->fetch()->process();
        return $this;
    }

    /**
     * Return the fetched DOMDocument
     *
     * @return DOMDocument
     *   The fetched DOMDocument.
     */
    public function getFetchedDom(){
        return $this->fetched_stack;
    }
    
    /**
     * Return the XML representation of the fetched DOMDocument
     *
     * @return string
     *   The XML representation of the fetched DOMDocument.
     */
    public function getFetchedXML(){
        $out = array();
        foreach ($this->fetched_stack as $dom){
            array_push($out, $this->fetcher->getXML($dom));
        }
        return $out;
    }
    
    /**
     * Return the JSON representation of the fetched DOMDocument
     *
     * @return string
     *   The JSON representation of the fetched DOMDocument.
     */
    public function getFetchedJSON(){
        $out = array();
        foreach ($this->fetched_stack as $dom){
            array_push($out, $this->fetcher->getJSON($dom));
        }
        return $out;
    }
    
    /**
     * Return the array representation of the fetched DOMDocument
     *
     * @return array
     *   The array representation of the fetched DOMDocument.
     */
    public function getFetchedArray(){
        $out = array();
        foreach ($this->fetched_stack as $dom){
            array_push($out, $this->fetcher->getArray($dom));
        }
        return $out;
    }

    /**
     * Return plain string representation of the fetched DOMDocument
     *
     * @return string
     *   The string representation of the fetched DOMDocument.
     */
    public function getFetchedString(){
        $out = array();
        foreach ($this->fetched_stack as $dom){
            array_push($out, $this->fetcher->getString($dom));
        }
        return $out;
    }
    
    /**
     * Return the processed DOMDocument
     *
     * @return DOMDocument
     *   The processed DOMDocument.
     */
    public function getProcessedDom(){
        return $this->processed_stack;
    }
    
    /**
     * Return the XML representation of the processed DOMDocument
     *
     * @return string
     *   The XML representation of the processed DOMDocument.
     */
    public function getProcessedXML(){
        $out = array();
        foreach ($this->processed_stack as $dom){
            array_push($out, $this->processor->getXML($dom));
        }
        return $out;
    }
    
    /**
     * Return the JSON representation of the processed DOMDocument
     *
     * @return string
     *   The JSON representation of the processed DOMDocument.
     */
    public function getProcessedJSON(){
        $out = array();
        foreach ($this->processed_stack as $dom){
            array_push($out, $this->processor->getJSON($dom));
        }
        return $out;
    }
    
    /**
     * Return the array representation of the processed DOMDocument
     *
     * @return array
     *   The array representation of the processed DOMDocument.
     */
    public function getProcessedArray(){
        $out = array();
        foreach ($this->processed_stack as $dom){
            array_push($out, $this->processor->getArray($dom));
        }
        return $out;
    }

    /**
     * Return plain string representation of the processed DOMDocument
     *
     * @return string
     *   The string representation of the processed DOMDocument.
     */
    public function getProcessedString(){
        $out = array();
        foreach ($this->processed_stack as $dom){
            array_push($out, $this->processor->getString($dom));
        }
        return $out;
    }
    
    /**
     * Return the instantiated fetcher
     *
     * @return MetaDataFetcher
     *   The the instantiated fetcher.
     */    
    public function getFetcher(){
        return $this->fetcher;
    }

    
    /**
     * Return the instantiated processor
     *
     * @return MetaDataProcessor
     *   The the instantiated processor.
     */
    public function getProcessor(){
        return $this->processor;
    }
}

/**
 * Class to get MODS from DOI using Crossref data 
 */
class Crossref2ModsServant extends MetaDataServant{
    
    /**
     * Constructor. 
     */    
    public function __construct() {
        $this->fetcher = new CrossrefFetcher();
        $this->processor = new MetaDataProcessor(null);
        $this->processor->addSteps(array('type' => 'callback', 
                                        'rule' => 'crossref2mods', 
                                        'source' => '', 
                                        'params' => array('editor' => FALSE, 
                                                          'subtitle' => FALSE, 
                                                          'publisher' => FALSE)
                                        )
                                  );
    }

    /**
     * Convenience method to set the class specific URL parameter 'pid' (the user's id)
     *
     * @return Crossref2ModsServant
     *   The instatiated class.
     */
    public function setPid($pid){
        $this->fetcher->setPid($pid);
        return $this;
    }

    /**
     * Convenience method to set the class specific URL parameter 'doi'
     *
     * @return Crossref2ModsServant
     *   The instatiated class.
     */
    public function setDoi($doi){
        $this->fetcher->setDoi($doi);
        return $this;
    }
}

/**
 * Class to get Pubmed ID from DOI
 */
class PubmedIdServant extends MetaDataServant{
    
    /**
     * Constructor.
     */
    public function __construct() {
        $this->fetcher = new PubmedFetcher();
        $this->processor = new MetaDataProcessor(null);
        $this->processor->addSteps(array('type' => 'xslt', 
                                        'rule' => '../xslts/pmed2pmed-id.xslt', 
                                        'source' => 'file')
                                 );
    }
}

/**
 * Class to get Web Of Science ID from DOI
 */
class WosIdServant extends MetaDataServant{
    
    /**
     * Constructor.
     */
    public function __construct() {
        $this->fetcher = new WosRedirectFetcher();
        $this->processor = new MetaDataProcessor(null);
        $this->processor->addSteps(array('type' => 'xslt', 
                                  'rule' => '../xslts/wos2wos-id.xslt', 
                                  'source' => 'file')
                           );
    }
}

/**
 * Generic Class for Scopus search 
 */
class ScopusSearchServant extends MetaDataServant{
    
    /**
     * Constructor.
     */
    public function __construct() {
        $this->fetcher = new ScopusSearchFetcher();
        $this->processor = new MetaDataProcessor(null);
    }
    
    /**
     * Convenience method to set the class specific URL parameter 'title'
     *
     * @return ScopusSearchServant
     *   The instatiated class.
     */
    public function setTitle($title){
        $this->fetcher->setTitle($title);
        return $this;
    }
    
    
    /**
     * Convenience method to set the class specific parameter 'key'
     *
     * @return ScopusSearchServant
     *   The instatiated class.
     */
    public function setKey($key){
        $this->fetcher->setKey($key);
        return $this;
    }
    
    /**
     * Convenience method to set the URL query
     *
     * @return ScopusSearchServant
     *   The instatiated class.
     */
    public function setQuery($query){
        $this->fetcher->setQuery($query);
        return $this;
    }
    
}

/**
 * Class to get Scopus ID from DOI
 */
class ScopusIdServant extends ScopusSearchServant{
    
    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->processor->addSteps(array('type' => 'xslt', 
                                  'rule' => '../xslts/scopus2scopus-id.xslt', 
                                  'source' => 'file')
                           );
    }
}

/**
 * Class to get Web Of Science ID from DOI
 */
class ScopusIdListServant extends ScopusSearchServant{
    
    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->processor->addSteps(array('type' => 'xslt',
            'rule' => '../xslts/scopusIdList.xslt',
            'source' => 'file')
            );
    }
}
