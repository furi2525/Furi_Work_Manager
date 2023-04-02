<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>FWM|名簿</title>
    <link rel="icon" href="favicon/favicon.ico">
    <link rel="stylesheet" type="text/css" href="public/style_basic.css">
	<link rel="stylesheet" type="text/css" href="member_list/style.css">
</head>
<body>
    <?php include('public/header.html'); ?>
    <div id="main">
		<div id="container">
			<h1>従業員リスト</h1>
			<div id="member_table">
				<table id="table">
				<thead>
					<tr>
					<th>番号<br><button onclick="sortData('user_id','ASC')">昇順</button><button onclick="sortData('user_id', 'DESC')">降順</button></th>
					<th>名前<br><button onclick="sortData('user_name','ASC')">昇順</button><button onclick="sortData('user_name', 'DESC')">降順</button></th>
					<th>時給<br><button onclick="sortData('hourly_wage','ASC')">昇順</button><button onclick="sortData('hourly_wage', 'DESC')">降順</button></th>
					</tr>
				</thead>
				<tbody id="table-body">
				</tbody>
				</table>
			</div>
			<button id="add-button" onclick="showAddForm()">追加</button>
			<div id="add-form-wrapper" class="form-wrapper" style="display: none;">
				<form id="add_user_from" method="post" target="frame-add-wrapper">
					<h2>従業員追加</h2>
					<label for="user_name">名前:</label>
					<input type="text" id="user_name" name="user_name" required>
					<label for="hourly_wage">時給:</label>
					<input type="number" id="hourly_wage" name="hourly_wage" pattern="[0-9]*" required>
					<div id="add_form_response"></div>
					<button type="button" id="add_form_submit" onclick="add_user()">追加</button>
					<button type="button" onclick="hideForm('add-form-wrapper')">キャンセル</button>
					
				</form>
			</div>
			<iframe name="frame-add-wrapper"style="width:0px;height:0px;border:0px;"></iframe>
			<div id="edit-form-wrapper" class="form-wrapper" style="display: none;"></div>
		</div>
        <script src="member_list/script.js"></script>
    </div>
    <?php include ('public/footer.html'); ?>
</body>
</html>