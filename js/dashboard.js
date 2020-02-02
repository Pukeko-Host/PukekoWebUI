$(document).ready(function(){
    $('.guild:not(.hamburger)').on('click',function(){
        $('.guild.active').removeClass('active');
        $(this).addClass('active');
        $('.gameservers').addClass('hidden');
        if($('.gameservers[data-guild='+$(this).data('id')+']').removeClass('hidden').length == 0){
            $('.gameservers.default').removeClass('hidden');
        }
    });
});