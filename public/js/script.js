var todoApp = function(){
	$ = jQuery.noConflict();
	var _this = this;
	
	this.events = function(){
		$( ".datepicker" ).datepicker({
			dateFormat: "yy-mm-dd"
		});
	};
	
	this.init = function(){
		$(document).ready( _this.events );
	};
	this.init();
};

new todoApp();