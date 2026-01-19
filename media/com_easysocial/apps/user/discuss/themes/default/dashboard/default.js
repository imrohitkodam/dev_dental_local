
EasySocial
.require()
.library( 'dialog' )
.done(function($){

    $('[data-es-discuss-unsubscribe]').on('click', function(event)
    {
        // Supply all the necessary info to the caller
        var id = $(this).data('id'),
            cid = $(this).data('cid'),
            type = $(this).data('type'),
            parent = $(this).parents('[data-subscription-item]'),
            message = parent.siblings("[data-unsubscribe-success]");

          EasySocial.dialog(
                {
                    content : EasySocial.ajax('apps/user/discuss/controllers/discuss/confirmUnsubscribe'),
                    bindings :
                    {
                        "{unsubscribeButton} click" : function()
                        {
                            EasySocial.ajax('apps/user/discuss/controllers/discuss/unsubscribe' ,
                            {
                                "id" : id,
                                "cid" : cid,
                                "type" : type
                            })
                            .done(function()
                            {
                                console.log(parent);
                                parent.addClass('hide');
                                message.removeClass('hide');

                                // Close the dialog
                                EasySocial.dialog().close();
                            });
                        }
                    }
                });
    });
        
});
