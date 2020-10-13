<?php

include '../lib/MetaDataFetchers.php';
include '../lib/MetaDataProcessors.php';

$fetcher = new ElsevierArticleFetcher();
$fetcher->setKey('YOUR_KEY_HERE');
//$fetcher->setDoi('10.1016/j.ijcard.2017.02.078');

switch ($argv[1]){
    case "doi":
        $fetcher->setDoi($argv[2]);
        break;
    case "scopus":
        $fetcher->setEid($argv[2]);
        break;
    case "eid":
        $fetcher->setEid($argv[2]);
        break;
}

$fetcher->getDom()->formatOutput = true;
$fetcher->fetch();


$processor = new MetaDataProcessor(null);
$processor->loadDom($fetcher->fetch()->getDom());
$processor->addSteps(array('type' => 'callback', 'rule' => 'get_else_authorsaffiliations', 'params' => array()))->process();

//echo $processor->getDom()->saveXML($processor->getDom()->firstChild->firstChild);
echo $processor->getString();

