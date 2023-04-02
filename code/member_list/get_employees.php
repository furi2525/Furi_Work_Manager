<?php
try{
    //データベース情報の指定
    $db['dbname'] = "";  // データベース名
    $db['user'] = "";  // ユーザー名
    $db['pass'] = "";  // ユーザー名のパスワード
    $db['host'] = "";  // DBサーバのURL
    $dsn = sprintf('mysql:host=%s; dbname=%s; charset=utf8', $db['host'], $db['dbname']); 
    // データベースに接続
    $pdo = new PDO($dsn, $db['user'], $db['pass'], array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

    // 従業員一覧を取得
    $stmt = $pdo->prepare('SELECT * FROM user_info');
    $stmt->execute();
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // 従業員一覧を表示
    foreach ($employees as $employee) {
        echo '<tr>';
        echo '<td>' . $employee['user_id'] . '</td>';
        echo '<td>' . $employee['user_name'] . '</td>';
        echo '<td>' . $employee['hourly_wage'] . '</td>';
        echo '<td><a href="edit_employee.php?id=' . $employee['id'] . '">編集</a></td>';
        echo '</tr>';
    }
    $pdo = null;
} catch (PDOException $e) {
    $errorMessage = 'データベースエラー';
    echo 'Error';
}
?>
