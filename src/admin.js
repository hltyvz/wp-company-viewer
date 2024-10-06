jQuery(document).ready(function($) {
    var file_frame;

    $(document).on('click', '.upload_logo_button', function(e) {
        e.preventDefault();

        if (file_frame) {
            file_frame.open();
            return;
        }

        file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Select or Upload Logo',
            button: {
                text: 'Use this logo',
            },
            multiple: false
        });

        file_frame.on('select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON();
            $('#company_logo').val(attachment.url);
        });

        file_frame.open();
    });
});
