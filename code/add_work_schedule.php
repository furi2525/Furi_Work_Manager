<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>FWM|シフト追加</title>
    <link rel="icon" href="favicon/favicon.ico">
    <link rel="stylesheet" type="text/css" href="public/style_basic.css">
    <link rel="stylesheet" type="text/css" href="add_work_schedule/style.css">
</head>
<body>
    <?php include('public/header.html'); ?>
    <div id="main">
        <div id="container">
            <h1>シフト追加</h1>
            <form id="work-form">
                <div class="form-group">
                    <label for="user-name">ユーザー名</label>
                    <select id="user-name" name="user_id">
                        <option value="">選択してください</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="scheduled-start">予定開始時刻</label>
                    <input type="datetime-local" id="scheduled-start" name="scheduled_start">
                </div>
                <div class="form-group">
                    <label for="scheduled-end">予定終了時刻</label>
                    <input type="datetime-local" id="scheduled-end" name="scheduled_end">
                </div>
                <button type="submit">登録</button>
            </form>
        </div>
        <script src="add_work_schedule/script.js"></script>
    </div>
    <?php include ('public/footer.html'); ?>
</body>
</html>