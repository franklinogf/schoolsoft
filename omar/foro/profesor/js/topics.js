$(document).ready(function () {
   let _class = '';

   const classesTableWrapper = $(".classesTable").parents('.dataTables_wrapper');
   const topicsTableWrapper = $(".topicsTable").parents('.dataTables_wrapper');
   topicsTableWrapper.hide(0);


   if (sessionStorage.getItem('class')) {
      getTopics(sessionStorage.getItem('class'));
   }


   $('.classesTable tbody').on('click', 'tr', function () {
      const row = classesTable.row(this)
      if (row.index() !== undefined) {
         const data = row.data();
         getTopics(data[0]);
      }
   });

   function getTopics(thisClass) {
      _class = thisClass      
      $('#class').val(thisClass)

      $.post(includeThisFile(), { topicsByClass: _class }, res => {
         console.log(res);
         if (res.data) {
            res.data.map(topic => {
               const thisRow = topicsTable.row.add({
                  0: topic.titulo,
                  1: formatDate(topic.desde),
                  2: formatDate(topic.fecha),
                  3: formatTime(topic.hora),
               }).draw();

               $(thisRow.node()).prop('id', topic.id)
               var today = new Date()
               today.setHours(0, 0, 0, 0)

               let closeDate = new Date(topic.desde)
               closeDate.setHours(0, 0, 0, 0)

               let status = topic.estado === 'a' ? 'table-success' : 'table-danger'
               status = topic.estado === 'a' && closeDate <= today ? 'table-warning' : status
               $(thisRow.node()).addClass(status)

            })
         }
         // hide first table and show second table
         classesTableWrapper.hide('drop', { direction: "left" }, 400, () => {
            $('#newTopic,.leyend').fadeToggle(250);
            topicsTableWrapper.show('drop', { direction: "right" }, 400);
            $("#header").animate({ opacity: 0 }, 250, () => {
               $("#header").text(__LANG === 'es' ? 'Lista de temas' : 'List of topics').animate({ opacity: 1 }, 250);
            });
         });
      },
         "json"
      );
      sessionStorage.clear('class');

   }

   $("#back").click((e) => {
      // hide second table and shows first table
      _class = ''
      $('#newTopic').fadeToggle(250);
      topicsTableWrapper.hide('drop', { direction: "right" }, 400, () => {
         // Reset the variables
         sessionStorage.clear('class');
         topicsTable.rows().remove();    
         $(topicsTable.rows().nodes()).remove();        
         classesTableWrapper.show('drop', { direction: "left" }, 400);
         $(".leyend").fadeToggle(250)
         $("#header").animate({ opacity: 0 }, 250, () => {
            $("#header").text(__LANG === 'es' ? 'Mis Cursos' : 'My classes').animate({ opacity: 1 }, 250);
         });
      });
   })

   $("#newTopic").click((e) => {
      const modal = $('#myModal');
      modal.modal('show');
   })

   $('.topicsTable tbody').on('click', 'tr', function () {
      const row = topicsTable.row(this)
      if (row.index() !== undefined) {
         const topicId = $(row.node()).prop('id');
         sessionStorage.setItem("class", _class);
         window.location.href = getBaseUrl('viewTopic.php?id=' + topicId)
      }
   });

   // save the class into a session if a new topic is inserted
   $('form').submit((e) => {
      sessionStorage.setItem("class", _class);
   })



});