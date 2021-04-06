<?php
//function lookupFreeBoard($likeCnt)
//{
//    $pdo = pdoSqlConnect();
//    $query = "SELECT no, title, content, id, thumbsUp, commetCnt, createdAt
//                FROM freeBoard
//                     left outer join (select articleNo,
//                          count(no) as commetCnt
//                from freeComment group by articleNo) commentCntTB
//                    on freeBoard.no = commentCntTB.articleNo
//              WHERE freeBoard.thumbsUp >= ?
//              ORDER BY no DESC;";
//
//    $st = $pdo->prepare($query);
//    $st->execute($likeCnt);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//
//    return $res;
//}

//function boardDetail($boardNo){
//    $pdo = pdoSqlConnect();
//    $query = "SELECT * FROM Board WHERE no = 2;";
//    $st = $pdo->prepare($query);
//    $st->execute([$boardNo]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//
//    return $res[0];
//}

//function boardDetail($boardNo){
//    $pdo = pdoSqlConnect();
//    $query = "ELECT * FROM Board WHERE no = 2;";
//
//    $st = $pdo->prepare($query);
//    $st->execute([$boardNo]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//    $st = null;
//    $pdo = null;
//    return $res[0];
//}


function articleDetail($articleNo){
    $pdo = pdoSqlConnect();
    $query = "SELECT no, title
                    from freeBoard
                    ORDER BY freeBoard.no DESC;";
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}


function freeListAll(){
    $pdo = pdoSqlConnect();
    $query = "SELECT no, title, content, id, thumbsUpCnt, commetCnt, createdAt
                FROM freeBoard
                     left outer join (select articleNo,
                          count(no) as commetCnt
                from freeComment group by articleNo) commentCntTB
                    on freeBoard.no = commentCntTB.articleNo
                     left outer join (select thumbsUpNo,
                          count(no) as thumbsUpCnt
                         from ThumbsUp group by thumbsUpNo)thumbsUpTB
                    on freeBoard.no = thumbsUpTB.thumbsUpNo
                    where category = 1 ORDER BY freeBoard.no DESC limit 0, 10";

    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function secretListAll(){
    $pdo = pdoSqlConnect();
    $query = "SELECT no, title, content, id, thumbsUpCnt, commetCnt, createdAt
                FROM freeBoard
                     left outer join (select articleNo,
                          count(no) as commetCnt
                from freeComment group by articleNo) commentCntTB
                    on freeBoard.no = commentCntTB.articleNo
                     left outer join (select thumbsUpNo,
                          count(no) as thumbsUpCnt
                         from ThumbsUp group by thumbsUpNo)thumbsUpTB
                    on freeBoard.no = thumbsUpTB.thumbsUpNo
                    where category = 2 ORDER BY freeBoard.no DESC limit 0, 10;";
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function scrollDown(){
    $pdo = pdoSqlConnect();
    $query = "SELECT freeBoard.no, title, freeBoard.content, freeBoard.id, thumbsUp, commetCnt, freeBoard.createdAt
                FROM freeBoard
                     left outer join (select articleNo,
                          count(no) as commetCnt
                from freeComment group by articleNo) commentCntTB
                    on freeBoard.no = commentCntTB.articleNo
                    where category = 1 and freeBoard.no < '".$_GET['last_id']."' ORDER BY freeBoard.no DESC limit 0, 8;";
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function infiniteScroll($lastNo){
    $pdo = pdoSqlConnect();
    $query = "SELECT no, title, content, id, thumbsUpCnt, commetCnt, createdAt
                FROM freeBoard
                     left outer join (select articleNo,
                          count(no) as commetCnt
                from freeComment group by articleNo) commentCntTB
                    on freeBoard.no = commentCntTB.articleNo
                     left outer join (select thumbsUpNo,
                          count(no) as thumbsUpCnt
                         from ThumbsUp group by thumbsUpNo)thumbsUpTB
                    on freeBoard.no = thumbsUpTB.thumbsUpNo
                    where category = 1 and freeBoard.no < ? ORDER BY freeBoard.no DESC limit 0, 10;";

    $st = $pdo->prepare($query);
    $st->execute([$lastNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function infiniteScrollSecret($lastNo){
    $pdo = pdoSqlConnect();
    $query = "SELECT no, title, content, id, thumbsUpCnt, commetCnt, createdAt
                FROM freeBoard
                     left outer join (select articleNo,
                          count(no) as commetCnt
                from freeComment group by articleNo) commentCntTB
                    on freeBoard.no = commentCntTB.articleNo
                     left outer join (select thumbsUpNo,
                          count(no) as thumbsUpCnt
                         from ThumbsUp group by thumbsUpNo)thumbsUpTB
                    on freeBoard.no = thumbsUpTB.thumbsUpNo
                    where category = 2 and freeBoard.no < ? ORDER BY freeBoard.no DESC limit 0, 10;";

    $st = $pdo->prepare($query);
    $st->execute([$lastNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function validScroll($lastNo){
    $pdo = pdoSqlConnect();
    $query = "select exists(select no, firstNo from freeBoard
                self join (select no as firstNo from freeBoard where category = 1 order by no limit 1 ) noTB
                on no = noTB.firstNo
                where no = ?) as exist;";
    $st = $pdo->prepare($query);
    $st->execute([$lastNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return intval($res[0]["exist"]);
}


function validSecretScroll($lastNo){
    $pdo = pdoSqlConnect();
    $query = "select exists(select no, firstNo from freeBoard
                self join (select no as firstNo from freeBoard where category = 2 order by no limit 1 ) noTB
                on no = noTB.firstNo
                where no = ?) as exist;";
    $st = $pdo->prepare($query);
    $st->execute([$lastNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return intval($res[0]["exist"]);
}

function successFreeBoard(){
    $pdo = pdoSqlConnect();
    $query = "SELECT no, title, content, id, thumbsUpCnt, commetCnt, createdAt
                FROM freeBoard
                     left outer join (select articleNo,
                          count(no) as commetCnt
                from freeComment group by articleNo) commentCntTB
                    on freeBoard.no = commentCntTB.articleNo
                     left outer join (select thumbsUpNo,
                          count(no) as thumbsUpCnt
                         from ThumbsUp group by thumbsUpNo)thumbsUpTB
                    on freeBoard.no = thumbsUpTB.thumbsUpNo
                    where category = 1
              ORDER BY no DESC limit 0,1";
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function successSecretBoard(){
    $pdo = pdoSqlConnect();
    $query = "SELECT no, title, content, id, thumbsUpCnt, commetCnt, createdAt
                FROM freeBoard
                     left outer join (select articleNo,
                          count(no) as commetCnt
                from freeComment group by articleNo) commentCntTB
                    on freeBoard.no = commentCntTB.articleNo
                     left outer join (select thumbsUpNo,
                          count(no) as thumbsUpCnt
                         from ThumbsUp group by thumbsUpNo)thumbsUpTB
                    on freeBoard.no = thumbsUpTB.thumbsUpNo
                    where category = 2
              ORDER BY no DESC limit 0,3";
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function lookupFreeBoard()
{
    $pdo = pdoSqlConnect();
    $query = "SELECT no, title, content, id, thumbsUpCnt, commetCnt, createdAt
                FROM freeBoard
                     left outer join (select articleNo,
                          count(no) as commetCnt
                from freeComment group by articleNo) commentCntTB
                    on freeBoard.no = commentCntTB.articleNo
                     left outer join (select thumbsUpNo,
                          count(no) as thumbsUpCnt
                         from ThumbsUp group by thumbsUpNo)thumbsUpTB
                    on freeBoard.no = thumbsUpTB.thumbsUpNo
                    where category = 1
              ORDER BY no DESC;";
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function lookupSecretBoard()
{
    $pdo = pdoSqlConnect();
    $query = "SELECT no, title, content, id, thumbsUpCnt, commetCnt, createdAt
                FROM freeBoard
                     left outer join (select articleNo,
                          count(no) as commetCnt
                from freeComment group by articleNo) commentCntTB
                    on freeBoard.no = commentCntTB.articleNo
                     left outer join (select thumbsUpNo,
                          count(no) as thumbsUpCnt
                         from ThumbsUp group by thumbsUpNo)thumbsUpTB
                    on freeBoard.no = thumbsUpTB.thumbsUpNo
                    where category = 2
              ORDER BY no DESC;";

    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function lookupFreeComment() //자유게시판 댓글 정렬
{
    $pdo = pdoSqlConnect();
    $query = "select content, id, createdAt
              from freeComment
              order by if(isnull(parent), no, parent)";
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function isExistsUser($id){

    $pdo = pdoSqlConnect();

    $query = "SELECT EXISTS(SELECT * FROM User WHERE id = ?) AS exist;";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $res = $st->fetchAll();

    return intval($res[0]["exist"]);
}



// 글 작성 pdo
function postBoard($id, $title, $contents, $type){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO Board (userId, title, contents, type) VALUES (?,?,?,?);";
    $st = $pdo->prepare($query);
    $st->execute([$id, $title, $contents, $type]);
    //$st->setFetchMode(PDO::FETCH_ASSOC); insert 문에서는 빼기
    //$res = $st->fetchAll();
    $st = null;
    $pdo = null;
}









function writeSecretBoard($id, $title, $content){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO freeBoard (id, title, content, category) VALUES (?,?,?,'2');";
    $st = $pdo->prepare($query);
    $st->execute([$id, $title, $content]);
    //$st->setFetchMode(PDO::FETCH_ASSOC); insert 문에서는 빼기
    //$res = $st->fetchAll();

    $st = null;
    $pdo = null;
}

function writeCommentFreeBoard(){
    $pdo = pdoSqlConnect();
    $query = "select *
              from freeComment
              order by if(isnull(parent), no, parent);";
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
    //1번글에 댓글 작성 -> articleNo 선택,
    //1건글의 1번 댓글에 대댓글 작성
}

//function articleThumbsUp($articleNo)
//{
//    $pdo = pdoSqlConnect();
//    $query = "UPDATE freeBoard SET thumbsUp = thumbsUp + 1 WHERE no=?;";
//    $st = $pdo->prepare($query);
//    $st->execute([$articleNo]);
//    $st = null;
//    $pdo = null;
//}

function showComment($articleNo){
    $pdo = pdoSqlConnect();
    $query = "select no, content, id, createdAt
              from freeComment
              where articleNo = ?
              order by if(isnull(parent), no, parent);";

    $st = $pdo->prepare($query);
    $st->execute([$articleNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;
}

function showArticle($articleNo){
    $pdo = pdoSqlConnect();
    $query = "SELECT no, title, content, id, thumbsUpCnt, commetCnt, createdAt
                FROM freeBoard
                     left outer join (select articleNo,
                          count(no) as commetCnt
                from freeComment group by articleNo) commentCntTB
                    on freeBoard.no = commentCntTB.articleNo
                     left outer join (select thumbsUpNo,
                          count(no) as thumbsUpCnt
                         from ThumbsUp group by thumbsUpNo)thumbsUpTB
                    on freeBoard.no = thumbsUpTB.thumbsUpNo
                    where no = ?
              ORDER BY no DESC;";

    $st = $pdo->prepare($query);
    $st->execute([$articleNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res[0];
}

function hotArticle($thumbsUpCnt)
    {
        $pdo = pdoSqlConnect();
        $query = "SELECT no, title, content, id, thumbsUpCnt, commetCnt, createdAt
                FROM freeBoard
                     left outer join (select articleNo,
                          count(no) as commetCnt
                from freeComment group by articleNo) commentCntTB
                    on freeBoard.no = commentCntTB.articleNo
                     left outer join (select thumbsUpNo,
                          count(no) as thumbsUpCnt
                         from ThumbsUp group by thumbsUpNo)thumbsUpTB
                    on freeBoard.no = thumbsUpTB.thumbsUpNo
                    where thumbsUpCnt >= ?
              ORDER BY no DESC;";

        $st = $pdo->prepare($query);
        $st->execute([$thumbsUpCnt]);
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $res = $st->fetchAll();

        $st = null;
        $pdo = null;

        return $res;
    }

function thumbsUpCntExists($thumbsUpCnt){
    $pdo = pdoSqlConnect();

    $query = "select exists(select no, title, content, id, thumbsUpCnt, commetCnt, createdAt
                FROM freeBoard
                     left outer join (select articleNo,
                          count(no) as commetCnt
                from freeComment group by articleNo) commentCntTB
                    on freeBoard.no = commentCntTB.articleNo
                     left outer join (select thumbsUpNo,
                          count(no) as thumbsUpCnt
                         from ThumbsUp group by thumbsUpNo)thumbsUpTB
                    on freeBoard.no = thumbsUpTB.thumbsUpNo
                    where thumbsUpCnt = ?) as exist;";
    $st = $pdo->prepare($query);
    $st->execute([$thumbsUpCnt]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function myArticle($id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT no, title, content, id, thumbsUpCnt, commetCnt, createdAt
                FROM freeBoard
                     left outer join (select articleNo,
                          count(no) as commetCnt
                from freeComment group by articleNo) commentCntTB
                    on freeBoard.no = commentCntTB.articleNo
                     left outer join (select thumbsUpNo,
                          count(no) as thumbsUpCnt
                         from ThumbsUp group by thumbsUpNo)thumbsUpTB
                    on freeBoard.no = thumbsUpTB.thumbsUpNo
                    where freeBoard.id = ?
              ORDER BY no DESC;";

    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function myComment($id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT no, title, content, id, thumbsUpCnt, commetCnt, createdAt
                FROM freeBoard
                     left outer join (select articleNo,
                          count(no) as commetCnt
                from freeComment group by articleNo) commentCntTB
                    on freeBoard.no = commentCntTB.articleNo
                     left outer join (select thumbsUpNo,
                          count(no) as thumbsUpCnt
                         from ThumbsUp group by thumbsUpNo)thumbsUpTB
                    on freeBoard.no = thumbsUpTB.thumbsUpNo
                    inner join (select articleNo
                from freeComment
                     where id = ?
                     group by articleNo) commentIdTB
                         on freeBoard.no = commentIdTB.articleNo
              ORDER BY no DESC;";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function myScrap($id){
    $pdo = pdoSqlConnect();
    $query = "SELECT no, title, content, id, thumbsUpCnt, commetCnt, createdAt
                FROM freeBoard
                     left outer join (select articleNo,
                          count(no) as commetCnt
                from freeComment group by articleNo) commentCntTB
                    on freeBoard.no = commentCntTB.articleNo
                     left outer join (select thumbsUpNo,
                          count(no) as thumbsUpCnt
                         from ThumbsUp group by thumbsUpNo)thumbsUpTB
                    on freeBoard.no = thumbsUpTB.thumbsUpNo
                    inner join (select scrapNo
                from Scrap
                     where userId = ?
                     group by scrapNo) scrapTB
                         on freeBoard.no = scrapTB.scrapNo
              ORDER BY no DESC;";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;
}

function writeComment($id, $articleNo, $content){
    $pdo = pdoSqlConnect();
    $query = "insert into freeComment (id, articleNo, content) values (?,?,?);";
    $st = $pdo->prepare($query);
    $st->execute([$id, $articleNo, $content]);
    $st = null;
    $pdo = null;
}

function writeReComment($id, $content, $articleNo, $parent){
    $pdo = pdoSqlConnect();
    $query = "insert into freeComment (id, content, articleNo, parent) values (?,?,?,?);";
    $st = $pdo->prepare($query);
    $st->execute([$id, $content, $articleNo, $parent]);
    $st = null;
    $pdo = null;
}

function Scrap($id, $articleNo){
    $pdo = pdoSqlConnect();
    $query = "insert into Scrap (userId, scrapNo) values (?,?);";
    $st = $pdo->prepare($query);
    $st->execute([$id, $articleNo]);
    $st = null;
    $pdo = null;
}

function thumbsUp($id, $articleNo){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO ThumbsUp (userId, thumbsUpNo) VALUES (?,?);";
    $st = $pdo->prepare($query);
    $st->execute([$id, $articleNo]);
    $st = null;
    $pdo = null;
}

function alreadyScrap($id, $articleNo){
    $pdo = pdoSqlConnect();

    $query = "SELECT EXISTS(SELECT * FROM Scrap WHERE userId = ? AND scrapNo = ?) AS exist;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $articleNo]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function existsUser($id){

    $pdo = pdoSqlConnect();

    $query = "SELECT EXISTS(SELECT * FROM User WHERE id = ?) AS exist;";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $res = $st->fetchAll();

    return intval($res[0]["exist"]);
}

function alreadyThumbsUp($id, $articleNo){
    $pdo = pdoSqlConnect();

    $query = "SELECT EXISTS(SELECT * FROM ThumbsUp WHERE userId = ? and thumbsUpNo = ?) AS exist;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $articleNo]);
    $res = $st->fetchAll();

    return intval($res[0]["exist"]);
}

function validNo($articleNo){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM freeBoard WHERE no = ?) AS exist;";
    $st = $pdo->prepare($query);
    $st->execute([$articleNo]);
    $res = $st->fetchAll();

    return intval($res[0]["exist"]);
}

function validParent($articleNo, $parent){
    $pdo = pdoSqlConnect();

    $query = "SELECT EXISTS(SELECT * FROM freeComment WHERE articleNo = ? and no = ?) AS exist;";
    $st = $pdo->prepare($query);
    $st->execute([$articleNo, $parent]);
    $res = $st->fetchAll();

    return intval($res[0]["exist"]);
}

//function commentList($articleNo){
//    $pdo = pdoSqlConnect();
//    $query = "select no, content, id, createdAt, parent
//              from freeComment
//              where articleNo = ?
//              order by if(isnull(parent), no, parent);";
//    $st = $pdo->prepare($query);
//    $st->execute([$articleNo]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//    for($i=0; $i<sizeof($res); $i++){
//        $query = "select no, content, id, createdAt, parent
//              from freeComment
//              where articleNo = ? and parent = ?
//              order by if(isnull(parent), no, parent);";
//        $st = $pdo->prepare($query);
//        $st->execute([$articleNo, $res[$i]["parent"]]);
//        $st->setFetchMode(PDO::FETCH_ASSOC);
//        $res1 = $st->fetchAll();
//        $res[$i]["commentList"] = $res1;
//    }
//    return $res;
//}



function commentList($articleNo){
    $pdo = pdoSqlConnect();
    $query = "select no, content, id, createdAt, parent
              from freeComment
              where articleNo = ? and parent is null
              order by if(isnull(parent), no, parent)";
    $st = $pdo->prepare($query);
    $st->execute([$articleNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    for($i=0; $i < sizeof($res); $i++){
        $query = "select no, content, id, createdAt, parent
              from freeComment
              where articleNo = ? and parent = ?";
        $st = $pdo->prepare($query);
        $st->execute([$articleNo, $res[$i]["no"]]);
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $res1 = $st->fetchAll();
        $res[$i]["commentList"] = $res1;
    }
    return $res;
}


function myPage($id)
{
    $pdo = pdoSqlConnect();
    $query = "select payDate, payDegree, IF(DATEDIFF(NOW(),payDate) < 2, '배송중', '배송완료') as 
deliveryStatus
            from Pay
            where id = ?
            group by payDate, payDegree
            order by payDate, payDegree;";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    for ($i = 0; $i < sizeof($res); $i++) {//배열에 있는 요소들의 갯수를 구할때 사용하는 함수
        $query = "select paySeq, amount, odaPrice, pName, Pay.pNum, imageUrl 
               from Pay inner join Product on Pay.pNum = Product.pNum 
               inner join ProductImage on ProductImage.pNum = Pay.pNum 
               where Pay.id = ? and ProductImage.type = 'main' and ProductImage.turn = 1
               and Pay.payDate = ? and Pay.payDegree = ?;";
        $st = $pdo->prepare($query);
        $st->execute([$id, $res[$i]["payDate"], $res[$i]["payDegree"]]);
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $res1 = $st->fetchAll();
        $res[$i]["orderList"] = $res1;
    }
    return $res;
}

