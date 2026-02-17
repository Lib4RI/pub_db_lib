# Metadata Fetch & Transform — PHP Library
 
A small, **pure-PHP** library that abstracts interaction with multiple metadata providers (CrossRef, PubMed, Scopus, Web of Science, …), and provides a flexible pipeline for transforming and enriching fetched metadata using **XSLT** or **callback** functions.
 
This library was built to power automated publication ingestion for an institutional repository (DORA), and is used by two Drupal/Islandora import modules: one generic Islandora helper module and one site-specific module for our customized installation.
 
# Highlights
 
* Fetch metadata (or IDs) from multiple providers via pluggable *Fetcher* classes.
* Transform & integrate metadata using an ordered chain of XSLT transforms or PHP callback functions.
* Return results as DOMDocument, XML, JSON, or PHP array.
* Simple to extend with new fetchers, processors, XSLTs, and callbacks.
* Intended to be usable standalone in any PHP codebase, or inside Drupal/Islandora import modules.
 
---
 
# Repository layout (example)
 
```
/
├── lib/
│   ├── MetaDataAbstract.php
│   ├── MetaDataFetchers.php
│   ├── MetaDataProcessors.php
│   └── MetaDataServants.php
├── callbacks/
│   └── callbacks.php
├── xslts/
│   ├── pmed2pmed-id.xslt
│   ├── scopus2scopus-id.xslt
│   └── wos2wos-id.xslt
├── modules/
│   ├── islandora_import_module/        # generic Islandora import module
│   └── site_specific_import_module/    # our custom module (site-specific)
└── README.md
```
 
 
# Basic usage examples
 
## 1) Get MODS from a DOI using CrossRef
 
```php
require_once 'lib/MetaDataServants.php';
require_once 'callbacks/callbacks.php';
 
// Create servant for CrossRef -> MODS conversion
$servant = new Crossref2ModsServant();
 
// set pid (optional, for CrossRef contact) and DOI
$servant->setPid('your-contact@example.org')
        ->setDoi('10.1038/s41586-020-2649-2');
 
// Fetch and process (fetch -> callback crossref2mods)
$servant->serve();
 
// Get processed MODS XML
$modsXml = $servant->getProcessedXML();
echo $modsXml;
```
 
## 2) Get PubMed / PubMed Central ID from DOI
 
```php
require_once 'lib/MetaDataServants.php';
 
$pubmed = new PubmedIdServant();
$pubmed->setDoi('10.1001/jama.2017.13737')   // sets DOI for fetcher
       ->fetch();
 
echo $pubmed->getFetchedXML();
```
 
## 3) Get Scopus ID from DOI
 
```php
require_once 'lib/MetaDataServants.php';
 
$sc = new ScopusIdServant();
// set your Elsevier API key in the fetcher headers
$sc->fetcher->setKey('YOUR_ELSEVIER_API_KEY'); // note: you can adjust fetcher directly
$sc->setDoi('10.1038/s41586-020-2649-2')
   ->fetch();
 
print_r($sc->getFetchedArray());
```
 
## 4) Get Web of Science redirect info for DOI
 
```php
require_once 'lib/MetaDataServants.php';
 
$wos = new WosIdServant();
$wos->setDoi('10.1038/s41586-020-2649-2')
    ->fetch();
 
echo $wos->getFetchedXML();
```
 
# Intended workflow (how DORA uses this)
 
1. Periodic or manual queries to external services (Scopus / PubMed / CrossRef / WOS / CrossRef) to fetch *candidate* publication IDs restricted by author affiliation and time-window.
2. Candidate IDs are stored in a staging list for library review.
3. Duplicates (already in repository or staging) are discarded; false-positives can be blacklisted.
4. For each candidate, full metadata is fetched from multiple sources, integrated and shown to the library team for enrichment (affiliations, funder metadata, accepted manuscript upload etc.).
5. Once validated, the publication is ingested into the repository and an email is sent to the author requesting review and, where possible, the accepted manuscript (green OA).
 
The PHP library provides the fetch + transform building blocks used in steps 1–4.
 
# License
 
See the LICENSE file for details.
