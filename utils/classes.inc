<?php

class lib4riMetaDataIntegrator{
    
    private $authors = array(); //DOM element?
    
    /**
     * Constructor
     */
    public function __construct() {
        
    }
    
    protected function matchAuthor(){
        
    }
    
    protected function getInternalAffiliation(){
        
    }
    
}

class eawagMetaDataIntegrator extends lib4riMetaDataIntegrator{
    
}

class empaMetaDataIntegrator extends lib4riMetaDataIntegrator{
    
}

class wslMetaDataIntegrator extends lib4riMetaDataIntegrator{
    
}

class psiMetaDataIntegrator extends lib4riMetaDataIntegrator{
    
}

class lib4riPubAlert{
    protected $fetched_pubs = array();
    protected $in_dora_pubs = array();
    protected $queued_pubs = array();
    protected $blacklisted_pubs = array();
    protected $new_pubs = array();
    
    protected $namespace;
    
    private $searchYear;
    private $metaDataServant;

    /**
     * Constructor
     */
    public function __construct($year = null) {
        module_load_include('php', 'publication_DB', 'lib/MetaDataServants');
        $this->metaDataServant = new ScopusIdListServant();
        $this->metaDataServant->setKey(variable_get('lib4ri_citco_api_key_scopus',''));
        $this->metaDataServant->setUriParam('field', 'eid,doi');
        $this->metaDataServant->setUriParam('count', '200');
        $this->setSearchYear($year);
    }
    
    public function setSearchYear($year){
        $this->searchYear = $year;
        return $this;
    }
    
    public function setQuery($query){
        $this->metaDataServant->setQuery($query);
        return $this;
    }
    
    public function fetch(){
        
        $this->metaDataServant->serve();
        foreach ($this->metaDataServant->getProcessedArray() as $page){
            foreach ($page['item'] as $pub){
                array_push($this->fetched_pubs, $pub);
                array_push($this->new_pubs, $pub);
            }
        }
        return $this;
    }
    
    public function getInDora(){
        global $base_url;
        $current_url = url(current_path(), array('absolute' => TRUE));
        
        $params['f'][0] = 'mods_originInfo_encoding_w3cdtf_keyDate_yes_dateIssued_dt:['.$this->searchYear.'-01-01T00:00:00Z TO '.$this->searchYear.'-12-31T23:59:59Z]';
        $params['f'][1] = 'mods_identifier_doi_ms:[* TO *] OR mods_identifier_scopus_ms:[* TO *]';
        
        $params['q'] = str_replace($base_url.'/', '', $current_url);
        $sq = new IslandoraSolrQueryProcessor();
        
        // Build and execute Apache Solr query.
        $sq->buildQuery($query, $params);
        $sq->solrParams['facet'] = "false";
        $sq->solrParams['fl'] = 'PID, mods_identifier_doi_ms, mods_identifier_scopus_ms';
        $sq->solrLimit = '100000';
        $sq->executeQuery(FALSE, TRUE);
        
        $indora = array();
        $ii = 0;
        foreach ($sq->islandoraSolrResult['response']['objects'] as $object) {
            array_push($this->in_dora_pubs, $object['solr_doc']);
        }
        return $this;
    }

    public function getQueued(){
        return $this;
    }

    public function getBlacklisted(){
        return $this;
    }

    public function setNewPubs(){
        $this->fetch();
        $this->getInDora();
        $this->removeInDora();
        return $this;
    }
    
    public function getNewPubs(){
        return $this->new_pubs;
    }
    
    private function removeInDora(){
        foreach ($this->new_pubs as $key => $elem){
            foreach ($this->in_dora_pubs as $doraobj){
                if (in_array($elem['doi'], $doraobj['mods_identifier_doi_ms']) or in_array($elem['eid'], $doraobj['mods_identifier_scopus_ms'])){
                    unset($this->new_pubs[$key]);
                    break;
                }
            }
        }
        return $this;
    }

    private function removeQueued(){
        return $this;
    }

    private function removeBlacklisted(){
        return $this;
    }
    
}

class lib4riScopusAlert extends lib4riPubAlert{
    
    public function setQuery($query){
        $this->metaDataServant->setQuery('(AFFIL(Paul Scherrer) OR AFFIL(PSI)) AND PUBYEAR = '.$this->searchYear);
        return $this;
    }
    
}