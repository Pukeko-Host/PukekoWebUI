$(document).ready(function(){
    $('.serverping').each(function(){
        console.log("pinging "+$(this).data('server')+"...");
        this.pingStart = new Date();
        $.ajax({
            url: 'https://'+$(this).data('server'),
            success: function(){
                $(this).text(new Date(this.pingStart - new Date()).getMilliseconds()/2+'ms');
            },
            error: function(){
                $(this).html("<span class=\"red\">down</span>");
            }
        });
        $(this).text("Pinging...");
    });
});