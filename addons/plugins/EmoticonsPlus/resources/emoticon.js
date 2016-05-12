var EmoticonAdv = {
	activeTextAreaId:"",
	showDropDown: function(id){
		this.activeTextAreaId = id;
		buttonPosition = $("#"+id+" .formattingButtons .emoticon").offset();
		$("#emoticonDropDown").css( { 
					left: buttonPosition.left, 
					top: buttonPosition.top+6
			} );

		$("#emoticonDropDown").fadeIn();
	},
	hideDropDown: function(){
		$("#emoticonDropDown").fadeOut();

	},
	insertSmiley : function(smiley){
		ETConversation.insertText($("#"+this.activeTextAreaId+" textarea"), " "+smiley+" ");
		
	},
	insertHammer : function(){
		ETConversation.insertText($("#"+this.activeTextAreaId+" textarea"), ":hammer:");
	},
	insertGlueck : function(){
		ETConversation.insertText($("#"+this.activeTextAreaId+" textarea"), ":glueck:");
	}
};

//bind mouse out event to automatically close the menu
$(function(){
	$("#emoticonDropDown").bind("mouseleave",function(){
		EmoticonAdv.hideDropDown();
	});
		
	$(".postBody").click(function(event){
		console.log("lel");
		EmoticonAdv.hideDropDown();
	});
	
	$("#emoticonDropDown *").click(function(event){
		event.stopPropagation(); //this is needed to prevent the reply area to collapse on click outside it
	});
});
