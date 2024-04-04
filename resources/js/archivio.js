function gotoLibroDetails(idLibro) {
  window.location = "/biblioteca/libri/" + idLibro;
}

function gotoClienteDetails(idCliente) {
  window.location = "/biblioteca/clienti/" + idCliente;
}

function gotoPrestitoDetails(idPrestito) {
  window.location = "/biblioteca/libri/prestiti/" + idPrestito;
}

$(document).ready(function () {
  // disabled all the input element in a form when a form is submitted
  $(function () {
    $("form").submit(function () {
      $(this)
        .find(":input")
        .filter(function () {
          return !this.value;
        })
        .attr("disabled", "disabled");
      return true; // ensure form still submits
    });
  });
});
