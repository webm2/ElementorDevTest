jQuery(document).ready(function($) {
    // Instantiate the media uploader
    var mediaUploader;

    // Set the IDs of the relevant elements
    var imageGalleryField = '#product-image-gallery';
    var imageGalleryPreview = '#product-image-gallery-preview';
    var imageGalleryUploadButton = '#product-image-gallery-upload-button';
    var imageGalleryClearButton = '#product-image-gallery-clear-button';

    // Run when the upload button is clicked
    $(imageGalleryUploadButton).on('click', function(e) {
        e.preventDefault();

        // If the media uploader already exists, open it
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        // Instantiate the media uploader
        mediaUploader = wp.media({
            title: 'Select Images',
            library: { type: 'image' },
            multiple: true,
        });

        // Run when images are selected
        mediaUploader.on('select', function() {
            var attachmentIds = [];

            // Get the IDs of the selected images
            mediaUploader.state().get('selection').forEach(function(attachment) {
                attachmentIds.push(attachment.attributes.id);
            });

            // Update the image gallery field with the selected IDs
            $(imageGalleryField).val(attachmentIds.join(','));

            // Update the image gallery preview
            updateImageGalleryPreview(attachmentIds);
        });

        // Open the media uploader
        mediaUploader.open();
    });

    // Run when the clear button is clicked
    $(imageGalleryClearButton).on('click', function(e) {
        e.preventDefault();

        // Clear the image gallery field and preview
        $(imageGalleryField).val('');
        $(imageGalleryPreview).empty();
    });

    // Helper function to update the image gallery preview
    function updateImageGalleryPreview(attachmentIds) {
        if (attachmentIds.length > 0) {
            var data = {
                action: 'product_image_gallery_preview',
                image_ids: attachmentIds
            };

            // Make an AJAX request to get the image URLs and update the preview
            $.post(ajaxurl, data, function(response) {
                $(imageGalleryPreview).html(response);
            });
        } else {
            $(imageGalleryPreview).empty();
        }
    }

    // Update the image gallery preview on page load
    updateImageGalleryPreview($(imageGalleryField).val().split(','));
});