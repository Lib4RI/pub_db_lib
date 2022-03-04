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
        if (isset($this->dom))
            return $this->dom;
        else
            return FALSE;
    }
    
    /**
     * Return the XML representation of the instatiated DOMDocument
     *
     * @return string
     *   The XML representation of the instatiated DOMDocument.
     */
    public function getXML($dom = NULL){
        if (!isset($dom))
            $dom = $this->getDom();
        
        $dom->formatOutput = true;
        return $dom->saveXML();
    }

    /**
     * Return the JSON representation of the instatiated DOMDocument
     *
     * @return string
     *   The JSON representation of the instatiated DOMDocument.
     */
    public function getJSON($dom = NULL){
        if (!isset($dom))
            $dom = $this->getDom();
            
        return json_encode(simplexml_load_string($this->getXML($dom)), JSON_PRETTY_PRINT);
    }

    /**
     * Return the array representation of the instatiated DOMDocument
     *
     * @return array
     *   The array representation of the instatiated DOMDocument.
     */
    public function getArray($dom = NULL){
        if (!isset($dom))
            $dom = $this->getDom();
            
        return json_decode($this->getJSON($dom),TRUE);
    }

    /**
     * Return plain string representation of the instatiated DOMDocument
     *
     * @return string
     *   The string representation of the instatiated DOMDocument.
     */
    public function getString($dom = NULL){
        if (!isset($dom))
            $dom = $this->getDom();
            
        return $dom->textContent;
    }
    
    /**
     * Convenience internal method to get relevant values from DOM
     *
     * @return String
     *   The value of the relevant node
     */
    protected function getValueFromDom($query){
        $xpath = new DOMXPath($this->getDom());
        $results = @$xpath->query($query);
        if ((!empty($results) )){
            return @$results[0]->nodeValue;
        }
    }
}
