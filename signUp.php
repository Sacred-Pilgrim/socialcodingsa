<?php

$listId = '1234';
$apiKey = 'abcd-DC';

$fName = $_POST['userName'];
$userEmail = $_POST['userEmail'];

function isSetIsNotEmpty($item)
{
    return isset($item) && !empty($item);
}

if (isSetIsNotEmpty($fName) && isSetIsNotEmpty($userEmail)) {

    filter_var($userEmail, FILTER_SANITIZE_EMAIL);
    filter_var($fName, FILTER_SANITIZE_STRING);

    // validate the email address
    if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        echo "The email address you entered is incorrect";
    }

    $data = [
        'apikey' => $apiKey,
        'email_address' => $userEmail,
        'status' => 'subscribed',
        'merge_fields' => [
            'FNAME' => $fName,
        ]
    ];

    $jsonData = json_encode($data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://DC.api.mailchimp.com/3.0/lists/$listId/members/");

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'content-type: application/json',
        'Authorization: apiKey ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

    $result = curl_exec($ch);

    curl_close($ch);

    $jsonResult = json_decode($result);

    if (isset($jsonResult->id)) {
        // successful & added new user
        echo 'Thank you ' . $fName . '. Your subscription went through smoothly';
    } else {
        // successful but user exists or has issues
        echo 'Hmm.. Turns out your request returned with the following error: ' . $jsonResult->title;
    }

} else {
    echo "Error signing Up";
}
