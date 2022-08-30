$.ajaxSetup({
    type: 'POST', 
    dataType: 'html', 
    beforeSend: function(){ 
      console.debug('Запрос отправлен. Ждите ответа.');
    },
    error: function(req, text, error){ 
      console.error('Упс! Ошибочка: ' + text + ' | ' + error);
    },
    complete: function(){ 
      console.debug('Запрос полностью завершен!');
    }
});

$("#form").on("submit", function(e){
    e.preventDefault()
    var $that = $(this)
    formData = new FormData($that.get(0));
    console.log(formData)
      $.ajax({
      url: 'http://migration.loc/core/handler.php',
      processData: false,
      contentType: false,
      cache:false,
      data : formData,
      beforeSend: function() {
        $('#bnt-submit').prop("disabled",true);
      },
      success: function(data){
        $('#bnt-submit').prop("disabled",false);
        alert(data);
      }
      });
  });
  
  $("#download").on("submit", function(e){
    e.preventDefault()
  
    $.ajax({
          url: 'core/presetHandler.php',
          method: 'post',
          dataType: 'html',
          data: $(this).serialize(),
          success: function(data){
            $("#download").append(data)
          }
      });
  });

  function hideHandler(event){
    setInterval(()=>{
      $("#btn-download").css('display', 'none')
    }, 500)
  }