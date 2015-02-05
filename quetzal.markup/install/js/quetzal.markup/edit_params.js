function addNewRow(tableID, row_to_clone) {
	var tbl = document.getElementById(tableID);
	var cnt = tbl.rows.length;
	if (row_to_clone == null)
		row_to_clone = -2;
	var sHTML = tbl.rows[cnt + row_to_clone].cells[0].innerHTML;
	var oRow = tbl.insertRow(cnt + row_to_clone + 1);
	var oCell = oRow.insertCell(0);

	oCell.innerHTML = sHTML;
}

function deleteRow(ob, tableID) {
	var row = BX.findParent(ob, {'tag':'tr'});
	var tbl = document.getElementById(tableID);
	var cnt = tbl.rows.length;

	if (cnt <= 2) {
		addNewRow(tableID);
	}

	row.parentNode.removeChild(row);
}