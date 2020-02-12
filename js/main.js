$(document).ready(function(){
    $("a[href='#']").on('click',function(e){
        e.preventDefault();
        return false;
    });
    $('.overlay, .overlay .close').on('click',function(e){
        e.preventDefault();
        $('.overlay').addClass('hidden');
        return false;
    });
});