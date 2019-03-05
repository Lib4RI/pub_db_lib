<?php

/** 
 * A set of PHP classes to interact with publication databases and services 
 * such us CrossRef, Scopus, Web of Science, Pubmed
 */ 

require_once  'MetaDataAbstract.php';

/******************************************************************************
 * Classes for data manipulation
 *****************************************************************************/

/**
 * Generic class to perform metadata transformation, extraction, merging
 */
class MetaDataProcessor extends MetaDataAbstract{
    
    /**
     * Array of tranformation steps
     * 
     * Each element must be an array as follow
     * array('type' => ['xslt' | 'callback'], 
     *       'rule' => [path_to_xslt | string | function_name], 
     *       'source' => ['file', 'str'])
     *       'params' => array of parameters to pass to the callback function or to the xslt processor
     */
    private $steps = array();

    /**
     * Constructor
     */
    public function __construct($dom) {
        if (!empty($dom)){
            $this->loadDom($dom);
        }
    }

    /**
     * Load a DOMDocument
     *
     * @param DOMDocument $dom
     *
     * @return MetaDataProcessor
     *   The instatiated class.
     */
    public function loadDom($dom){
        $this->dom = $dom;
        return $this;
    }
    
    /**
     * Add tranformation steps
     *
     * @param array $steps
     *
     * @return MetaDataProcessor
     *   The instatiated class.
     */
    public function addSteps($steps){
        array_push($this->steps, $steps);
        return $this;
    }
    
    /**
     * Perform the transformation chain
     *
     * @return MetaDataProcessor
     *   The instatiated class.
     */
    public function process(){
        foreach ($this->steps as $ii => $step){
            switch ($step['type']){
                case "xslt":
                    $xsl = new DOMDocument;
                    switch ($step['source']){
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
                    $this->dom = $proc->transformToDoc($this->dom);
                    break;
                case "callback":
                    $this->dom = $step['rule']($this->dom, (isset($step['params']) ? $step['params'] : NULL)); // prevent warning if $step['params'] is undefined
                    break;
            }
        }
        return $this;
    }
}


class MetaDataMerger extends MetaDataProcessor{
    
    private $add_step;
    
    /**
     * Constructor
     */
    public function __construct($dom) {
        parent::__construct($dom);
        $this->add_step = array('type' => 'callback', 'rule' => 'add_element', 'source' => '', 'params' => array('element' => NULL, 'parent' => NULL));
        $this->addSteps($this->add_step);
    }
    
    public function setElement($element){
        $this->add_step['params']['element'] = $element;
        return $this;
    }
    
    public function setParent($parent){
        $this->add_step['params']['parent'] = $parent;
        return $this;
    }
    
}