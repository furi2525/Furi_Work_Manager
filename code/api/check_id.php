<?php
// 
function check_id($request_data) {
    // 空だったら失敗にする
    $user_id = $request_data["user_id"];
    $user_name = $request_data["user_name"];
    if (!isset($user_id)) {
        return [false, 'Error: user_id is empty'];
    }
    if (!isset($user_name)) {
        return [false, 'Error: user_name is empty'];
    }
    //データベース情報の指定
    $db['dbname'] = "";  // データベース名
    $db['user'] = "";  // ユーザー名
    $db['pass'] = "";  // ユーザー名のパスワード
    $db['host'] = "";  // DBサーバのURL
    $dsn = sprintf('mysql:host=%s; dbname=%s; charset=utf8', $db['host'], $db['dbname']);
    //PDOを使ってMySQLに接続
    try {
        $pdo = new PDO($dsn, $db['user'], $db['pass'], array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));
    } catch (PDOException $e) {
        return [false, 'Error: not connect database'];
    }
    try {
        // SQL文セット
        $stmt = $pdo->prepare("SELECT * FROM user_info WHERE user_id=$user_id");   
        // SQL実行
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);  
    } catch (PDOException $e) {
        return [false, 'Error: failed get database'];
    }
    // 名前が合っているか判定
    if($result[0]["user_name"]==$user_name){
        return [true, ''];
    }else{
        return [false, 'Error: miss the name'];
    }
}

$json = file_get_contents("php://input");
$contents = json_decode($json, true);

$check_result = check_id($contents);

echo json_encode([
    'success' => $check_result[0],
    'message' => $check_result[1]
],JSON_UNESCAPED_UNICODE);
?>
