<?php
// データベースに接続する
$host = '';
$dbname = '';
$username = '';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password,array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ));

    // フォームからのデータを取得する
    $user_id = $_POST['user_id'];
    // $user_name = $_POST['user_name'];
    $scheduled_start = $_POST['scheduled_start'];
    $scheduled_end = $_POST['scheduled_end'];

    // scheduled_hourを計算する
    $scheduled_start_time = new DateTime($scheduled_start);
    $scheduled_end_time = new DateTime($scheduled_end);
    $time_diff = $scheduled_end_time->diff($scheduled_start_time);

    // SQL文を準備する
    $stmt = $pdo->prepare('INSERT INTO work_management (key_number, user_id, scheduled_start, scheduled_end, scheduled_hour, work_start, work_end, work_hour, status) VALUES (NULL, :user_id, :scheduled_start, :scheduled_end, :scheduled_hour, DEFAULT, DEFAULT, DEFAULT, DEFAULT)');
    // $stmt->bindParam(':key_number', null, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':scheduled_start', $scheduled_start, PDO::PARAM_STR);
    $stmt->bindParam(':scheduled_end', $scheduled_end, PDO::PARAM_STR);
    $stmt->bindParam(':scheduled_hour', $time_diff->format('%H:%i:%s'), PDO::PARAM_STR);

    // SQL文を実行する
    $check = $stmt->execute();
    // JSON形式で返す
    header('Content-Type: application/json');
    echo json_encode(['success' => $check]);

    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage(),'id' =>$user_id]);
    exit;
}
?>

