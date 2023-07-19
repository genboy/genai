<?php
$openai_api_key = 'xxxxxxxxxxxxxxxxxx'; // your OpenAI API key. Get one from https://platform.openai.com/
$openai_api_url = 'https://api.openai.com/v1/chat/completions'; 
$openai_api_model = 'gpt-3.5-turbo';
$openai_name = 'Neathan';
if( isset($_POST['model_select']) ){
    $openai_api_model = $_POST['model_select']; 
}
if( $openai_api_model  == 'davinci' || $openai_api_model  == 'curie' ){
    $apiUrl = 'https://api.openai.com/v1/engines/'.$openai_api_model.'/completions';
    
    $userInput = $_POST['user_input']; // validate..
    // Prepare the request data
    $requestData = array(
      'prompt' => $userInput, //'User: ' . $userInput . '\nAI:',
      'max_tokens' => 100,
    );
    
    // Send the request to OpenAI API
    $curl = curl_init($apiUrl);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Authorization: Bearer ' . $openai_api_key
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($curl);
    $response = json_decode($response, true);
    curl_close($curl);
    if(isset($response['choices'][0]['text'])){
        $content = $response['choices'][0]['text'];
    }else{
        $content = "Something went wrong! Don't know what, sorry..";
        if (isset($response['error']['message'])) {
            $content = "Something went wrong! Error: ". $response['error']['message'];
        }
    }
    
}else{
    $requestData = array(
        'model' => $openai_api_model,
        'messages' => array(
        array(
            'role' => 'system',
            'content' => 'You are called '.$openai_name.'. You give short, friendly reponses.'
        )
        )
    );
    $text = $_POST['user_input']; // validate..
    array_push($requestData['messages'], array(
        'role' => 'user',
        'content' => $text,
        //'prompt' => 'User: ' . $userInput . '\nAI:',
        //'max_tokens' => 100,
    ));
    // make the call to the OpenAI API
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => $openai_api_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode($requestData),
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $openai_api_key,
        'Content-Type: application/json'
    ),
    ));
    $response = curl_exec($curl);
    $response = json_decode($response, true);
    curl_close($curl);
    if (isset($response['choices'][0]['message']['content'])) {
        // The key exists, do something here
        $content = $response['choices'][0]['message']['content'];
    } else {
        // The key doesn't exist
        $content = "Something went wrong! ```" . json_encode($response) . "```";
    }
}
  
echo $content;
