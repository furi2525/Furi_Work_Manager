// ユーザー名の選択肢を取得する
fetch('add_work_schedule/get_users.php')
	.then(response => response.json())
	.then(data => {
		const select = document.getElementById('user-name');
		data.forEach(user => {
			const option = document.createElement('option');
			option.value = user.user_id;
			option.textContent = user.user_name;
			select.appendChild(option);
		});
	})
	.catch(error => console.error(error));

// フォームの送信を処理する
const form = document.getElementById('work-form');
form.addEventListener('submit', event => {
	event.preventDefault();

	const formData = new FormData(form);

	fetch('add_work_schedule/add_data.php', {
		method: 'POST',
		body: formData
	})
		.then(response => response.json())
		.then(data => {
			alert('シフトを追加しました。');
			console.log(data)
			form.reset();
		})
		.catch(error => {
			console.error(error);
			alert('エラー。');
		});
});
