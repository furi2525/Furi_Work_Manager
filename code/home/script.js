// 
function getData(key='user_id', order='ASC') {
    const params = {
        'sortColumn':key,
        'sortOrder':order
    }
    const urlSearchParam =  new URLSearchParams(params).toString();
    return fetch("home/get_data.php?"+urlSearchParam)
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