<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>FWM|ホーム</title>
    <link rel="icon" href="favicon/favicon.ico">
    <link rel="stylesheet" type="text/css" href="public/style_basic.css">
	<link rel="stylesheet" type="text/css" href="home/style.css">
</head>
<body>
    <?php include('public/header.html'); ?>
    <div id="main">
		<div id="container">
			<h1>勤務状況</h1>
			<div id="member_table">
				<table id="table">
				<thead>
					<tr>
					<th>番号<br><button onclick="sortData('user_id','ASC')">昇順</button><button onclick="sortData('user_id', 'DESC')">降順</button></th>
					<th>名前<br><button onclick="sortData('user_name','ASC')">昇順</button><button onclick="sortData('user_name', 'DESC')">降順</button></th>
                    <th>年月<br><button onclick="sortData('work_month','ASC')">昇順</button><button onclick="sortData('work_month', 'DESC')">降順</button></th>
                    <th>合計勤務時間<br><button onclick="sortData('total_working','ASC')">昇順</button><button onclick="sortData('total_working', 'DESC')">降順</button></th>
                    <th>時給<br><button onclick="sortData('hourly_wage','ASC')">昇順</button><button onclick="sortData('hourly_wage', 'DESC')">降順</button></th>
                    <th>給与<br><button onclick="sortData('salary','ASC')">昇順</button><button onclick="sortData('salary', 'DESC')">降順</button></th>
                    <th>出勤数<br><button onclick="sortData('count_work','ASC')">昇順</button><button onclick="sortData('count_work', 'DESC')">降順</button></th>
                    <th>欠勤数<br><button onclick="sortData('count_absence','ASC')">昇順</button><button onclick="sortData('count_absence', 'DESC')">降順</button></th>
					<th>無断欠勤数<br><button onclick="sortData('count_absenteeism','ASC')">昇順</button><button onclick="sortData('count_absenteeism', 'DESC')">降順</button></th>
                    <th>遅刻数<br><button onclick="sortData('count_lateness','ASC')">昇順</button><button onclick="sortData('count_lateness', 'DESC')">降順</button></th>
					</tr>
				</thead>
				<tbody id="table-body">
				</tbody>
				</table>
			</div>
		</div>
        <script src="home/script.js"></script>
    </div>
    <?php include ('public/footer.html'); ?>
</body>
</html>