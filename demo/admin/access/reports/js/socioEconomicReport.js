$(document).ready(function () {    
    const action = $('form').prop('action');
    $("form").submit(function () {       
        $(this).prop('action', action + 'socioEconomicReport' + $("#option").val() + '.php')
    })
});