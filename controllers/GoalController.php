<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));

try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case "list":
            echo "API ContentsServer";
            break;




        case "addGoal":
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
            $contentsNo = $req->contentsNo;
            $goal = $req->goal;

            if(empty($goal)){
                $res->isSucces = FALSE;
                $res->code = 00;
                $res->message = "공백이 입력되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }else {
                addGoal($id, $goal);
                $res->goalNo = latestGoal($id);
                $res->isSuccess = TRUE;
                $res->code = 77;
                $res->message = "목표 추가";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

        case "ongoingGoal":
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

            if (keepGoingGoal($id) == null) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 400;
                $res->message = "진행중인 목표가 없습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            http_response_code(200);
            $res->result = keepGoingGoal($id);
            $res->isSuccess = TRUE;
            $res->code = 70;
            $res->message = "진행중인 나의 목표 조회";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "goalListDetail":

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
            $goalNo = $vars["goalNo"];

            if (validNo($goalNo, $id) == 0) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 400;
                $res->message = "종료됐거나 존재하지 않는 목표 번호입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else {
                http_response_code(200);
                $res->result = (Object)Array();
                $res->result = goalList($id, $goalNo);
                $res->result["checkResult"] = checkList($id, $goalNo);
                $res->isSuccess = TRUE;
                $res->code = 50;
                $res->message = "목표와 목표 별 수행 날짜 조회";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

        case "checkListPage":

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
//            $goalNo = $vars["goalNo"];

            if(checkPageList($id)==null){
                $res->isSuccess = FALSE;
                $res->code = 388;
                $res->message = "목표와 체크 리스트 정보가 없습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            } else {
                    http_response_code(200);
                    $res->result = checkPageList($id);
                    $res->isSuccess = TRUE;
                    $res->code = 50;
                    $res->message = "목표와 목표 별 수행 날짜 조회 페이지 리스트";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }


        case "finishedGoal":
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

            if (finishedGoal($id) == null) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 399;
                $res->message = "종료된 목표가 없습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            http_response_code(200);
            $res->result = finishedGoal($id);
            $res->isSuccess = TRUE;
            $res->code = 71;
            $res->message = "종료된 나의 목표 조회";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "updateGoal":
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
            $goal = $req->goal;
            $goalNo = $req->goalNo;

            if(empty($goal)&&empty($goalNo)){
                $res->isSucces = FALSE;
                $res->code = 00;
                $res->message = "공백이 입력되었습니다.";
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
                        updateGoal($goal, $id, $goalNo);
                        $res->isSuccess = TRUE;
                        $res->code = 80;
                        $res->message = "기존 목표 수정 성공";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        break;
                }

        case "deleteGoal":
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
            $goalNo = $_GET["goalNo"];

            if(empty($goalNo)){
                $res->isSucces = FALSE;
                $res->code = 00;
                $res->message = "공백이 입력되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }else if (validNo($goalNo, $id) == 0){
                $res->isSucces = FALSE;
                $res->code = 01;
                $res->message = "존재하지 않는 목표 번호입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }else if(alreadyDelete($goalNo, $id)==1){
                $res->isSucces = FALSE;
                $res->code = 8;
                $res->message = "이미 삭제된 목표 입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else{
                deleteGoal($id, $goalNo);
                $res->isSuccess = TRUE;
                $res->code = 81;
                $res->message = "기존 목표 삭제 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

        case "goalCheck":
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

            if (empty($goalNo)) {
                $res->isSucces = FALSE;
                $res->code = 00;
                $res->message = "공백이 입력되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else if (!is_numeric($goalNo)) {
                $res->isSucces = FALSE;
                $res->code = 04;
                $res->message = "번호를 숫자로 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else if (validNo($goalNo, $id) == 0){
                $res->isSucces = FALSE;
                $res->code = 01;
                $res->message = "존재하지 않는 목표 번호입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else {
                if (alreadyChecked($id, $goalNo)) {
                    deleteCheck($id, $goalNo);
                    $res->isSuccess = FALSE;
                    $res->code = 90;
                    $res->message = "일일 목표 체크 삭제";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                } else {
                    addCheck($id, $goalNo);
                    $res->isSuccess = TRUE;
                    $res->code = 91;
                    $res->message = "일일 목표 체크 추가";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }
            }


        case "getUrl":
        {
            $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        }


    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
