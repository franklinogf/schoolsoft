$(document).ready(function () {
	let virtualId = ''
	let row
	$(".classesTable tbody").on("click", "tr", function () {
		row = classesTable.row(this)
		if (row.index() !== undefined) {
			const data = row.data()
			const subjectCode = data[0]
			teacherId = row.selector.rows.attributes[0].nodeValue
			$("#virtualModal").modal('show')
			$("#virtualModal").find('.modal-title').text(`${__LANG === 'es' ? 'Salón virtual para' : 'Virtual classroom for'} ${subjectCode}`)
			$("#virtualModal").find('.modal-body').html(`
			<div class="text-center">
                <div class="spinner-border" role="status">
                  <span class="sr-only">${__LANG === 'es' ? 'Cargando' : 'Loading'}...</span>
                </div>
              </div>
			`)
			$.ajax({
				type: "POST",
				url: includeThisFile(),
				data: {
					find: subjectCode,
					teacherId
				},
				dataType: 'json',
				complete: function (response) {
					if (response.responseJSON.response) {
						const data = response.responseJSON.data
						virtualId = data.id
						$("#virtualModal").find('.modal-body').html(`
						<div class="alert alert-primary" role="alert">
							<h4 class="alert-heading">${data.title}</h4>
							<p>${__LANG === 'es' ? 'Para acceder al curso virtual haga' : 'To access the virtual classroom'} <a href="#" data-link="${data.link}" class="alert-link linkBtn">${__LANG === 'es' ? 'click aquí' : 'click here'}</a></p>
							<hr>
							<p>${__LANG === 'es' ? 'Fecha' : 'Date'}: ${formatDate(data.date)}</p>
							<p>${__LANG === 'es' ? 'Hora' : 'Time'}: ${formatTime(data.time)}</p>
						</div>
						`)
					}
				}
			});
		}
	});

	$(document).on('click', '.linkBtn', function (e) {
		e.preventDefault()
		const linkBtn = $(this)
		loadingBtn(linkBtn)
		$.ajax({
			type: "POST",
			url: includeThisFile(),
			data: {
				asis: virtualId,
			},
			complete: function (response) {
				window.open(linkBtn.data('link'), '_blank')
				loadingBtn(linkBtn, __LANG === 'es' ? 'click aquí' : 'click Here')

			}
		});
	})



});
