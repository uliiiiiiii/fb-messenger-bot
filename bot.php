<?php 

if (isset($_GET['hub_mode']) && isset($_GET['hub_challenge']) && isset($_GET['hub_verify_token'])) {
		if ($_GET['hub_verify_token'] == '1234') //it can be everything (that's what you write when you set up a webhook on developer page of meta)
			echo $_GET['hub_challenge'];
}else{

$access_token = 'YOUR TOKEN';
$page_id = 'ID'; //you can get this id by this link https://graph.facebook.com/me?access_token=$access_token



$response = file_get_contents('php://input');
$response = json_decode($response, true);

$message = $response['entry'][0]['messaging'][0]['message']['text'];
$id_recepient = $response['entry'][0]['messaging'][0]['sender']['id'];

if (mb_strtolower($message, 'UTF-8') === 'привіт'){
$reply_message = '{
	"messaging_type":"RESPONSE",
	"recipient": {"id": "'.$id_recepient.'"},
	"message":{
		"text": "Привіт! Як справи?"
	} 
}';
}elseif(mb_strtolower($message, 'UTF-8') === 'кіт'){
$reply_message = '{
"recipient":{
    "id":"'.$id_recepient.'"
  },
  "message":{
    "attachment":{
      "type":"template", 
      "payload":{
        "template_type":"button",
        "text":"Оберіть картинку",
        "buttons":[
          {
            "type":"web_url",
            "url":"https://i.natgeofe.com/n/548467d8-c5f1-4551-9f58-6817a8d2c45e/NationalGeographic_2572187_square.jpg",
            "title":"Кіт 1"
          },
          {
            "type":"web_url",
            "url":"https://i.pinimg.com/originals/ba/92/7f/ba927ff34cd961ce2c184d47e8ead9f6.jpg",
            "title":"Кіт 2"
          },
          {
            "type":"web_url",
            "url":"https://i.cbc.ca/1.5359228.1577206958!/fileImage/httpImage/image.jpg_gen/derivatives/16x9_620/smudge-the-viral-cat.jpg",
            "title":"Кіт 3"
          },
        ]
      }
    }
  }
}';

}else{
	$reply_message = '{
	"messaging_type":"RESPONSE",
	"recipient": {"id": "'.$id_recepient.'"},
	"message":{
		"text": "Такої команди немає. Є тільки \"кіт\" та \"привіт\""
	} 
}';
};
function send_reply($reply, $page_id, $access_token){	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v16.0/$page_id/messages?access_token=$access_token");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $reply);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $reply);
	$response = curl_exec($ch);
	file_put_contents('ch.txt', $ch);
	if (curl_errno($ch)) {
		file_put_contents('error.txt', curl_error($ch).curl_errno($ch));
	}
	curl_close($ch);
	$result = json_decode($response, TRUE);
	return $result;
}

send_reply($reply_message, $page_id, $access_token);
}

 ?>