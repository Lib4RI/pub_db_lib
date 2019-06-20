<?php

module_load_include('inc', 'publication_DB', 'includes/define');

class PubAlert{
    protected $queued_table = PUB_DB_ALERT_TABLE;
    
    protected $fetched_pubs = array();
    protected $in_repo_pubs = array();
    protected $queued_pubs = array();
    protected $blacklisted_pubs = array();
    protected $new_pubs = array();
    
    protected $namespace;
    
    protected $searchYear;
    protected $metaDataServant;

    /**
     * Constructor
     */
    public function __construct($year = null) {
        module_load_include('php', 'publication_DB', 'lib/MetaDataServants');
//        $this->metaDataServant = new ScopusIdListServant();
//        $this->metaDataServant->setKey(variable_get('publication_DB_scopus_api_key',''));
        $this->metaDataServant->setUriParam('field', 'eid,doi,title,aggregationType,subtypeDescription');
        $this->metaDataServant->setUriParam('count', '200');
        $this->setSearchYear(variable_get('publication_DB_scopus_search_year'));
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
    
    public function getInRepo(){
        global $base_url;
        $current_url = url(current_path(), array('absolute' => TRUE));
        
        //TODO Parametrize query and add hook 
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
        
        $ii = 0;
        foreach ($sq->islandoraSolrResult['response']['objects'] as $object) {
            array_push($this->in_repo_pubs, $object['solr_doc']);
        }
        return $this;
    }

    public function getQueued(){
        $query = db_select($this->queued_table, 'g');
        $query->fields('g');
        $res = $query->execute();
        
        $this->queued_pubs = [];
        foreach ($res as $pub) {
            array_push($this->queued_pubs, $pub);
        }
        
        return $this;
    }

    public function getBlacklisted(){
        return $this;
    }

    public function setNewPubs(){
        $this->setQuery();
        $this->fetch();
        $this->getInRepo();
        $this->removeInRepo();
        $this->getQueued();
        $this->removeQueued();
        $this->insertPubs();
        return $this;
    }
    
    protected function insertPubs(){
        foreach ($this->new_pubs as $key => $pub)
         db_insert($this->queued_table)
        ->fields($this->buildFieldsArray($pub))
        ->execute();
    }
    
    public function getNewPubs(){
        return $this->new_pubs;
    }
    
    private function removeInRepo(){
        foreach ($this->new_pubs as $key => $elem){
            foreach ($this->in_repo_pubs as $repoobj){
                if (in_array($elem['doi'], $repoobj['mods_identifier_doi_ms']) or in_array($elem['eid'], $repoobj['mods_identifier_scopus_ms'])){
                    unset($this->new_pubs[$key]);
                    break;
                }
            }
        }
        return $this;
    }

    private function removeQueued(){
        foreach ($this->new_pubs as $key => $elem){
            foreach ($this->queued_pubs as $key_q => $queued){
                if (($queued->doi == $elem['doi']) or ($queued->eid == $elem['eid'])){
                    unset($this->new_pubs[$key]);
                    break;
                }
            }
        }
        return $this;
    }

    private function removeBlacklisted(){
        return $this;
    }
    
    protected function buildFieldsArray($data){
        return array(
            'title' => (!empty($data['title']) ? $data['title'] : ''),
            'doi' => (!empty($data['doi']) ? $data['doi'] : ''),
            'eid' => (!empty($data['eid']) ? $data['eid'] : ''),
            'type' => (!empty($data['type']) ? $data['type'] : ''),
            'status' => 'queued',
            'tstmp' => REQUEST_TIME,
        );
    }
    
}

class ScopusAlert extends PubAlert{
    
    public function __construct($year = null) {
        module_load_include('php', 'publication_DB', 'lib/MetaDataServants');
        $this->metaDataServant = new ScopusIdListServant();
        $this->metaDataServant->setKey(variable_get('publication_DB_scopus_api_key',''));
        parent::__construct($year);
    }
            
    public function setQuery(){
        $this->metaDataServant->setQuery('('.variable_get('publication_DB_scopus_alert_query', '').') AND PUBYEAR = '.$this->searchYear);
        return $this;
    }
}


class TestAlert extends PubAlert{
    
    public function __construct($year = null) {
                
    }
    
    public function setNewPubs(){
        $this->new_pubs = array(array('doi' => 'Test doi', 'eid' => 'Test eid'));
        $this->insertPubs();
        return $this;
    }
}