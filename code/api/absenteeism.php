<?php
// ユーザー名の取得
function get_user_names(){
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
        return null;
    }
    try {
        // SQL文セット
        $stmt = $pdo->prepare("SELECT user_id, user_name FROM user_info");   
        // SQL実行
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    } catch (PDOException $e) {
        return null;
    }
    return $result;
}

// 欠勤処理
function absenteeism($request_data) {
    // 空だったら失敗にする
    $user_id = $request_data["user_id"];
    if (!isset($user_id)) {
        return [false, 'Error: user_id is empty'];
    }
    // 日付が無ければ失敗
    $date_string = $request_data["date"];
    if (!isset($date_string)) {
        return [false, 'Error: date is empty'];
    }
    // 書式が異なることを返信
    if (!preg_match('/^[1-2]??[0-9]月[1-3]??[0-9]日$/', $date_string)){
        return [true, '日付の書式は〇月〇日です。半角数字で先頭の0埋めは必要有りません。'];
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
        // 条件似合うキー番号を取得　IDの一致かつ状態が0（操作無し）かつ日付の一致
        $stmt = $pdo->prepare("SELECT key_number FROM work_management WHERE user_id=:user_id AND status=0 AND DATE_FORMAT(scheduled_start,  '%c月%e日') = :date_string");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':date_string', $date_string, PDO::PARAM_STR);
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
        // 欠勤処理
        try {
            // SQL文セット
            $stmt = $pdo->prepare("UPDATE work_management SET status=3, work_end=scheduled_end, work_start=scheduled_end WHERE key_number=:key_number");
            $stmt->bindParam(':key_number', $result[0], PDO::PARAM_INT);
            // SQL実行
            $stmt->execute();
        } catch (PDOException $e) {
            return [false, 'Error: not set start time'];
        }
        // 管理者に通知
        try {
            $user_name = get_user_names()[$user_id];
            $data = array("user_id"=>0,"pass"=>"" ,"message"=>"欠勤連絡\n名前:".$user_name."\n日付:".$date_string);
            $json_data = json_encode($data);
            $url = 'gas url';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            $response = curl_exec($ch);
            curl_close($ch);
        } catch (PDOException $e) {
            return [true, '管理者に連絡ができなかったため、自身で連絡をお願いします。'];
        }
        return [true, "OK"];
    }
}

$json = file_get_contents("php://input");
$contents = json_decode($json, true);
$check_result = absenteeism($contents);

echo json_encode([
    'success' => $check_result[0],
    'message' => $check_result[1],
    'debug' => $contents
],JSON_UNESCAPED_UNICODE);
?>
