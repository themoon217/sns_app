<?php
require('../dbconnect.php');
session_start();

if (!isset($_SESSION['join'])) {
	header('Location: index.php');
	exit();
}

if (!empty($_POST)) {
	// 登録処理をする
	$statement = $db->prepare('INSERT INTO members SET name=?, email=?, password=?, picture=?, created=NOW()');
	echo $ret = $statement->execute(array(
		$_SESSION['join']['name'],
		$_SESSION['join']['email'],
		sha1($_SESSION['join']['password']),
		$_SESSION['join']['image']
	));
	unset($_SESSION['join']);

	header('Location: thanks.php');
	exit();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Rain and Cappuccino</title>
	<link rel="stylesheet" href="../style.css" />
	<link rel="stylesheet" href="../rainDrops.css">
</head>

<body>
	<div id="backScreen"></div>

		<div id="wrap">
			<div id="head">
				<h1>会員登録</h1>
			</div>
			<div id="content">
				<p>記入した内容を確認して、<br>「登録する」ボタンをクリックしてください</p>
				<form action="" method="post">
					<input type="hidden" name="action" value="submit" />
					<dl>
						<dt class="thick_font">ニックネーム</dt>
						<dd>
							<?php echo htmlspecialchars($_SESSION['join']['name'], ENT_QUOTES, 'UTF-8'); ?>
						</dd>
						<dt class="thick_font">メールアドレス</dt>
						<dd>
							<?php echo htmlspecialchars($_SESSION['join']['email'], ENT_QUOTES, 'UTF-8'); ?>
						</dd>
						<dt class="thick_font">パスワード</dt>
						<dd>
							【表示されません】
						</dd>
						<dt class="thick_font">写真など</dt>
						<dd>
							<img src="../member_picture/<?php echo $_SESSION['join']['image']; ?>" width="auto" height="100" alt="" />
						</dd>
					</dl>
					<div>
					<div><a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a></div>
					<div id="custom-submit-button"><input type="submit" value="登録する" class="custom-submit-button"/></div>
					</div>
				</form>
			</div>
		</div>

	<script src="../main.js"></script>
</body>
</html>
