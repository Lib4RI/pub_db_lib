<?php

include '../lib/MetaDataServants.php';

$serv = new ScopusIdServant();
$serv->setKey('YOUR_KEY_HERE');
//$fetcher->setDoi('10.1016/j.ijcard.2017.02.078');
$serv->setTitle($argv[1]);
//$serv->getProcessedDom()->formatOutput = true;
$serv->serve();

//echo $serv->getProcessedXML();
//echo $serv->getProcessor()->getDom()->firstChild->firstChild->nodeValue;
var_dump($serv->getProcessedString());
// echo $serv->getProcessor()->getDom()->saveXML($serv->getProcessor()->getDom()->firstChild->firstChild);
