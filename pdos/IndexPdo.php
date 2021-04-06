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
    $query = "SELECT EXISTS(SELECT * FROM User WHERE id = ? AND pw = ?) AS exist;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id, $pw]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}
