/*-----------------------------------------------------------------------------------*/
/*	Functions
/*-----------------------------------------------------------------------------------*/
jQuery(document).ready(function(){
    oc_filter_toggle();
});



// =============================================
// FILTER TOGGLE
// =============================================
function oc_filter_toggle(){
    $(document).on("click", ".toggle-filter", function(){
        $('body').removeClass('offcanvas-menu-left');
        if ($('body').hasClass('show-nav')) {
            $('body').removeClass('show-nav');
            setTimeout(function () {
                $('body').removeClass('before-show-nav');
            }, 300);
        } else {
            $('body').addClass('before-show-nav');
            setTimeout(function () {
                $('body').addClass('show-nav');
            }, 42);
        }
        return false;
    });
}