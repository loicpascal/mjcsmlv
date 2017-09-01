$(document).ready(function(){

	$("input[name='mjc_activites_age_min'], input[name='mjc_activites_age_max']").keyup( function () {
		var age_min = $("input[name='mjc_activites_age_min']").val();
		var age_max = $("input[name='mjc_activites_age_max']").val();

		if (age_min == '' && age_max == '') {
			$("input[name='mjc_activites_age']").val("");
		} else if (age_min != '' && age_max == '') {
			$("input[name='mjc_activites_age']").val("À partir de " + age_min + " ans");
		} else if (age_min == '' && age_max != '') {
			$("input[name='mjc_activites_age']").val("Jusqu'à " + age_max + " ans");
		} else {
			$("input[name='mjc_activites_age']").val("De " + age_min + " à " + age_max + " ans");
		}
	});

	$("input[name='mjc_activites_t1'], input[name='mjc_activites_t4']").keyup( function () {
		var t1 = $("input[name='mjc_activites_t1']").val();
		var t4 = $("input[name='mjc_activites_t4']").val();

		if (t1 == '' || t4 == '') {
			$("input[name='mjc_activites_tarif']").val("");
		} else {
			$("input[name='mjc_activites_tarif']").val("De " + t1 + " à " + t4 + " €");
		}
	});

	/*
	Confirm
	 */
	$('a.js-confirm').click(function() {
		var txt = 'Êtes-vous sûr ';
		if (typeof $(this).data('confirm') !== typeof undefined && $(this).data('confirm') !== false) {
			txt += 'de vouloir ' + $(this).data('confirm') + ' ?';
		} else {
			txt += '?';
		}

		if(!confirm(txt)) return false;
	});

	/*
	Accordeon
	 */
	$('.accordeon').click(function() {
		if($(this).children('.fa').data('sens') == "up") {
			$(this).children('.fa').removeClass('fa-chevron-up');
			$(this).children('.fa').addClass('fa-chevron-down');
			$(this).children('.fa').data('sens', 'down');
			$(this).next('table').toggle();
		} else {
			$(this).children('.fa').removeClass('fa-chevron-down');
			$(this).children('.fa').addClass('fa-chevron-up');
			$(this).children('.fa').data('sens', 'up');
			$(this).next('table').toggle();
		}
	});

	/*
	Colorbox
	 */
	$('#photo_activite').colorbox({rel:'photo_activite'});


	/*
	Upload - Resize
	 */

    $('#mjc_activites_photo').change(function() {
        if ($('#mjc_activites_photo')[0].files.length) {
            var file = $('#mjc_activites_photo')[0].files[0];

	        var taille = file.size;
            for (var aMultiples = ["Ko", "Mo", "Go", "To", "Po"], nMultiple = 0, nApprox = taille / 1024; nApprox > 1; nApprox /= 1024, nMultiple++) {
		    	sOutput = nApprox.toFixed(1) + " " + aMultiples[nMultiple] + " (" + taille + " octets / 3145728 max)";
		  	}

		  	var style = '';
		  	if (taille > 3145728) {
		  		style = "style='color:red'";
		  		fa = "exclamation";
		  	} else {
		  		style = "style='color:green'";
		  		fa = "check";
		  	}
	        $('#photo_activite_taille').html("<span " + style + " class='fa fa-" + fa + "' aria-hidden='true'>&nbsp;Taille du fichier : " + sOutput + "</span>");

	        // Affichage de l'image en petit
	        $('#photo_activite').remove();
	        $('#photo_activite_preview').append("<img id='photo_activite' title='Miniature' src='" + window.URL.createObjectURL(file) + "'/><br>");
        }
    });
});