$(document).ready(function () {
   const $messages = $("#messages")
   const $message = $("#message")
   const $messageOptions = $(".message-option")
   let messages = []

   getMessages();

   $messageOptions.click(function (e) {
      e.preventDefault();
      if (!$(this).hasClass('active')) {
         $messageOptions.removeClass('active')
         $(this).addClass('active')
         const option = $(this).data('option')
         getMessages(option);
         animateCSS($message.children(), `bounceOutLeft faster`,()=>{
            $message.html(`<div class="d-flex justify-content-center align-items-center h-100 font-bree">
            Seleccione un mensaje
         </div>`);
         })
      }
   })


   $($messages).on('click', 'div.card', function (e) {
      const $thisMessage = $(this)
      const index = messages.findIndex(message => message.id === $thisMessage.data('id'))
      const message = messages[index]
      // show the messsage
      $message.html(`<div class="media p-2 mt-2">
      <img src="${message.foto}" class="align-self-start mr-2 rounded-circle" alt="Profile Picture" width="52" height="52">
      <div class="media-body">
         <p class="m-0"><strong>${message.nombre}</strong> <small>(teacher)</small></p>
         <small class="text-muted font-weight-light">${message.fecha}</small>
      </div>
      <button title="Responder" class="btn btn-secondary btn-sm" data-toggle="tooltip" type="button"><i class="fas fa-reply text-primary"></i></button>
   </div>
   <p class="p-2 my-0 font-bree">${message.asunto}</p>
   <hr class="my-1">
   <p class="p-2 mt-1 message-text font-markazi">${message.mensaje}</p>`)

      // change the message read status
      $.post(includeThisFile(), { changeStatus: message.id });
      const $status = $thisMessage.find('.status')
      animateCSS($status, 'zoomOut faster', () => {
         $status.remove();
      })

   })

   $($messages).on('DOMSubtreeModified', () => {
      animateCSS($messages.children(), 'bounceInDown faster')
   })

   $($message).on('DOMSubtreeModified', () => {
      const effect = window.innerWidth > 768 ? 'bounceInLeft' : 'bounceInDown'
      animateCSS($message.children(), `${effect} faster`)
   })


   // inbox
   function getMessages(type = 'inbound') {
      $.post(includeThisFile(), { getMessages: type }, res => {
         if (res.response) {
            $messages.html('')
            messages = res.data
            res.data.map(message => {

               $messages.append(`<div data-id="${message.id}" class="card w-100 rounded-0 pointer-cursor">
               <div class="card-body p-2">
                  <p class="card-text mb-0 font-weight-bold">${message.nombre}</p>
                  <p class="card-text mb-0 text-muted d-flex justify-content-between"><small>${message.fecha}</small><small>${message.hora}</small></p>
                  <p class="card-text mb-0 text-truncate font-weight-light">${message.asunto}</p>
                  <p class="card-text text-right">${message.leido !== 'si' ? '<small class="badge badge-success rounded-0 status">Nuevo</small>' : ''}</p>
               </div>
            </div>`)
            })
         } else {
            $messages.html(`
            <div class="d-flex justify-content-center align-items-center h-100 font-bree">
                  No tiene mensajes
               </div>`)
         }

      },
         "json"
      );
   }

});

