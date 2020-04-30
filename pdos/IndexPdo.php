<?php

//READ
function userTest()
{
    $pdo = pdoSqlConnect();
    $query = "SELECT * FROM User;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

//READ
function userDetail($userNo)
{
    //$userNo =$_GET["no"];
    $pdo = pdoSqlConnect();
    $query = "SELECT * FROM User WHERE no = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$userNo]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}


function signup($username, $password)
{
    $pdo = pdoSqlConnect();

    // query to insert record
    $query = "INSERT INTO User (username, password) VALUES (?,?);";

    // prepare query
    $st = $pdo->prepare($query);

    // sanitize
    $username=htmlspecialchars(strip_tags($username));
    $password=htmlspecialchars(strip_tags($password));

    // bind values
//    $st->bindParam(":username", $username);
//    $st->bindParam(":password", $password);
    $res = $st->fetchAll();
    // execute query
    $st->execute([$username, $password]);

    return $res;
}

//function isExitUser($username){
//
//    $pdo = pdoSqlConnect();
//
//    $query = "SELECT EXISTS(SELECT * FROM User WHERE username =?) AS exist;";
//    // prepare query statement
//    $st = $pdo->prepare($query);
//    // execute query
//    $st->execute([$username]);
//    $res = $st->fetchAll();
//
////    if($st->rowCount() > 0){
////        return true;
////    }
////    else{
////        return false;
////    }
//    return intval($res[0]["exist"]);
//}

function login($username, $password){

    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM User WHERE username =? AND password =?) AS exist;";//
    $st = $pdo->prepare($query);
    $st->execute([$username, $password]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    return $res[0];
}

function isValidUser($id, $pw){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM User WHERE userId= ? AND userPw = ?) AS exist;";


    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id, $pw]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function category(){
        $pdo = pdoSqlConnect();
        $query = "SELECT * FROM Category;";

        $st = $pdo->prepare($query);
        //    $st->execute([$param,$param]);
        $st->execute();
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $res = $st->fetchAll();

        $st = null;
        $pdo = null;

        return $res;
}

function catDetail($catName){

    $pdo = pdoSqlConnect();
    $query = "SELECT * FROM Restaurant WHERE catname = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$catName]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}



function restaurant(){
    $pdo = pdoSqlConnect();
    $query = "SELECT * FROM Restaurant;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function resDetail($resName){

    $pdo = pdoSqlConnect();
    $query = "SELECT * FROM Menu WHERE reNo = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$resName]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function menu(){
    $pdo = pdoSqlConnect();
    $query = "SELECT * FROM Menu;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

//CREATE
function addRestaurant($resname, $resdes, $catname){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO Restaurant (resname, resdes, catname) VALUES (?,?,?);";

    $st = $pdo->prepare($query);//prepare query

    $st->execute([$resname, $resdes, $catname]);//execute query
    $st->setFetchMode(PDO::FETCH_ASSOC);
    //$res = $st->fetchAll();

    $st = null;
    $pdo = null;
}
//CREATE
function addMenu($reNo, $mName, $price, $resName){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO Menu (reNo, mName, price, resName) VALUES (?,?,?,?);";

    $st = $pdo->prepare($query);//prepare query

    $st->execute([$reNo, $mName, $price, $resName]);//execute query
    $st->setFetchMode(PDO::FETCH_ASSOC);
    //$res = $st->fetchAll();

    $st = null;
    $pdo = null;
}
//UPDATE
function updateMenu($mName, $price, $no){
    $pdo = pdoSqlConnect();

    $query = "UPDATE Menu
              SET mName = ?, price = ? 
              WHERE no = ?;";

    $st = $pdo->prepare($query);

    $st->execute([$mName, $price, $no]);//execute query
    $st->setFetchMode(PDO::FETCH_ASSOC);
    //$res = $st->fetchAll();

    $st = null;
    $pdo = null;
}

function updateRestaurant($resdes, $reId){
    $pdo = pdoSqlConnect();
    $query = "UPDATE Restaurant SET resdes =? WHERE reId = ?";

    $st = $pdo->prepare($query);

    $st->execute([$resdes, $reId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $st = null;
    $pdo = null;
}

//DELETE
function deleteMenu($mName){
    $pdo = pdoSqlconnect();
    $query = "DELETE FROM Menu WHERE mName = ? ";

    $st = $pdo->prepare($query);

    $st->execute([$mName]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $st = null;
    $pdo = null;
}

//DELETE
function deleteRestaurant($resname){
    $pdo = pdoSqlconnect();
    $query = "DELETE FROM Restautrant WHERE resname = ? ";

    $st = $pdo->prepare($query);

    $st->execute([$resname]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $st = null;
    $pdo = null;
}

function deleteAllMenu($resname){ //레스토랑이 삭제 되면 모든 메뉴도 삭제
    $pdo = pdoSqlconnect();
    $query = "DELETE FROM Menu WHERE resName = ? ";

    $st = $pdo->prepare($query);

    $st->execute([$resname]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $st = null;
    $pdo = null;
}

// CREATE
//    function addMaintenance($message){
//        $pdo = pdoSqlConnect();
//        $query = "INSERT INTO MAINTENANCE (MESSAGE) VALUES (?);";
//
//        $st = $pdo->prepare($query);
//        $st->execute([$message]);
//
//        $st = null;
//        $pdo = null;
//
//    }


// UPDATE
//    function updateMaintenanceStatus($message, $status, $no){
//        $pdo = pdoSqlConnect();
//        $query = "UPDATE MAINTENANCE
//                        SET MESSAGE = ?,
//                            STATUS  = ?
//                        WHERE NO = ?";
//
//        $st = $pdo->prepare($query);
//        $st->execute([$message, $status, $no]);
//        $st = null;
//        $pdo = null;
//    }

// RETURN BOOLEAN
//    function isRedundantEmail($email){
//        $pdo = pdoSqlConnect();
//        $query = "SELECT EXISTS(SELECT * FROM USER_TB WHERE EMAIL= ?) AS exist;";
//
//
//        $st = $pdo->prepare($query);
//        //    $st->execute([$param,$param]);
//        $st->execute([$email]);
//        $st->setFetchMode(PDO::FETCH_ASSOC);
//        $res = $st->fetchAll();
//
//        $st=null;$pdo = null;
//
//        return intval($res[0]["exist"]);
//
//    }
