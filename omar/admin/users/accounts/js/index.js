$(document).ready(function () {
    let searched = false;
    $("#personToPay").change(function () {
        const personToPay = $(this).val();
        if (personToPay === 'P') {
            $("#inChargeRelationship").val("Padre").prop('readonly', true);
            $("#inChargeName").val($("#nameP").val());
            $("#inChargeEmail").val($("#emailP").val());
            $("#inChargePhone").val($("#residentialPhoneP").val());
            $("#inChargeWorkPhone").val($("#jobPhoneP").val());
            $("#inChargeCellPhone").val($("#cellPhoneP").val());
            $("#inChargeCellCompany").val($("#cellCompanyP").val());
            $("#inChargeDir1").val($("#dir2").val());
            $("#inChargeDir2").val($("#dir4").val());
            $("#inChageCity").val($("#city2").val());
            $("#inChageState").val($("#state2").val());
            $("#inChageZip").val($("#zip2").val());

        } else if (personToPay === 'M') {
            $("#inChargeRelationship").val("Madre").prop('readonly', true);
            $("#inChargeName").val($("#nameM").val());
            $("#inChargeEmail").val($("#emailM").val());
            $("#inChargePhone").val($("#residentialPhoneM").val());
            $("#inChargeWorkPhone").val($("#jobPhoneM").val());
            $("#inChargeCellPhone").val($("#cellPhoneM").val());
            $("#inChargeCellCompany").val($("#cellCompanyM").val());
            $("#inChargeDir1").val($("#dir1").val());
            $("#inChargeDir2").val($("#dir3").val());
            $("#inChageCity").val($("#city1").val());
            $("#inChageState").val($("#state1").val());
            $("#inChageZip").val($("#zip1").val());

        } else if (personToPay === 'E') {
            $("#inChargeRelationship").val("").prop('readonly', false);
            $("#inChargeName").val("");
            $("#inChargeEmail").val("");
            $("#inChargePhone").val("");
            $("#inChargeWorkPhone").val("");
            $("#inChargeCellPhone").val("");
            $("#inChargeCellCompany").val("");
            $("#inChargeDir1").val("");
            $("#inChargeDir2").val("");
            $("#inChageCity").val("");
            $("#inChageState").val("");
            $("#inChageZip").val("");
        }
    });


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
});