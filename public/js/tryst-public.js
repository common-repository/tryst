(function( $ ) {
	'use strict';
	/**
	* All of the code for your public-facing JavaScript source
	* should reside in this file.
	*
	* Note: It has been assumed you will write jQuery code here, so the
	* $ function reference has been prepared for usage within the scope
	* of this function.
	*
	* This enables you to define handlers, for when the DOM is ready:
	*
	* $(function() {
		*
		* });
		*
		* When the window is loaded:
		*
		* $( window ).load(function() {
			*
			* });
			*
			* ...and/or other possibilities.
			*
			* Ideally, it is not considered best practise to attach more than a
			* single DOM-ready or window-load handler for a particular page.
			* Although scripts in the WordPress core, Plugins and Themes may be
			* practising this, we should strive to set a better example in our own work.
			*/
			$(document).ready(function() {		
				if($('form.readonly').length > 0){
					$('form.readonly').find('.form-control').attr('readonly', 'readonly');
					//$('#address_state').find('option[value="'+$('input[name="address_state_selected"]').val()+'"]').attr('selected', 'selected');
				}
				$('[data-toggle="tooltip"]').tooltip(); 	  
				$('.available').on('click', function(e){
					e.preventDefault();
					e.stopPropagation();
					$('#modal-form-meeting #f-date').val($(this).data('date'));
					$('#modal-form-meeting #f-time').val($(this).data('time'));
					$('#modal-form-meeting').modal('show');
				});	
				$(".validate").validationEngine('attach');
				var path = $('meta[name="tryst_path"]').attr('content');
				$("#zipcode").on('blur', function () {
					var cep_code = $(this).val();
					if (cep_code.length == 0)
					return;
					$.ajax({
						url: "https://apps.widenet.com.br/busca-cep/api/cep.json",
						context: document.body,
						data: {code: cep_code},
						method: 'GET'
					}).done(function(result) {
						if (result.status != 1) {
							alert(result.message || "Houve um erro desconhecido. Reporte aos administradores.");
							return;
						}
						$("input#zipcode").val(result.code);
						$("input#address_district").val(result.district);
						$("input#address_street").val(result.address);
						$('select#address_state > option[value="' + result.state + '"]').attr("selected", "selected");
						var scripts = document.getElementsByTagName("script");
						var script = scripts[scripts.length-1];
						var cityList = path+'public/js/estados-cidades.json';
						console.log(cityList);
						var cityName = result.city;
						$.getJSON(cityList, function (result) {
							$.each(result, function (i, values) {
								$.each(values, function (i, states) {
									if (states.sigla == $('select#address_state').val()) {
										//$('select#cidade').find('option').remove();
										$.each(states.cidades, function (i, city) {
											// $('select#cidade').append('<option>' + city + '</option>');
											if (cityName == city) {
												$('input#address_city').val(city);
											}
										});
									}
								});
							});
						});
					});
				});
				$(".cpf").mask("999.999.999-99");
				$(".rg").mask("99.999-999");
				$(".cnpj").mask("99.999.999/9999-99");
				$('.phone').mask('(99) Z9999-999Z', {
					translation: {
						'Z': {
							pattern: /[0-9]/, optional: true
						}
					}
				});
				$(".date").mask("99/99/9999");
				$("#zipcode").mask("99.999-999");
				$('#zipcode').change(function () {
					$('.zipcode-control').removeAttr('readonly');
				});
			});
		})( jQuery );
		