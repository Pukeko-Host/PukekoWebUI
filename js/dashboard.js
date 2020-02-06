let guild = null;
let gsms = null;


function urlstate(){
    var params = window.location.pathname.split("/");
    guild = params[2];
    if(guild == "" || typeof guild == "undefined") guild = null;
    gsms = params[3];
    if(gsms == "" || typeof gsms == "undefined") gsms = null;

    if(guild){
        if(gsms){
            // show panel for this gameserver, failing that, the default.
            if($('.status[data-id='+gsms+']').removeClass('hidden').length == 0)
                $('.status.default').removeClass('hidden');
        }else{
            $('.status.default').removeClass('hidden');
        }
        $('.guild.active').removeClass('active');
        $('.guild[data-id='+guild+']').addClass('active');
        $('.gameservers').addClass('hidden');
        if($('.gameservers[data-guild='+guild+']').removeClass('hidden').length == 0)
            $('.gameservers.default').removeClass('hidden');
        document.title = $('.guild[data-id='+guild+']').find('.tooltip').text() + " Dashboard - Pukeko Host";
    }else{
        $('.guild.active').removeClass('active');
        $('.gameservers').addClass('hidden');
        $('.gameservers.default').removeClass('hidden');
        $('.status').addClass('hidden');
        $('.status.default').removeClass('hidden');
        document.title = "Dashboard - Pukeko Host";
    }
}
window.onpopstate = urlstate;

$(document).ready(function(){
    $('.guild:not(.hamburger)').on('click',function(){
        if($(this).data('id')){
            history.pushState($(this).data('id'), $(this).find('.tooltip').text() + " Dashboard - Pukeko Host", "/dashboard/" + $(this).data('id') + "/");
            document.title = $(this).find('.tooltip').text() + " Dashboard - Pukeko Host";
        }else if(window.location.pathname != '/dashboard/'){
            history.pushState(0, "Dashboard - Pukeko Host", "/dashboard/");
            document.title = "Dashboard - Pukeko Host";
        }
        urlstate();
    });
    $('.gameserver').on('click',function(){
        if(guild){
            if($(this).data('id')){
                history.pushState($(this).data('id'), $('.guild[data-id='+guild+']').find('.tooltip').text() + "Dashboard - Pukeko Host", "/dashboard/"+guild+"/"+$(this).data('id')+"/");
            }else{
                history.pushState(guild, $('.guild[data-id='+guild+']').find('.tooltip').text() + "Dashboard - Pukeko Host", "/dashboard/"+guild+"/");
            }
        }
    });
    urlstate();
});