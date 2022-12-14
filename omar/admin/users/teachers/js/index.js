$(document).ready(function () {
    $("#type").change(function () {
        console.log("Type Changed")
        if ($("#type").val() === 'Docente') {

            $("#level,#home,#licences input,#club input").prop('disabled', false)
        } else {
            $("#level,#home,#licences input,#club input").prop('disabled', true)
        }
    })
    $("#type").change()

    /* ------------------------------ pass address ------------------------------ */
    $("#passAddress").click(function (e) {
        e.preventDefault();
        $("#dir3").val($("#dir1").val())
        $("#dir4").val($("#dir2").val())
        $("#city2").val($("#city1").val())
        $("#state2").val($("#state1").val())
        $("#zip2").val($("#zip1").val())
    })

    /* --------- Look for the username to know if it does already exist --------- */
    let searched = false;
    $("#username").keyup(function (e) {
        if ($("#username").val().length > 0) {
            if ($("#username").data('lastusername').toString() !== $("#username").val()) {
                if (!searched) {
                    $.post(includeThisFile(), { searchUsername: $("#username").val() },
                        function (data, textStatus, jqXHR) {
                            searched = false;
                            if (data.exist) {
                                $("#username").removeClass('is-valid').addClass('is-invalid')
                                $("#submit").prop('disabled', true)
                            } else {
                                $("#username").removeClass('is-invalid').addClass('is-valid')
                                $("#submit").prop('disabled', false)

                            }
                        },
                        "json"
                    );
                }
            } else {
                $("#username").removeClass('is-invalid').removeClass('is-valid')
                $("#submit").prop('disabled', false)
            }
        } else {
            searched = true
        }

    })

    // Change profile picture
    let prevPicture = $('.profile-picture').prop('src');
    $("#pictureBtn").click(() => {
        $("#picture").click();
    });


    $('#picture').change(e => {
        if ($('#picture').val() !== '') {
            const valid = previewPicture(e.target, '.profile-picture');
            if (valid) {
                $('#pictureCancel').removeAttr('hidden');
            } else if (valid === false) {
                alert(__LANG === 'es' ? 'El archivo seleccionado no es una imagen' : 'The selected file is not an image');
            }
        } else {
            $('.profile-picture').prop('src', prevPicture);
        }

    })



    $('#pictureCancel').click(e => {
        $('.profile-picture').prop('src', prevPicture);
        $('#picture').val('');
        if (e.target.tagName === 'I') {
            $(e.target).parent().attr('hidden', true);
        } else {
            $(e.target).attr('hidden', true);
        }
    })

    function previewPicture(input, where) {
        if (input.files && input.files[0]) {
            const fileType = input.files[0]["type"];
            const validImageTypes = ["image/gif", "image/jpeg", "image/png", "image/jpg"];
            if (validImageTypes.includes(fileType)) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $(where).prop('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
                return true;
            }
            return false;
        }


        return undefined;

    }

});