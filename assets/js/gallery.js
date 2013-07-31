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

	$('#gallery .delete-wrap ul li').click( function(e) 
	{
		e.preventDefault();

		$('#gallery .delete').removeClass('active');
		$('#gallery .delete').addClass('active');
		
		$(this).toggleClass('active');

		var ids = [];

		$('#gallery .delete-wrap ul li.active').each( function(i) 
		{
			var id = $(this).data('id');
			ids[i] = id;
		});

		$('#gallery .delete a').attr('data-ids', ids);
	});


	$('#gallery .delete a').click( function(e) 
	{
		$('#gallery .delete').removeClass('active');
		
		$('#gallery .delete-wrap ul li.active').each( function()
		{
			$(this).removeClass('active');
			$('#gallery .add-wrap ul').prepend(this);
		});

	
		var ids = $(this).data('ids');
		$.ajax(
		{
		    url: ajaxurl,
		    type: 'POST',
		    async: true,
		    cache: false,
		    dataType: 'json',
		    data: {
		        action: 'remove_attachment',
		        ids: ids,
		        post_id: $('#post_ID').val()
		    }
		});
	});




	// Add

	$('#gallery .add-wrap ul li').click( function(e) 
	{
		e.preventDefault();

		$('#gallery .add').removeClass('active');
		$('#gallery .add').addClass('active');
		
		$(this).toggleClass('active');

		var ids = [];

		$('#gallery .add-wrap ul li.active').each( function(i) 
		{
			var id = $(this).data('id');
			ids[i] = id;
		});

		$('#gallery .add a').attr('data-ids', ids);
	});

	$('#gallery .add a').click( function(e) 
	{
		$('#gallery .add').removeClass('active');
		
		$('#gallery .add-wrap ul li.active').each( function()
		{
			$(this).removeClass('active');
			$('#gallery .delete-wrap ul').prepend(this);
		});

		var ids = $(this).data('ids');

		$.ajax(
		{
		    url: ajaxurl,
		    type: 'POST',
		    async: true,
		    cache: false,
		    dataType: 'json',
		    data: {
		        action: 'add_attachment',
		        ids: ids,
		        post_id: $('#post_ID').val()
		    }
		});
	});
});  