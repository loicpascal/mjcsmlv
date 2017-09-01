$(document).ready(function(){

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
			$(this).next('table').slideToggle();
		} else {
			$(this).children('.fa').removeClass('fa-chevron-down');
			$(this).children('.fa').addClass('fa-chevron-up');
			$(this).children('.fa').data('sens', 'up');
			$(this).next('table').slideToggle();
		}
	});
});