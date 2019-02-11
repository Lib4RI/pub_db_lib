<?php

/** 
 * A set of PHP classes to interact with publication databases and services 
 * such us CrossRef, Scopus, Web of Science, Pubmed
 */ 

require 'main.php';

/******************************************************************************
 * Classes for data manipulation
 *****************************************************************************/

class MetaDataCruncher extends MetaDataAbstract{
    
    private $steps = array();
    
    public function __construct() {
        
    }

    public function loadDoc($doc){
        $this->doc = $doc;
    }
    
    public function addSteps($steps){
        array_push($this->steps, $steps);
    }
    
    public function cruch(){
        foreach ($steps as $ii => $step){
            switch ($step['type']){
                case "xslt":
                    $xsl = new DOMDocument;
                    switch ($step['params']){
                        case 'file':
                            $xsl->load($step['rule']);
                            break;
                        case 'str':
                            $xsl->loadXML($step['rule']);
                            break;
                        case 'dom':
                            break;
                    }
                    $proc = new XSLTProcessor;
                    $proc->importStyleSheet($xsl); // attach the xsl rules
                    $this->doc = $proc->transformToXML($xml);
                    break;
                case "callback":
                    $this->doc = $step['rule']($this->doc, $step['params']);
                    break;
            }
        }
    }
}
