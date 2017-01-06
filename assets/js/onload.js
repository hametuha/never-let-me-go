jQuery(document).ready(function($){

	var incSearch = {
		currentChar: '',
		timer: null,
		onloading: false,
		setUserId: function(e){
			e.preventDefault();
			$('#nlmg_assign_to').val($(this).attr('href').replace(/[^0-9]/, ''));
		},
		
		fire: function(){
			incSearch.onloading = true;
			$('.inc-search-container .loader').removeClass('toggle');
			$.post(NLMG.endpoint, {
				action: NLMG.action,
				query: incSearch.currentChar
			}, incSearch.result);
		},
		
		result: function(response){
			incSearch.onloading = false;
			incSearch.timer = null;
			$('.inc-search-container .loader').addClass('toggle');
			//Empty container
			var container = $('#user-inc-search-result');
			container.empty().css({
				display: 'block'
			});
			if(response.total > 0){
				container.append('<li class="no-result">' + NLMG.found.replace(/%%/, response.total) + '</li>');
				for(i = 0, l = response.results.length; i < l; i++){
					container.prepend('<a href="#' + response.results[i].ID + '">' + response.results[i].display_name + '</a>');
				}
				container.find('a').click(incSearch.setUserId).fadeIn();
			}else{
				container.append('<li class="no-result">' + NLMG.noResults + '</li>').fadeIn('fast', function(){
					setTimeout(function(){
						container.fadeOut();
					}, 2000);
				});
			}
		},
		
		clearResults: function(e){
			$(this).val('');
			$('#user-inc-search-result').fadeOut('fast', function(){
				$(this).css('display', 'none');
			});
		}
	};
	
	$('#user-inc-search').keyup(function(e){
		incSearch.currentChar = $(this).val();
		if(incSearch.currentChar.length > 2 && !incSearch.onloading){
			if(incSearch.timer){
				//Timer exists, reset
				clearTimeout(incSearch.timer);
			}
			//Enqueue AJAX search
			incSearch.timer = setTimeout(incSearch.fire, 500);
		}
	});
	
	$('#user-inc-search').blur(incSearch.clearResults);
});
