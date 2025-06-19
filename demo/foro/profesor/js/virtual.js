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
			$("#virtualModal").find('.modal-title').text(__LANG === 'es' ? 'Salón virtual para ' : 'Virtual classroom for ' + subjectCode)
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
						$('#password').val(data.password)
						$('#information').val(data.information)
						$('#virtualId').val(data.id)
						$("#virtualModal .btn-danger").removeClass('hidden')
						virtualId = data.id
						loadingBtn($('#virtualBtn'), __LANG === 'es' ? 'Modificar' : 'Update')
					} else {
						loadingBtn($('#virtualBtn'), __LANG === 'es' ? 'Crear' : 'Create')
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
						password: $('#password').val(),
						information: $('#information').val(),
					},
					dataType: 'json',
					complete: function (response) {
						loadingBtn($('#virtualBtn'), 'Modificar')
						loadingBtn($('#virtualModal .btn-secondary'), 'Cerrar')
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
						password: $('#password').val(),
						information: $('#information').val(),
					},
					dataType: 'json',
					complete: function (response) {
						loadingBtn($('#virtualBtn'), __LANG === 'es' ? 'Modificar' : 'Update')
						loadingBtn($('#virtualModal .btn-secondary'), 'Cerrar')

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
					loadingBtn($('#virtualDelBtn'), __LANG === 'es' ? 'Aceptar' : 'Accept')
					loadingBtn($('#deleteModal .btn-secondary'), __LANG === 'es' ? 'Cancelar' : 'Cancel')

				}
			});
		} else {
			alert(__LANG === 'es' ? 'VirtualId esta vacio, contacte con el soporte' : 'VirtualId is empty, contact with support')
		}
	});

	function reset() {
		$("form").removeClass('was-validated').trigger("reset");
		$("#virtualId").val('')
		loadingBtn($("#virtualBtn"), __LANG === 'es' ? 'Guardar' : 'Save')
		$("#virtualModal .btn-danger").addClass('hidden')
		virtualId = ''

	}

});
