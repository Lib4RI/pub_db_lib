<?php

function get_pmid($dom){
    $out = new DOMDocument('1.0', 'utf-8');
    $xpath = new DOMXPath($dom);
    $filtered = $xpath->query("//record");
    if (!empty($filtered)){
        $element = $out->createElement('pmid', $filtered->item(0)->getAttribute('pmid'));
    }
    else{
        $element = $out->createElement('pmid', 'false');
    }
    $out->appendChild($element);
    return $out;
}
