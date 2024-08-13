<?php
require('dbconnect.php');

// セッションを開始する
session_start();

// セッションにidがあり、かつ有効期限内であるかを確認する
if (isset($_SESSION['id']) && isset($_SESSION['time']) && $_SESSION['time'] + 3600 > time()) {
    // ログインしている場合、セッションの時間を更新する
    $_SESSION['time'] = time();

    // ログインしているユーザーの情報を取得する
    $stmt_member = $db->prepare('SELECT * FROM members WHERE id=?');
    $stmt_member->execute(array($_SESSION['id']));
    $member = $stmt_member->fetch();
} else {
    // ログインしていない場合はログインページにリダイレクトする
    header('Location: top.html');
    exit();
}

// 返信の場合
if (isset($_REQUEST['res'])) {
    $stmt_response = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=? ORDER BY p.created DESC');
    $stmt_response->execute(array($_REQUEST['res']));

    $table = $stmt_response->fetch();
    $message = '@' . $table['name'] . ' ' . $table['message'];
} else {
    // 返信でない場合は空のメッセージを設定する
    $message = '';
}

// 投稿が行われた場合
if (!empty($_POST)) {
    $picture_post = '';
    $fileName = $_FILES['image']['name'];
    echo ($picture_post);
    if (!empty($fileName)) {
        $ext = substr($fileName, -4);
        if ($ext == '.gif' || $ext == '.jpg' || $ext == '.png') {   // 拡張子
            $picture_post = date('YmdHis') . $_FILES['image']['name'];
            $success = move_uploaded_file($_FILES['image']['tmp_name'], 'post_picture/' . $picture_post);
        }
    }
    if ($_POST['message'] != '') {


        // 返信先のIDが空でないか確認し、空でなければその値をセットする
        $reply_post_id = isset($_POST['reply_post_id']) && $_POST['reply_post_id'] !== '' ? $_POST['reply_post_id'] : null;

        //＠投稿者を追加
        if(isset($table)){$realMessage = h($_POST['message']).$message;}
        else{$realMessage = h($_POST['message']);}

        // 投稿をデータベースに挿入する
        $stmt_message = $db->prepare('INSERT INTO posts (member_id, message, reply_post_id, created, picture_post) VALUES (?, ?, ?, NOW(), ?)');
        $stmt_message->execute(array(
            $member['id'],
            $realMessage,
            $reply_post_id,
            $picture_post  // 画像のパスを代入したい
        ));

        // 投稿後はトップページにリダイレクトする
        header('Location: index.php');
        exit();
    }
}

// 投稿の取得
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$page = max($page, 1);

// 最終ページを計算する
$stmt_count = $db->query('SELECT COUNT(*) AS cnt FROM posts');
$cnt = $stmt_count->fetch();
$maxPage = ceil($cnt['cnt'] / 5);
$page = min($page, $maxPage);

$start = ($page - 1) * 5;
$start = max(0, $start);

$stmt_posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC LIMIT ?, 5');
$stmt_posts->bindParam(1, $start, PDO::PARAM_INT);
$stmt_posts->execute();


// HTMLエスケープのショートカット関数
function h($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// 本文内のURLにリンクを設定する関数
function makeLink($value)
{
    return mb_ereg_replace("(https?)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)", '<a href="\1\2">\1\2</a>', $value);
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rain and Cappuccino</title>
    <link rel="stylesheet" href="itiran.css">
    <link rel="stylesheet" href="rainDrops.css">

    <!-- タイトルフォントここから -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cookie&display=swap" rel="stylesheet">
    <!-- ここまで -->

</head>

<body>
    <div id="backScreen"></div>

    <!-- ここから変更　こうの -->
    <div class="nav">
        <div class="Login-button"><a href="logout.php">ログアウト</a></div>
        <div class="taikai-button"><a href="taikai_check.php">退会</a></div>
    </div>
    <div class="cookie-regular">
        <h3>Rain and Cappuccino</h3>
    </div>
    <div id="content">
        <div id="wrap">
            <!-- 投稿フォーム -->
            <!-- ここ変更した！　こうの -->
            <div class="message">
                <h2>カプチーノの香りに包まれながら、</h2>
                <h2>心地よいひとときを ☕</h2>
            </div>
            <!-- ここまで変更した！ こうの　-->

            <form action="" method="post" enctype="multipart/form-data">
                <dl>
                    <dt><?php echo h($member['name']); ?>さん、メッセージをどうぞ</dt>
                    <!-- <dd> -->
                    <textarea class="mes" name="message"></textarea>
                    <input type="hidden" name="reply_post_id" value="<?php echo isset($_REQUEST['res']) ? h($_REQUEST['res']) : ''; ?>" />
                    <?php if(isset($table)): ?>
                        <div style="overflow-wrap: break-word;">
                        <div style="word-break: break-all;">返信先コメント：<?php print(h($table['message'])); ?></div>
                        <div style="word-break: break-all;">返信先の投稿者：<?php print(h($table['name'])); ?></div>
                        </div>
                    <?php endif; ?>
                    <!-- </dd> -->
                    <!-- <dd> -->
                    <input id="upload" type="file" name="image" size="35" accept="image/png, image/jpeg, image/gif" />
                    <div id="fileAlert"></div>
                    <script>
                        const upload = document.getElementById('upload');
                        console.log(upload);
                        upload.addEventListener("change", function(event) {
                            //ファイル名を取得する
                            const fileName = event.target.files[0].name;
                            //ファイル名から拡張子を取得する（splitメソッド）
                            const extension_1 = fileName.split(".")[1];
                            //ファイル名から拡張子を取得する（matchメソッド、正規表現）
                            const extension_2 = fileName.match(/[^.]+$/)[0];
                            // 拡張子がokか判定
                            const ext = ['jpg', 'png', 'gif'];
                            const judge = ext.includes(extension_2);

                            const FileAlert = document.getElementById('fileAlert');
                            // okなら警告なし、ngなら警告+添付ファイル削除
                            if (judge == true) {
                                FileAlert.textContent = '';
                            } else {
                                FileAlert.textContent = 'jpg,png,gifファイルを選択してください';
                                upload.value = '';
                            };
                        });
                    </script>
                    <!-- </dd> -->

                </dl>
                <div>
                    <p id="custom-submit-button">
                        <input type="submit" value="シェア" class="custom-submit-button" />
                    </p>
                </div>
            </form>
        </div><!-- wrap -->
        <!-- 投稿フォーム -->
    </div> <!-- 追加 -->
    <?php foreach ($stmt_posts as $post) : ?>
        <div class="msg" style="overflow-wrap: break-word;">
            <img class="icon" src="member_picture/<?php echo h($post['picture']); ?>" width="48" height="48" alt="<?php echo h($post['name']); ?>" />
            <p style="word-break: break-all;"><?php echo makeLink(h($post['message'])); ?><span class="name">（<?php echo h($post['name']); ?>）</span>[<a href="index.php?res=<?php echo h($post['id']); ?>">Re</a>]</p>
            <!--  -->
            <?php if ($post['picture_post']) : ?>
                <img class="itiran_gazou" src="post_picture/<?php echo h($post['picture_post']); ?>" alt="画像の詳細<?php echo h($post['name']); ?>" />
            <?php endif; ?>
            <!--  -->
            <p class="day"><a href="view.php?id=<?php echo h($post['id']); ?>"><?php echo h($post['created']); ?></a>
                <?php if ($post['reply_post_id'] > 0) : ?>
                    <a href="view.php?id=<?php echo h($post['reply_post_id']); ?>">返信元のメッセージ</a>
                <?php endif; ?>
                <?php if ($_SESSION['id'] == $post['member_id']) : ?>
                    [<a href="delete.php?id=<?php echo h($post['id']); ?>" style="color: #F33;">削除</a>]
                <?php endif; ?>
            </p>
        </div>
    <?php endforeach; ?>

    <!-- ここから -->
    <ul>
        <div class="move">
            <?php if ($page > 1) { ?>
                <div class="back-to-previous">
                    <li><a href="index.php?page=<?php print($page - 1); ?>">≪≪前へ戻る</a></li>
                </div>
            <?php } ?>
            <div class="past-record">
                <?php if ($page < $maxPage) { ?>
                    <li><a href="index.php?page=<?php print($page + 1); ?>">過去の投稿を見る≫≫</a></li>
                <?php } else { ?>
                    <li><a href="index.php">最初に戻る</a></li>
                <?php } ?>
            </div>
        </div>
    </ul>
    <!-- ここまで -->



    <script src="main.js"></script>

</body>

</html>
