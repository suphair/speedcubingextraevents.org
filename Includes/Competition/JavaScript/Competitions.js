    var ce_select='competitions_events_select';
    var ce_panel='competitions_events_panel';
    $('.' + ce_panel+ ' i').on("click",function(){
        if($(this).hasClass(ce_select)){
            $(this).removeClass(ce_select);
        }else{
            $(this).addClass(ce_select);
        }
        reload_competitions();
    });
    
    $('.' + ce_panel+ '_none').on("click",function(){
        $('.' + ce_panel+ ' i').removeClass(ce_select);
        reload_competitions();
    });
    
    function reload_competitions(){
        var events = [];
        $('.' + ce_panel+ ' i.' + ce_select).each(function(){
            $(this).attr('class').split(' ').forEach(
                (element) => {
                    var tmp=element.replace('ee-','');
                    if(tmp!==element){
                        events.push( tmp);
                    }
                }
            );
        });
        
        if(events.length>0){
            $('.competition').hide();    
            var i=1;
            $('.competition').each(function() {
                var show=false;
                events.forEach(
                    (element) => {
                    if($(this).hasClass(element)){
                        show=true;
                    }
                });
                if(show){
                    $(this).show();
                    if(i%2!==0){
                        $(this).addClass('odd');
                        $(this).removeClass('even');
                    }else{
                        $(this).addClass('even');
                        $(this).removeClass('odd');
                    }
                    i=i+1;  
                }
            });
            if(i===1){
                $('#competitionsNotFound').show();
            }else{
                $('#competitionsNotFound').hide();
            }
        }else{
            $('.competition').show();    
            $('.competition').removeClass('odd');
            $('.competition').removeClass('even');
        }
    }
    
    if($('.competition').length>0){
        $('#competitionsNotFound').hide();
    }

    var filter=$('#filter').data('filter');
    $('#filter option[value="' + filter + '"]').prop('selected', true);

    $('#filter').on("change",function(){
        document.location=$('#pageIndex').data('location') + 'Competitions/' + $(this).val();
    });
    
    
    $('.Competition').filter(function() {
        return $(this).data('icon') !== undefined;
    }).each(function(){
        alert($(this).data('icon'));
    });