<?php
// HTMLエスケープのショートカット関数
function h($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

require('dbconnect.php');//データベースに接続

session_start();//セッションスタート

if(isset($_COOKIE['email'])){
    if($_COOKIE['email'] != ''){
        $_POST['email'] = $_COOKIE['email'];
        $_POST['password'] = $_COOKIE['password'];
        $_POST['save'] = 'on';
    }
}

if(!empty($_POST)){
    //ログインの処理
    if($_POST['email'] != '' && $_POST['password'] != ''){
        $login = $db->prepare('SELECT * FROM members WHERE email=? AND password=?');//password？
        $login->execute(array(
            $_POST['email'],
            sha1($_POST['password'])
        ));
        $member = $login->fetch();

        if($member){
            //ログイン成功
            $_SESSION['id'] = $member['id'];
            $_SESSION['time'] = time();

                //ログイン情報を記録する
                if($_POST['save'] == 'on'){
                    setcookie('email',$_POST['email'],time()+60*60*24*14);
                    setcookie('password',$_POST['password'],time()+60*60*24*14);
                }

            header('Location: index.php');
            exit();//必要？
        }else{
            $error['login'] = 'failed';
        }
    }else{
        $error['login'] = 'blank';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rain and Cappuccino</title>
    <link rel="stylesheet" href="./style.css" />
    <link rel="stylesheet" href="rainDrops.css">
</head>

<body>
    <div id="backScreen"></div>

        <div id="wrap">
            <div id="head">
                <h1>ログイン</h1>
            </div>
            <div id="content">
                <div id="lead">
                    <p>メールアドレスとパスワードを記入して <br>ログインしてください。</p>
                    <p>入会手続きがまだの方はこちらからどうぞ。</p>
                    <p>&raquo;<a href="join/">入会手続きをする</a></p>
                </div>
                <form action="" method="post">
                    <dl>
                        <dt>メールアドレス</dt>
                        <dd>
                            <input type="text" name="email" size="35" maxlength="255" value="<?php if(isset($_POST['email'])){echo h($_POST['email']);} ?>">
                            <?php if(isset($error['login'])): ?>
                            <?php if($error['login'] == 'blank'): ?>
                                <p class="error">* メールアドレスとパスワードをご記入ください。</p>
                            <?php endif; ?>
                            <?php if($error['login'] == 'failed'): ?>
                                <p class="error">* ログインに失敗しました。<br><span class="align_left">正しくご記入ください。</span></p>
                            <?php endif; ?>
                            <?php endif; ?>
                        </dd>
                        <dt>パスワード</dt>
                        <dd>
                            <input type="password" name="password" size="35" maxlength="255" value="<?php if(isset($_POST['password'])){echo h($_POST['password']);} ?>">
                        </dd>
                        <dt>ログイン情報の記録</dt>
                        <dd>
                            <input id="save" type="checkbox" name="save" value="on"><label for="save">次回からは自動的にログインする</label>
                        </dd>
                        <div id="custom-submit-button"><input type="submit" value="ログインする" class="custom-submit-button"></div>
                    </dl>
                </form>
            </div>
        </div>

    <script src="main.js"></script>
</body>
</html>