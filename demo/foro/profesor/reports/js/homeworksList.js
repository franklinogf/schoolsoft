$(document).ready(function () {  
  $('.classesTable tbody').on('click', 'tr', function () {
    const row = classesTable.row(this)
    if (row.index() !== undefined) {
      const data = row.data();      
      window.open(getBaseUrl('pdf/pdfHomeworks.php?class='+data[0]),'homework')
    }
  });
});