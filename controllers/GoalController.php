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

            if (ongoingGoal($id) == null) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 400;
                $res->message = "진행중인 목표가 없습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            http_response_code(200);
            $res->result = ongoingGoal($id);
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
                $res->message = "진행중인 목표가 없습니다.";
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
                $res->code = 400;
                $res->message = "진행중인 목표가 없습니다.";
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
            $goalNo = $req->goalNo;

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

        case "contentsScrap":
        {
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

            if (empty($contentsNo)) {
                $res->isSucces = FALSE;
                $res->code = 00;
                $res->message = "공백이 입력되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else if (!is_numeric($contentsNo)) {
                $res->isSucces = FALSE;
                $res->code = 00;
                $res->message = "번호를 숫자로 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else if (validNo($contentsNo) == 0) {
                $res->isSucces = FALSE;
                $res->code = 00;
                $res->message = "존재하지 않는 콘텐츠 번호입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else {
                if (alreadyScrap($id, $contentsNo)) {
                    $res->result = deleteScrap($id, $contentsNo);
                    $res->isSuccess = FALSE;
                    $res->code = 201;
                    $res->message = "스크랩 목록 삭제";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                } else {
                    Scrap($id, $contentsNo);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "스크랩 목록 추가";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }
            }
        }

        case "myScrap":
        {
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
            http_response_code(200);
            $res->result = myScrap($id);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "내 스크랩 조회";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        }

        case "contentsLike":
        {
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

            if (empty($contentsNo)) {
                $res->isSucces = FALSE;
                $res->code = 00;
                $res->message = "공백이 입력되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else if (!is_numeric($contentsNo)) {
                $res->isSucces = FALSE;
                $res->code = 00;
                $res->message = "번호를 숫자로 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else if (validNo($contentsNo) == 0) {
                $res->isSucces = FALSE;
                $res->code = 00;
                $res->message = "존재하지 않는 콘텐츠 번호입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else {
                if (existsDislikes($id, $contentsNo)) {
                    $res->isSucces = FALSE;
                    $res->code = 00;
                    $res->message = "존재하는 싫어요를 삭제 해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                } else {
                    if (existsLikes($id, $contentsNo)) {
                        $res->result = deleteLikes($id, $contentsNo);
                        $res->isSuccess = TRUE;
                        $res->code = 201;
                        $res->message = "컨텐츠 좋아요 삭제";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    } else {
                        $res->result = likes($id, $contentsNo);
                        $res->isSuccess = TRUE;
                        $res->code = 100;
                        $res->message = "컨텐츠 좋아요 추가";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        break;
                    }
                }
            }
        }

        case "contentsDislikes":
        {
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

            if (empty($contentsNo)) {
                $res->isSucces = FALSE;
                $res->code = 00;
                $res->message = "공백이 입력되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else if (!is_numeric($contentsNo)) {
                $res->isSucces = FALSE;
                $res->code = 00;
                $res->message = "번호를 숫자로 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else if (validNo($contentsNo) == 0) {
                $res->isSucces = FALSE;
                $res->code = 00;
                $res->message = "존재하지 않는 콘텐츠 번호입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else {
                if (existsLikes($id, $contentsNo)) {
                    $res->isSucces = FALSE;
                    $res->code = 00;
                    $res->message = "존재하는 좋아요를 삭제 해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                } else {
                    if (existsDislikes($id, $contentsNo)) {
                        $res->result = deleteDislikes($id, $contentsNo);
                        $res->isSuccess = TRUE;
                        $res->code = 201;
                        $res->message = "컨텐츠 싫어요 삭제";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    } else {
                        $res->result = dislikes($id, $contentsNo);
                        $res->isSuccess = TRUE;
                        $res->code = 100;
                        $res->message = "컨텐츠 싫어요 추가";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        break;
                    }
                }
            }
        }
        case "watchingVideo":
        {
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
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
            $type = $req->type;
            $check_type = '/^[1-2]{1}$/';

            if (empty($contentsNo) || empty($type)) {
                $res->isSucces = FALSE;
                $res->code = 00;
                $res->message = "공백이 입력되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else if(!preg_match($check_type, "$type")) {
                $res->isSucces = FALSE;
                $res->code = 100;
                $res->message = "콘텐츠의 타입을 1(Movie), 2(TV Show) 중 숫자로 선택해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else if (!is_numeric($contentsNo)) {
                $res->isSucces = FALSE;
                $res->code = 00;
                $res->message = "콘텐츠 번호를 숫자로 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else if (!validNo($contentsNo) && (!validSeriesNo($contentsNo))) {
                $res->isSucces = FALSE;
                $res->code = 00;
                $res->message = "존재하지 않는 콘텐츠 번호입니다. 다시 확인해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else {
                if (alreadyWatching($id, $contentsNo)) {
                    $res->result = countPlay($id, $contentsNo);
                    $res->isSuccess = TRUE;
                    $res->code = 201;
                    $res->message = "재생 기록 카운트";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                } else {
                    $res->result = watchingVideo($id, $type, $contentsNo);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "시청 중인 재생 목록에 추가";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }
            }
        }

        case "getUrl":
        {
            $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        }

        case "tvWatchingList":
        {
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
            if (tvWatchingList($id) == null) {
                $res->isSucces = FALSE;
                $res->code = 00;
                $res->message = "사용자가 시청중인 티비 프로그램이 없습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else {
                http_response_code(200);
                $res->result = tvWatchingList($id);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "시청 중인 티비 목록 조회";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
        }

        case "movieWatchingList":
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
            if (movieWatchingList($id) == null) {
                $res->isSucces = FALSE;
                $res->code = 00;
                $res->message = "사용자가 시청중인 영화가 없습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else {
                http_response_code(200);
                $res->result = movieWatchingList($id);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "시청중인 영화 목록 조회";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

//        case "contentsWatchingList":
//            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];//사용자가 가지고 있는 토큰이 유효한지 확인하고
//            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
//                $res->isSuccess = FALSE;
//                $res->code = 201;
//                $res->message = "유효하지 않은 토큰입니다";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                addErrorLogs($errorLogs, $res, $req);
//                return;
//            }
//            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
//            $id = $data->id;
//            if ((movieWatchingList($id) == null) && (tvWatchingList($id) == null)) {
//                $res->isSucces = FALSE;
//                $res->code = 00;
//                $res->message = "사용자가 시청중인 콘텐츠가 없습니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                return;
//            } else {
//                http_response_code(200);
//                $res->result->tv = (Object) Array();
//                $res->result->movie = (Object) Array();
//                $res->result->tv = tvWatchingList($id);
//                $res->result->movie = movieWatchingList($id);
//                $res->isSuccess = TRUE;
//                $res->code = 100;
//                $res->message = "시청중인 영화 목록 조회";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }

        case "contentsHistory":
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
            $id2 = $data->id;
            if (contentsHistory($id, $id2) == null) {
                $res->isSucces = FALSE;
                $res->code = 00;
                $res->message = "사용자가 시청중인 콘텐츠가 없습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else {
                http_response_code(200);
                $res->result = contentsHistory($id, $id2);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "시청중인 콘텐츠 목록 조회";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}