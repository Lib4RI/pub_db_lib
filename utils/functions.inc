<?php

function publication_DB_in_dora($indora, $list){
    $out = $list;
    foreach ($out as $elem){
        foreach ($indora as $doraobj){
            if (in_array($elem['doi'], $doraobj['mods_identifier_doi_ms']) or in_array($elem['eid'], $doraobj['mods_identifier_scopus_ms'])){
                unset($elem);
                break;
            }
        }
    }
    return $out;
}