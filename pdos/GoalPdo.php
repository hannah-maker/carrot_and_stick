<?php

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
    $query = "select contents as goal, createdAt, isDeleted from GoalList
where userId = ? and isDeleted = 'Y'";
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
WHERE consecutive = 1 AND DATE_FORMAT(createdAt, '%Y-%m-%d') = CURDATE()
ORDER BY createdAt limit 1) as exist;";
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
WHERE consecutive = 3 AND DATE_FORMAT(createdAt, '%Y-%m-%d') = CURDATE()
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

function getCollectionOneDone($id, $goalNo){
    $pdo = pdoSqlConnect();
    $query = "insert into HavingCollection(userId, goalNo, collectionNo) values (?,?,4);";
    $st = $pdo->prepare($query);
    $st->execute([$id, $goalNo]);
    $st = null;
    $pdo = null;
}

function alreadySetCollection($id, $goalNo, $collectionNo){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM HavingCollection
    WHERE userId = ? and goalNo =? and collectionNo =?) as exist;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $goalNo, $collectionNo]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function getUserCollection($id){
    $pdo = pdoSqlConnect();
    $query = "SELECT collectionTB.no, imageUrl FROM HavingCollection
INNER JOIN (SELECT * FROM Collection) collectionTB
on collectionNo = collectionTB.no
WHERE userId = ?";
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

function watchingVideo($id, $type, $contentsNo){
    $pdo = pdoSqlConnect();
    $query = "insert into WatchingVideo (userId, type, contentsNo) values (?,?,?);";
    $st = $pdo->prepare($query);
    $st->execute([$id, $type, $contentsNo]);
    $st = null;
    $pdo = null;
}

function countPlay($id, $contentsNo){
    $pdo = pdoSqlConnect();
    $query = "update WatchingVideo set hit = hit + 1 where userId = ? and contentsNo = ?";
    $st = $pdo->prepare($query);
    $st->execute([$id, $contentsNo]);
    $st = null;
    $pdo = null;
}

function alreadyWatching($id, $contentsNo){
    $pdo = pdoSqlConnect();
    $query = "select exists(select * from WatchingVideo where userId = ? and contentsNo = ?)AS exist;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $contentsNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function tvWatchingList($id){
    $pdo = pdoSqlConnect();
    $query = "select contentsNo, title, posterUrl, duration from
(select no, contentsNo, seasonNum as duration, episodeName as title, posterUrl, title as name, runtime, userId,
                      cNo, updatedAt from WatchingVideo
inner join
    (select Series.no as sNo, contentsNo as sContetnsNo, seasonNum, episodeName, runtime from Series) S
on WatchingVideo.contentsNo = sNo
inner join
    (select Contents.no as cNo, posterUrl, title from Contents) C
on sContetnsNo = cNo
where userId = ? and type = 2
    )
    as allContents order by updatedAt;";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;
}

function movieWatchingList($id){
    $pdo = pdoSqlConnect();
    $query = "select contentsNo, title, posterUrl, duration from WatchingVideo
inner join (select no, title, duration, posterUrl, videoUrl from Contents) as cTB
on WatchingVideo.contentsNo = cTB.no
where userId = ?
order by updatedAt desc;";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;
}

function userScrapInfo($id, $contentsNo){
    $pdo = pdoSqlConnect();
    $query = "select no from Contents
inner join (select userId, contentsNo, isDeleted from Scrap) scrapTB
on Contents.no = scrapTB.contentsNo
where userId = ? and no = ? and isDeleted = 'N';";
    $st = $pdo->prepare($query);
    $st->execute([$id, $contentsNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res[0];
}

function userLikeInfo($id, $contentsNo){
    $pdo = pdoSqlConnect();
    $query = "select likeFlag from Contents
inner join (select userId, contentsNo, likeFlag, isDeleted from Likes) likeTB
on Contents.no = likeTB.contentsNo
where userId = ? and contentsNo = ? and isDeleted = 'N';";
    $st = $pdo->prepare($query);
    $st->execute([$id, $contentsNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res[0];
}

function contentsHistory($id, $id2){
    $pdo = pdoSqlConnect();
    $query = "select contentsNo, title, posterUrl, duration from (select * from (select contentsNo, title, posterUrl, duration, updatedAt from WatchingVideo
inner join (select no, title, duration, posterUrl, videoUrl from Contents) as cTB
on WatchingVideo.contentsNo = cTB.no
where userId = ? and type = 1) AS M
UNION all
select contentsNo, title, posterUrl, duration, updatedAt from
(select no, contentsNo, seasonNum as duration, episodeName as title, posterUrl, title as name, runtime, userId,
                      cNo, updatedAt from WatchingVideo
inner join
    (select Series.no as sNo, contentsNo as sContetnsNo, seasonNum, episodeName, runtime from Series) S
on WatchingVideo.contentsNo = sNo
inner join
    (select Contents.no as cNo, posterUrl, title from Contents) C
on sContetnsNo = cNo
where userId = ? and type = 2
    ) as T)
    as allContents order by updatedAt;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $id2]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;
}

function scrapAndLike($id, $contentsNo){
    $pdo = pdoSqlConnect();
    $query = "select no,type, title, director, cast, overview, `release`, rating, duration, posterUrl, videoUrl, likeFlag as likeStatus, scrapTB.isDeleted as sacrapStatus from Contents
inner join (select userId, contentsNo, isDeleted from Scrap) scrapTB
on Contents.no = scrapTB.contentsNo
inner join (select userId, contentsNo, likeFlag, isDeleted from Likes) likeTB
on Contents.no = likeTB.contentsNo
where scrapTB.userId = ? and likeTB.userId = scrapTB.userId and no = ? and scrapTB.isDeleted = 'N' and likeTB.isDeleted = 'N';";
    $st = $pdo->prepare($query);
    $st->execute([$id, $contentsNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res[0];
}

function onlyScrap($id, $contentsNo){
    $pdo = pdoSqlConnect();
    $query = "select no,type, title, director, cast, overview, `release`, rating, duration, posterUrl, videoUrl, scrapTB.isDeleted as scrapStatus from Contents
inner join (select userId, contentsNo, isDeleted from Scrap) scrapTB
on Contents.no = scrapTB.contentsNo
where userId = ? and no = ? and isDeleted = 'N';";
    $st = $pdo->prepare($query);
    $st->execute([$id, $contentsNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res[0];
}

function onlyLike($id, $contentsNo){
    $pdo = pdoSqlConnect();
    $query = "select no,type, title, director, cast, overview, `release`, rating, duration, posterUrl, videoUrl, likeFlag as likeStatus from Contents
inner join (select userId, contentsNo, likeFlag, isDeleted from Likes) likeTB
on Contents.no = likeTB.contentsNo
where userId = ? and no = ? and isDeleted = 'N';";
    $st = $pdo->prepare($query);
    $st->execute([$id, $contentsNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res[0];
}