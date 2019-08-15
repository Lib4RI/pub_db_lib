<?php

/**
 * Implements hook_form().
 */
function publication_DB_admin_form() {
    
    $form = array(
        'publication_DB_tabs' => array(
            '#type' => 'vertical_tabs',
            'publication_DB_generic' => array(
                '#type' => 'fieldset',
                '#title' => t('General configuration'),
            ),
            'publication_DB_scopus_alert_tab' => array(
                '#type' => 'fieldset',
                '#title' => t('Scopus Alert'),
            )
            
        )
    );
    
    
    $form['publication_DB_tabs']['publication_DB_scopus_alert_tab']['publication_DB_scopus_alert'] = array(
        '#type' => 'checkbox',
        '#title' => t('Scopus Alert'),
        '#default_value' => variable_get('publication_DB_scopus_alert', FALSE),
        '#required' => FALSE,
    );
    
    $form['publication_DB_tabs']['publication_DB_scopus_alert_tab']['publication_DB_scopus_search_year'] = array(
        '#type' => 'textfield',
        '#title' => t('Search Year'),
        '#default_value' => variable_get('publication_DB_scopus_search_year', ''),
        '#required' => FALSE,
        '#states' => array(
            'visible' => array(
                ':input[name="publication_DB_scopus_alert"]' => array('checked' => TRUE),
            ),
        ),
    );
    
    $form['publication_DB_tabs']['publication_DB_scopus_alert_tab']['publication_DB_scopus_alert_query'] = array(
        '#type' => 'textfield',
        '#title' => t('Scopus Alert Query'),
        '#default_value' => variable_get('publication_DB_scopus_alert_query', ''),
        '#required' => FALSE,
        '#states' => array(
            'visible' => array(
                ':input[name="publication_DB_scopus_alert"]' => array('checked' => TRUE),
            ),
        ),
    );
     
    $form['publication_DB_tabs']['publication_DB_scopus_alert_tab']['publication_DB_scopus_api_key'] = array(
        '#type' => 'textfield',
        '#title' => t('Scopus API key'),
        '#default_value' => variable_get('publication_DB_scopus_api_key', ''),
        '#required' => FALSE,
        '#states' => array(
            'visible' => array(
                ':input[name="publication_DB_scopus_alert"]' => array('checked' => TRUE),
            ),
        ),
    );

    $form['publication_DB_tabs']['publication_DB_generic']['publication_DB_queued_url'] = array(
        '#type' => 'textfield',
        '#title' => t('Queued publications url'),
        '#default_value' => variable_get('publication_DB_queued_url', ''),
        '#required' => FALSE,
    );
    
    $form['publication_DB_tabs']['publication_DB_generic']['publication_DB_inprocess_url'] = array(
        '#type' => 'textfield',
        '#title' => t('In process publications url'),
        '#default_value' => variable_get('publication_DB_inprocess_url', ''),
        '#required' => FALSE,
    );
    
    $form['publication_DB_tabs']['publication_DB_generic']['publication_DB_blacklisted_url'] = array(
        '#type' => 'textfield',
        '#title' => t('Blacklisted publications url'),
        '#default_value' => variable_get('publication_DB_blacklisted_url', ''),
        '#required' => FALSE,
    );

    $form['publication_DB_tabs']['publication_DB_generic']['publication_DB_ingested_url'] = array(
        '#type' => 'textfield',
        '#title' => t('Ingested publications url'),
        '#default_value' => variable_get('publication_DB_ingested_url', ''),
        '#required' => FALSE,
    );
    
    $form['publication_DB_tabs']['publication_DB_generic']['publication_DB_blacklist_landing'] = array(
        '#type' => 'textfield',
        '#title' => t('Blacklist action landing page'),
        '#default_value' => variable_get('publication_DB_blacklist_landing', ''),
        '#required' => FALSE,
    );

    $form['publication_DB_tabs']['publication_DB_generic']['publication_DB_whitelist_landing'] = array(
        '#type' => 'textfield',
        '#title' => t('Whitelist action landing page'),
        '#default_value' => variable_get('publication_DB_whitelist_landing', ''),
        '#required' => FALSE,
    );

    $form['publication_DB_tabs']['publication_DB_generic']['publication_DB_remove_landing'] = array(
        '#type' => 'textfield',
        '#title' => t('Remove action landing page'),
        '#default_value' => variable_get('publication_DB_remove_landing', ''),
        '#required' => FALSE,
    );

    $form['publication_DB_tabs']['publication_DB_generic']['publication_DB_restore_landing'] = array(
        '#type' => 'textfield',
        '#title' => t('Restore action landing page'),
        '#default_value' => variable_get('publication_DB_restore_landing', ''),
        '#required' => FALSE,
    );
    
    $form['publication_DB_tabs']['publication_DB_generic']['publication_DB_search_collections'] = array(
        '#type' => 'textfield',
        '#title' => t('Collection to search for existing publications'),
        '#default_value' => variable_get('publication_DB_search_collections', ''),
        '#required' => FALSE,
    );
    
    return system_settings_form($form);
}

function publication_DB_run_batch_form() {
    $form['publication_DB_run_batch'] = array(
        '#type' => 'markup',
        '#markup' => l('Run alert batch', 'islandora/pubdb/alert'),
    );
    
    return($form);
}

function publication_DB_lists_form() {
        $form['publication_DB_queued'] = array(
        '#type' => 'markup',
        '#markup' => l('Queued publications | ', variable_get('publication_DB_queued_url')),
    );

    $form['publication_DB_inprocess'] = array(
        '#type' => 'markup',
        '#markup' => l('In process publications | ', variable_get('publication_DB_inprocess_url')),
    );

    $form['publication_DB_blacklisted'] = array(
        '#type' => 'markup',
        '#markup' => l('Blacklisted publications | ', variable_get('publication_DB_blacklisted_url')),
    );

    $form['publication_DB_ingested'] = array(
        '#type' => 'markup',
        '#markup' => l('Ingested publications', variable_get('publication_DB_ingested_url')),
    );
    
    return($form);
}