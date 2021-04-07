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
			$("#virtualModal").find('.modal-title').text(`Salón virtual para ${subjectCode}`)
			$("#virtualModal").find('.modal-body').html(`
			<div class="text-center">
                <div class="spinner-border" role="status">
                  <span class="sr-only">Loading...</span>
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
							<p>Para acceder al curso virtual haga <a href="#" data-link="${data.link}" class="alert-link linkBtn">click aquí</a></p>
							<hr>
							<p>Fecha: ${formatDate(data.date)}</p>
							<p>Hora: ${formatTime(data.time)}</p>
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
				window.open(linkBtn.data('link'),'_blank')
				loadingBtn(linkBtn,'click Aqui')

			 }
		});
	})

	

});
