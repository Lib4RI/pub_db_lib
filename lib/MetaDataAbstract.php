<?php

/** 
 * Abstract class to implement a set of PHP classes to interact with publication databases and services 
 * such us CrossRef, Scopus, Web of Science, Pubmed
 */ 


abstract class MetaDataAbstract{
    protected $dom;

    /**
     * Constructor. To be implemented in subclasses 
     */
    
    public function __construct() {
        
    }
    
    /**
     * Set class specific parameters.
     *
     * @param array $params
     *   An associative array containing the specific class parameters.
     *
     * @return MetaDataAbstract
     *   The instatiated class.
     */
    public function setParams($params){
        $this->params = $params;
        return $this;
    }

    /**
     * Return the instatiated DOMDocument
     *
     * @return DOMDocument
     *   The instatiated DOMDocument.
     */
    public function getDom(){
        return $this->dom;
    }
    
    /**
     * Return the XML representation of the instatiated DOMDocument
     *
     * @return string
     *   The XML representation of the instatiated DOMDocument.
     */
    public function getXML(){
        $this->dom->formatOutput = true;
        return $this->getDom()->saveXML();
    }

    /**
     * Return the JSON representation of the instatiated DOMDocument
     *
     * @return string
     *   The JSON representation of the instatiated DOMDocument.
     */
    public function getJSON(){
        return json_encode(simplexml_load_string($this->getXML()), JSON_PRETTY_PRINT);
    }

    /**
     * Return the array representation of the instatiated DOMDocument
     *
     * @return array
     *   The array representation of the instatiated DOMDocument.
     */
    public function getArray(){
        return json_decode($this->getJSON(),TRUE);
    }
    
}
