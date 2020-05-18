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
    $('.overlay>*').on('click',function(){
        return false;
    });
    $('.copy').on('click',function(){
        var $temp = $("<input>");
        $('body').append($temp);
        $temp.val($('#'+$(this).data('for')).text()).select();
        document.execCommand('copy');
        $temp.remove();
        $(this).text("✓");
    });
});