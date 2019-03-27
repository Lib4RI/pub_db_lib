<?php

/**
 * Implements hook_form().
 */
function publication_DB_admin_form() {
    $form['publication_DB_scopus_alert_query'] = array(
        '#type' => 'textfield',
        '#title' => t('Scopus Alert Query'),
        '#default_value' => variable_get('publication_DB_scopus_alert_query', ''),
        '#required' => TRUE,
    );
        
    return system_settings_form($form);
}
