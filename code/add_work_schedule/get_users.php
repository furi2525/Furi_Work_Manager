<?php
// データベースのパラメータ
$host = '';
$dbname = '';
$username = '';
$password = '';

try {
    // データベースに接続
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // 取得
    $stmt = $pdo->prepare('SELECT user_id, user_name FROM user_info');
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // JSONを送信
    header('Content-Type: application/json');
    echo json_encode($result);
} catch (PDOException $e) {
    echo 'データベースにアクセスできませんでした。理由：' . $e->getMessage();
    exit;
}
?>

