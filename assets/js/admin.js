jQuery(document).ready(function($) {
    
    // Background image upload functionality
    var mediaUploader;
    
    $('#upload_background_image').on('click', function(e) {
        e.preventDefault();
        
        // If the uploader object has already been created, reopen the dialog
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        
        // Create the media uploader
        mediaUploader = wp.media({
            title: 'Select Background Image',
            button: {
                text: 'Use this image'
            },
            multiple: false,
            library: {
                type: 'image'
            }
        });
        
        // When an image is selected, run a callback
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            
            // Update the hidden input
            $('#background_image_id').val(attachment.id);
            
            // Update the preview
            $('#background_image_preview').html('<img src="' + attachment.url + '" style="max-width: 100%; height: auto; border-radius: 4px;" />');
            
            // Update the button text
            $('#upload_background_image').text('Change Background Image');
            
            // Show the remove button
            if ($('#remove_background_image').length === 0) {
                $('#upload_background_image').after('<button type="button" id="remove_background_image" class="button" style="margin-top: 5px; color: #dc3232;">Remove Background Image</button>');
            }
        });
        
        // Open the uploader dialog
        mediaUploader.open();
    });
    
    // Remove background image
    $(document).on('click', '#remove_background_image', function(e) {
        e.preventDefault();
        
        // Clear the hidden input
        $('#background_image_id').val('');
        
        // Clear the preview
        $('#background_image_preview').empty();
        
        // Update the button text
        $('#upload_background_image').text('Set Background Image');
        
        // Remove the remove button
        $(this).remove();
    });
    
}); 