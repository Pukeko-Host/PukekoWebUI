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
            $('.gameserver.active').removeClass('active');
            $('.gameserver[data-id='+gsms+']').addClass('active');
            $('.status:not(.hidden)').addClass('hidden');
            if($('.status[data-id='+gsms+']').removeClass('hidden').length == 0)
                $('.status.default').removeClass('hidden');
            document.title = $('.gameserver[data-id='+gsms+']').get(0).childNodes[3].nodeValue.substr(2) + " Dashboard - Pukeko Host";
            // This is a very hacky way of getting the name of the gameserver, TODO: make something more sturdy.
        }else{
            $('.gameserver.active').removeClass('active');
            $('.status:not(.hidden)').addClass('hidden');
            $('.status.default').removeClass('hidden');
            document.title = $('.guild[data-id='+guild+']').find('.tooltip').text() + " Dashboard - Pukeko Host";
        }
        $('.guild.active').removeClass('active');
        $('.guild[data-id='+guild+']').addClass('active');
        $('.gameservers').addClass('hidden');
        if($('.gameservers[data-guild='+guild+']').removeClass('hidden').length == 0)
            $('.gameservers.default').removeClass('hidden');
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
    $('.guild:not(.hamburger)').on('click',function(e){
        e.preventDefault();
        if($(this).data('id')){
            history.pushState($(this).data('id'), $(this).find('.tooltip').text() + " Dashboard - Pukeko Host", "/dashboard/" + $(this).data('id') + "/");
            document.title = $(this).find('.tooltip').text() + " Dashboard - Pukeko Host";
        }else if(window.location.pathname != '/dashboard/'){
            history.pushState(0, "Dashboard - Pukeko Host", "/dashboard/");
            document.title = "Dashboard - Pukeko Host";
        }
        urlstate();
        return false;
    });
    $('.gameserver').on('click',function(e){
        e.preventDefault();
        if(guild){
            if($(this).data('id')){
                history.pushState($(this).data('id'), $('.guild[data-id='+guild+']').find('.tooltip').text() + "Dashboard - Pukeko Host", "/dashboard/"+guild+"/"+$(this).data('id')+"/");
            }else{
                history.pushState(guild, $('.guild[data-id='+guild+']').find('.tooltip').text() + "Dashboard - Pukeko Host", "/dashboard/"+guild+"/");
            }
        }
        urlstate();
        return false;
    });
    urlstate();
    
    // Guild drag and drop
    let draggedguild = null;
    $('.guild').on('dragstart', function(e){
        draggedguild = e.target;
    });
    function getGuildPos(my){
        var max = 1;
        var closest = 1000000;
        $('.guild').each(function(i) {
            var pos = my - $(this).position().top;
            if(pos > 0 && pos < closest && $(this).css('order') > max){
                max = $(this).css('order');
            }
        });
        return max-1;
    }
    $('.guilds').on('dragover', function(e){
        e.preventDefault();
        var topPos = getGuildPos(e.pageY);
        $('.droppreview').css({'order': topPos}).removeClass('hidden');
    });
    $('.guilds').on('mouseleave', function(e){
        $('.droppreview').addClass('hidden');
    });
    $('.guild').on('drop', function(e){
        e.preventDefault();
        var topPos = getGuildPos(e.pageY);
        $(draggedguild).css({'order': topPos});
        $('.droppreview').addClass('hidden');
    });
});