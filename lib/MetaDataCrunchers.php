<?php

/** 
 * A set of PHP classes to interact with publication databases and services 
 * such us CrossRef, Scopus, Web of Science, Pubmed
 */ 

require_once  'MetaDataAbstract.php';

/******************************************************************************
 * Classes for data manipulation
 *****************************************************************************/

class MetaDataCruncher extends MetaDataAbstract{
    
    private $steps = array();
    
    public function __construct($dom) {
        if (!empty($dom)){
            $this->loadDom($dom);
        }
    }

    public function loadDom($dom){
        $this->dom = $dom;
        return $this;
    }
    
    public function addSteps($steps){
        array_push($this->steps, $steps);
        return $this;
    }
    
    public function cruch(){
        foreach ($this->steps as $ii => $step){
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
                    $this->dom->loadXML($proc->transformToXML($xsl));
                    break;
                case "callback":
                    $this->dom = $step['rule']($this->dom, $step['params']);
                    break;
            }
        }
        return $this;
    }
}
