;(function($){
	
	$.fn.wizardnube = function(options){
		
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
		var options = $.extend({}, $.fn.wizardnube.defaults, options);
		
			
		return this.each(function(){
			
			
			$container = $(this);
			
			
			
		}); 