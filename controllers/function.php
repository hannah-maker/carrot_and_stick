<?php

use Firebase\JWT\JWT;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Google\Auth\ApplicationDefaultCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

$res = (Object)Array();
function getSQLErrorException($errorLogs, $e, $req)
{
    $res = (Object)Array();
    http_response_code(500);
    $res->code = 500;
    $res->message = "SQL Exception -> " . $e->getTraceAsString();
    echo json_encode($res);
    addErrorLogs($errorLogs, $res, $req);
}

function getUserInfoByKakaoToken($accessToken){
    $token = $accessToken;
    $header = "Bearer ".$token; // Bearer 다음에 공백 추가
    $url = "https://kapi.kakao.com/v2/user/me";
    $is_post = false;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, $is_post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $headers = array();
    $headers[] = "Authorization: ".$header;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec ($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close ($ch);

    if($status_code == 200) {
        return array(true, json_decode($response));
    } else {
        return array(false, $response);
    }
}

function isValidHeader($jwt, $key)
{
    try {
        $data = getDataByJWToken($jwt, $key);
        //로그인 함수 직접 구현 요함
        return isValidUser($data->id, $data->pw);
    } catch (\Exception $e) {
        return false;
    }
}

function isValidUser($id, $pw){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM User WHERE id= ? AND pw = ?) AS exist;";


    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id, $pw]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}


function sendFcm($fcmToken, $data)
{
    $url = 'https://fcm.googleapis.com/fcm/send';
    $key = "AAAAKX9eur0:APA91bH0ybAgZWKjWxUnzJwFkVmFbRZnwwD1P30EhWzq0cj370-k5NaE80Kz9djoj-QO9aUiWsTp6fIBtGYoZW8PIJ2EuagQftUQI4disHFE4RzXdDZ7pFSY0gbYVVnlEExweqITbd8M";
    $headers = array(
        'Authorization: key='. $key,
        'Content-Type: application/json'
    );
//
    $fields['data'] = $data;
//    //if ($deviceType == 'IOS') {
        $notification['title'] = $data['title'];
        $notification['body'] = $data['body'];
        $notification['sound'] = 'default';
        $fields['notification'] = $notification;
//    //}

    $fields['to'] = $fcmToken;
    $fields['content_available'] = true;
    $fields['priority'] = "high";

    $fields = json_encode($fields, JSON_NUMERIC_CHECK);

//    echo $fields;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

    $result = curl_exec($ch);
    if ($result === FALSE) {
        //die('FCM Send Error: ' . curl_error($ch));
    }
    curl_close($ch);
    return $result; // 결과값 넣기.
}

function getTodayByTimeStamp()
{
    return date("Y-m-d H:i:s");
}

function getJWToken($id, $hash, $secretKey)
{
    $data = array(
        'date' => (string)getTodayByTimeStamp(),
        'id' => (string)$id,
        'pw' => (string)$hash
    );

//    echo json_encode($data);

    return $jwt = JWT::encode($data, $secretKey);

//    echo "encoded jwt: " . $jwt . "n";
//    $decoded = JWT::decode($jwt, $secretKey, array('HS256'))
//    print_r($decoded);
}

function getDataByJWToken($jwt, $secretKey){
    try{
        $decoded = JWT::decode($jwt, $secretKey, array('HS256'));
    }catch(\Exception $e){
        return "";
    }

//    print_r($decoded);
    return $decoded;

}


function checkAndroidBillingReceipt($credentialsPath, $token, $pid)
{

    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $credentialsPath);
    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    $client->addScope("https://www.googleapis.com/auth/androidpublisher");
    $client->setSubject("USER_ID.iam.gserviceaccount.com");


    $service = new Google_Service_AndroidPublisher($client);
    $optParams = array('token' => $token);

    return $service->purchases_products->get("PACKAGE_NAME", $pid, $token);
}


function addAccessLogs($accessLogs, $body)
{
    if (isset($_SERVER['HTTP_X_ACCESS_TOKEN']))
        $logData["JWT"] = getDataByJWToken($_SERVER['HTTP_X_ACCESS_TOKEN'], JWT_SECRET_KEY);
    $logData["GET"] = $_GET;
    $logData["BODY"] = $body;
    $logData["REQUEST_METHOD"] = $_SERVER["REQUEST_METHOD"];
    $logData["REQUEST_URI"] = $_SERVER["REQUEST_URI"];
//    $logData["SERVER_SOFTWARE"] = $_SERVER["SERVER_SOFTWARE"];
    $logData["REMOTE_ADDR"] = $_SERVER["REMOTE_ADDR"];
    $logData["HTTP_USER_AGENT"] = $_SERVER["HTTP_USER_AGENT"];
    $accessLogs->addInfo(json_encode($logData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

}

function addErrorLogs($errorLogs, $res, $body)
{
    if (isset($_SERVER['HTTP_X_ACCESS_TOKEN']))
        $req["JWT"] = getDataByJWToken($_SERVER['HTTP_X_ACCESS_TOKEN'], JWT_SECRET_KEY);
    $req["GET"] = $_GET;
    $req["BODY"] = $body;
    $req["REQUEST_METHOD"] = $_SERVER["REQUEST_METHOD"];
    $req["REQUEST_URI"] = $_SERVER["REQUEST_URI"];
//    $req["SERVER_SOFTWARE"] = $_SERVER["SERVER_SOFTWARE"];
    $req["REMOTE_ADDR"] = $_SERVER["REMOTE_ADDR"];
    $req["HTTP_USER_AGENT"] = $_SERVER["HTTP_USER_AGENT"];

    $logData["REQUEST"] = $req;
    $logData["RESPONSE"] = $res;

    $errorLogs->addError(json_encode($logData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

//        sendDebugEmail("Error : " . $req["REQUEST_METHOD"] . " " . $req["REQUEST_URI"] , "<pre>" . json_encode($logData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "</pre>");
}


function getLogs($path)
{
    $fp = fopen($path, "r", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!$fp) echo "error";

    while (!feof($fp)) {
        $str = fgets($fp, 10000);
        $arr[] = $str;
    }
    for ($i = sizeof($arr) - 1; $i >= 0; $i--) {
        echo $arr[$i] . "<br>";
    }
//        fpassthru($fp);
    fclose($fp);
}
