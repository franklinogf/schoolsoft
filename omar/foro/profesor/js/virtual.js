$(document).ready(function () {
	let subjectCode = ''
	let virtualId = ''
	let row = null
	$(".classesTable tbody").on("click", "tr", function () {
		loadingBtn($('#virtualBtn'))
		row = classesTable.row(this)
		if (row.index() !== undefined) {
			const data = row.data()
			subjectCode = data[0]
			$("#virtualModal").modal('show')
			$("#virtualModal").find('.modal-title').text(`Salón virtual para ${subjectCode}`)
			$.ajax({
				type: "POST",
				url: includeThisFile(),
				data: {
					find: subjectCode,
				},
				dataType: 'json',
				complete: function (response) {
					if (response.responseJSON.response) {
						const data = response.responseJSON.data;
						$("#link").val(data.link)
						$('#title').val(data.title)
						$('#date').val(data.date)
						$('#time').val(data.time)
						$('#virtualId').val(data.id)
						$("#virtualModal .btn-danger").removeClass('hidden')
						virtualId = data.id
						loadingBtn($('#virtualBtn'), 'Modificar')
					} else {
						loadingBtn($('#virtualBtn'), 'Guardar')
					}
				}
			});
		}
	});

	$("form").submit(function (event) {
		event.preventDefault();
		if ($(this)[0].checkValidity() === false) {
			event.stopPropagation();
		} else {
			loadingBtn($('#virtualBtn, #virtualModal .btn-secondary'))
			//save
			if ($("#virtualId").val().length === 0) {
				$.ajax({
					type: "POST",
					url: includeThisFile(),
					data: {
						add: subjectCode,
						link: $("#link").val(),
						title: $('#title').val(),
						date: $('#date').val(),
						time: $('#time').val(),
					},
					dataType: 'json',
					complete: function (response) {
						loadingBtn($('#virtualBtn'), 'Modificar')
						loadingBtn($('#virtualModal .btn-secondary'),'Cerrar')
						const data = response.responseJSON.data;
						$('#virtualId').val(data.id)
						$(row.selector.rows).addClass('table-success')
						$("#virtualModal .btn-danger").removeClass('hidden')
						virtualId = data.id
						// send email
						$.ajax({
							type: "POST",
							url: getBaseUrl("includes/email/mailVirtual.php"),
							data: {
								id: virtualId,
							},
							complete: function (response) {
								console.log('responseEmail:', response)
			
							}
						});
					}
				});

			} else {
				// update
				$.ajax({
					type: "POST",
					url: includeThisFile(),
					data: {
						update: $("#virtualId").val(),
						link: $("#link").val(),
						title: $('#title').val(),
						date: $('#date').val(),
						time: $('#time').val(),
					},
					dataType: 'json',
					complete: function (response) {
						loadingBtn($('#virtualBtn'), 'Modificar')
						loadingBtn($('#virtualModal .btn-secondary'),'Cerrar')

					}
				});
			}
		}
		$(this)[0].classList.add('was-validated');
	})

	$('#virtualModal').on('hidden.bs.modal', function (event) {
		reset()
	})


	$("#virtualModal .btn-danger").click(function (e) {
		e.preventDefault();
		$("#deleteModal").modal('show')

	});

	$("#virtualDelBtn").click(function (e) {
		e.preventDefault();
		if (virtualId !== '') {
			loadingBtn($('#virtualDelBtn, #deleteModal .btn-secondary'))
			$.ajax({
				type: "POST",
				url: includeThisFile(),
				data: {
					delete: virtualId,
				},
				complete: function (response) {
					$("#deleteModal").modal('hide')
					$(row.selector.rows).removeClass('table-success')
					reset()
					loadingBtn($('#virtualDelBtn'), 'Aceptar')
					loadingBtn($('#deleteModal .btn-secondary'), 'Cancelar')

				}
			});
		} else {
			alert('VirtualId esta en blanco, contacte con el soporte')
		}
	});

	function reset() {
		$("form").removeClass('was-validated').trigger("reset");
		$("#virtualId").val('')
		loadingBtn($("#virtualBtn"),'Guardar')
		$("#virtualModal .btn-danger").addClass('hidden')
		virtualId = ''

	}

	function loadingBtn(btn, clear = '') {
		if (clear.length === 0) {
			btn.prop('disabled', true).html(`
			<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
			Cargando...
			`)
		} else {
			btn.prop('disabled', false).text(clear)
		}
	}

});
