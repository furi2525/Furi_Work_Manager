<?php
function test ($contents) {
    $key_year = $contents['key_year'];
    $key_month = $contents['key_month'];
    $key_date = $key_year.$key_month;
    $key_date2 = $key_year.'/'.$key_month;
    if($key_date==''){
        return [false, 'date is empty'];
    }
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
        $stmt = $pdo->prepare("INSERT INTO work_status (key_date_id, user_id, user_name, work_month, total_working, hourly_wage, salary, count_work, count_absence, count_absenteeism, count_lateness)
        SELECT CONCAT( :key_date1, '-', work_management.user_id), 
        work_management.user_id, 
        user_name, 
        :key_date2, 
        SEC_TO_TIME(SUM(TIME_TO_SEC(work_hour)))  AS total_working, 
        hourly_wage, 
        SUM(TIME_TO_SEC(work_hour))*hourly_wage/3600, 
        COUNT(status = 2 OR NULL), 
        COUNT(status = 3 OR NULL), 
        COUNT(status = 4 OR NULL), 
        COUNT(scheduled_start < work_start OR NULL) 
        FROM work_management LEFT OUTER JOIN user_info ON work_management.user_id = user_info.user_id
        WHERE DATE_FORMAT( work_end, '%Y%m' )=:key_date4 
        GROUP BY CONCAT( :key_date3 , '-', work_management.user_id)");

        $stmt->bindParam(':key_date1', $key_date, PDO::PARAM_STR);
        $stmt->bindParam(':key_date2', $key_date2, PDO::PARAM_STR);
        $stmt->bindParam(':key_date3', $key_date, PDO::PARAM_STR);
        $stmt->bindParam(':key_date4', $key_date, PDO::PARAM_STR);
        // SQL実行
        $stmt->execute();  
        $result = $stmt->rowCount();
    } catch (PDOException $e) {
        return array(false, 'データベース読み取りエラー'.$e);
    }
    return array($key_date, $result);
}

$json = file_get_contents("php://input");
$contents = json_decode($json, true);

$check_result = test($contents);

echo json_encode([
    'success' => $check_result[0],
    'message' => $check_result[1],
    'debug' => $json
],JSON_UNESCAPED_UNICODE);
?>
