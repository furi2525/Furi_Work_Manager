<?php
// 
function check_in($request_data) {
    // 空だったら失敗にする
    $user_id = $request_data["user_id"];  
    if (!isset($user_id)) {
        return [false, 'Error: user_id is empty'];
    }
    // 時間を取得
    $time_now = new DateTime();
    $time_now_string = $time_now->format('Y-m-d H:i:s');
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
        // 条件似合うキー番号を取得　IDの一致かつ状態が0（操作無し）かつ勤務開始予定15分前以降
        $stmt = $pdo->prepare("SELECT key_number FROM work_management WHERE user_id=:user_id AND status=0 AND TIMESTAMPDIFF(MINUTE, :time_now, scheduled_start) < 15 AND :time_now<scheduled_end");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':time_now', $time_now_string, PDO::PARAM_STR);
        // SQL実行
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        return [false, 'Error: failed get database >> '.$e];
    }
    if(empty($result)){
        // シフトが無い場合
        return [true, "シフトがありません"];
    }else{
        // 出勤できた場合
        try {
            // SQL文セット
            $stmt = $pdo->prepare("UPDATE work_management SET work_start=:time_now, status=1 WHERE key_number=:key_number");
            $stmt->bindParam(':key_number', $result[0], PDO::PARAM_INT);
            $stmt->bindParam(':time_now', $time_now_string, PDO::PARAM_STR);
            // SQL実行
            $stmt->execute();
        } catch (PDOException $e) {
            return [false, 'Error: not set start time'];
        }
        return [true, "OK"];
    }
}

$json = file_get_contents("php://input");
$contents = json_decode($json, true);
$check_result = check_in($contents);

echo json_encode([
    'success' => $check_result[0],
    'message' => $check_result[1],
    'debug' => $contents
],JSON_UNESCAPED_UNICODE);
?>
