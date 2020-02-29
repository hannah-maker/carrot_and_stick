<?php

function Scrap($id, $contentsNo){
    $pdo = pdoSqlConnect();
    $query = "insert into Scrap (userId, contentsNo) values (?,?);";
    $st = $pdo->prepare($query);
    $st->execute([$id, $contentsNo]);
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

function validNo($contentsNo){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM Contents WHERE no = ?) AS exist;";
    $st = $pdo->prepare($query);
    $st->execute([$contentsNo]);
    $res = $st->fetchAll();

    return intval($res[0]["exist"]);
}

function validSeriesNo($contentsNo){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM Series WHERE no = ?) AS exist;";
    $st = $pdo->prepare($query);
    $st->execute([$contentsNo]);
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

function myScrap($id){
    $pdo = pdoSqlConnect();
    $query = "select no, title, posterUrl from Contents
inner join (select userId, contentsNo, isDeleted from Scrap) scrapTB
on Contents.no = scrapTB.contentsNo
where userId = ? and isDeleted = 'N'";
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