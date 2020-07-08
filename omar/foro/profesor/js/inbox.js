$(document).ready(function () {
	let _class = "";
	const $classesTableWrapper = $(".classesTable").parents(".dataTables_wrapper");
	const $studentsTableWrapper = $(".studentsTable").parents(".dataTables_wrapper");
	const $messages = $("#messages");
	const $message = $("#message");
	const $messageOptions = $(".messageOption");
	const $unreadMessages = $(".unreadMessages");
	const $newMessageBtn = $("#newMessageBtn");
	const $respondModal = $("#respondModal");
	const $respondForm = $("#respondForm");
	const $newMessageModal = $("#newMessageModal");
	const $newMessageForm = $newMessageModal.find(".form");
	const $modalAlert = $("#modalAlert");
	let messages = [];
	let message = [];
	getMessages();
	$studentsTableWrapper.hide(0);

	$("#newMessageModal form").submit(function (e) {
		e.preventDefault();
		const fd = new FormData(this);
		const students = $(studentsTable.rows().nodes())
			.find("[type='checkbox'].check:checked")
			.serializeArray();
		const files = $('[name="file[]"]');
		fd.append("newMessage", true);
		// append the students list
		students.map((studentInput) => {
			fd.append("students[]", studentInput.value);
		});
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
				const option = $(this).data("option");
				getMessages(option);
				$newMessageModal.modal("hide");
			},
		});
	});

	$(".classesTable tbody").on("click", "tr", function () {
		const row = classesTable.row(this);
		if (row.index() !== undefined) {
			const data = row.data();
			_class = data[0];
			$.ajax({
				type: "POST",
				url: getBaseUrl("includes/classes.php"),
				data: { studentsByClass: _class },
				dataType: "json",
				success: (res) => {
					if (res.response === true) {
						res.data.map((student) => {
							const thisRow = studentsTable.row
								.add({
									0: ` <div class="custom-control custom-checkbox">
                        <input class="custom-control-input check bg-success" type="checkbox" id="${student.mt}" name="student[]" value="${student.mt}">
                        <label class=" custom-control-label" for="${student.mt}"></label>
                     </div>`,
									1: `${student.apellidos} ${student.nombre}`,
									2: student.usuario,
								})
								.draw();

							$(thisRow.node()).prop("id", student.mt);
						});
						//   hide classes and show students
						animateCSS($classesTableWrapper, "zoomOut faster", () => {
							$classesTableWrapper.hide(0);
							$studentsTableWrapper.show(0);
							animateCSS($studentsTableWrapper, "zoomIn faster");
						});
					} else {
						alert("No existen estudiantes en esta clase");
					}
				},
			});
		}
	});

	$("#back").click((e) => {
		//   hide students and show classes
		animateCSS($studentsTableWrapper, "zoomOut faster", () => {
			$studentsTableWrapper.hide(0);
			studentsTable.rows().remove();
			$(studentsTable.rows().nodes()).remove();
			$classesTableWrapper.show(0);
			animateCSS($classesTableWrapper, "zoomIn faster");
		});
	});

	$("#newMessageModal .continueBtn").click((e) => {
		//  hide students and show form

		const checked = $(studentsTable.rows().nodes()).find("[type='checkbox'].check:checked")
			.length;
		if (checked > 0) {
			const plural = checked > 1 ? "estudiantes" : "estudiante";
			$("#newMessageModal .studentsAmount").text(
				`Este correo se le enviara a ${checked} ${plural}`
			);
			animateCSS($studentsTableWrapper, "zoomOut faster", () => {
				$studentsTableWrapper.hide(0);

				$newMessageForm.show(0, () => {
					animateCSS($newMessageForm, "zoomIn faster");
				});
			});
		} else {
			$modalAlert.modal("show");
			// alert('Debe de seleccionar al menos un estudiante')
		}
	});

	$("#newMessageModal .back").click(() => {
		//  hide form and show students
		animateCSS($newMessageForm, "zoomOut faster", () => {
			$newMessageForm.hide(0);

			$studentsTableWrapper.show(0, () => {
				animateCSS($studentsTableWrapper, "zoomIn faster");
			});
		});
	});

	$("#modalAlert .close").click(() => {
		$modalAlert.modal("hide");
		$newMessageModal.modal("handleUpdate");
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
		$studentsTableWrapper.hide(0);
		$classesTableWrapper.show(0);
		$("input.file").parents(".input-group").remove();
		studentsTable.rows().remove();
		$(studentsTable.rows().nodes()).remove();
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
				$message.html(`
				<div class="d-flex justify-content-center align-items-center h-100 font-bree">
					Seleccione un mensaje
				</div>
				`);
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
					message.enviadoPor !== "p" &&
					`<button id="respondBtn" title="Responder" class="btn btn-secondary btn-sm" data-toggle="tooltip" type="button">
                     <i class="fas fa-reply text-primary"></i>
                  </button>`
				}
            </div>
         </div>
         <div class="col-2 d-flex justify-content-center align-items-center">
            <p class="m-0">Para:</p>
         </div>
         <div class="col-10">
            ${
				message.to.length === 1
					? `
            <div class="media p-2 mt-2">
               <img src="${message.to[0].foto}" class="align-self-start mr-2 rounded-circle" alt="Profile Picture" width="52" height="52">
               <div class="media-body">
                  <p class="m-0"><strong>${message.to[0].nombre}</strong> <small>(${message.to[0].info})</small></p>
               </div>        
            </div>
            `
					: `Enviado a ${message.to.length} estudiantes <a id="viewStudents" href="#" class="badge badge-primary">Ver</a>`
			}
         </div>
      </div>
   <p class="p-2 mb-0 mt-3 font-bree">${message.asunto}</p>
   <hr class="my-1">
   ${
		message.archivos.length > 0 ?
		`
   <div class="row row-cols-4 row-cols-lg-6"> 
   ${message.archivos
		.map(
			(file) => `<div class="col my-1 overflow-hidden text-truncate">
               <a href="${file.url}" title='${file.nombre}' class="btn btn-outline-dark btn-block btn-sm p-2" download="${file.nombre}">
                  ${file.icon}
               </a>
               <small title='${file.nombre}' class='text-muted'>${file.nombre}</small>
               </div>`
		)
		.join("")}
   </div>
   <hr class="my-1">`
   : ""}  
   <h5 class='text-center mt-2'>${message.titulo}</h5>
   <p class="p-2 mt-1 message-text font-markazi">${message.mensaje}</p>`);

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

	$(document).on("click", "#viewStudents", () => {
		$("body").append(`
      <div class="modal fade" id="viewStudentsModal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
         <div class="modal-dialog" role="document">
            <div class="modal-content">
               <div class="modal-header bg-gradient-primary bg-primary">
               <h5 class="modal-title">Lista de estudiantes</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
               </div>
               <div class="modal-body">
               ${message.to
					.map(
						(to) => `
                  <div class="media p-2 mt-2">
                     <img src="${to.foto}" class="align-self-start mr-2 rounded-circle" alt="Profile Picture" width="52" height="52">
                     <div class="media-body">
                        <p class="m-0"><strong>${to.nombre}</strong></p>
                     </div>        
                  </div>
               `
					)
					.join("")}
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>               
               </div>
            </div>
         </div>
      </div>
      `);
		$("#viewStudentsModal").modal("show");
	});

	$(document).on("hidden.bs.modal", "#viewStudentsModal", function (e) {
		$("#viewStudentsModal").remove();
	});

	$($messages).on("DOMSubtreeModified", () => {
		animateCSS($messages.children(), "bounceInDown faster");
	});

	$($message).on("DOMSubtreeModified", () => {
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
					:""}</p>
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
