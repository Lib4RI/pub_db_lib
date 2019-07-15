<?php

/**
 * Implements hook_form().
 */
function publication_DB_admin_form() {
    $form['publication_DB_scopus_alert'] = array(
        '#type' => 'checkbox',
        '#title' => t('Scopus Alert'),
        '#default_value' => variable_get('publication_DB_scopus_alert', FALSE),
        '#required' => FALSE,
    );
    
    $form['publication_DB_scopus_search_year'] = array(
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
    
    $form['publication_DB_scopus_alert_query'] = array(
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
     
    $form['publication_DB_scopus_api_key'] = array(
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
    
    $form['publication_DB_blacklist_landing'] = array(
        '#type' => 'textfield',
        '#title' => t('Blacklist action landing page'),
        '#default_value' => variable_get('publication_DB_blacklist_landing', ''),
        '#required' => FALSE,
    );

    $form['publication_DB_whitelist_landing'] = array(
        '#type' => 'textfield',
        '#title' => t('Whitelist action landing page'),
        '#default_value' => variable_get('publication_DB_whitelist_landing', ''),
        '#required' => FALSE,
    );

    $form['publication_DB_search_collections'] = array(
        '#type' => 'textfield',
        '#title' => t('Collection to search for existing publications'),
        '#default_value' => variable_get('publication_DB_search_collections', ''),
        '#required' => FALSE,
    );
    
    return system_settings_form($form);
}
