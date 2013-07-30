jQuery(document).ready(function( $ )
{
	// create the uploader and pass the config from above
	var uploader = new plupload.Uploader( gallery_settings );

	// checks if browser supports drag and drop upload, makes some css adjustments if necessary
	uploader.bind('Init', function(up) 
	{
		var uploaddiv = $('#plupload-upload-ui');

		if( up.features.dragdrop )
		{
			uploaddiv.addClass('drag-drop');
			$('#drag-drop-area')
			.bind('dragover.wp-uploader', function()
			{ 
				uploaddiv.addClass('drag-over'); 
			})
			.bind('dragleave.wp-uploader, drop.wp-uploader', function()
			{ 
				uploaddiv.removeClass('drag-over'); 
			});

			}
			else
			{
				uploaddiv.removeClass('drag-drop');
				$('#drag-drop-area').unbind('.wp-uploader');
			}
	});

	uploader.init();

	// a file was added in the queue
	uploader.bind('FilesAdded', function( up, files )
	{
		var hundredmb = 100 * 1024 * 1024, max = parseInt( up.settings.max_file_size, 10 );

		plupload.each( files, function( file )
		{
			if( max > hundredmb && file.size > hundredmb && up.runtime != 'html5' )
			{
				// file size error?
			}
			else
			{
				// a file was added, you may want to update your DOM here...
				console.log(file);
			}
		});

		up.refresh();
		up.start();
	});

	// a file was uploaded
	uploader.bind('FileUploaded', function( up, file, response ) 
	{
    	// this is your ajax response, update the DOM with it or something...
    	console.log(response);
	});



	$('#gallery .media-router a').click( function(e) 
	{
		e.preventDefault();

		// var selected;
		// var clicked  = $(this).data('click');
		
		// $('#gallery .media-router a').each(function() 
		// {
		// 	if( $(this).hasClass('active') )
		// 	{
		// 		var selected = $(this).data('click');
		// 	}
		// });

		// console.log(selected);
		// $('#gallery div[data-panel='+ clicked +']').addClass('active');

	});
});  