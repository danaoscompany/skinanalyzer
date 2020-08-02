<?php

class FCM {

	public static function send_message($title, $body, $token, $data) {
		$url = 'https://fcm.googleapis.com/fcm/send';
	    $fields = array(
            'registration_ids' => array($token),
            'notification' => array(
            	'title' => $title,
            	'body' => $body
            )
    	);
    	if (sizeof($data) > 0) {
    		$fields['data'] = $data;
    	}
    	$fields = json_encode($fields);
    	$headers = array (
            'Authorization: key=' . "AAAA1jpZgqw:APA91bHr9mKtyIim6h5y4-hSxaanf5F7EgQmqEJYP-C8EEdQzyEOBgwV9K0TY5ST3F1r4dkBoSVadohGXPx_aKF_-eKXrdfNFLzRCv1LLY-e0Op1ioP0Y1YDlurV3EkqWHIwEwJXC9GS",
            'Content-Type: application/json'
    	);
    	$ch = curl_init ();
    	curl_setopt ( $ch, CURLOPT_URL, $url );
    	curl_setopt ( $ch, CURLOPT_POST, true );
    	curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
    	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
    	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );
	    $result = curl_exec ( $ch );
	    echo $result;
	    curl_close ( $ch );
	}
}
