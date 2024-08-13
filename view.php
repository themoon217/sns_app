<?php
session_start();
require('dbconnect.php');

if(empty($_REQUEST['id'])){
    header('Location:index.php');exit();
}
// HTMLエスケープのショートカット関数
function h($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

//投稿を取得する
$posts=$db->prepare('SELECT m.name,m.picture,p.*FROM members m,posts p WHERE m.id=p.member_id AND p.id=? 
ORDER BY p.created DESC');
$posts->execute(array($_REQUEST['id']));
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv.="X-UA-Compatible" content="ie=edge">
    <title>Rain and Cappuccino</title>
    <link rel="stylesheet" href="itiran.css">
    <link rel="stylesheet" href="rainDrops.css">
    
</head>
<body>
    <div id="backScreen"></div>

    
        
          
            <p>&laquo;<a href="index.php">一覧に戻る</a></p>
            
                <?php  if($post=$posts->fetch()): ?>
                <div class="msg">
                    <img class="icon" src="member_picture/<?php echo htmlspecialchars($post['picture'],ENT_QUOTES);?>" width="48" height="48" alt="<?php echo htmlspecialchars($post['name'],ENT_QUOTES);?>"/>
                    <p style="word-break: break-all;"><?php echo htmlspecialchars($post['message'],ENT_QUOTES);?><span class="name">(<?php echo  htmlspecialchars($post['name'],ENT_QUOTES); ?>)</span></p> 
                    <?php if($post['picture_post']):?>
                        <img class="itiran_gazou" src="post_picture/<?php echo htmlspecialchars($post['picture_post'],ENT_QUOTES); ?>" width="200" height="auto" alt="画像の詳細<?php echo htmlspecialchars($post['name'],ENT_QUOTES); ?>" />
                    <?php endif; ?>
                    <p class="day"><?php echo htmlspecialchars($post['created'],ENT_QUOTES);?>
                        <?php if ($_SESSION['id'] == $post['member_id']) : ?>
                        [<a href="delete.php?id=<?php echo h($post['id']); ?>" style="color: #F33;">削除</a>]
                        <?php endif; ?>
                    </p>
                </div>
                    <?php else: ?>
                        <p>その投稿は削除されたか、URLが間違えてます</p>
                    <?php endif; ?>
            
    
<script src="main.js"></script>
</body>
</html>