<?php
/**
 * Template Name: Nafathcallback Page
 */

 $data = [];
 $NafathHeaders = [];
 $entityBody = file_get_contents('php://input');
 
 $log = new WC_Logger(); 
 $log->add('Nafath_2', 'NafathBOdy: ' .$entityBody);
 $log->add('Nafath_2', 'NafathHeaders: ' . print_r(getallheaders(), true));
 $data = [];

if( ! empty($entityBody) ) {

     $nafath_respons = json_decode( $entityBody );
     $response       = explode('.', $nafath_respons->response);
     $userInfo       = ag_urlsafeB64Decode($response[1]);
     $userInfo       = json_decode($userInfo);

     $data['userInfo'] = $userInfo->user_info;
     $data['response'] = $nafath_respons->response;
     $data['transId']  = $nafath_respons->transId;
     $data['cardId']   = isset($userInfo->user_info->id) ? $userInfo->user_info->id : '';
     $data['status']   = $nafath_respons->status;
    
     $NafathDB = new NafathDB();
    
     $NafathDB->update_nafath_callback($data);
 }
