<?php
/*-------------------------------------------------------+
| SYSTOPIA REMOTE EVENT REGISTRATION                     |
| Copyright (C) 2017 SYSTOPIA                            |
| Author: B. Endres (endres@systopia.de)                 |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+--------------------------------------------------------*/


/**
 * Register a contact for the given event
 */
function civicrm_api3_remote_registration_register($params) {
  unset($params['check_permissions']);
  CRM_Revent_APIProcessor::preProcess($params, 'RemoteRegistration.register');
  CRM_Revent_APIProcessor::stripEmptyParameters($params);

  // first: load the  event
  $event_search = array();
  if (!empty($params['event_id'])) {
    $event_search['id'] = $params['event_id'];
  }

  if (!empty($params['event_external_identifier'])) {
    $event_search['remote_event_connection.external_identifier'] = $params['event_external_identifier'];
  }

  if (empty($event_search)) {
    return civicrm_api3_create_error("You have to provide either 'event_id' or 'event_external_identifier'");
  }

  // run the search
  CRM_Revent_CustomData::resolveCustomFields($event_search);
  $event = civicrm_api3('Event', 'get', $event_search + array(
    'option.limit' => 2,
    'return'       => 'id'));
  if ($event['id']) {
    $params['event_id'] = $event['id'];
  } else {
    return civicrm_api3_create_error("Cannot identify event with parameters: " . json_encode($event_search));
  }

  // create a processor
  $processor = new CRM_Revent_RegistrationProcessor($params['event_id']);

  // then: resolve the contact
  $params['contact_id'] = $processor->resolveContact($params);

  // register
  $participant = $processor->registerContact($params);

  // add contact hash
  $contact_data = civicrm_api3('Contact', 'getsingle', array(
    'id'     => $participant['contact_id'],
    'return' => 'hash'));
  $participant['contact_hash'] = $contact_data['hash'];

  return $participant;
}

/**
 * Schedule a Contract modification
 */
function _civicrm_api3_remote_registration_register_spec(&$params) {
  $params['event_external_identifier'] = array(
    'name'         => 'event_external_identifier',
    'api.required' => 0,
    'type'         => CRM_Utils_Type::T_STRING,
    'title'        => 'Event External Identifier',
    );
  $params['event_id'] = array(
    'name'         => 'event_id',
    'api.required' => 0,
    'type'         => CRM_Utils_Type::T_INT,
    'title'        => 'CiviCRM Event ID',
    );
  $params['email'] = array(
    'name'         => 'email',
    'api.required' => 1,
    'type'         => CRM_Utils_Type::T_STRING,
    'title'        => 'Participant Email',
    );
}
