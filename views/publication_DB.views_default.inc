<?php

/**
 * @file
 * Default views hook.
 */

/**
 * Implements hook_views_default_views().
 */
function publication_DB_views_default_views() {
  $views = array();

  $view = new view();
  $view->name = 'alert_queued';
  $view->description = '';
  $view->tag = 'default';
  $view->base_table = 'publication_DB_scopus_alert_list';
  $view->human_name = 'Scopus alert queued';
  $view->core = 7;
  $view->api_version = '3.0';
  $view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */
  
  /* Display: Master */
  $handler = $view->new_display('default', 'Master', 'default');
  $handler->display->display_options['title'] = 'Scopus alert - queued publications';
  $handler->display->display_options['use_more_always'] = FALSE;
  $handler->display->display_options['access']['type'] = 'none';
  $handler->display->display_options['cache']['type'] = 'none';
  $handler->display->display_options['query']['type'] = 'views_query';
  $handler->display->display_options['exposed_form']['type'] = 'basic';
  $handler->display->display_options['pager']['type'] = 'full';
  $handler->display->display_options['pager']['options']['items_per_page'] = '50';
  $handler->display->display_options['pager']['options']['offset'] = '0';
  $handler->display->display_options['pager']['options']['id'] = '0';
  $handler->display->display_options['pager']['options']['quantity'] = '9';
  $handler->display->display_options['pager']['options']['expose']['items_per_page_options_all'] = TRUE;
  $handler->display->display_options['style_plugin'] = 'table';
  $handler->display->display_options['style_options']['columns'] = array(
      'id' => 'id',
      'tstmp' => 'tstmp',
      'eid' => 'eid',
      'tstmp_1' => 'tstmp_1',
  );
  $handler->display->display_options['style_options']['default'] = 'id';
  $handler->display->display_options['style_options']['info'] = array(
      'id' => array(
          'sortable' => 1,
          'default_sort_order' => 'asc',
          'align' => '',
          'separator' => '',
          'empty_column' => 0,
      ),
      'tstmp' => array(
          'sortable' => 0,
          'default_sort_order' => 'asc',
          'align' => '',
          'separator' => '',
          'empty_column' => 0,
      ),
      'eid' => array(
          'sortable' => 0,
          'default_sort_order' => 'asc',
          'align' => '',
          'separator' => '',
          'empty_column' => 0,
      ),
      'tstmp_1' => array(
          'sortable' => 1,
          'default_sort_order' => 'asc',
          'align' => '',
          'separator' => '',
          'empty_column' => 0,
      ),
  );
  $handler->display->display_options['style_options']['sticky'] = TRUE;
  /* Field: Scopus Alert: ID */
  $handler->display->display_options['fields']['id']['id'] = 'id';
  $handler->display->display_options['fields']['id']['table'] = 'publication_DB_scopus_alert_list';
  $handler->display->display_options['fields']['id']['field'] = 'id';
  /* Field: Scopus Alert: DOI */
  $handler->display->display_options['fields']['doi']['id'] = 'doi';
  $handler->display->display_options['fields']['doi']['table'] = 'publication_DB_scopus_alert_list';
  $handler->display->display_options['fields']['doi']['field'] = 'doi';
  /* Field: Scopus Alert: Scopus ID */
  $handler->display->display_options['fields']['eid']['id'] = 'eid';
  $handler->display->display_options['fields']['eid']['table'] = 'publication_DB_scopus_alert_list';
  $handler->display->display_options['fields']['eid']['field'] = 'eid';
  /* Field: Scopus Alert: Fetched Date */
  $handler->display->display_options['fields']['tstmp_1']['id'] = 'tstmp_1';
  $handler->display->display_options['fields']['tstmp_1']['table'] = 'publication_DB_scopus_alert_list';
  $handler->display->display_options['fields']['tstmp_1']['field'] = 'tstmp';
  $handler->display->display_options['fields']['tstmp_1']['date_format'] = 'long';
  $handler->display->display_options['fields']['tstmp_1']['second_date_format'] = 'long';
  /* Field: Scopus Alert: Pub status */
  $handler->display->display_options['fields']['status']['id'] = 'status';
  $handler->display->display_options['fields']['status']['table'] = 'publication_DB_scopus_alert_list';
  $handler->display->display_options['fields']['status']['field'] = 'status';
  $handler->display->display_options['fields']['status']['exclude'] = TRUE;
  /* Field: Global: Custom text */
  $handler->display->display_options['fields']['nothing_1']['id'] = 'nothing_1';
  $handler->display->display_options['fields']['nothing_1']['table'] = 'views';
  $handler->display->display_options['fields']['nothing_1']['field'] = 'nothing';
  $handler->display->display_options['fields']['nothing_1']['label'] = '';
  $handler->display->display_options['fields']['nothing_1']['alter']['text'] = 'Ingest';
  $handler->display->display_options['fields']['nothing_1']['alter']['make_link'] = TRUE;
  $handler->display->display_options['fields']['nothing_1']['alter']['path'] = 'ingest?doi=[doi]&ied=[eid]';
  $handler->display->display_options['fields']['nothing_1']['element_label_colon'] = FALSE;
  /* Field: Global: Custom text */
  $handler->display->display_options['fields']['nothing']['id'] = 'nothing';
  $handler->display->display_options['fields']['nothing']['table'] = 'views';
  $handler->display->display_options['fields']['nothing']['field'] = 'nothing';
  $handler->display->display_options['fields']['nothing']['label'] = '';
  $handler->display->display_options['fields']['nothing']['alter']['text'] = 'Blacklist';
  $handler->display->display_options['fields']['nothing']['alter']['make_link'] = TRUE;
  $handler->display->display_options['fields']['nothing']['alter']['path'] = 'http://localhost/blacklist?doi=[doi]&eid=[eid]';
  $handler->display->display_options['fields']['nothing']['alter']['absolute'] = TRUE;
  $handler->display->display_options['fields']['nothing']['element_label_colon'] = FALSE;
  /* Contextual filter: Global: Null */
  $handler->display->display_options['arguments']['null']['id'] = 'null';
  $handler->display->display_options['arguments']['null']['table'] = 'views';
  $handler->display->display_options['arguments']['null']['field'] = 'null';
  $handler->display->display_options['arguments']['null']['default_argument_type'] = 'fixed';
  $handler->display->display_options['arguments']['null']['summary']['format'] = 'default_summary';
  /* Filter criterion: Scopus Alert: Pub status */
  $handler->display->display_options['filters']['status']['id'] = 'status';
  $handler->display->display_options['filters']['status']['table'] = 'publication_DB_scopus_alert_list';
  $handler->display->display_options['filters']['status']['field'] = 'status';
  $handler->display->display_options['filters']['status']['value'] = 'queued';
  
  /* Display: Block */
  $handler = $view->new_display('block', 'Block', 'block_1');
  
  /* Display: Page */
  $handler = $view->new_display('page', 'Page', 'page_1');
  $handler->display->display_options['path'] = 'bbb';
  $translatables['alert_queued'] = array(
      t('Master'),
      t('Scopus alert - queued publications'),
      t('more'),
      t('Apply'),
      t('Reset'),
      t('Sort by'),
      t('Asc'),
      t('Desc'),
      t('Items per page'),
      t('- All -'),
      t('Offset'),
      t('« first'),
      t('‹ previous'),
      t('next ›'),
      t('last »'),
      t('ID'),
      t('.'),
      t(','),
      t('DOI'),
      t('Scopus ID'),
      t('Fetched Date'),
      t('Pub status'),
      t('Ingest'),
      t('Blacklist'),
      t('All'),
      t('Block'),
      t('Page'),
  );
  

  $views['scopus_alert_queued'] = $view;

  $view = new view();
  $view->name = 'scopus_alert_blacklisted';
  $view->description = '';
  $view->tag = 'default';
  $view->base_table = 'publication_DB_scopus_alert_list';
  $view->human_name = 'Scopus alert blacklisted';
  $view->core = 7;
  $view->api_version = '3.0';
  $view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */
  
  /* Display: Master */
  $handler = $view->new_display('default', 'Master', 'default');
  $handler->display->display_options['title'] = 'Scopus alert - queued publications';
  $handler->display->display_options['use_more_always'] = FALSE;
  $handler->display->display_options['access']['type'] = 'none';
  $handler->display->display_options['cache']['type'] = 'none';
  $handler->display->display_options['query']['type'] = 'views_query';
  $handler->display->display_options['exposed_form']['type'] = 'basic';
  $handler->display->display_options['pager']['type'] = 'full';
  $handler->display->display_options['pager']['options']['items_per_page'] = '50';
  $handler->display->display_options['pager']['options']['offset'] = '0';
  $handler->display->display_options['pager']['options']['id'] = '0';
  $handler->display->display_options['pager']['options']['quantity'] = '9';
  $handler->display->display_options['pager']['options']['expose']['items_per_page_options_all'] = TRUE;
  $handler->display->display_options['style_plugin'] = 'table';
  $handler->display->display_options['style_options']['columns'] = array(
      'id' => 'id',
      'tstmp' => 'tstmp',
      'eid' => 'eid',
      'tstmp_1' => 'tstmp_1',
  );
  $handler->display->display_options['style_options']['default'] = 'id';
  $handler->display->display_options['style_options']['info'] = array(
      'id' => array(
          'sortable' => 1,
          'default_sort_order' => 'asc',
          'align' => '',
          'separator' => '',
          'empty_column' => 0,
      ),
      'tstmp' => array(
          'sortable' => 0,
          'default_sort_order' => 'asc',
          'align' => '',
          'separator' => '',
          'empty_column' => 0,
      ),
      'eid' => array(
          'sortable' => 0,
          'default_sort_order' => 'asc',
          'align' => '',
          'separator' => '',
          'empty_column' => 0,
      ),
      'tstmp_1' => array(
          'sortable' => 1,
          'default_sort_order' => 'asc',
          'align' => '',
          'separator' => '',
          'empty_column' => 0,
      ),
  );
  $handler->display->display_options['style_options']['sticky'] = TRUE;
  /* Field: Scopus Alert: ID */
  $handler->display->display_options['fields']['id']['id'] = 'id';
  $handler->display->display_options['fields']['id']['table'] = 'publication_DB_scopus_alert_list';
  $handler->display->display_options['fields']['id']['field'] = 'id';
  /* Field: Scopus Alert: DOI */
  $handler->display->display_options['fields']['doi']['id'] = 'doi';
  $handler->display->display_options['fields']['doi']['table'] = 'publication_DB_scopus_alert_list';
  $handler->display->display_options['fields']['doi']['field'] = 'doi';
  /* Field: Scopus Alert: Scopus ID */
  $handler->display->display_options['fields']['eid']['id'] = 'eid';
  $handler->display->display_options['fields']['eid']['table'] = 'publication_DB_scopus_alert_list';
  $handler->display->display_options['fields']['eid']['field'] = 'eid';
  /* Field: Scopus Alert: Fetched Date */
  $handler->display->display_options['fields']['tstmp_1']['id'] = 'tstmp_1';
  $handler->display->display_options['fields']['tstmp_1']['table'] = 'publication_DB_scopus_alert_list';
  $handler->display->display_options['fields']['tstmp_1']['field'] = 'tstmp';
  $handler->display->display_options['fields']['tstmp_1']['date_format'] = 'long';
  $handler->display->display_options['fields']['tstmp_1']['second_date_format'] = 'long';
  /* Field: Scopus Alert: Pub status */
  $handler->display->display_options['fields']['status']['id'] = 'status';
  $handler->display->display_options['fields']['status']['table'] = 'publication_DB_scopus_alert_list';
  $handler->display->display_options['fields']['status']['field'] = 'status';
  $handler->display->display_options['fields']['status']['exclude'] = TRUE;
  /* Field: Global: Custom text */
  $handler->display->display_options['fields']['nothing_1']['id'] = 'nothing_1';
  $handler->display->display_options['fields']['nothing_1']['table'] = 'views';
  $handler->display->display_options['fields']['nothing_1']['field'] = 'nothing';
  $handler->display->display_options['fields']['nothing_1']['label'] = '';
  $handler->display->display_options['fields']['nothing_1']['alter']['text'] = 'Ingest';
  $handler->display->display_options['fields']['nothing_1']['alter']['make_link'] = TRUE;
  $handler->display->display_options['fields']['nothing_1']['alter']['path'] = 'ingest?doi=[doi]&ied=[eid]';
  $handler->display->display_options['fields']['nothing_1']['element_label_colon'] = FALSE;
  /* Field: Global: Custom text */
  $handler->display->display_options['fields']['nothing']['id'] = 'nothing';
  $handler->display->display_options['fields']['nothing']['table'] = 'views';
  $handler->display->display_options['fields']['nothing']['field'] = 'nothing';
  $handler->display->display_options['fields']['nothing']['label'] = '';
  $handler->display->display_options['fields']['nothing']['alter']['text'] = 'Blacklist';
  $handler->display->display_options['fields']['nothing']['alter']['make_link'] = TRUE;
  $handler->display->display_options['fields']['nothing']['alter']['path'] = 'http://localhost/blacklist?doi=[doi]&eid=[eid]';
  $handler->display->display_options['fields']['nothing']['alter']['absolute'] = TRUE;
  $handler->display->display_options['fields']['nothing']['element_label_colon'] = FALSE;
  /* Contextual filter: Global: Null */
  $handler->display->display_options['arguments']['null']['id'] = 'null';
  $handler->display->display_options['arguments']['null']['table'] = 'views';
  $handler->display->display_options['arguments']['null']['field'] = 'null';
  $handler->display->display_options['arguments']['null']['default_argument_type'] = 'fixed';
  $handler->display->display_options['arguments']['null']['summary']['format'] = 'default_summary';
  /* Filter criterion: Scopus Alert: Pub status */
  $handler->display->display_options['filters']['status']['id'] = 'status';
  $handler->display->display_options['filters']['status']['table'] = 'publication_DB_scopus_alert_list';
  $handler->display->display_options['filters']['status']['field'] = 'status';
  $handler->display->display_options['filters']['status']['value'] = 'blacklisted';
  $handler->display->display_options['filters']['status']['expose']['operator_id'] = 'status_op';
  $handler->display->display_options['filters']['status']['expose']['label'] = 'Pub status';
  $handler->display->display_options['filters']['status']['expose']['operator'] = 'status_op';
  $handler->display->display_options['filters']['status']['expose']['identifier'] = 'status';
  $handler->display->display_options['filters']['status']['expose']['remember_roles'] = array(
      2 => '2',
      1 => 0,
      4 => 0,
      5 => 0,
      3 => 0,
      6 => 0,
      7 => 0,
      8 => 0,
  );
  
  /* Display: Block */
  $handler = $view->new_display('block', 'Block', 'block_1');
  
  /* Display: Page */
  $handler = $view->new_display('page', 'Page', 'page_1');
  $handler->display->display_options['path'] = 'bbb';
  $translatables['scopus_alert_blacklisted'] = array(
      t('Master'),
      t('Scopus alert - queued publications'),
      t('more'),
      t('Apply'),
      t('Reset'),
      t('Sort by'),
      t('Asc'),
      t('Desc'),
      t('Items per page'),
      t('- All -'),
      t('Offset'),
      t('« first'),
      t('‹ previous'),
      t('next ›'),
      t('last »'),
      t('ID'),
      t('.'),
      t(','),
      t('DOI'),
      t('Scopus ID'),
      t('Fetched Date'),
      t('Pub status'),
      t('Ingest'),
      t('Blacklist'),
      t('All'),
      t('Block'),
      t('Page'),
  );
  
  $views['scopus_alert_blacklisted'] = $view;
  
  return $views;
}
