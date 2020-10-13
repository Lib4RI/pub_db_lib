<?php

include '../lib/MetaDataFetchers.php';
include '../lib/MetaDataProcessors.php';

date_default_timezone_set('UTC');
$fetcher = new ScopusAbstractFetcher();
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

if ($fetcher->getErrorStatus()){
    fwrite(STDERR, date('Y-m-d H:i:s').' - '.$argv[2].' - '.$fetcher->getErrorMessage()."\n");
    exit;
}

$processor = new MetaDataProcessor(null);
$processor->loadDom($fetcher->fetch()->getDom());
$processor->addSteps(array('type' => 'callback', 'rule' => 'get_scopus_authorsaffiliations', 'params' => array()))->process();

//echo $processor->getDom()->saveXML($processor->getDom()->firstChild->firstChild);
echo $processor->getString();

