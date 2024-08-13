<?php
// データベース接続の試行
try {
    $dsn = 'mysql:host=localhost;dbname=mini_bbs';
    $username = 'root';
    $password = '';
    $db = new PDO($dsn, $username, $password);
    // エラーが発生した場合は例外を投げる
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // 接続エラーが発生した場合の処理
    echo "接続エラー: " . $e->getMessage();
    exit(); // スクリプトの実行を停止する
}
