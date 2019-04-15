<?php
/**
 * @file
 * Views hook implementations.
 */

/**
 * Implements hook_views_data().
 */
 
function publication_DB_views_data() {
    $data = array();
    
    $data['publication_DB_scopus_alert_list'] = array(
        'table' => array(
            'group' => t('Scopus Alert'),
            'base' => array(
                'field' => 'id',
                'title' => t('Scopus alert queued'),
                'help' => t(''),
            ),
        ),  
        'id' => array(
            'title' => t('ID'),
            'help' => t(''),
            'field' => array(
                'handler' => 'views_handler_field_numeric',
                'click sortable' => TRUE,
            ),
            'filter' => array(
                'handler' => 'views_handler_filter_numeric',
            ),
            'sort' => array(
                'handler' => 'views_handler_sort',
            ),
        ),
        'tstmp' => array(
            'title' => t('Fetched Date'),
            'help' => t('The date on which the given pub was fetched.'),
            'field' => array(
                'handler' => 'views_handler_field_date',
                'click sortable' => TRUE,
            ),
            'sort' => array(
                'handler' => 'views_handler_sort_date',
            ),
            'filter' => array(
                'handler' => 'views_handler_filter_date',
            ),
        ),
        'doi' => array(
            'title' => t('DOI'),
            'help' => t(''),
            'field' => array(
                'handler' => 'views_handler_field_markup',
                'click sortable' => TRUE,
            ),
//             'sort' => array(
//                 'handler' => 'views_handler_sort_date',
//             ),
//             'filter' => array(
//                 'handler' => 'views_handler_filter_date',
//             ),
        ),
        'eid' => array(
            'title' => t('Scopus ID'),
            'help' => t(''),
            'field' => array(
                'handler' => 'views_handler_field_markup',
                'click sortable' => TRUE,
            ),
            //             'sort' => array(
                //                 'handler' => 'views_handler_sort_date',
                //             ),
            //             'filter' => array(
                //                 'handler' => 'views_handler_filter_date',
            //             ),
            ),
        
        'status' => array(
            'title' => t('Pub status'),
            'help' => t(''),
            'field' => array(
                'handler' => 'views_handler_field_markup',
                'click sortable' => TRUE,
            ),
            //             'sort' => array(
            //                 'handler' => 'views_handler_sort_date',
            //             ),
                        'filter' => array(
                            'handler' => 'views_handler_filter_string',
                        ),
        ),
        
    );
    

//     $data['islandora_batch_queue'] = array(
//         'table' => array(
//             'group' => t('Scopus alert'),
//             'base' => array(
//                 'field' => 'id',
//                 'title' => t('Scopus alert queued'),
//                 'help' => t('The queue of objects to batch ingest.'),
//             ),
//             'join' => array(
//                 'islandora_batch_queue' => array(
//                     'left_field' => 'id',
//                     'field' => 'parent',
//                 ),
//                 'islandora_batch_set' => array(
//                     'left_field' => 'id',
//                     'field' => 'sid',
//                 ),
//             ),
//         ),
//         'id' => array(
//             'title' => t('ID'),
//             'help' => t('Identifier of an item in the queue.'),
//             'field' => array(
//                 'handler' => 'views_handler_field',
//                 // This is use by the table display plugin.
//                 'click sortable' => TRUE,
//             ),
//             'sort' => array(
//                 'handler' => 'views_handler_sort',
//             ),
//             'filter' => array(
//                 'handler' => 'views_handler_filter_string',
//             ),
//             'argument' => array(
//                 'handler' => 'views_handler_argument_string',
//             ),
//         ),
//         'parent' => array(
//             'title' => t('Parent'),
//             'help' => t('Identifier of the parent of the current item.'),
//             'field' => array(
//                 'handler' => 'views_handler_field',
//                 // This is use by the table display plugin.
//                 'click sortable' => TRUE,
//             ),
//             'sort' => array(
//                 'handler' => 'views_handler_sort',
//             ),
//             'filter' => array(
//                 'handler' => 'views_handler_filter_string',
//             ),
//             'argument' => array(
//                 'handler' => 'views_handler_argument_string',
//             ),
//         ),
//         'sid' => array(
//             'title' => t('Set ID'),
//             'help' => t('ID of the set in which an item resides.'),
//             'field' => array(
//                 'handler' => 'views_handler_field_numeric',
//                 'click sortable' => TRUE,
//             ),
//             'filter' => array(
//                 'handler' => 'views_handler_filter_numeric',
//             ),
//             'sort' => array(
//                 'handler' => 'views_handler_sort',
//             ),
//             'argument' => array(
//                 'handler' => 'views_handler_argument_numeric',
//             ),
//             'relationship' => array(
//                 'base' => 'islandora_batch_set',
//                 'base field' => 'id',
//                 'handler' => 'views_handler_relationship',
//                 'label' => t('Associated Set'),
//                 'title' => t('Associated Set'),
//                 'help' => t('The set of objects with which the given object is associated.'),
//             ),
//         ),
//         'actions' => array(
//             'title' => t('Item Actions'),
//             'help' => t('A list of actions which may be performed on an item in the queue.'),
//             'field' => array(
//                 'handler' => 'IslandoraBatchViewsFieldHandlerQueueActions',
//             ),
//         ),
//     );
    
    return $data;
}
