<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";
$res = (Object)Array();//배열을 object로 변환
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));

//$res->result = array_filter($res);
//var_dump($res->result);
//print_r(array_filter($res));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case "index":
            echo "API Server carrot domain checkk";
            break;
        case "ACCESS_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/access.log");
            break;
        case "ERROR_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/errors.log");
            break;
        /*
         * API No. 0
         * API Name : 테스트 API
         * 마지막 수정 날짜 : 19.04.29
         *
         */




        case "postCollection":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];//사용자가 가지고 있는 토큰이 유효한지 확인하고
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $id = $data->id;
            $goalNo = $req->goalNo;
            if(empty($goalNo)){
                $res->isSucces = FALSE;
                $res->code = 00;
                $res->message = "목표 번호가 입력되지 않았습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }else if (validNo($goalNo, $id) == 0){
                $res->isSucces = FALSE;
                $res->code = 01;
                $res->message = "존재하지 않는 목표 번호입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else{
                    if(oneDayDone($id, $goalNo)&&(!alreadySetCollectionFour($id, $goalNo))) {
                        getCollectionFour($id, $goalNo);
                        $res->result =latestCollection($id);
                        $res->isSuccess = TRUE;
                        $res->code = 80;
                        $res->message = "1회 최초 체크 컬렉션 발급 성공";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                    else if(threeDaysDone($id, $goalNo)&&(!alreadySetCollectionFive($id, $goalNo))){
                        getCollectionFive($id, $goalNo);
                        $res->result =latestCollection($id);
                        $res->isSuccess = TRUE;
                        $res->code = 80;
                        $res->message = "3회 연속 체크 컬렉션 발급 성공";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                    else if(fiveDaysDone($id, $goalNo)&&(!alreadySetCollectionSix($id, $goalNo))){
                        getCollectionSix($id, $goalNo);
                        $res->result =latestCollection($id);
                        $res->isSuccess = TRUE;
                        $res->code = 80;
                        $res->message = "5회 연속 체크 컬렉션 발급 성공";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                    else if(tenDaysDone($id, $goalNo)&&(!alreadySetCollectionSeven($id, $goalNo))){
                        getCollectionSeven($id, $goalNo);
                        $res->result =latestCollection($id);
                        $res->isSuccess = TRUE;
                        $res->code = 80;
                        $res->message = "10회 연속 체크 컬렉션 발급 성공";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                    else{
                        $res->isSuccess = FALSE;
                        $res->code = 445;
                        $res->message = "컬렉션 발급 실패. 이미 등록된 컬렉션이거나 컬렉션 발급 대상이 아닙니다.";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                }


        case "getCollection":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];//사용자가 가지고 있는 토큰이 유효한지 확인하고
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $id = $data->id;

            if (getUserCollection($id) == null) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 400;
                $res->message = "수집된 컬렉션이 없습니다. 목표를 생성하고 매일 체크해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else {
                http_response_code(200);
                $res->result = getUserCollection($id);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "사용자가 수집한 컬렉션 리스트 조회";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }


    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
