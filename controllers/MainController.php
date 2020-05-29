<?php
require 'function.php';
include "./password.php";
const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
//$res = array_filter($res, 'is_not_null');


try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        /*
         * API No. 01
         * API Name : JWT 유효성 검사 테스트 API
         * 마지막 수정 날짜 : 19.04.25
         */
        case "validateJwt":
            // jwt 유효성 검사

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                $res = (Object)Array();
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            http_response_code(200);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 1
         * API Name : JWT 생성 테스트 API (로그인)
         * 마지막 수정 날짜 : 19.04.25
         */
        case "createJwt":
            // jwt 유효성 검사
            http_response_code(200);

            if (!isValidUser($req->id, $req->pw)) {
                $res->isSuccess = FALSE;
                $res->code = 100;
                $res->message = "유효하지 않은 아이디 입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            //페이로드에 맞게 다시 설정 요함

            $jwt = getJWToken($req->id, $req->pw, JWT_SECRET_KEY);
            $res->result->jwt = $jwt;
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "signUp":
        {
            $pw = $req->pw;
            $id = $req->id;
            $nickName = $req->nickName;

            $check_email = filter_var($id, FILTER_VALIDATE_EMAIL);
//            $check_pw = '/^.*(?=^.{8,15}$)(?=.*\d)(?=.*[a-zA-Z])(?=.*[!*@#$%^&+=]).*$/';
            $check_pw = '/^(?=.*[a-zA-Z])(?=.*[0-9]).{6,16}$/';
            $check_id = '/^[0-9a-z]{4,9}$/';
            $check_type = '/^[1-3]{1}$/';

            if (empty($nickName) || empty($pw) || empty($id)) {
                $res->isSucces = FALSE;
                $res->code = 00;
                $res->message = "공백이 입력됐습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else {
                if (validUser($id) == 1) {
                    $res->isSucces = FALSE;
                    $res->code = 11;
                    $res->message = "이미 가입된 email 입니다.";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                } else if (!preg_match($check_pw, "$pw")) {
                    $res->isSucces = FALSE;
                    $res->code = 12;
                    $res->message = "영어 소문자, 숫자를 포함하여 6-16자로 비밀번호를 생성하세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                } else if ($check_email == false) {
                    $res->isSucces = FALSE;
                    $res->code = 13;
                    $res->message = "이메일 형식에 부합하지 않습니다.";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                } else {
                    $hash = password_hash($pw, PASSWORD_DEFAULT);
                    signUp($id, $hash, $nickName);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "회원 가입 성공!";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }
                $res->isSucces = FALSE;
                $res->code = 14;
                $res->message = "회원가입에 실패하였습니다. 다시 시도해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
        }
//
//        case "login":
//        {
//            $id = $req->id;
//            $pw = $req->pw;
//            http_response_code(200);
//            if (empty($id) || empty($pw)) {
//                $res->isSucces = FALSE;
//                $res->code = 00;
//                $res->message = "공백이 입력됐습니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                return;
//            } else {
//                if (validUser($id)==0) {
//                    $res->isSuccess = FALSE;
//                    $res->code = 21;
//                    $res->message = "유효하지 않은 아이디 입니다.";
//                    echo json_encode($res, JSON_NUMERIC_CHECK);
//                    return;
//                } else if(!validPw($id, $pw)){
//                    $res->isSuccess = FALSE;
//                    $res->code = 22;
//                    $res->message = "유효하지 않은 비밀번호... 입니다.";
//                    echo json_encode($res, JSON_NUMERIC_CHECK);
//                    return;
//                }
//                else {
//                    $jwt = getJWToken($id, $pw, JWT_SECRET_KEY);
//                    $res->result["jwt"] = $jwt;
//                    $res->isSuccess = TRUE;
//                    $res->code = 20;
//                    $res->message = "로그인 성공";
//                    echo json_encode($res, JSON_NUMERIC_CHECK);
//                    break;
//                }
//            }
//        }

        case "login":
            $id = $req->id;
            $pw = $req->pw;
            $conn = mysqli_connect("database-1.cdv6gaks3mrb.ap-northeast-2.rds.amazonaws.com", "admin", "Hsh0913**", "Carrot");
            if (mysqli_connect_errno())
            {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            }
            mysqli_set_charset($conn, "utf8");
            $sql = "select pw from User where id = '$id'";
            $resp = mysqli_query($conn, $sql);
            $row = mysqli_fetch_array($resp);
            $hash = $row['pw'];

            if (empty($id) || empty($pw)) {
                $res->isSucces = FALSE;
                $res->code = 00;
                $res->message = "공백이 입력됐습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else {
                if (!validUser($id)) {
                    $res->isSuccess = FALSE;
                    $res->code = 100;
                    $res->message = "유효하지 않은 아이디 입니다.";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                else {
                    if (password_verify($pw ,$hash)) {
                        $jwt = getJWToken($id, $hash, JWT_SECRET_KEY);
                        $res->result["jwt"] = $jwt;
                        $res->isSuccess = TRUE;
                        $res->code = 100;
                        $res->message = "로그인 성공";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        break;
                    } else {
                        $res->isSuccess = FALSE;
                        $res->code = 100;
                        $res->message = "비밀번호가 일치하지 않습니다.";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        break;
                    }
                }
                }

        case "deleteUser":
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
            $pw = $req->pw;
            $conn = mysqli_connect("database-1.cdv6gaks3mrb.ap-northeast-2.rds.amazonaws.com", "admin", "Hsh0913**", "Carrot");
            if (mysqli_connect_errno())
            {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            }
            mysqli_set_charset($conn, "utf8");
            $sql = "select pw from User where id = '$id'";
            $resp = mysqli_query($conn, $sql);
            $row = mysqli_fetch_array($resp);
            $hash = $row['pw'];

            if (empty($pw)) {
                $res->isSucces = FALSE;
                $res->code = 00;
                $res->message = "공백이 입력됐습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else {
                if (deletedUser($id)) {
                    $res->isSuccess = FALSE;
                    $res->code = 192;
                    $res->message = "이미 삭제된 계정입니다.";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                } else {
                    if (password_verify($pw, $hash)) {
                        deleteUser($id);
                        $res->isSuccess = TRUE;
                        $res->code = 190;
                        $res->message = "비밀번호 일치, user 삭제 성공";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    } else {
                        $res->isSuccess = FALSE;
                        $res->code = 191;
                        $res->message = "비밀번호가 일치하지 않습니다.";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        break;
                    }
                }
            }


        case "validateJWT" :
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];//사용자가 가지고 있는 토큰이 유효한지 확인하고
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }else{
                $res->isSuccess = TRUE;
                $res->code = 201;
                $res->message = "유효한 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                break;
            }

        case "user":
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
            $res->result = userInfo($id);
            $res->isSuccess = TRUE;
            $res->code = 33;
            $res->message = "로그인 한 사용자 정보 조회";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        }

        case "userDelete":
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
            $res->result = userDelete($id);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "사용자 탈퇴 완료";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        }


        case "userDetail" :
        {
            $userNo = $_GET["no"];
            if (!is_numeric($userNo)) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 400;
                $res->message = "형식에 맞지 않는 번호입니다. 숫자를 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            } else if (validUserNo($userNo) == 0) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 400;
                $res->message = "존재하지 않는 사용자 번호입니다. 다시 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else {
                http_response_code(200);
                $res->result = userDetail($userNo);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "사용자 번호별 정보 조회";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
        }

        case "userAll":
        {
            http_response_code(200);
            $res->result = userAll();
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "전체 사용자 정보 조회";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        }

        case "listUp":

            http_response_code(200);
            $res->result = listUp();
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "리뷰 검색";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "lectureDetail":
            $lectureNo = $_GET["no"];
            http_response_code(200);
            $res->result = lectureDetail($lectureNo);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "번호별 강의 개요 조회";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "reviewList":
            http_response_code(200);
            $res->result = reviewList();
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "강의 리뷰 리스트 조회";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "reviewScroll":
            $lastNo = $_GET["lastNo"];
            http_response_code(200);
            $res->result = reviewScroll($lastNo);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "강의 리뷰 무한 스크롤";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;



    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
