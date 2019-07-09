
jQuery(document).ready(function(){
    
    wpsp_close_support_widget();
    
    wpspjq('#wpsp_helpdesk_agent').click(function(){
        wpsp_open_support_widget();
    });
    
    wpspjq('#wpsp_helpdesk_widget_minimize').click(function(){
        wpsp_close_support_widget();
    });
    
});

function wpsp_open_support_widget(){
    wpspjq('#wpsp_helpdesk_agent').animate({
        right : -90
    },
    {
        complete : function(){
            wpspjq('#wpsp_helpdesk_widget').slideDown();
        }
    });
}

function wpsp_close_support_widget(){
    wpspjq('#wpsp_helpdesk_widget').slideUp({
        complete : function(){
            wpspjq('#wpsp_helpdesk_agent').animate({
                right : 25
            });
        }
    });
}