$(document).ready(function () {
    $("form").submit(function (e) {
        if ($('.page:checked').length === 0) {
            alert(__LANG === 'es' ? 'Debe seleccionar al menos una página' : 'You must select at least one page');
            e.preventDefault();
        } else if ($('.trimester:checked').length === 0) {
            alert(__LANG === 'es' ? 'Debe seleccionar al menos un trimestre' : 'You must select at least one trimester');
            e.preventDefault();
        }
    });
});