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
        $this->fetcher->fetch();
        $error = $this->getFetcherErrorStatus();
        if ($error['status'] == FALSE){
            $this->processor->loadDom($this->fetcher->getDom());
            $this->fetched = TRUE;
        }
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
            $this->processor->process();
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
        return $this->fetcher->getDom();
    }
    
    /**
     * Return the XML representation of the fetched DOMDocument
     *
     * @return string
     *   The XML representation of the fetched DOMDocument.
     */
    public function getFetchedXML(){
        return $this->fetcher->getXML();
    }
    
    /**
     * Return the JSON representation of the fetched DOMDocument
     *
     * @return string
     *   The JSON representation of the fetched DOMDocument.
     */
    public function getFetchedJSON(){
        return $this->fetcher->getJSON();
    }
    
    /**
     * Return the array representation of the fetched DOMDocument
     *
     * @return array
     *   The array representation of the fetched DOMDocument.
     */
    public function getFetchedArray(){
        return $this->fetcher->getArray();
    }

    /**
     * Return the procesed DOMDocument
     *
     * @return DOMDocument
     *   The processed DOMDocument.
     */
    public function getProcessedDom(){
        return $this->processor->getDom();
    }
    
    /**
     * Return the XML representation of the processed DOMDocument
     *
     * @return string
     *   The XML representation of the processed DOMDocument.
     */
    public function getProcessedXML(){
        return $this->processor->getXML();
    }
    
    /**
     * Return the JSON representation of the processed DOMDocument
     *
     * @return string
     *   The JSON representation of the processed DOMDocument.
     */
    public function getProcessedJSON(){
        return $this->processor->getJSON();
    }
    
    /**
     * Return the array representation of the processed DOMDocument
     *
     * @return array
     *   The array representation of the processed DOMDocument.
     */
    public function getProcessedArray(){
        return $this->processor->getArray();
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
 * Class to get Scopus ID from DOI
 */
class ScopusIdServant extends MetaDataServant{
    
    /**
     * Constructor.
     */
    public function __construct() {
        $this->fetcher = new ScopusSearchFetcher();
        $this->processor = new MetaDataProcessor(null);
        $this->processor->addSteps(array('type' => 'xslt', 
                                  'rule' => '../xslts/scopus2scopus-id.xslt', 
                                  'source' => 'file')
                           );
    }
    
    /**
     * Convenience method to set the class specific URL parameter 'title'
     *
     * @return MetaDataServant
     *   The instatiated class.
     */
    public function setTitle($title){
        $this->fetcher->setTitle($title);
        return $this;
    }
    
    /**
     * Convenience method to set the class specific parameter 'key'
     *
     * @return MetaDataServant
     *   The instatiated class.
     */
    public function setKey($key){
        $this->fetcher->setKey($key);
        return $this;
    }
}