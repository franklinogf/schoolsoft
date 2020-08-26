$(document).ready(function () {
	let prevPicture = $(".profile-picture").prop("src");

	// Change profile picture
	$("#pictureBtn").click(() => {
		$("#picture").click();
	});

	$("#picture").change((e) => {
		if ($("#picture").val() !== "") {
			const valid = previewPicture(e.target, ".profile-picture");
			if (valid) {
				$("#pictureCancel").removeAttr("hidden");
			} else if (valid === false) {
				alert("Solo se aceptan imagenes");
			}
		} else {
			$(".profile-picture").prop("src", prevPicture);
		}
	});

	$("#pictureCancel").click((e) => {
		$(".profile-picture").prop("src", prevPicture);
		$("#picture").val("");
		if (e.target.tagName === "I") {
			$(e.target).parent().attr("hidden", true);
		} else {
			$(e.target).attr("hidden", true);
		}
	});

	function previewPicture(input, where) {
		if (input.files && input.files[0]) {
			const fileType = input.files[0]["type"];
			const validImageTypes = ["image/gif", "image/jpeg", "image/png", "image/jpg"];
			if (validImageTypes.includes(fileType)) {
				const reader = new FileReader();
				reader.onload = function (e) {
					$(where).prop("src", e.target.result);
				};
				reader.readAsDataURL(input.files[0]);
				return true;
			}
			return false;
		}

		return undefined;
	}

	// check passwords to submit

	$("#pass1,#pass2").change(() => {
		checkPasswords();
	});

	$("form").submit((event) => {
		if (!checkPasswords()) {
			event.preventDefault();
		} else {
         
			$("#progressModal").modal("show");
			count = 1;
			timer = setInterval(() => {
				console.log("count: ", count);
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
