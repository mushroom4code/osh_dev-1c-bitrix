BX.namespace('BX.Sale.PersonalProfileComponent');

(function() {
	BX.Sale.PrivateProfileComponent = {
		init: function ()
		{
			var passwordNode = BX('main-profile-password');
			var confirmNode = BX('main-profile-password-confirm');

			var stateNode = BX('main-profile-state');
			var cityNode = BX('main-profile-city');
			var streetNode = BX('main-profile-street');

			BX.ready(function(){
				BX.bind(confirmNode, 'input', function(){
					if (!BX.type.isNotEmptyString(confirmNode.value))
					{
						BX.removeClass(passwordNode.parentNode, 'has-error');
					}
					else if (!BX.type.isNotEmptyString(passwordNode.value))
					{
						BX.addClass(passwordNode.parentNode, 'has-error');
					}
				});
				BX.bind(passwordNode, 'input', function(){
					if (BX.type.isNotEmptyString(passwordNode.value))
					{
						BX.removeClass(passwordNode.parentNode, 'has-error');
					}
					else if (BX.type.isNotEmptyString(confirmNode.value))
					{
						BX.addClass(passwordNode.parentNode, 'has-error');
					}
				});

				$('#main-profile-day2').change(function (){
					// console.log('tryagain');
					// var dateArray = BX('main-profile-day2').value.split('/');
					// var readyDateString = dateArray[1] + '/' + dateArray[0] + '/' + dateArray[2];
					// $('#main-profile-day2').val(readyDateString);
				});

				BX.bind(BX('main-profile-submit'), 'click', function () {
					if ($('#main-profile-password').val() !== $('#main-profile-password-confirm').val()) {
						event.preventDefault();
						$('#password-notification-error').text('Пароли не совпадают').removeClass('d-none');
					} else {
						$('#password-notification-error').addClass('d-none');
					}

					if (BX('notification').checked !== true) {
						BX.removeClass(
							BX("notification-error"),
							'd-none'
						);
						event.preventDefault();
					}
				});

				$('#main-profile-address').suggestions({
					token: window.daDataParam.token,
					type: "ADDRESS",
					hint: false,
					onSelect: function (suggestion) {
						stateNode.value = suggestion.data.region_with_type ?? '';
						cityNode.value = suggestion.data.city ?? '';
						streetNode.value = suggestion.data.street ?? '';
						if( suggestion.data.street_type !== null)
							streetNode.value += ' '+ suggestion.data.street_type;
						console.log(suggestion.data);
						
						if (suggestion.data.house!==undefined && suggestion.data.house!==null) {
							streetNode.value += ', ' + suggestion.data.house_type + ' ' + suggestion.data.house;
						}

						if (suggestion.data.block_type_full!==undefined && suggestion.data.block_type_full!==null) {
							streetNode.value += ', ' + suggestion.data.block_type_full + ' ' + suggestion.data.block;
						}

						if (suggestion.data.flat!==undefined && suggestion.data.flat!== null ) {
							streetNode.value += ', ' + suggestion.data.flat_type + ' ' + suggestion.data.flat;
						}						
						
						
						
						

					}.bind(this)
				});

			});
		},
	}

})();


$(document).ready(function(){
	$('#main-profile-email').bind("input", function(event) {
		if (event.target.validity.patternMismatch == true) {
			event.target.setCustomValidity('Неверный формат почты. Почта должна иметь вид *@*.*');
		} else {
			event.target.setCustomValidity('');
		}
	});

    $(".form-control:not(.profile)").keyup(function(){
        $(".CHANGE_FORM").val('1');     
    });

	$('a').on('click', function(){
		console.log($(".CHANGE_FORM").val());
		if(  $(".CHANGE_FORM").val() == '1' )
			return confirm ('Введенные в форме данные не сохранятся! Вы действительно хотите перейти на другую страницу?');
	}); 

  });