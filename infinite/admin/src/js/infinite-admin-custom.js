const editBtns = document.querySelectorAll('.edit-modal-btn');

if (editBtns.length > 0) {
	editBtns.forEach((btn) => {
		const modal_id = btn.dataset.modal;
		// console.log(modal_id);
		const modal = document.getElementById(modal_id);
		const close_id = modal_id + '-close';
		// console.log(close_id);
		const close = document.getElementById(close_id);

		btn.onclick = function () {
			modal.style.display = 'block';
		};

		close.onclick = function () {
			modal.style.display = 'none';
		};

		window.onclick = function (event) {
			if (event.target == modal) {
				modal.style.display = 'none';
			}
		};
	});
}

const passFailStudent = (el) => {
	const parent = el.parentNode.parentNode;
	const psCol = parent.querySelector('#pass-fail');
	const action = el.dataset.action;
	const SID = el.dataset.student;
	const RID = el.dataset.roster;
	const url = infinite_ajax_obj.ajaxurl + '&action=' + action + '&SID=' + SID + '&RID=' + RID;

	fetch(url)
		.then((response) => response.json())
		.then((data) => {
			if (data.success == true) {
				psCol.innerHTML = data.result;
			}
		});
};
