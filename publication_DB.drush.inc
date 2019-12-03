<?php

/**
 * Implements hook_drush_command().
 */
function publication_DB_drush_command() {
    return array(
        'pub-alert' => array(
            'description' => dt('PDB proxy data update command'),
            'drupal_dependencies' => array('islandora'),
            'examples' => array('drush -u 1 -v publication-alert --operation=<>'),
            'options' => array(
                'operation' => array('description' => dt('Update operation to run')),
                'log' => array('description' => dt('wheter to log executed commands')),
                'log_options' => array('description' => dt('Option relevent to logging')),
            ),
        ),
    );
}


/**
 * Drush command.
 * drush_[MODULE_NAME]_[COMMAND_NAME]().
 */
function drush_publication_DB_pub_alert($log = null, $log_options = null) {
    
    $log_o = drush_get_option('log');
    if ($log_o == 'true'){ //Default to true
        $log = true;
    }
    else{
        $log = false;
    }
            
    $batch = array(
        'operations' => array(
        //            array('publication_DB_scopus_alert', array()),
        ),
        'title' => t('Processing Example Batch'),
        'init_message' => t('Example Batch is starting.'),
        'progress_message' => t('Processed @current out of @total.'),
        'error_message' => t('Example Batch has encountered an error.'),
        'file' => drupal_get_path('module', 'publication_DB') . '/publication_DB.module',
    );
    
    //Populating batch operation
    if (variable_get('publication_DB_scopus_alert')){
        //        $AlertClass = new ScopusAlert();
        //        array_push($batch['operations'], array('publication_DB_alert', array($AlertClass)));
        array_push($batch['operations'], array('publication_DB_scopus_alert', array()));
    }
    
    batch_set($batch);
    drush_backend_batch_process();
}
