$(function() {
    $("input[name='search[fromdate]']").datepicker({
        dateFormat: 'yy-mm-dd',
        onSelect: function(selected) {
          $("input[name='search[todate]']").datepicker("option","minDate", selected)
        }
    });
    $("input[name='search[todate]']").datepicker({ 
        dateFormat: 'yy-mm-dd',
        onSelect: function(selected) {
           $("input[name='search[fromdate]']").datepicker("option","maxDate", selected)
        }
    });  
});