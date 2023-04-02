<?php

$host = '';
$dbname = '';
$username = '';
$password = '';

$sortColumn = isset($_GET['sortColumn']) ? $_GET['sortColumn'] : 'user_id';
$sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'ASC';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $pdo->prepare("SELECT user_id, user_name, work_month, total_working, hourly_wage, salary, count_work, count_absence, count_absenteeism, count_lateness FROM work_status ORDER BY $sortColumn $sortOrder");
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  header('Content-Type: application/json');
  echo json_encode($result);
} catch(PDOException $e) {
  die('Error: ' . $e->getMessage());
}

?>
