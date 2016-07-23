$(document).ready(function(){
	// Variables
	// alert('Bonjour');

	$("input[name='mjc_activites_age_min'], input[name='mjc_activites_age_max']").keyup( function () {
		var value_age = "De " + $("input[name='mjc_activites_age_min']").val() + " à " + $("input[name='mjc_activites_age_max']").val() + " ans";
		$("input[name='mjc_activites_age']").val(value_age);
	});

	$("input[name='mjc_activites_t1'], input[name='mjc_activites_t4']").keyup( function () {
		var value_age = "De " + $("input[name='mjc_activites_t1']").val() + " à " + $("input[name='mjc_activites_t4']").val() + " €";
		$("input[name='mjc_activites_tarif']").val(value_age);
	});
});