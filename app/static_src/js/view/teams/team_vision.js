$(document).ready(function () {
    $('[rel="tooltip"]').tooltip();

    $('#AddTeamVisionForm').bootstrapValidator({
        live: 'enabled',
        fields: {
            "data[TeamVision][photo]": {
                validators: {
                    file: {
                        extension: 'jpeg,jpg,png,gif',
                        type: 'image/jpeg,image/png,image/gif',
                        maxSize: 10485760,   // 10mb
                        message: '<?=__("10MB or less, and Please select one of the formats of JPG or PNG and GIF.")?>'
                    }
                }
            }
        }
    });
});