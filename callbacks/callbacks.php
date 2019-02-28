<?php
function add_element($dom, $params){
    if (!isset($params['xquery'])){
        $target_node = $dom;
    }
    else{
        $target_node = $dom;
    }
    
    $node = $params['element'];
    $node = $target_node->importNode($node,true);
    $target_node->getElementsByTagName($params['parent'])[0]->appendChild($node);
    return $dom;
}

function get_pmid($dom, $params){
    $out = new DOMDocument('1.0', 'utf-8');
    $xpath = new DOMXPath($dom);
    $filtered = $xpath->query("//record");
    $root_element = $out->createElement('result');
    $out->appendChild($root_element);
    if (!empty($filtered)){
        $element = $out->createElement('pmid', $filtered->item(0)->getAttribute('pmid'));
    }
    else{
        $element = $out->createElement('pmid', 'false');
    }
    $root_element->appendChild($element);
    return $out;
}

function crossref2mods($doi_xml, $params){
    // Create MODS XML.
    $mods = new DOMDocument('1.0');
    $mods->loadXML('<mods xmlns="http://www.loc.gov/mods/v3" xmlns:mods="http://www.loc.gov/mods/v3" xmlns:xlink="http://www.w3.org/1999/xlink"/>');
    
    // Add metadata for journal articles.
    foreach ($doi_xml->getElementsByTagName('journal') as $journal) {
        //    $genre = $mods->createElement('genre');
        //    $genre->nodeValue = 'article';
        //    $mods->firstChild->appendChild($genre);
        
        $relateditem = $mods->createElement('relatedItem');
        $mods->firstChild->appendChild($relateditem);
        $relateditem_attribute = $mods->createAttribute('type');
        $relateditem_attribute->value = 'host';
        $relateditem->appendChild($relateditem_attribute);
        
        foreach ($journal->getElementsByTagName('journal_metadata') as $metadata) {
            foreach ($metadata->childNodes as $child) {
                switch ($child->nodeName) {
                    case 'full_title':
                        $titleinfo = $mods->createElement('titleInfo');
                        $relateditem->appendChild($titleinfo);
                        $title = $mods->createElement('title');
                        $titleinfo->appendChild($title);
                        if (!is_null($child->firstChild)) {
                            $title_text = $mods->importNode($child->firstChild);
                            $title->appendChild($title_text);
                        }
                        break;
                        
                    case 'abbrev_title':
                        $titleinfo = $mods->createElement('titleInfo');
                        $titleinfo_attribute = $mods->createAttribute('type');
                        $titleinfo_attribute->value = 'abbreviated';
                        $titleinfo->appendChild($titleinfo_attribute);
                        $relateditem->appendChild($titleinfo);
                        $title = $mods->createElement('title');
                        $titleinfo->appendChild($title);
                        if (!is_null($child->firstChild)) {
                            $title_text = $mods->importNode($child->firstChild);
                            $title->appendChild($title_text);
                        }
                        break;
                        
                    case 'issn':
                        $identifier = $mods->createElement('identifier');
                        $identifier_attribute = $mods->createAttribute('type');
                        $identifier_attribute_value = 'issn';
                        if ($child->hasAttributes()) {
                            foreach ($child->attributes as $att) {
                                switch ($att->name) {
                                    case 'media_type':
                                        if ($att->value == 'electronic') {
                                            $identifier_attribute_value = 'e-issn';
                                        }
                                        break;
                                }
                            }
                        }
                        $identifier_attribute->value = $identifier_attribute_value;
                        $identifier->appendChild($identifier_attribute);
                        $relateditem->appendChild($identifier);
                        if (!is_null($child->firstChild)) {
                            $identifier_text = $mods->importNode($child->firstChild);
                            $identifier->appendChild($identifier_text);
                        }
                        break;
                        
                    case 'e-issn': // possibly unnecessary...
                        $identifier = $mods->createElement('identifier');
                        $identifier_attribute = $mods->createAttribute('type');
                        $identifier_attribute->value = 'e-issn';
                        $identifier->appendChild($identifier_attribute);
                        $relateditem->appendChild($identifier);
                        if (!is_null($child->firstChild)) {
                            $identifier_text = $mods->importNode($child->firstChild);
                            $identifier->appendChild($identifier_text);
                        }
                        break;
                }
            }
        }
        foreach ($journal->getElementsByTagName('journal_issue') as $issue) {
            if ($issue->hasChildNodes()) {
                foreach ($issue->childNodes as $child) {
                    switch ($child->nodeName) {
                        case 'publication_date':
                            if ($child->hasChildNodes()) {
                                foreach ($child->childNodes as $date_part) {
                                    switch ($date_part->nodeName) {
                                        case 'month':
                                            $pubdate_month = $date_part->nodeValue;
                                            break;
                                            
                                        case 'year':
                                            $pubdate_year = $date_part->nodeValue;
                                            break;
                                            
                                        case 'day':
                                            $pubdate_day = $date_part->nodeValue;
                                            break;
                                    }
                                }
                            }
                            break;
                            
                        case 'journal_volume':
                            if ($child->hasChildNodes()) {
                                foreach ($child->childNodes as $volume) {
                                    switch ($volume->nodeName) {
                                        case 'volume':
                                            if (!is_null($volume->firstChild)) {
                                                $volume_number = $mods->importNode($volume->firstChild);
                                            }
                                            break;
                                    }
                                }
                            }
                            break;
                            
                        case 'issue':
                            if (!is_null($child->firstChild)) {
                                $issue_number = $mods->importNode($child->firstChild);
                            }
                            break;
                    }
                }
            }
        }
        foreach ($journal->getElementsByTagName('journal_article') as $article) {
            if ($article->hasAttributes()) {
                foreach ($article->attributes as $att) {
                    switch ($att->name) {
                        case 'language':
                            $language = $mods->createElement('language');
                            $mods->firstChild->appendChild($language);
                            $languageterm = $mods->createElement('languageTerm');
                            $language->appendChild($languageterm);
                            $language_text = $mods->createTextNode($att->value);
                            $languageterm->appendChild($language_text);
                            if (strlen($att->value) == 2) {
                                $type_attribute = $mods->createAttribute('type');
                                $type_attribute->value = 'code';
                                $languageterm->appendChild($type_attribute);
                                $authority_attribute = $mods->createAttribute('authority');
                                $authority_attribute->value = 'rfc4646';
                                $languageterm->appendChild($authority_attribute);
                            }
                            break;
                    }
                }
            }
            if ($article->hasChildNodes()) {
                foreach ($article->childNodes as $child) {
                    switch ($child->nodeName) {
                        case 'titles':
                            $titleinfo = $mods->createElement('titleInfo');
                            $mods->firstChild->appendChild($titleinfo);
                            foreach ($child->childNodes as $grandchild) {
                                switch ($grandchild->nodeName) {
                                    case 'title':
                                        $article_title = $mods->importNode($grandchild, TRUE);
                                        $article_title->nodeValue = strip_tags($article_title->ownerDocument->saveXML($article_title),'<sub><sup>');
                                        $article_title->nodeValue = preg_replace('/[ \t]+/', ' ', preg_replace('/\s*$^\s*/m', "\n", htmlspecialchars($article_title->nodeValue)));
                                        $article_title->nodeValue = preg_replace( "/\r|\n /", "", $article_title->nodeValue);
                                        $titleinfo->appendChild($article_title);
                                        break;
                                        
                                    case 'subtitle':
                                        if (!empty($params['subtitle'])){
                                            $article_subtitle = $mods->createElement('subTitle');
                                            if (!is_null($grandchild->firstChild)) {
                                                $article_subtitle_text = $mods->importNode($grandchild->firstChild, TRUE);
                                                $article_subtitle->appendChild($article_subtitle_text);
                                            }
                                            $titleinfo->appendChild($article_subtitle);
                                        }
                                        break;
                                        
                                    case 'original_language_title':
                                        $translated_titleinfo = $mods->createElement('titleInfo');
                                        $mods->firstChild->appendChild($translated_titleinfo);
                                        $titleinfo_attribute = $mods->createAttribute('type');
                                        $titleinfo_attribute->value = 'translated';
                                        $translated_titleinfo->appendChild($titleinfo_attribute);
                                        foreach ($grandchild->attributes as $att) {
                                            switch ($att->nodeName) {
                                                case 'language':
                                                    $titleinfo_attribute = $mods->createAttribute('lang');
                                                    $titleinfo_attribute->value = $att->nodeValue;
                                                    $translated_titleinfo->appendChild($titleinfo_attribute);
                                                    break;
                                            }
                                        }
                                        
                                        $translated_title = $mods->createElement('title');
                                        $translated_titleinfo->appendChild($translated_title);
                                        if (!is_null($grandchild->firstChild)) {
                                            $translated_title_text = $mods->importNode($grandchild->firstChild);
                                            $translated_title->appendChild($translated_title_text);
                                        }
                                        break;
                                }
                            }
                            break;
                            
                        case 'contributors':
                            foreach ($child->getElementsByTagName('organization') as $organization) {
                                $name = $mods->createElement('name');
                                $mods->firstChild->appendChild($name);
                                $name_attribute = $mods->createAttribute('type');
                                $name_attribute->value = 'corporate';
                                $name->appendChild($name_attribute);
                                $namepart = $mods->createElement('namePart');
                                $name->appendChild($namepart);
                                if (!is_null($organization->firstChild)) {
                                    $namepart_text = $mods->importNode($organization->firstChild);
                                    $namepart->appendChild($namepart_text);
                                }
                            }
                            
                            foreach ($child->getElementsByTagName('person_name') as $person) {
                                if ((strtolower($person->getAttribute('contributor_role')) != 'editor') || !empty($params['editor'])){
                                    
                                    $name = $mods->createElement('name');
                                    $mods->firstChild->appendChild($name);
                                    $name_attribute = $mods->createAttribute('type');
                                    $name_attribute->value = 'personal';
                                    $name->appendChild($name_attribute);
                                    
                                    foreach ($person->childNodes as $person_part) {
                                        switch ($person_part->nodeName) {
                                            case 'given_name':
                                                $namepart = $mods->createElement('namePart');
                                                $name->appendChild($namepart);
                                                $namepart_attribute = $mods->createAttribute('type');
                                                $namepart_attribute->value = 'given';
                                                $namepart->appendChild($namepart_attribute);
                                                if (!is_null($person_part->firstChild)) {
                                                    $given_name_part = $mods->importNode($person_part->firstChild);
                                                    $namepart->appendChild($given_name_part);
                                                }
                                                break;
                                                
                                            case 'surname':
                                                $namepart = $mods->createElement('namePart');
                                                $name->appendChild($namepart);
                                                $namepart_attribute = $mods->createAttribute('type');
                                                $namepart_attribute->value = 'family';
                                                $namepart->appendChild($namepart_attribute);
                                                if (!is_null($person_part->firstChild)) {
                                                    $surname_part = $mods->importNode($person_part->firstChild);
                                                    $namepart->appendChild($surname_part);
                                                }
                                                break;
                                                
                                            case 'suffix':
                                                $terms_of_address = $mods->createElement('namePart');
                                                $name->appendChild($terms_of_address);
                                                if (!is_null($person_part->firstChild)) {
                                                    $terms_of_address_text = $mods->importNode($person_part->firstChild);
                                                    $terms_of_address->appendChild($terms_of_address_text);
                                                }
                                                $terms_of_address_attribute = $mods->createAttribute('type');
                                                $terms_of_address_attribute->value = 'termsOfAddress';
                                                $terms_of_address->appendChild($terms_of_address_attribute);
                                                break;
                                                
                                                // HK, not required
                                                /*
                                                 case 'affiliation':
                                                 $affiliation = $mods->createElement('affiliation');
                                                 $name->appendChild($affiliation);
                                                 if (!is_null($person_part->firstChild)) {
                                                 $affiliation_text = $mods->importNode($person_part->firstChild);
                                                 $affiliation->appendChild($affiliation_text);
                                                 }
                                                 break;
                                                 */
                                        }
                                    }
                                    
                                    foreach ($person->attributes as $att) {
                                        switch ($att->nodeName) {
                                            case 'contributor_role':
                                               if ((strtolower($att->nodeValue) != 'editor') || !empty($params['editor'])){
                                                $role = $mods->createElement('role');
                                                $name->appendChild($role);
                                                $roleterm = $mods->createElement('roleTerm');
                                                $role->appendChild($roleterm);
                                                $roleterm_text = $mods->createTextNode(strtolower($att->nodeValue));
                                                $roleterm->appendChild($roleterm_text);
                                                
                                                $roles = array('author', 'editor', 'translator');
                                                if (in_array(strtolower($att->nodeValue), $roles)) {
                                                    $roleterm_attribute = $mods->createAttribute('authority');
                                                    $roleterm_attribute->value = 'marcrelator';
                                                    $roleterm->appendChild($roleterm_attribute);
                                                    $roleterm_attribute = $mods->createAttribute('type');
                                                    $roleterm_attribute->value = 'text';
                                                    $roleterm->appendChild($roleterm_attribute);
                                                }
                                            }
                                                break;
                                                
                                            case 'sequence':
                                                if (strcmp('first', strtolower($att->nodeValue)) == 0) {
                                                    $name_attribute = $mods->createAttribute('usage');
                                                    $name_attribute->value = 'primary';
                                                    $name->appendChild($name_attribute);
                                                }
                                        }
                                    }
                                }
                            }
                            break;
                            
                        case 'publication_date':
                            // Note that this overwrites the publication date that came
                            // from the journal metadata above.
                            if ($child->hasChildNodes()) {
                                foreach ($child->childNodes as $date_part) {
                                    switch ($date_part->nodeName) {
                                        case 'month':
                                            $pubdate_month = $date_part->nodeValue;
                                            break;
                                            
                                        case 'year':
                                            $pubdate_year = $date_part->nodeValue;
                                            break;
                                            
                                        case 'day':
                                            $pubdate_day = $date_part->nodeValue;
                                            break;
                                    }
                                }
                            }
                            break;
                            
                        case 'pages':
                            if ($child->hasChildNodes()) {
                                foreach ($child->childNodes as $page_part) {
                                    switch ($page_part->nodeName) {
                                        case 'first_page':
                                            if (!is_null($page_part->firstChild)) {
                                                $first_page = $mods->importNode($page_part->firstChild);
                                            }
                                            break;
                                            
                                        case 'last_page':
                                            if (!is_null($page_part->firstChild)) {
                                                $last_page = $mods->importNode($page_part->firstChild);
                                            }
                                            break;
                                            
                                        case 'other_pages':
                                            if (!is_null($page_part->firstChild)) {
                                                $other_pages = $mods->importNode($page_part->firstChild);
                                            }
                                            break;
                                    }
                                }
                            }
                            break;
                            
                        case 'publisher_item':
                            // @todo
                            break;
                            
                        case 'crossmark':
                            // @todo
                            break;
                            
                        case 'doi_data':
                            if ($child->hasChildNodes()) {
                                foreach ($child->childNodes as $identifier) {
                                    switch ($identifier->nodeName) {
                                        case 'doi':
                                            if (!is_null($identifier->firstChild)) {
                                                $doi = $mods->createElement('identifier');
                                                $mods->firstChild->appendChild($doi);
                                                $doi_attribute = $mods->createAttribute('type');
                                                $doi_attribute->value = 'doi';
                                                $doi->appendChild($doi_attribute);
                                                $doi_text = $mods->importNode($identifier->firstChild);
                                                $doi->appendChild($doi_text);
                                            }
                                            break;
                                            
                                        case 'resource':
                                            if (!is_null($identifier->firstChild) && !empty($params['publisher'])) {
                                                $uri = $mods->createElement('identifier');
                                                $mods->firstChild->appendChild($uri);
                                                $uri_attribute = $mods->createAttribute('type');
                                                $uri_attribute->value = 'uri';
                                                $uri->appendChild($uri_attribute);
                                                $uri_text = $mods->importNode($identifier->firstChild);
                                                $uri->appendChild($uri_text);
                                            }
                                            break;
                                    }
                                }
                            }
                            break;
                            
                        case 'citation_list':
                            // @todo
                            break;
                            
                        case 'component_list':
                            // @todo
                            break;
                    }
                }
            }
        }
        // Add the publication date if it was read in one of the two places above.
        $origininfo = $mods->createElement('originInfo');
        $mods->firstChild->appendChild($origininfo);
        
        if (!empty($pubdate_year)) {
            $date_value = $pubdate_year;
            //       if (!empty($pubdate_month)) {
            //         if (strlen($pubdate_month) == 1) {
            //           $pubdate_month = "0" . $pubdate_month;
            //         }
            //         $date_value .= "-" . $pubdate_month;
            //       }
            //       if (!empty($pubdate_day)) {
            //         if (strlen($pubdate_day) == 1) {
            //           $pubdate_day = "0" . $pubdate_day;
            //         }
            //         $date_value .= "-" . $pubdate_day;
            //       }
            $dateissued = $mods->createElement('dateIssued');
            $dateissued_attribute = $mods->createAttribute('encoding');
            $dateissued_attribute->value = 'w3cdtf';
            $dateissued->appendChild($dateissued_attribute);
            $dateissued_attribute = $mods->createAttribute('keyDate');
            $dateissued_attribute->value = 'yes';
            $dateissued->appendChild($dateissued_attribute);
            $origininfo->appendChild($dateissued);
            $dateissued->nodeValue = $date_value;
}

// Add the page, volume, and issue data if it was read above.
if (!empty($volume_number) || !empty($issue_number) || !empty($other_pages) || !empty($last_page) || !empty($first_page) || !empty($pubdate_year) || !empty($pubdate_month) || !empty($pubdate_day)) {
    $part = $mods->createElement('part');
    /*HK: Added $part under relatedItem type host*/
    $relateditem->appendChild($part);
    
    if (!empty($last_page) || !empty($first_page) || !empty($other_pages)) {
        $extent = $mods->createElement('extent');
        $part->appendChild($extent);
        $extent_attribute = $mods->createAttribute('unit');
        $extent_attribute->value = 'page';
        $extent->appendChild($extent_attribute);
        
        if (!empty($other_pages)) {
            $list = $mods->createElement('list');
            $list->appendChild($other_pages);
            $extent->appendChild($list);
        }
        if (!empty($first_page)) {
            $start = $mods->createElement('start');
            $start->appendChild($first_page);
            $extent->appendChild($start);
        }
        if (!empty($last_page)) {
            $end = $mods->createElement('end');
            $end->appendChild($last_page);
            $extent->appendChild($end);
        }
    }
    
    if (!empty($volume_number)) {
        $volume = $mods->createElement('detail');
        $part->appendChild($volume);
        $volume_attribute = $mods->createAttribute('type');
        $volume_attribute->value = 'volume';
        $volume->appendChild($volume_attribute);
        $number = $mods->createElement('number');
        $volume->appendChild($number);
        $number->appendChild($volume_number);
    }
    if (!empty($issue_number)) {
        $issue = $mods->createElement('detail');
        $part->appendChild($issue);
        $issue_attribute = $mods->createAttribute('type');
        $issue_attribute->value = 'issue';
        $issue->appendChild($issue_attribute);
        $number = $mods->createElement('number');
        $issue->appendChild($number);
        $number->appendChild($issue_number);
    }
    
    /*
     if (!empty($date_value)) {
     $date = $mods->createElement('date');
     $date->nodeValue = $date_value;
     $part->appendChild($date);
     }
     */
}
// Return after first instance.
return $mods;
}

}