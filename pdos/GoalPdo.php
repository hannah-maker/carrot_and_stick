<?php

function boardDetail($boardNo){
    $pdo = pdoSqlConnect();
    $query = "SELECT * FROM Board WHERE no = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$boardNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res[0];
}

function boardList(){
    $pdo = pdoSqlConnect();
    $query = "select Board.no, title, contents, createdAt, userId, name as type from Board
INNER JOIN Category C on Board.type = C.no;";

    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;
}

function postBoard($id, $title, $contents, $type){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO Board(userId, title, contents, type) VALUES (?,?,?,?);";
    $st = $pdo->prepare($query);
    $st->execute([$id, $title, $contents, $type]);
    $st = null;
    $pdo = null;
}

function categotyList(){
    $pdo = pdoSqlConnect();
    $query = "SELECT * FROM Category;";

    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;
}

//function addGoal($id, $goal){
//    $pdo = pdoSqlConnect();
//    $query = "insert into GoalList(userId, contents) values (?,?);";
//    $st = $pdo->prepare($query);
//    $st->execute([$id, $goal]);
//    $st = null;
//    $pdo = null;
//}

function latestCollection($id){
    $pdo = pdoSqlConnect();
    $query = "SELECT collectionTB.no, name, imageUrl, HavingCollection.createdAt FROM HavingCollection
INNER JOIN (SELECT * FROM Collection) collectionTB
on collectionNo = collectionTB.no
WHERE userId = ?
ORDER BY HavingCollection.createdAt desc limit 1;";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res[0];;
}

function keepGoingGoal($id){
    $pdo = pdoSqlConnect();
    $query = "select * from (select GoalList.no as no, contents as goal, GoalList.createdAt, userCollectionTB.name , userCollectionTB.imageUrl, GoalList.isDeleted from GoalList
   left join
    (SELECT collectionNo, HavingCollection.goalNo, imageUrl, name, HavingCollection.createdAt, HavingCollection.no FROM HavingCollection
        inner join
        (SELECT * FROM Collection) collectionTB
        on collectionTB.no = HavingCollection.collectionNo) userCollectionTB
    on GoalList.no = userCollectionTB.goalNo
where GoalList.userId = ? and GoalList.isDeleted = 'N'
group by userCollectionTB.createdAt desc
order by userCollectionTB.createdAt desc) goalTB
group by goalTB.no;";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;
}

function checkPageList($id){
    $pdo = pdoSqlConnect();
    $query = "select no, contents as goal, date_format(createdAt, '%Y-%m-%d') as createdAt from GoalList
where userId = ? and isDeleted = 'N'
order by createdAt;";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    for($i=0; $i < sizeof($res); $i++){
        $query = "SELECT createdAt FROM date_t
left join (SELECT date_format(createdAt, '%Y-%m-%d') as createdAt, userId, goalNo, isDeleted FROM GoalCheck
    WHERE userId = ? and goalNo = ? and isDeleted = 'N')checkTB
on d = checkTB.createdAt
WHERE d BETWEEN DATE_ADD(NOW(),INTERVAL -1 WEEK ) AND NOW()
order by d desc limit 3;";
        $st = $pdo->prepare($query);
        $st->execute([$id, $res[$i]["no"]]);
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $res1 = $st->fetchAll();
        $res[$i]["checkResult"] = $res1;
    }
    return $res;
}


function goalList($id, $goalNo){
    $pdo = pdoSqlConnect();
    $query = "select no, contents, createdAt from GoalList
where userId = ? and no = ? and isDeleted = 'N';";
    $st = $pdo->prepare($query);
    $st->execute([$id, $goalNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res[0];;
}

function checkList($id, $goalNo){
    $pdo = pdoSqlConnect();
    $query = "select createdAt from GoalCheck
where userId = ? and goalNo = ? and isDeleted = 'N'
order by createdAt desc;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $goalNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;
}

function addGoal($id, $goal){
    $pdo = pdoSqlConnect();
    $query = "insert into GoalList(userId, contents) values (?,?);";
    $st = $pdo->prepare($query);
    $st->execute([$id, $goal]);
    $st = null;
    $pdo = null;
}

function deleteGoal($id, $goalNo){
    $pdo = pdoSqlConnect();
    $query = "UPDATE GoalList
SET isDeleted = 'Y'
WHERE userId = ? and no = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $goalNo]);
    $st = null;
    $pdo = null;
}

function updateGoal($goal, $id, $goalNo){
    $pdo = pdoSqlConnect();
    $query = "UPDATE GoalList
SET contents = ?
WHERE userId = ? and no = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$goal, $id, $goalNo]);
    $st = null;
    $pdo = null;
}

function alreadyScrap($id, $contentsNo){
    $pdo = pdoSqlConnect();
    $query = "select exists (select * from Scrap where userId = ? and contentsNo =? and isDeleted = 'N') as exist;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $contentsNo]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function validNo($goalNo, $userId){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM GoalList WHERE no = ? and userId = ? and isDeleted = 'N') AS exist;";
    $st = $pdo->prepare($query);
    $st->execute([$goalNo, $userId]);
    $res = $st->fetchAll();

    return intval($res[0]["exist"]);
}

function alreadyDelete($goalNo, $id){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM GoalList WHERE no = ? and userId = ? and isDeleted = 'Y') AS exist;";
    $st = $pdo->prepare($query);
    $st->execute([$goalNo, $id]);
    $res = $st->fetchAll();

    return intval($res[0]["exist"]);
}

function deleteScrap($id, $contentsNo){
    $pdo = pdoSqlConnect();
    $query = "update Scrap set isDeleted = 'Y' where userId =? and contentsNo =?;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $contentsNo]);
    $st = null;
    $pdo = null;
}

function deleteCheck($id, $goalNo){
    $pdo = pdoSqlConnect();
    $query = "UPDATE GoalCheck set isDeleted = 'Y' where DATE_FORMAT(createdAt, '%Y-%m-%d') = CURDATE() and
                              userId = ? and goalNo = ? and isDeleted = 'N';";
    $st = $pdo->prepare($query);
    $st->execute([$id, $goalNo]);
    $st = null;
    $pdo = null;
}

function ongoingGoal($id){
    $pdo = pdoSqlConnect();
    $query = "select no, contents as goal, createdAt, isDeleted from GoalList
where userId = ? and isDeleted = 'N'
order by createdAt;";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;
}

function latestGoal($id){
    $pdo = pdoSqlConnect();
    $query = "SELECT no FROM GoalList WHERE userId = ?
ORDER BY createdAt DESC limit 1;";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res[0];
}

function finishedGoal($id){
    $pdo = pdoSqlConnect();
    $query = "select * from (select GoalList.no as no, contents as goal, GoalList.createdAt, userCollectionTB.name , userCollectionTB.imageUrl, GoalList.isDeleted from GoalList
   left join
    (SELECT collectionNo, HavingCollection.goalNo, imageUrl, name, HavingCollection.createdAt, HavingCollection.no FROM HavingCollection
        inner join
        (SELECT * FROM Collection) collectionTB
        on collectionTB.no = HavingCollection.collectionNo) userCollectionTB
    on GoalList.no = userCollectionTB.goalNo
where GoalList.userId = ? and GoalList.isDeleted = 'Y'
group by userCollectionTB.createdAt desc
order by userCollectionTB.createdAt desc) goalTB
group by goalTB.no;";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;
}

function addCheck($id, $goalNo){
    $pdo = pdoSqlConnect();
    $query = "insert into GoalCheck(userId, goalNo) values (?,?);";
    $st = $pdo->prepare($query);
    $st->execute([$id, $goalNo]);
    $st = null;
    $pdo = null;
}

function alreadyChecked($id, $goalNo){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM GoalCheck WHERE DATE_FORMAT(createdAt, '%Y-%m-%d') = CURDATE() and
                              userId = ? and goalNo = ? and isDeleted = 'N') as exist;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $goalNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function oneDayDone($id, $goalNo){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM (SELECT curr.goalNo
     , curr.createdAt
     , 1 + DATEDIFF(curr.createdAt, MAX(streak.createdAt)) AS consecutive
     , curr.isDeleted
  FROM GoalCheck curr
  LEFT OUTER
  JOIN (SELECT *
             , CASE
                 WHEN DATEDIFF(createdAt, prev) = 1 THEN 1
                 ELSE 0
               END AS diff
          FROM (SELECT *
                     , (SELECT MAX(createdAt)
                          FROM GoalCheck
                         WHERE goalNo = top.goalNo and GoalCheck.isDeleted = 'N'
                           AND createdAt < top.createdAt)  AS Prev
                  FROM GoalCheck top
		     ) withPrev
     ) streak
    ON streak.goalNo = curr.goalNo
   AND streak.createdAt <= curr.createdAt
   AND streak.diff = 0
    WHERE curr.isDeleted = 'N' and streak.isDeleted = 'N'
            and curr.userId = ? and streak.userId = curr.userId
            and curr.goalNo = ? and streak.goalNo = curr.goalNo
 GROUP BY curr.goalNo, curr.createdAt
 ORDER BY curr.goalNo, curr.createdAt) checkDate
WHERE consecutive = 1 AND DATE_FORMAT(createdAt, '%Y-%m-%d') = CURDATE()
AND isDeleted = 'N'
ORDER BY createdAt DESC limit 1) as exist;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $goalNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function threeDaysDone($id, $goalNo){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM (SELECT curr.goalNo
     , curr.createdAt
     , 1 + DATEDIFF(curr.createdAt, MAX(streak.createdAt)) AS consecutive
     , curr.isDeleted
  FROM GoalCheck curr
  LEFT OUTER
  JOIN (SELECT *
             , CASE
                 WHEN DATEDIFF(createdAt, prev) = 1 THEN 1
                 ELSE 0
               END AS diff
          FROM (SELECT *
                     , (SELECT MAX(createdAt)
                          FROM GoalCheck
                         WHERE goalNo = top.goalNo and GoalCheck.isDeleted = 'N'
                           AND createdAt < top.createdAt)  AS Prev
                  FROM GoalCheck top
		     ) withPrev
     ) streak
    ON streak.goalNo = curr.goalNo
   AND streak.createdAt <= curr.createdAt
   AND streak.diff = 0
    WHERE curr.isDeleted = 'N' and streak.isDeleted = 'N'
            and curr.userId = ? and streak.userId = curr.userId
            and curr.goalNo = ? and streak.goalNo = curr.goalNo
 GROUP BY curr.goalNo, curr.createdAt
 ORDER BY curr.goalNo, curr.createdAt) checkDate
WHERE consecutive = 3 AND DATE_FORMAT(createdAt, '%Y-%m-%d') = CURDATE()
AND isDeleted = 'N'
ORDER BY createdAt limit 1) as exist;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $goalNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function fiveDaysDone($id, $goalNo){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM (SELECT curr.goalNo
     , curr.createdAt
     , 1 + DATEDIFF(curr.createdAt, MAX(streak.createdAt)) AS consecutive
  FROM GoalCheck curr
  LEFT OUTER
  JOIN (SELECT *
             , CASE
                 WHEN DATEDIFF(createdAt, prev) = 1 THEN 1
                 ELSE 0
               END AS diff
          FROM (SELECT *
                     , (SELECT MAX(createdAt)
                          FROM GoalCheck
                         WHERE goalNo = top.goalNo
                           AND createdAt < top.createdAt)  AS Prev
                  FROM GoalCheck top
		     ) withPrev
     ) streak
    ON streak.goalNo = curr.goalNo
   AND streak.createdAt <= curr.createdAt
   AND streak.diff = 0
    WHERE curr.isDeleted = 'N' and streak.isDeleted = 'N'
            and curr.userId = ? and streak.userId = curr.userId
            and curr.goalNo = ? and streak.goalNo = curr.goalNo
 GROUP BY curr.goalNo, curr.createdAt
 ORDER BY curr.goalNo, curr.createdAt) checkDate
WHERE consecutive = 5 AND DATE_FORMAT(createdAt, '%Y-%m-%d') = CURDATE()
ORDER BY createdAt limit 1) as exist;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $goalNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function tenDaysDone($id, $goalNo){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM (SELECT curr.goalNo
     , curr.createdAt
     , 1 + DATEDIFF(curr.createdAt, MAX(streak.createdAt)) AS consecutive
  FROM GoalCheck curr
  LEFT OUTER
  JOIN (SELECT *
             , CASE
                 WHEN DATEDIFF(createdAt, prev) = 1 THEN 1
                 ELSE 0
               END AS diff
          FROM (SELECT *
                     , (SELECT MAX(createdAt)
                          FROM GoalCheck
                         WHERE goalNo = top.goalNo
                           AND createdAt < top.createdAt)  AS Prev
                  FROM GoalCheck top
		     ) withPrev
     ) streak
    ON streak.goalNo = curr.goalNo
   AND streak.createdAt <= curr.createdAt
   AND streak.diff = 0
    WHERE curr.isDeleted = 'N' and streak.isDeleted = 'N'
            and curr.userId = ? and streak.userId = curr.userId
            and curr.goalNo = ? and streak.goalNo = curr.goalNo
 GROUP BY curr.goalNo, curr.createdAt
 ORDER BY curr.goalNo, curr.createdAt) checkDate
WHERE consecutive = 10 AND DATE_FORMAT(createdAt, '%Y-%m-%d') = CURDATE()
ORDER BY createdAt limit 1) as exist;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $goalNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function getCollectionFour($id, $goalNo){
    $pdo = pdoSqlConnect();
    $query = "insert into HavingCollection(userId, goalNo, collectionNo) values (?,?,4);";
    $st = $pdo->prepare($query);
    $st->execute([$id, $goalNo]);
    $st = null;
    $pdo = null;
}

function getCollectionFive($id, $goalNo){
    $pdo = pdoSqlConnect();
    $query = "insert into HavingCollection(userId, goalNo, collectionNo) values (?,?,5);";
    $st = $pdo->prepare($query);
    $st->execute([$id, $goalNo]);
    $st = null;
    $pdo = null;
}

function getCollectionSix($id, $goalNo){
    $pdo = pdoSqlConnect();
    $query = "insert into HavingCollection(userId, goalNo, collectionNo) values (?,?,6);";
    $st = $pdo->prepare($query);
    $st->execute([$id, $goalNo]);
    $st = null;
    $pdo = null;
}

function getCollectionSeven($id, $goalNo){
    $pdo = pdoSqlConnect();
    $query = "insert into HavingCollection(userId, goalNo, collectionNo) values (?,?,7);";
    $st = $pdo->prepare($query);
    $st->execute([$id, $goalNo]);
    $st = null;
    $pdo = null;
}

function alreadySetCollectionFour($id, $goalNo){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM HavingCollection
    WHERE userId = ? and goalNo =? and collectionNo = 4) as exist;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $goalNo]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function alreadySetCollectionFive($id, $goalNo){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM HavingCollection
    WHERE userId = ? and goalNo =? and collectionNo = 5) as exist;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $goalNo]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function alreadySetCollectionSix($id, $goalNo){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM HavingCollection
    WHERE userId = ? and goalNo =? and collectionNo = 6) as exist;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $goalNo]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function alreadySetCollectionSeven($id, $goalNo){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM HavingCollection
    WHERE userId = ? and goalNo =? and collectionNo = 7) as exist;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $goalNo]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function getUserCollection($id){
    $pdo = pdoSqlConnect();
    $query = "SELECT collectionTB.no, name, imageUrl, COUNT(collectionNo) cnt FROM HavingCollection
INNER JOIN (SELECT * FROM Collection) collectionTB
on collectionNo = collectionTB.no
WHERE userId = ?
GROUP BY collectionNo, collectionTB.no
ORDER BY collectionTB.no;";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;
}

function likes($id, $contentsNo){
    $pdo = pdoSqlConnect();
    $query = "insert into Likes (userId, contentsNo, likeFlag) values (?,?,1);";
    $st = $pdo->prepare($query);
    $st->execute([$id, $contentsNo]);
    $st = null;
    $pdo = null;
}

function existsLikes($id, $contentsNo){
    $pdo = pdoSqlConnect();
    $query = "select exists (select * from Likes where userId = ? and contentsNo =? and likeFlag = 1 and isDeleted = 'N') as exist;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $contentsNo]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function deleteLikes($id, $contentsNo){
    $pdo = pdoSqlConnect();
    $query = "update Likes set isDeleted = 'Y' where userId = ? and contentsNo = ? and likeFlag = 1;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $contentsNo]);
    $st = null;
    $pdo = null;
}

function existsDislikes($id, $contentsNo){
    $pdo = pdoSqlConnect();
    $query = "select exists (select * from Likes where userId = ? and contentsNo =? and likeFlag = 0 and isDeleted = 'N') as exist;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $contentsNo]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function dislikes($id, $contentsNo){
    $pdo = pdoSqlConnect();
    $query = "insert into Likes (userId, contentsNo, likeFlag) values (?,?,0);";
    $st = $pdo->prepare($query);
    $st->execute([$id, $contentsNo]);
    $st = null;
    $pdo = null;
}

function deleteDislikes($id, $contentsNo){
    $pdo = pdoSqlConnect();
    $query = "update Likes set isDeleted = 'Y' where userId = ? and contentsNo = ? and likeFlag = 0;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $contentsNo]);
    $st = null;
    $pdo = null;
}
