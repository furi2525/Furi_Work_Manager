function getData() {
    return fetch("get_data.php")
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
  
function sortData(key, descending = false) {
getData().then((data) => {
    data.sort((a, b) => {
    const aVal = a[key];
    const bVal = b[key];
    if (aVal < bVal) {
        return descending ? 1 : -1;
    }
    if (aVal > bVal) {
        return descending ? -1 : 1;
    }
    return 0;
    });
    displayData(data);
});
}
  
document.addEventListener("DOMContentLoaded", function () {
getData().then((data) => {
    displayData(data);
});
});
  
