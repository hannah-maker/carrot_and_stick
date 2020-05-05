<?php
function signUp($id, $pw, $nickName)
{
    $pdo = pdoSqlConnect();
    $query = "insert into User(id, pw, nickName) values (?,?,?);";
    $st = $pdo->prepare($query);
    $st->execute([$id, $pw, $nickName]);
    $st = null;
    $pdo = null;
}

function validUser($id){

    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM User WHERE id = ?) AS exist;";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $res = $st->fetchAll();

    return intval($res[0]["exist"]);
}

function validPw($id, $pw){
    $pdo = pdoSqlConnect();

    $query = "SELECT EXISTS(SELECT * FROM User WHERE id = ? and pw = ?) AS exist;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $pw]);
    $res = $st->fetchAll();

    return intval($res[0]["exist"]);
}

function login($id, $pw){
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

function userInfo($id){
    $pdo = pdoSqlConnect();
    $query = "select id, nickName, imgUrl from User where id = ?";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res[0];
}


function UserDetail($UserNo){
    $pdo = pdoSqlConnect();
    $query = "select * from User where no = ?";

    $st = $pdo->prepare($query);
    $st->execute([$UserNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res[0];
}

function validUserNo($UserNo){
    $pdo = pdoSqlConnect();

    $query = "SELECT EXISTS(SELECT * FROM User WHERE no = ?) AS exist;";
    $st = $pdo->prepare($query);
    $st->execute([$UserNo]);
    $res = $st->fetchAll();

    return intval($res[0]["exist"]);
}

function UserAll(){
    $pdo = pdoSqlConnect();
    $query = "select id, pw, name from User";
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;
}

//function getPw($id){
//    $pdo = pdoSqlConnect();
//    $query = "select pw from Userwhere id = ?;";
//    $st = $pdo->prepare($query);
//    $st->execute([$id]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//    $st = null;
//    $pdo = null;
//    return $res[0];
//}

function getPw($id){
    $pdo = pdoSqlConnect();
    $query = "select pw from User where id = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;
}