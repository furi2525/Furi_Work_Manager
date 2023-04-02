<?php

function add_db ($name, $hourly_wage) {
    //データベース情報の指定
    $db['dbname'] = "";  // データベース名
    $db['user'] = "";  // ユーザー名
    $db['pass'] = "";  // ユーザー名のパスワード
    $db['host'] = "";  // DBサーバのURL
    $dsn = sprintf('mysql:host=%s; dbname=%s; charset=utf8', $db['host'], $db['dbname']);
    try {
        //PDOを使ってMySQLに接続
        $pdo = new PDO($dsn, $db['user'], $db['pass'], array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));
    } catch (PDOException $e) {
        return array(false, 'データベース接続エラー');
    }
    try {
        // SQL文セット
        $stmt = $pdo->prepare('INSERT INTO user_info (user_id, user_name, hourly_wage) VALUES(?, ?, ?)');   
        // SQL実行
        $stmt->execute([NULL, $name, $hourly_wage]);  
    } catch (PDOException $e) {
        return array(false, 'データベース書き込みエラー');
    }
    return array(true, 'データベース書き込み完了');
}

// POSTデータの取得
$name = $_POST["user_name"];
$hourly_wage = $_POST["hourly_wage"];

//エラーメッセージの初期化
$alert_message = "もう一度試してください";

//フラグの初期化
$add_completed = false;

//入力チェック
if (empty($_POST["user_name"])) {
    $alert_message = '名前が未入力です。';
}else{
    $res = add_db($name, $hourly_wage);
    $add_completed = $res[0];
    $alert_message = $res[1];
}

echo json_encode([
    'ret' => $add_completed,
    'message' => $alert_message
],JSON_UNESCAPED_UNICODE);
?>