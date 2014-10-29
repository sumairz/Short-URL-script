$(document).ready(function() {

		// This function will ajax post to generate short url
		$("#submit_url").click(function() {
			
			  var url = jQuery.trim($("#url").val());
			  
			  var stringData = "url="+url;
			  
			  $.ajax({
				  type: 'POST',
				  url: "generateShortUrl.php",
				  data: stringData,
				  success: function(data) 
					{	 
						 $('#ShortUrlShow').html(jQuery.trim(data));
					}		  
				});
		});		
});