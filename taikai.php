<?php 
require('dbconnect.php');//データベースに接続

session_start();//セッションスタート

if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()){
    //ログインしている
    $id = $_SESSION['id'];

    //ログイン者の投稿を削除する
    $del_post = $db->prepare('DELETE FROM posts WHERE member_id=?');
    $del_post->execute(array($id));

    //ログイン者の会員情報を削除する
    $del_member = $db->prepare('DELETE FROM members WHERE id=?');
    $del_member->execute(array($id));

    //ログアウトする
    //セッション情報を削除
    $_SESSION = array();
    if(ini_get("session.use_cookies")){
        $params = session_get_cookie_params();
        setcookie(session_name(), '',time() - 42000, 
        $param["path"], $param["domain"], $param["secure"], $param["httponly"]);
    }
    session_destroy();

    //Cookie情報も削除
    setcookie('email', '', time()-3600);
    setcookie('password', '', time()-3600);

    header('Location: taikai_thanks.html');
    exit();

}else{
    //ログインしていない
    header('Location: login.php');
    exit();
}
?>
