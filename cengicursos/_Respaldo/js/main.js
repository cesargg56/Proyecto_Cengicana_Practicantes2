/*
 * Mostrar u ocultar div principales
 */
$(function (){
  $("#btnLoadCSV").click(function(){
    $("#cargarParticipantes").show("slow");
	  $("#gridParticipantes").hide("slow");
  });
  $("#btnShowGrid").click(function(){
    $("#cargarParticipantes").hide("slow");
    $("#gridParticipantes").show("slow");
  });
})