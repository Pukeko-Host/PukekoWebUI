$(document).ready(function(){
    $('.serverping').each(function(){
        var e = $(this);
        console.log("pinging "+e.data('server')+"...");
        $.ajax({
            url: 'https://ping.yiays.workers.dev/?ping='+encodeURIComponent('http://'+e.data('server')),
            success: function(data){
                console.log(data);
                e.text(data);
            },
            error: function(){
                e.html("<span class=\"red\">Offline</span>");
            }
        });
        e.text("Pinging...");
    });
});