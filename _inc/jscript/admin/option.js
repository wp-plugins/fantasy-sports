jQuery.option =
{
    addArray: function(obj)
    {
        var item = jQuery(obj).prev().clone();
        item.find('input').val('');
        jQuery(obj).before(item);
        this.initArray();
        return false;
    },
    
    removeArray: function(obj)
    {
        if(confirm(wpfs['a_sure']))
        {
            jQuery(obj).parent('div').remove();
        }
        this.initArray();
        return false;
    },
    
    initArray: function()
    {
        jQuery('.array-holder').each(function(){
            var item = jQuery(this).find('.array-item');
            if(item.length < 2)
            {
                item.children('a').hide();
            }
            else
            {
                item.children('a').show();
            }
        })
    }
}

jQuery(window).load(function(){
    jQuery.option.initArray();
})
