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
    
    $form['publication_DB_tabs']['publication_DB_scopus_alert_tab']['publication_DB_scopus_search_year_start'] = array(
        '#type' => 'textfield',
        '#title' => t('Search Year'),
        '#default_value' => variable_get('publication_DB_scopus_search_year_start', ''),
        '#required' => FALSE,
        '#states' => array(
            'visible' => array(
                ':input[name="publication_DB_scopus_alert"]' => array('checked' => TRUE),
            ),
        ),
    );

    $form['publication_DB_tabs']['publication_DB_scopus_alert_tab']['publication_DB_scopus_search_year_stop'] = array(
        '#type' => 'textfield',
        '#title' => t('Search Year'),
        '#default_value' => variable_get('publication_DB_scopus_search_year_stop', ''),
        '#required' => FALSE,
        '#states' => array(
            'visible' => array(
                ':input[name="publication_DB_scopus_alert"]' => array('checked' => TRUE),
            ),
        ),
    );
    
    $form['publication_DB_tabs']['publication_DB_scopus_alert_tab']['publication_DB_scopus_alert_query'] = array(
        '#type' => 'textarea',
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
        '#default_value' => variable_get('publication_DB_queued_url', 'admin/islandora/publication_DB/queued'),
        '#required' => FALSE,
    );
    
    $form['publication_DB_tabs']['publication_DB_generic']['publication_DB_inprocess_url'] = array(
        '#type' => 'textfield',
        '#title' => t('In process publications url'),
        '#default_value' => variable_get('publication_DB_inprocess_url', 'admin/islandora/publication_DB/inprocess'),
        '#required' => FALSE,
    );
    
    $form['publication_DB_tabs']['publication_DB_generic']['publication_DB_blacklisted_url'] = array(
        '#type' => 'textfield',
        '#title' => t('Blacklisted publications url'),
        '#default_value' => variable_get('publication_DB_blacklisted_url', 'admin/islandora/publication_DB/blacklisted'),
        '#required' => FALSE,
    );

    $form['publication_DB_tabs']['publication_DB_generic']['publication_DB_ingested_url'] = array(
        '#type' => 'textfield',
        '#title' => t('Ingested publications url'),
        '#default_value' => variable_get('publication_DB_ingested_url', 'admin/islandora/publication_DB/ingested'),
        '#required' => FALSE,
    );

    $form['publication_DB_tabs']['publication_DB_generic']['publication_DB_inprocess_landing'] = array(
        '#type' => 'textfield',
        '#title' => t('Set in process action landing page'),
        '#default_value' => variable_get('publication_DB_inprocess_landing', ''),
        '#required' => FALSE,
    );
    
    $form['publication_DB_tabs']['publication_DB_generic']['publication_DB_blacklist_landing'] = array(
        '#type' => 'textfield',
        '#title' => t('Blacklist action landing page'),
        '#default_value' => variable_get('publication_DB_blacklist_landing', ''),
        '#required' => FALSE,
    );

    $form['publication_DB_tabs']['publication_DB_generic']['publication_DB_restore_landing'] = array(
        '#type' => 'textfield',
        '#title' => t('Put in queue action landing page'),
        '#default_value' => variable_get('publication_DB_restore_landing', ''),
        '#required' => FALSE,
    );

    $form['publication_DB_tabs']['publication_DB_generic']['publication_DB_restore_landing'] = array(
        '#type' => 'textfield',
        '#title' => t('Put in queue action landing page'),
        '#default_value' => variable_get('publication_DB_restore_landing', ''),
        '#required' => FALSE,
    );
    
    $form['publication_DB_tabs']['publication_DB_generic']['publication_DB_ingested_landing'] = array(
        '#type' => 'textfield',
        '#title' => t('Set ingested action landing page'),
        '#default_value' => variable_get('publication_DB_ingested_landing', ''),
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


    
    $form['publication_DB_tabs']['publication_DB_generic']['publication_DB_search_collections'] = array(
        '#type' => 'textfield',
        '#title' => t('Collection to search for existing publications'),
        '#default_value' => variable_get('publication_DB_search_collections', ''),
        '#required' => FALSE,
    );

    $form['publication_DB_tabs']['publication_DB_generic']['publication_DB_alert_landing'] = array(
        '#type' => 'textfield',
        '#title' => t('Alert batch landing page'),
        '#default_value' => variable_get('publication_DB_alert_landing', ''),
        '#required' => FALSE,
    );
    
    return system_settings_form($form);
}

function publication_DB_run_batch_form() {
    $form['publication_DB_run_batch'] = array(
        '#type' => 'markup',
        '#markup' => l('Run alert batch | ', 'islandora/pubdb/alert'),
    );

    $form['publication_DB_clear_list'] = array(
        '#type' => 'markup',
        '#markup' => l('Clear queued list', 'islandora/pubdb/clear'),
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

function publication_DB_blacklist_form($form, &$form_state){
    $form['publication_DB_blacklist_comments'] = array(
        '#type' => 'textarea',
        '#title' => t('Blacklisting reason'),
        '#required' => FALSE,
    );
    
    $form['submit'] = array(
        '#type' => 'submit',
        '#value' => 'Blacklist',
    );
    
    return $form;
}

function publication_DB_blacklist_form_submit($form, &$form_state){
    module_load_include('module', 'publication_DB', '');
    publication_db_blacklist($form_state['values']['publication_DB_blacklist_comments']);
}