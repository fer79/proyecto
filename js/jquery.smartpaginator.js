(function ($) {
	$.fn.fixOverflow = function () {
		if ($.browser.msie) {
			return this.each(function () {
				if (this.scrollWidth > this.offsetWidth) {
					$(this).css({ 'padding-bottom' : '20px', 'overflow-y' : 'hidden' });
				}
			});
			} else {
			return this;
		}
	};
})(jQuery);


;(function($){
	
	$.fn.paginator = function(options){
		
			var current_page = 'current_page';
			var items_per_page = 'items_per_page';
			var total_items = 'total_items';
			var number_of_pages = 'number_of_pages';
			var element;
			var $container;
			
		var defaults = {
			items_per_page 				: 	10,
			num_page_links_to_display 	: 	4,
			start_page 					: 	1,
			total_items					: 	20,
			label_first 				:	 'First',
			label_prev 					: 	'Prev',
			label_next 					: 	'Next',
			label_last 					: 	'Last',
            show_first_last				: 	true,
			onChange					: 	function(){return false;}
		};
		var options = $.extend({}, $.fn.paginator.defaults, options);
		
		
		
			
		return this.each(function(){
			
			
			$container = $(this);
			
			
			
			element=$container;
			element.data(current_page,options.start_page);
			element.data(items_per_page, options.items_per_page);
			element.data(total_items,options.total_items);
			
			if (options.total_items ==0){
				element.data(number_of_pages,1);
			}else{
				element.data(number_of_pages,Math.ceil(options.total_items/options.items_per_page));
			}
		
			
			
			
			/*CREATING DE PAGINATOR*/
			var more = '<span class="ellipse more">...</span>';
			var less = '<span class="ellipse less">...</span>';
			
            var first = !options.show_first_last ? '' : '<a class="first" href="#">'+ options.label_first +'</a>';
            var last = !options.show_first_last ? '' : '<a class="last" href="#">'+ options.label_last +'</a>';
			
			var box = first;
			box += '<a class="previous" href="#">'+ options.label_prev +'</a>';
			box+=less + '<div class="links" style="overflow:hidden;width:0px;position:relative;">';
			
			var current_link = 1;

			while(current_link <= element.data(number_of_pages)){
				if (current_link== options.start_page){
					var current='current';
				}else{
					var current='';
				}
				
				box += '<a class="number page '+current+'" href="#" pagenumb="' + current_link +'">'+ current_link +'</a>';
				current_link++;
			}
			
			box += '</div>'+ more + '<a class="next" href="#">'+ options.label_next +'</a>';
			box += last;
			
			$container.html(box).each(function(){
				
				$(this).find('.number:first').addClass('first_page');
				$(this).find('.number:last').addClass('last_page');
				
			});
			$container.css('position','relative');
			
			/*CREATING DE PAGINATOR*/
			
			
			
			var linkswidth = $container.find('.page').outerWidth(true);
			var boxwidth=0;
			
			if (element.data(number_of_pages) < options.num_page_links_to_display){
				boxwidth = linkswidth * element.data(number_of_pages);
			}else{
				boxwidth = linkswidth * options.num_page_links_to_display;
			}
			$container.find('.links').width(boxwidth);
			
			
			var width = 0;
			$container.children().each(function() {
				width += $(this).outerWidth( true );
			});
			$container.css('width',width);
			
			$container.find('.next').off( 'click',$(this)).on( 'click',$(this) ,function(e) {
				e.preventDefault();
				var left = $container.find('.links').scrollLeft() + boxwidth;
				$container.find('.links').animate({scrollLeft:left });
						
			});
			
			
			$container.find('.previous').off( 'click',$(this)).on('click',$(this) ,function(e) {
				e.preventDefault();
				var right = $container.find('.links').scrollLeft() - boxwidth;
				$container.find('.links').animate({scrollLeft:right });
				
				
			});
			
			
			// Event handler for 'first' link
			$container.find('.first').off( 'click',$(this)).on('click',$(this) ,function(e){
				e.preventDefault();
				if ($(this).hasClass('enabled')){
					animateToPlace(1,boxwidth,linkswidth);
					go_to(1);
				}
			});
			
			// Event handler for 'Last' link
			$container.find('.last').off( 'click',$(this)).on('click',$(this) ,function(e){
				e.preventDefault();
				if ($(this).hasClass('enabled')){
					animateToPlace(element.data(number_of_pages),boxwidth,linkswidth);
					go_to(element.data(number_of_pages));
				}
			});
			
			
			// Event handler for each 'Page' link
			$container.find('.page').off( 'click',$(this)).on('click',$(this) ,function(e){
				e.preventDefault();
				animateToPlace($(this).attr('pagenumb'),boxwidth,linkswidth);
				go_to($(this).attr('pagenumb'));
			});
			
			
			// Bind Events
			 $container.bind('update', function (e,number) {
                        
                	element.data(total_items,element.data(total_items) - number); 
					element.data(number_of_pages,Math.ceil(element.data(total_items)/options.items_per_page));
					
					var size=$container.find('.links .page').size();
					if (size > element.data(number_of_pages)){
						if (element.data(number_of_pages) !=0){
							animateToPlace(size-1,boxwidth,linkswidth);
							$container.find('.links .page[pagenumb='+(size-1)+']').addClass('last_page');
							$container.find('.links .page[pagenumb='+size+']').remove();
							go_to(size-1);
						
						}
					}
			 }).bind('gotopage',function(e,number){
				e.preventDefault();
				animateToPlace(number,boxwidth,linkswidth);
				go_to(number);
			 
			 
			 }).bind('reload_page',function(e,number){
				 
				 	e.preventDefault();
					go_to(options.start_page);
			 }).bind('destroy',function(){
					
					
					$container.find('.next').off( 'click',$(this));
					$container.find('.previous').off( 'click',$(this));
					$container.find('.first').off( 'click',$(this));
					$container.find('.last').off( 'click',$(this));
					$container.find('.page').off( 'click',$(this));
					$container.unbind('update');
					$container.unbind('gotopage');
					$container.unbind('reload_page');
					$container.empty();
					
				});
			
			
			$container.trigger('gotopage',options.start_page);
			
			
		
		}); 
		
		function animateToPlace(pagenumb,boxwidth,linkswidth){
			var left = $container.find('.links').scrollLeft()
			var position = $container.find('.page[pagenumb='+pagenumb+']').position().left;
			var move = 0;
			
			if  (position < (boxwidth / 2)){
				move= left - ((boxwidth/2)-(linkswidth/2))+position;
				$container.find('.links').animate({scrollLeft: move +'px'});
			}else if(position > (boxwidth / 2)){
				move= left + (position - ((boxwidth/2)-(linkswidth/2)))  ;
				$container.find('.links').animate({
				scrollLeft: move});
				
			}
			
		};
		
		
		function go_to(page_num){
			
			
			// Reassign the active class
			$container.children('.links').children('.page[pagenumb='+page_num+']').addClass('current')
			.siblings('.current')
			.removeClass('current');
			
			// Set the current page meta data
			element.data(current_page,page_num);
			
			// Hide the more and/or less indicators
			toggleMoreLess();
				
			if(typeof options.onChange == 'function'){

				options.onChange(element.data(current_page));
			
			}
		};
		
		
		
		function toggleMoreLess(){
			
			if(!$container.find('.current').hasClass('last_page')){
				$container.find('.more').addClass('enabled').removeClass('disabled');
				$container.find('.last').addClass('enabled').removeClass('disabled');
				$container.find('.next').addClass('enabled').removeClass('disabled');
				}else {
				$container.find('.more').addClass('disabled').removeClass('enabled');
				$container.find('.last').addClass('disabled').removeClass('enabled');
				$container.find('.next').addClass('disabled').removeClass('enabled');
			}
			
			if(!$container.find('.current').hasClass('first_page')){
				$container.find('.less').addClass('enabled').removeClass('disabled');
				$container.find('.first').addClass('enabled').removeClass('disabled');
				$container.find('.previous').addClass('enabled').removeClass('disabled');
				}else {
				$container.find('.less').addClass('disabled').removeClass('enabled');
				$container.find('.first').addClass('disabled').removeClass('enabled');
				$container.find('.previous').addClass('disabled').removeClass('enabled');
			}
		}
		
		
		
		
		
	};
	
	
	
	
})(jQuery);