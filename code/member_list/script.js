function showAddForm() {
	document.getElementById("add-form-wrapper").style.display = "block";
}

function showEditForm(id) {
	var xhr = new XMLHttpRequest();
	xhr.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById("edit-form-wrapper").innerHTML = this.responseText;
			document.getElementById("edit-form-wrapper").style.display = "block";
		}
	};
	xhr.open("GET", "member_list/edit_employee.php?id=" + id, true);
	xhr.send();
}

function hideForm(id) {
	document.getElementById(id).style.display = "none";
}


function add_user(){
    // 送信中UI処理
    const button = document.getElementById("add_form_submit")
    button.disabled = true
    document.getElementById("add_form_response").innerHTML = "送信中";
    // 送信データの作成
    const form = document.getElementById("add_user_from")
    const action = "member_list/add_employees.php"
    const formData = new FormData(form)
    const options = {
        method: 'POST',
        body: formData,
    }
    // 送信とリターン処理
    fetch(action, options)
    .then(response => response.json())
    .then(data =>{
        if (data.ret){
            // DBの書き込み成功
            document.getElementById("add_form_response").innerHTML = "";
            form.reset();
            sortData();
            hideForm("add-form-wrapper");
        }else{
            // 失敗
            document.getElementById("add_form_response").innerHTML = data.message;
        }
        button.disabled = false
        
    });
}


// 
function getData(key='user_id', order='ASC') {
    const params = {
        'sortColumn':key,
        'sortOrder':order
    }
    const urlSearchParam =  new URLSearchParams(params).toString();
    return fetch("member_list/get_data.php?"+urlSearchParam)
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .catch((e) => {
        console.log(`[Error]: ${e}`);
      });
}
  
function displayData(data) {
const tableBody = document.getElementById("table-body");
tableBody.innerHTML = "";
for (let i = 0; i < data.length; i++) {
    const row = document.createElement("tr");
    for (const key in data[i]) {
    const cell = document.createElement("td");
    cell.textContent = data[i][key];
    row.appendChild(cell);
    }
    tableBody.appendChild(row);
}
}

function sortData(key, order) {
    getData(key, order).then((data) => {
        displayData(data);
    });
}
  
document.addEventListener("DOMContentLoaded", function () {
getData().then((data) => {
    displayData(data);
});
});