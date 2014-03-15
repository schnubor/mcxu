var SC = {
	 resolveLink : function(url, e){
		console.log(e);
		$.get(
		  'http://api.soundcloud.com/resolve.json?url=' + url + '&client_id=12c138ffb184282ce1729781bfb13fb0', 
		  function (result) {
		    console.log(result.id);
		    $(e).attr('src','https://w.soundcloud.com/player/?url=https://api.soundcloud.com/tracks/'+result.id+'&amp;color=ff6600&amp;auto_play=false&amp;show_artwork=true');
		    $(e).removeAttr('onload');
		  }
		);
	}
}