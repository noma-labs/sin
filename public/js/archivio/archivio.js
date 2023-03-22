
function gotoLibroDetails(idLibro) {
  window.location = "/biblioteca/libri/"+idLibro;
}

function gotoClienteDetails(idCliente) {
  window.location = "/biblioteca/clienti/"+idCliente;
}

function goToPersonaDetails(idPersona) {
  window.location = "/nomadelfia/persone/"+idPersona;
}


function   gotoPrestitoDetails(idPrestito) {
  window.location = "/biblioteca/libri/prestiti/"+idPrestito;
}

 // $('div.alert-dismissable').not('.alert-important').delay(10000).slideUp(300);

//
// $.datepicker.setDefaults($.datepicker.regional['it']);
// //
// $(document).ready(function(){
//   $('.datepicker').datepicker();

 //  $("#dater").datepicker({ 'dateFormat': 'yy-mm-dd'});
 //
 // $("#datep").datepicker({ 'dateFormat': 'yy-mm-dd'});
 //
 //  $('#dataPubblicazione').datepicker({
 //       changeMonth: true,
 //       changeYear: true,
 //       showButtonPanel: true,
 //       dateFormat: 'MM yy',
 //
 //       onClose: function () {
 //           var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
 //           var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
 //           $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
 //           $(this).datepicker('refresh');
 //       },
 //   });
 //
 //   $("#dataPubblicazione").focus(function () {
 //       $(".ui-datepicker-calendar").hide();
 //       $("#ui-datepicker-div").position({
 //           my: "center top",
 //           at: "center bottom",
 //           of: $(this)
 //       });
 //   });
 //
 //   $("#dataPubblicazione").blur(function () {
 //       $(".ui-datepicker-calendar").hide();
 //   });
// });



$(document).ready(function(){
  // disabled all the input element in a form when a form is submitted
  $(function() {
   $("form").submit(function() {
      $(this).find(":input").filter(function(){ return !this.value; }).attr("disabled", "disabled");
      return true; // ensure form still submits
    });
  });
});
