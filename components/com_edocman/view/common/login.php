<?php
defined( '_JEXEC' ) or die ;
EDocmanHelper::loadBootstrapJs();
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass     = $bootstrapHelper->getClassMapping('controls');
?>
<div class="modal fade" id="login-form" style="width:340px;max-width:90%;margin-left:0px;">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">x</a>
        <h3><?php echo JText::_('EDOCMAN_LOGIN_FORM'); ?></h3>
    </div>
    <div class="alert alert-success nodisplay">
        <p id="login-meesage"></p>
    </div>
    <div class="modal-body" style="margin: 20px 20px;">
        <div id="register-link">
            <?php echo JText::_('EDOCMAN_DONT_HAVE_ACCOUNT'); ?>
            <a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>"><?php echo JText::_('EDOCMAN_REGISTER'); ?></a>
        </div>
            <div class="<?php echo $controlGroupClass; ?>">
                <label class="<?php echo $controlLabelClass; ?>" for="name">
                    <?php echo  JText::_('EDOCMAN_USERNAME') ?><span class="required">*</span>
                </label>
                <div class="<?php echo $controlsClass; ?>">
                    <input type="text" name="username" id="username" class="input-medium" style="width:150px !important;"/>
                </div>
            </div>

            <div class="login-form-password">
                <label class="label-password" for="name">
                    <?php echo  JText::_('EDOCMAN_PASSWORD') ?><span class="required">*</span>
                </label>
                <div class="<?php echo $controlsClass; ?>">
                    <input id="password" type="password" name="password" value="" class="input-medium" style="width:150px !important;"><br />
                </div>
            </div>
            <div class="clear-fix"></div>
            <input class="btn btn-login btn-primary" type="button" value="<?php echo JText::_('EDOCMAN_LOGIN'); ?>" style="width:150px !important;" />
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function($){
            jQuery('.btn-login').click(function(){
                var check = checkForm();
                if(check)
                {
                    var data={
                        'task'    :'login',
                        'username':jQuery('#username').val(),
                        'password':jQuery('#password').val(),
                        'return'  :jQuery('#return').val()
                    };
                    $.ajax({
                        type:'POST',
                        url :'index.php?option=com_edocman',
                        data:data,
                        success:function(html){
                            if(html)
                            {
                                jQuery('.alert-success').show();
                                jQuery('#login-meesage').html(html);
                            }
                            else
                            {
                                window.location.href='<?php echo JFactory::getUri()->toString(); ?>';
                            }
                        }

                    });
                }

            });
            function checkForm()
            {
                var name = jQuery('#username').val();
                var pass = jQuery('#password').val();
                if(name == "")
                {
                    alert("Please enter your name !");
                    jQuery('#username').focus();
                    return false;

                }
                else if(pass =="")
                {
                    alert("Please enter your password !");
                    jQuery('#password').focus();
                    return false;
                }
                return true;
            }

            var screen_width = jQuery( window ).width();
            var left_value = screen_width/2 - 170;
            jQuery("#login-form").css("left",left_value);
        });
    </script>
</div>