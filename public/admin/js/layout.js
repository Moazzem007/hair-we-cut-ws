$(document).ready(function() {

    $('.footable').footable();
    $('.footable2').footable();

});
$(document).ready(function(){
    $('.dataTables-example').DataTable({
        pageLength: 25,
        responsive: true,
        dom: '<"html5buttons">lTfgitp',

    });

    $('.dataTables-example1').DataTable();

});

// For SELECT 2
$(document).ready(function(){
    $('.chosen-select').chosen({width: "100%"});

});

// FOR validation
 $(document).ready(function(){

     $("#form").validate({
         rules: {
             password: {
                 required: true,
                 minlength: 3
             },
             url: {
                 required: true,
                 url: true
             },
             number: {
                 required: true,
                 number: true
             },
             min: {
                 required: true,
                 minlength: 6
             },
             max: {
                 required: true,
                 maxlength: 4
             }
         }
     });
});





