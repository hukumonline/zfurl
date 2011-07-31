function zfurlinit(){ 
	$("#shorten").click(function() {
		var content = '';

		//define ajax config object
		var input = encodeURI($("#shorty").find("textarea").val());
		
		
		
		$.post('url/shorten/', {url: input},
		
			function(data){
				content += 'Your new url: <input type="text" style="width: 204px;" value="' + data['shorturl'] + '"/>';
				$("div#shortened").html(content);
			}, "json");
	});		
}
