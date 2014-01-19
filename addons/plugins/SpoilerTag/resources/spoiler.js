$(document).ready(function() { 
	$(document).on("click",".spoiler span", function(){
		$(this).siblings(".content").fadeToggle(600);
	});
	$(document).on("click",".nsfw span", function(){
		$(this).siblings(".content").fadeToggle(600);
	});
});

var SpoilerEditor = {
	insertSpoiler : function($id){
		ETConversation.wrapText($("#"+$id+" textarea"), "[spoiler]", "[/spoiler]");

	}
};
