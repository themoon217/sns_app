<?php
require('../dbconnect.php');
session_start();
$error = []; // エラー配列を初期化

if(!empty($_POST)){
    //エラー項目の確認
    if($_POST['name'] == ''){
        $error['name'] = 'blank';
    }
    
    if($_POST['email'] == ''){
        $error['email'] = 'blank';
    }
    if ($_POST['password'] == '') {
        $error['password'] = 'blank';
    } elseif (strlen($_POST['password']) < 4) {
        $error['password'] = 'length';
    }
    $fileName = $_FILES['image']['name'];
    if (!empty($fileName)) {
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)); // ファイル拡張子を取得し、小文字に変換
        if (($ext != 'jpg' && $ext != 'gif') && $ext != 'png') {
            $error['image'] = 'type';
        }
    }
    
    //重複アカウントのチェック
    if(empty($error)){
        $member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
        $member->execute(array($_POST['email']));
        $record = $member->fetch();
        if($record['cnt'] > 0){
            $error['email'] = 'duplicate';
        }
    }

    if(empty($error)){
        //画像をアップロードする
        if(!empty($fileName)){
            $image = date('YmdHis') . $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], '../member_picture/' . $image);
        }else{
            $image = "images.jpg";
        }
        $_SESSION['join'] = $_POST;
        $_SESSION['join']['image'] = $image;
        header('Location: check.php');
        exit();
    }
}

//書き直し
if (isset ($_REQUEST['action']) && $_REQUEST['action'] == 'rewrite') {
    $_POST = $_SESSION['join'];
    $error['rewrite'] = true;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rain and Cappuccino</title>
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="../rainDrops.css">
</head>

<body>
    <div id="backScreen"></div>

        <div id="wrap">
            <div id="head">
                <h1>はじめまして！</h1>
            </div>
            <div id="content">
                <p>次のフォームに入力してください。</p>
                <p>&raquo;<a href="../login.php">ログインはこちらから</a></p>
                <form action="" method="post" enctype="multipart/form-data">
                    <dl>
                        <dt>ニックネーム<span class="required">必須</span></dt>
                        <dd>
                            <input type="text" name="name" size="35" maxlength="255" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8') : ''; ?>" />
                            <?php if (isset($error['name']) && $error['name'] == 'blank') : ?>
                                <p class="error">* ニックネームを入力してください</p>
                            <?php endif; ?>
                        </dd>
                        <dt>メールアドレス<span class="required">必須</span></dt>
                        <dd>    
                            <input type="text" name="email" size="35" maxlength="255" value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES); ?>" />
                            <?php if(isset ($error['email']) && $error['email'] == 'blank'): ?>
                                <p class="error">* メールアドレスを入力してください</p>
                            <?php endif; ?>
                            <?php if (isset($error['email']) && $error['email'] == 'duplicate') : ?>
                                <p class="error">* 指定されたメールアドレスはすでに登録されています</p>
                            <?php endif; ?>
                        </dd>
                        <dt>パスワード<span class="required">必須</span></dt>
                        <dd>
                            <input type="password" name="password" size="10" maxlength="20" value="<?php echo isset($_POST['password']) ? htmlspecialchars($_POST['password'], ENT_QUOTES) : ''; ?>" />
                            <?php if(isset ($error['password']) && $error['password'] == 'blank'): ?>
                                <p class="error">* パスワードを入力してください</p>
                            <?php endif; ?>
                            <?php if(isset ($error['password']) && $error['password'] == 'length'): ?>
                                <p class="error">* パスワードは4文字以上で入力してください</p>
                            <?php endif; ?>
                        </dd>
                        <dt>写真など<span class="required">必須</span></dt>
                        <dd>
                            <input id="upload" type="file" name="image" size="35" accept="image/png, image/jpeg, image/gif" value="<?php echo htmlspecialchars($_POST['image'] ?? '', ENT_QUOTES); ?>"/>
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

                            <?php if (isset($error['image']) && $error['image'] == 'type') : ?>
                                <p class="error">* 写真などは「.gif」または「.jpg」の画像を指定してください</p>
                            <?php endif; ?>
                            <?php if (!empty($error)) : ?>
                                <p class="error">* 画像を入力してください</p>
                            <?php endif; ?>
                        </dd>
                    </dl>
                    <div id="custom-submit-button">
                        <input type="submit" value="入力内容を確認する" class="custom-submit-button"/>
                    </div>
                </form>
            </div>
        </div>

    <script src="../main.js"></script>
</body>
</html>