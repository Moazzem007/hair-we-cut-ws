$(document).ready(function(){

    $('#type').change(function(){

        var type = $('#type').val();
        if(type==4){
            $('.hidediv').show(500);
        }else if(type==6){
            $('.brand').show(500);
            $('.hidediv').hide(500);

        }
        else{
            $('.hidediv').hide();
            $('.brand').hide();
        }
    });

    $('.dprice').keyup(function(){
        percent();
    });
    $('saleprice').keyup(function(){
        reverse();
    });

    $('.percent').keyup(function(){
        reverse();
    });

    function percent(){
        var price =  $('#saleprice').val();
        var dprice =  $('.dprice').val();
        var diff = price - dprice;
        var dis = diff/dprice *100;
        $('.percent').val(dis.toFixed(0));
    }


    function reverse(){
        var percent = $('.percent').val();
        var price =  $('#saleprice').val();

        var dper = percent/100 * price;

        var tper = price - dper;

        $('.dprice').val(tper);
    }

});