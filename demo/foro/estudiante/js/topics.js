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
    $.post(
      includeThisFile(),
      { topicsByClass: _class },
      (res) => {
        if (res.response) {
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
            var today = new Date()
            today.setHours(0, 0, 0, 0)

            let closeDate = new Date(topic.desde)
            closeDate.setHours(0, 0, 0, 0)

            let status = topic.estado === 'a' ? 'table-success' : 'table-danger'
            status = topic.estado === 'a' && closeDate <= today ? 'table-warning' : status
            $(thisRow.node()).addClass(status)
          })
        } else {
          alert(
            __LANG === 'es' ? 'No hay temas en esta clase' : 'There are no topics in this class'
          )
        }
      },
      'json'
    )
  }

  $('#back').click((e) => {
    window.location.href = getBaseUrl('topics.php')
  })

  $('.topicsTable tbody').on('click', 'tr', function () {
    const row = topicsTable.row(this)
    if (row.index() !== undefined) {
      const topicId = $(row.node()).prop('id')
      window.location.href = getBaseUrl('viewTopic.php?id=' + topicId)
    }
  })
})
