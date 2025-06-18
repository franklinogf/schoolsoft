$(document).ready(function () {
	// check passwords to submit
	$("#pass1,#pass2").change(() => {
		console.log('change')
		checkPasswords();
	});

	$("form").submit(function(event){
		if (!checkPasswords()) {
			event.preventDefault();
			console.log('password false')
		} else {

			$("#progressModal").modal("show");
			count = 1;
			timer = setInterval(() => {
				if (count > 100) {
					clearInterval(timer);
				} else {
					$("#progressModal .progress-bar")
						.prop("aria-valuenow", count)
						.css("width", count + "%")
						.text(count + "%");
					count++;
				}
			}, 50);
		}
	});
});
