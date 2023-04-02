<?php
// データベースから情報を取得
function get_work_data ($status_number=null) {
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
        if(is_null($status_number)){
            // 指定無しなら全部
            $stmt = $pdo->prepare("SELECT * FROM work_management ORDER BY scheduled_start ASC");
        }else{
            // statusの指定があればその番号の行のみ
            $stmt = $pdo->prepare("SELECT * FROM work_management WHERE status = $status_number ORDER BY scheduled_start ASC");
        }
        
        // SQL実行
        $stmt->execute();  
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return array(false, 'データベース読み取りエラー');
    }
    return array(true, $result);
}

// 指定したキーの行のstatusを変更
function set_status($key_number,$status_number){
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
        $stmt = $pdo->prepare("UPDATE work_management SET status=:status_number, work_end=scheduled_end, work_start=scheduled_end WHERE key_number=:key_number");
        $stmt->bindParam(':key_number', $key_number, PDO::PARAM_INT);
        $stmt->bindParam(':status_number', $status_number);
        // SQL実行
        $stmt->execute();  
    } catch (PDOException $e) {
        return array(false, 'データベース読み取りエラー'.$e);
    }
    return array(true, '');
}

// ユーザー名の取得 > iranai?
function get_user_data($user_id){
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
        $stmt = $pdo->prepare("SELECT * FROM user_info WHERE user_id=$user_id");   
        // SQL実行
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);  
    } catch (PDOException $e) {
        return null;
    }
    return $result[0];
}

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

// 遅刻、無断欠勤を見つけてテーブルの更新と通知を行う
function update_schedule(){
    // レスポンスデータの初期値
    $response_data = [];
    // 各種データの取得
    $time_now = new DateTime();
    $user_names = get_user_names();
    // 状態がデフォルト（未処理）の人を取得
    $result_s0 = get_work_data(0);
    if($result_s0[0]){
        foreach ($result_s0[1] as $one_task){
            $time_start = new DateTime($one_task['scheduled_start']);
            $time_end = new DateTime($one_task['scheduled_end']);
            $user_id = $one_task['user_id'];
            $user_name = $user_names[$user_id];
            // 終了時間を過ぎた場合　無断欠勤処理
            if($time_now >= $time_end){
                set_status($one_task['key_number'],4);
                // 管理者への通知
                $response_data[] = [
                    "user_id" => 0,
                    "message" => "欠勤通知\n名前:".$user_name."\n日時:".$one_task['scheduled_start']
                ];
                continue;
            }
            // 現在時刻が開始時間を過ぎた場合 遅刻処理
            if($time_now >= $time_start){
                // 管理者への通知
                $response_data[] = [
                    "user_id" => 0,
                    "message" => "遅刻通知\n名前:".$user_name."\n日時:".$one_task['scheduled_start']
                ];
                // 勤務者への通知
                $response_data[] = [
                    "user_id" => $user_id,
                    "message" => "勤務開始時間を過ぎました"
                ];
                continue;
            }else{
                // 時間順にソート済みのため現在時刻が開始時間前なら終わる
                break;
            }
        }
    }
    return $response_data;
}

echo json_encode(update_schedule(), JSON_UNESCAPED_UNICODE);
?>