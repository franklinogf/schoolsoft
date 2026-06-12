$(document).ready(function () {
  const params = new URLSearchParams(window.location.search)
  const _class = params.get('class')

  const classesTableWrapper = $('.classesTable').parents('.dataTables_wrapper')
  const topicsTableWrapper = $('.topicsTable').parents('.dataTables_wrapper')

  if (_class !== null) {
    getTopics(_class)
  }

  $('.classesTable tbody').on('click', 'tr', function () {
    const row = classesTable.row(this)
    if (row.index() !== undefined) {
      const data = row.data()
      window.location.href = getBaseUrl('topics.php?class=' + data[0])
    }
  })

  function getTopics(thisClass) {
    $('#class').val(thisClass)
    topicsTable.clear().draw()

    $.post(
      includeThisFile(),
      { topicsByClass: _class },
      (res) => {
        if (res.data) {
          res.data.map((topic) => {
            const thisRow = topicsTable.row
              .add({
                0: topic.titulo,
                1: formatDate(topic.desde),
                2: formatDate(topic.fecha),
                3: formatTime(topic.hora)
              })
              .draw()

            $(thisRow.node()).prop('id', topic.id)
            const today = new Date()
            today.setHours(0, 0, 0, 0)

            const closeDate = new Date(topic.desde)
            closeDate.setHours(0, 0, 0, 0)

            const status =
              topic.estado === 'a'
                ? closeDate <= today
                  ? 'table-warning'
                  : 'table-success'
                : 'table-danger'
            $(thisRow.node()).addClass(status)
          })
        }
      },
      'json'
    )
  }

  $('#newTopic').click((e) => {
    const modal = $('#myModal')
    modal.modal('show')
  })

  $('.topicsTable tbody').on('click', 'tr', function () {
    const row = topicsTable.row(this)

    if (row.index() !== undefined) {
      const topicId = $(row.node()).prop('id')

      window.location.href = getBaseUrl('viewTopic.php?id=' + topicId)
    }
  })
})
