<?php

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


function searchReview($name, $professor){
    $pdo = pdoSqlConnect();
    $query = "select name, professor, round(scoreAVG,1) as totalScore
from Lecture
left join (select lectureNo, avg(score) as scoreAVG
    from LectureReview group by lectureNo) reviewTB
    on Lecture.no = reviewTB.lectureNo
    where name like ? or professor like ?;;";
    $st = $pdo->prepare($query);
    $st->execute([$name, $professor]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function listUp(){
    $pdo = pdoSqlConnect();
    $query = "select * from Lecture;";
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function lectureDetail($lectureNo){
    $pdo = pdoSqlConnect();
    $query = "select no, name, professor, semester from Lecture
              where no = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$lectureNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function reviewList(){
    $pdo = pdoSqlConnect();
    $query = "select reviewTB.no, name, professor, round(scoreAVG,1) as totalScore, reviewTB.review, reviewTB.semester
from Lecture
right join (select no, semester, review, lectureNo, avg(score) as scoreAVG
    from LectureReview group by lectureNo) reviewTB
    on Lecture.no = reviewTB.lectureNo order by no desc limit 0,10;";
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function reviewScroll($lastNo){
    $pdo = pdoSqlConnect();
    $query = "select reviewTB.no, name, professor, round(scoreAVG,1) as totalScore, reviewTB.review, reviewTB.semester
                from Lecture
                right join (select no, semester, review, lectureNo, avg(score) as scoreAVG
                from LectureReview group by lectureNo) reviewTB
                on Lecture.no = reviewTB.lectureNo
                where reviewTB.no < ?
                order by no desc limit 0,10;";
    $st = $pdo->prepare($query);
    $st->execute([$lastNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}