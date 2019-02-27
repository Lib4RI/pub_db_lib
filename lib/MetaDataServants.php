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
    private $fetcher;
    private $processor;

    
    /**
     * Constructor. To be implemented in subclasses
     */
    
    public function __construct() {
        $fetcher = new MetaDataFetcher();
        $cruncher = new MetaDataProcessor(null);
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
     * Fetch data from the selected web service
     *
     * @return MetaDataServant
     *   The instatiated class.
     */
    public function fetch(){
        $this->processor->loadDom($fetcher->fetch()->getDom());
        return $this;
    }
    
    /**
     * Perform the transformation chain
     *
     * @return MetaDataServant
     *   The instatiated class.
     */
    public function process(){
        $this->processor->process();
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
}

/**
 * Class to get MODS from DOI using Crossref data 
 */
class Crossref2ModsServant extends MetaDataServant{
    
    /**
     * Constructor. 
     */    
    public function __construct() {
        $fetcher = new CrossrefFetcher();
        $cruncher = new MetaDataProcessor(null);
        $this->cruncher->addSteps(array('type' => 'callback', 
                                        'rule' => 'crossref2mods', 
                                        'source' => '', 
                                        'params' => array('editor' => FALSE, 
                                                          'subtitle' => FALSE, 
                                                          'publisher' => FALSE)
                                        )
                                  );
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
        $fetcher = new PubmedFetcher();
        $cruncher = new MetaDataProcessor(null);
        $this->cruncher->addSteps(array('type' => 'xslt', 
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
        $fetcher = new WosRedirectFetcher();
        $cruncher = new MetaDataProcessor(null);
        $cruncher->addSteps(array('type' => 'xslt', 
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
        $fetcher = new ScopusSearchFetcher();
        $cruncher = new MetaDataProcessor(null);
        $cruncher->addSteps(array('type' => 'xslt', 
                                  'rule' => '../xslts/scopus2scopus-id.xslt', 
                                  'source' => 'file')
                           );
    }
}