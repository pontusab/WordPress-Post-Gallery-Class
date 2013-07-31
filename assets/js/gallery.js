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
				$('#gallery div[data-panel]').removeClass('active');
				$('#gallery .media-router a').removeClass('active');
				$('#gallery a[data-click="1"]').addClass('active');
				$('#gallery div[data-panel="1"]').addClass('active');
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
				$('#gallery div[data-panel]').removeClass('active');
				$('#gallery .media-router a').removeClass('active');
				$('#gallery a[data-click="2"]').addClass('active');
				$('#gallery div[data-panel="2"]').addClass('active');
			}
		});

		up.refresh();
		up.start();
	});

	uploader.bind('UploadProgress', function(up, file) 
	{
		//$('#' + file.id + " b").html(file.percent + "%");
	});



	// a file was uploaded
	uploader.bind('FileUploaded', function(up, file, response) 
	{
	    src = response['response'];	   
	    $('#gallery .scrollable ul').prepend('<li><img src="'+ src +'"/></li>');
	});



	$('#gallery .media-router a').click( function(e) 
	{
		e.preventDefault();

		var clicked = $(this).data('click');

		$('#gallery div[data-panel]').removeClass('active');
		$('#gallery .media-router a').removeClass('active');
		
		$(this).addClass('active');
		$('#gallery div[data-panel='+ clicked +']').addClass('active');

	});
});  