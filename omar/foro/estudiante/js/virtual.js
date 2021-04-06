$(document).ready(function () {
	let subjectCode = ''
	let row
	$(".classesTable tbody").on("click", "tr", function () {

		row = classesTable.row(this)
		if (row.index() !== undefined) {
			const data = row.data()
			subjectCode = data[0]
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
					console.log(response)
					if (response.responseJSON) {
						const data = response.responseJSON.data;
						$("#virtualModal").find('.modal-body').html(`
			<div class="alert alert-primary" role="alert">
				<h4 class="alert-heading">${data.title}</h4>
				<p>Para acceder al curso virtual haga <a href="${data.link}" class="alert-link" target="_blank">click aquí</a></p>
				<hr>
				<p>Fecha: ${formatDate(data.date)}</p>
				<p>Hora: ${formatTime(data.time)}</p>
				<small class="text-muted">Si no le funciona el enlace, copie y pegue este enlace en el navegador: ${data.link} </small>
			</div>
			`)




					}
				}
			});

		}
	});






});
