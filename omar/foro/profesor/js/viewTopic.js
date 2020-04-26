$(document).ready(function () {
  $('#editTopicBtn').on('click', function () {

    const modal = $('#myModal')
    const title = $('#title').text().trim();
    const description = $('#description').text()
    modal.find('#modalTitle').val(title)
    modal.find('#modalDescription').val(description)
    modal.modal('show')

  });
});