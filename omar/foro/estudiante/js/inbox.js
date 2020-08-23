$(document).ready(function () {
	const $classesTableWrapper = $(".classesTable").parents(".dataTables_wrapper");
	const $messages = $("#messages");
	const $message = $("#message");
	const $messageOptions = $(".messageOption");
	const $unreadMessages = $(".unreadMessages");
	const $newMessageBtn = $("#newMessageBtn");
	const $respondModal = $("#respondModal");
	const $respondForm = $("#respondForm");
	const $newMessageModal = $("#newMessageModal");
	const $newMessageForm = $newMessageModal.find(".form");
	let messages = [];
	let message = [];
	let _teacherId = [];
	getMessages();

	$("#newMessageModal form").submit(function (e) {
		e.preventDefault();
		const fd = new FormData(this);
		const files = $('[name="file[]"]');
		fd.append("newMessage", true);

		// append the teacher id
		fd.append("teacher_id", _teacherId);

		// append files
		files.map((input) => {
			fd.append("file[]", input.files);
		});

		// send messages
		$.ajax({
			type: "POST",
			url: includeThisFile(),
			data: fd,
			contentType: false,
			cache: false,
			processData: false,
			success: function (res) {
				$newMessageModal.modal("hide");
			},
		});
	});

	$(".classesTable tbody").on("click", "tr", function () {
		const row = classesTable.row(this);
		if (row.index() !== undefined) {
			const teacherId = $(row.node()).data("id");
			_teacherId = teacherId;
			$.ajax({
				type: "POST",
				url: includeThisFile(),
				data: { teacherById: teacherId },
				dataType: "json",
				success: (res) => {
					if (res.response === true) {
						$newMessageModal
							.find(".toTeacher")
							.text(`Enviar mensaje a ${res.data.nombre} ${res.data.apellidos}`);
						//   hide classes and show form
						animateCSS($classesTableWrapper, "zoomOut faster", () => {
							$classesTableWrapper.hide(0);
							$newMessageForm.show(0);
							animateCSS($newMessageForm, "zoomIn faster");
						});
					}
				},
			});
		}
	});

	$("#newMessageModal .back").click(() => {
		//  hide form and show classes
		animateCSS($newMessageForm, "zoomOut faster", () => {
			$newMessageForm.hide(0);
			$classesTableWrapper.show(0, () => {
				animateCSS($classesTableWrapper, "zoomIn faster");
			});
		});
	});

	$newMessageModal.on("click", ".closeModal", function (e) {
		if (
			$("#newTitle").val().length > 0 ||
			$("#newSubject").val().length > 0 ||
			$("#newMessage").val().length > 0
		) {
			if (confirm("Tiene cambios sin guardar, seguro quiere cerrarlo?")) {
				$newMessageModal.modal("hide");
			}
		} else {
			$newMessageModal.modal("hide");
		}
	});

	$newMessageModal.on("hidden.bs.modal", function (e) {
		$newMessageForm.hide(0);
		$classesTableWrapper.show(0);
		$("input.file").parents(".input-group").remove();
	});
	$respondModal.on("hidden.bs.modal", function (e) {
		$("#respondMessage").removeClass("is-invalid");
	});

	$respondForm.submit(function (e) {
		e.preventDefault();
		const formData = $(this).serializeArray();
		if (formData[0].value.length > 0) {
			message.respondMessage = formData[0].value;

			$.post(includeThisFile(), { respondMessage: message }, (res) => {
				delete message.respondMessage;
				$respondModal.modal("hide");
			});
		} else {
			$("#respondMessage").addClass("is-invalid").focus();
		}
	});

	$($message).on("click", "#respondBtn", function (e) {
		$respondModal.modal("show");
		$respondModal.find(".modal-title").text(`Responder a ${message.nombre}`);
		// $respondModal.find('.title').val(message.titulo)
		$respondModal.find("#respondSubject").val(`RE: ${message.asunto}`);
	});

	$newMessageBtn.click(function (e) {
		$newMessageModal.modal("show");
	});

	$messageOptions.click(function (e) {
		e.preventDefault();
		if (!$(this).hasClass("active")) {
			$messageOptions.removeClass("active");
			$(this).addClass("active");
			const option = $(this).data("option");
			getMessages(option);
			animateCSS($message.children(), `bounceOutLeft faster`, () => {
				$message.html(`<div class="d-flex justify-content-center align-items-center h-100 font-bree">
            Seleccione un mensaje
         </div>`);
			});
		}
	});

	$($messages).on("click", "div.card", function (e) {
		const $thisMessage = $(this);
		const index = messages.findIndex((message) => message.id === $thisMessage.data("id"));
		message = messages[index];
		// show the messsage
		$message.html(`
      <div class="row">
         <div class="col-2 d-flex justify-content-center align-items-center">
            <p class="m-0">Desde:</p>
         </div>
         <div class="col-10">
            <div class="media p-2 mt-2">
               <img src="${
					message.foto
				}" class="align-self-start mr-2 rounded-circle" alt="Profile Picture" width="52" height="52">
               <div class="media-body">
                  <p class="m-0"><strong>${
						message.nombre
					}</strong> <small>(${message.info})</small></p>
                  <small class="text-muted font-weight-light">${message.fecha}</small>
               </div>
               ${
					message.enviadoPor !== "e"
						? `<button id="respondBtn" title="Responder" class="btn btn-secondary btn-sm" data-toggle="tooltip" type="button">
                     <i class="fas fa-reply text-primary"></i>
                  </button>`
				: ''}
            </div>
         </div>
         <div class="col-2 d-flex justify-content-center align-items-center">
            <p class="m-0">Para:</p>
         </div>
         <div class="col-10">
            <div class="media p-2 mt-2">
               <img src="${
					message.toFoto
				}" class="align-self-start mr-2 rounded-circle" alt="Profile Picture" width="52" height="52">
               <div class="media-body">
                  <p class="m-0"><strong>${
						message.toNombre
					}</strong> <small>(${message.toInfo})</small></p>
               </div>        
            </div>
         </div>
      </div>
      <p class="p-2 my-0 font-bree">${message.asunto}</p>
      <hr class="my-1">
      ${
			message.archivos.length > 0 ?
			`
      <div class="row row-cols-4 row-cols-lg-6"> 
      ${message.archivos
			.map((file) => {
				return `<div class="col my-1 overflow-hidden text-truncate">
               <a href="${file.url}" title='${file.nombre}' class="btn btn-outline-dark btn-block btn-sm p-2" download="${file.nombre}">
                  ${file.icon}
               </a>
               <small title='${file.nombre}' class='text-muted'>${file.nombre}</small>
               </div>`;
			})
			.join("")}
   </div>
   <hr class="my-1">`
		: ""}  
   <h5 class='text-center mt-2'>${message.titulo}</h5>
   <p class="p-2 mt-1 message-text font-markazi">${message.mensaje}</p>
   
   `);

		// change the message read status
		$.post(
			includeThisFile(),
			{ changeStatus: message.id },
			(res) => {
				message.leido = "si";
				$unreadMessages.text(res.unreadMessages);
			},
			"json"
		);

		const $status = $thisMessage.find(".status");
		animateCSS($status, "zoomOut faster", () => {
			$status.remove();
		});
	});

	$($messages).on("MutationObserver", () => {
		animateCSS($messages.children(), "bounceInDown faster");
	});

	$($message).on("MutationObserver", () => {
		const effect = window.innerWidth > 768 ? "bounceInLeft" : "bounceInDown";
		animateCSS($message.children(), `${effect} faster`);
	});

	// inbox
	function getMessages(type = "inbound") {
		$messages.html(`
      <div class="d-flex justify-content-center align-items-center h-100 font-bree">
      Cargando...
      </div>`);
		$.post(
			includeThisFile(),
			{ getMessages: type },
			async (res) => {
				if (res.response) {
					$messages.empty();
					messages = await res.data;
					res.data.map((message) => {
						$messages.append(`<div data-id="${
							message.id
						}" class="card w-100 rounded-0 pointer-cursor">
               <div class="card-body p-2">
                  <p class="card-text mb-0 font-weight-bold">${message.nombre}</p>
                  <p class="card-text mb-0 text-muted d-flex justify-content-between"><small>${
						message.fecha
					}</small><small>${message.hora}</small></p>
                  <p class="card-text mb-0 text-truncate font-markazi">${message.asunto}</p>
                  <p class="card-text mb-0 text-truncate font-weight-light">${message.mensaje}</p>
                  <p class="card-text text-right">${
						message.leido !== "si" ?
						'<small class="badge badge-success rounded-0 status">Nuevo</small>'
					: ""}</p>
               </div>
            </div>`);
					});
				} else {
					$messages.html(`
            <div class="d-flex justify-content-center align-items-center h-100 font-bree">
                  No tiene mensajes
               </div>`);
				}
			},
			"json"
		);
	}
});
