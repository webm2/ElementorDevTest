(function($) {
  $(document).ready(function() {

    // Set variables
    var imageGalleryFrame;
    var imageGalleryIdsField = $('#product-image-gallery');
    var imageGalleryList = $('ul.product-image-gallery');

    // Handle image gallery upload button
    $('#product-image-gallery-upload-button').click(function(e) {
      e.preventDefault();
      
      // If the media frame already exists, reopen it.
      if ( imageGalleryFrame ) {
        imageGalleryFrame.open();
        return;
      }

      // Create a new media frame
      imageGalleryFrame = wp.media.frames.imageGallery = wp.media({
        title: 'Select Images for Gallery',
        multiple: true,
        library: { type: 'image' },
        button: { text: 'Insert Images' }
      });

      // When images are selected, add them to the image gallery
      imageGalleryFrame.on('select', function() {
        var attachmentIds = [];
        var attachmentUrls = [];
        var attachments = imageGalleryFrame.state().get('selection');
        attachments.map(function(attachment) {
          attachmentIds.push(attachment.id);
          attachmentUrls.push(attachment.attributes.sizes.thumbnail.url);
        });

        // Update the hidden input field with the comma-separated list of attachment IDs
        imageGalleryIdsField.val(attachmentIds.join(','));

        // Clear the current list of images and display the new list
        imageGalleryList.empty();
        $.each(attachmentUrls, function(i, url) {
          var listItem = $('<li></li>').html('<img src="' + url + '" width="150" height="150" />');
          imageGalleryList.append(listItem);
        });
      });

      // Open the media frame
      imageGalleryFrame.open();
    });
  });
})(jQuery);