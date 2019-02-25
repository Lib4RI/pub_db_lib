<?php

/** 
 * A set of PHP classes to interact with publication databases and services 
 * such us CrossRef, Scopus, Web of Science, Pubmed
 */ 

include_once '../lib/MetaDataFetchers.php';
include_once '../lib/MetaDataCrunchers.php';
include_once '../callbacks/callbacks.php';


/******************************************************************************
 * Convenience classes for data retrieve
 *****************************************************************************/

/**
 * Generic class to fetch data from metadta providers web services
 */
class MetaDataRetriever{
    private $fetcher;
    private $cruncher;

    
    /**
     * Constructor. To be implemented in subclasses
     */
    
    public function __construct() {
        $fetcher = new CrossrefFetcher();
        $cruncher = new MetaDataCruncher(null);
    }
    
    /**
     * Convenience method to set the class specific URL parameter 'doi'
     *
     * @return MetaDataRetriever
     *   The instatiated class.
     */
    public function setDoi($doi){
        $this->fetcher->setDoi($doi);
        return $this;
    }
    
    /**
     * Fetch data from the selected web service
     *
     * @return MetaDataFetcher
     *   The instatiated class.
     */
    public function fetch(){
        $this->cruncher->loadDom($fetcher->fetch()->getDom());
        return $this;
    }
    
    /**
     * Perform the transformation chain
     *
     * @return MetaDataCruncher
     *   The instatiated class.
     */
    public function cruch(){
        $this->cruncher->crunch();
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
     * Return the XML representation of the crunched DOMDocument
     *
     * @return string
     *   The XML representation of the crunched DOMDocument.
     */
    public function getCrunchedXML(){
        return $this->cruncher->getXML();
    }
    
    /**
     * Return the JSON representation of the crunched DOMDocument
     *
     * @return string
     *   The JSON representation of the crunched DOMDocument.
     */
    public function getCrunchedJSON(){
        return $this->cruncher->getJSON();
    }
    
    /**
     * Return the array representation of the crunched DOMDocument
     *
     * @return array
     *   The array representation of the crunched DOMDocument.
     */
    public function getCrunchedArray(){
        return $this->cruncher->getArray();
    }
}

