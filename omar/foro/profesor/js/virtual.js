$(document).ready(function () {
	let subjectCode = ''
	let row
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
					if (response.responseJSON) {
						const data = response.responseJSON.data;
						$("#link").val(data.link)
						$('#title').val(data.title)
						$('#date').val(data.date)
						$('#time').val(data.time)
						$('#virtualId').val(data.id)
						loadingBtn($('#virtualBtn'), 'Modificar')
					}else{

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
			loadingBtn($('#virtualBtn'))

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
						loadingBtn($('#virtualBtn'), 'Guardar')
						const data = response.responseJSON.data;
						$('#virtualId').val(data.id)
						$(row.selector.rows).addClass('table-success')
					}
				});

			} else {				
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
					}
				});
			}
		}
		$(this)[0].classList.add('was-validated');
	})

	$('#virtualModal').on('hidden.bs.modal', function (event) {
		$("form").removeClass('was-validated')
	  })

	function loadingBtn(btn, clear = '') {
		if (clear.length === 0) {
			btn.prop('disabled', true).html(`
			<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
			Loading...
			`)
		} else {
			btn.prop('disabled', false).text(clear)
		}
	}

});
