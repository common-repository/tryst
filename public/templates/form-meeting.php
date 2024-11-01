<div class="container">
    <div class="row">
        <div class="col-md-12">
            <form action="<?php echo $agenda->getOptions()['tryst_meeting_request']; ?>" method="POST" name="form-meeting" class="validate <?php if(isset($_GET['tryst_meeting_hash'])): ?> readonly <?php endif; ?>">
                <?php if(isset($meeting) && empty($meeting->getPost())): ?>
                <input type="hidden" name="register" value="1">
                <?php else: ?>
                <input type="hidden" name="meeting_post" value="1">
                <?php endif; ?>
                <input type="hidden" name="agenda_id" value="<?php echo $agenda->getId(); ?>">
                <?php $user = wp_get_current_user(); ?>
                <?php if(!empty($member)): ?>
                <input type="hidden" name="user_id" value="<?php echo $member->getUser()->ID; ?>">
                <?php endif; ?>
                <?php if(isset($meeting)): ?>
                <input type="text" name="meet[id]" value="<?php echo $meeting->getId(); ?>" style="display:none;">
                <?php endif; ?>
                <input type="hidden" name="page_url" value="<?php echo home_url($_SERVER['REQUEST_URI']); ?>">
                <?php if(isset($_GET['tryst_meeting_hash'])): ?>
                <input type="hidden" name="tryst_meeting_hash" value="<?php echo  $_GET['tryst_meeting_hash'];  ?>">
                <?php endif; ?>
                <?php wp_nonce_field( 'meet', 'meeting_fields' ); ?>
                <?php if(isset($meeting) && !empty($meeting->getMember()) && !empty($meeting->getMember()->getUser()->user_login) && !empty($meeting->getPost())): ?>
                <div class="alert  row bg-light">
                    <div class="col-md-4 d-flex">
                        <figure class="d-block m-auto align-self-center">
                            <img src="<?php echo plugin_dir_url( __FILE__ ).'../img/keys-small.png'; ?>" alt="calendar-meeting" >
                        </figure>
                    </div>
                    <div class="col-md-8">
                        <p><?php _e('Please save your access credentials to login the site:', 'tryst'); ?>
                            <br>
                            <strong>Login:</strong> <?php echo $meeting->getMember()->getUser()->user_login; ?>
                            <br>
                            <strong>Pass:</strong> <?php echo $meeting->getMember()->getPasswordDescription(); ?>
                        </p>
                    </div>
                </div>
                <?php endif; ?>
                <?php if(isset($meeting) && !empty($meeting->getPost())): ?>
                <div class="alert row bg-light">
                    <div class="col-md-4 d-flex">
                        <figure class="d-block m-auto align-self-center">
                            <img src="<?php echo plugin_dir_url( __FILE__ ).'../img/calendar-1-small.png'; ?>" alt="calendar-meeting" >
                        </figure>
                    </div>
                    <div class="col-md-8">
                        <p><?php _e('This is your meeting confirmation. An e-mail should already have been sent to the filled e-mail below as well to help you memorize the tryst.', 'tryst'); ?>
                        </p>
                    </div>
                </div>
                <?php endif; ?>
                <h6 class="mt-3 t-section"><?php _e('Meeting data', 'tryst') ?></h6>
                <div class="form-group row">
                    <div class="col-md-6">
                        <label><?php _e('Date', 'tryst') ?></label>
                        <input type="text" class="form-control" name="meet[date]" id="f-date" value="<?php if(isset($meeting)) echo $meeting_date; ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label><?php _e('Hour', 'tryst') ?></label>
                        <input type="time" class="form-control" name="meet[time]" id="f-time" value="<?php if(isset($meeting)) echo $meeting->getTimestamp()->format('H:i') ?>" readonly>
  
                    </div>
                </div>
                <div class="form-group row">
                <!-- DOMAIN FIELDS -->
                <div class="col-md-12">
                <?php global $tryst_plugin; 
                $options = get_option('tryst_option');
        
                if(class_exists("\\Tryst\\Domain\\Main")){
                    $domain_file = realpath(dirname(__FILE__))."/".$options['form_country']."/Domain/fields.php";
                    if(file_exists($domain_file)){
                        include $domain_file;
                    }
                } else {
                    include realpath(dirname(__FILE__))."/".$options['form_country']."/fields.php";
                }         
                ?>
                </div>
            </div>
                <!-- TRYST MEMBER SUPPORT -->
                <div class="row">
                    <?php 
                    if(!empty($tryst_plugin) && $tryst_plugin->isExtensionActive('member')){
                        include $tryst_plugin->getExtensionPath('member').'/public/templates/'.$options['form_country'].'/fields/base-fields.php';
                        $file_domain_fields = $tryst_plugin->getExtensionPath('member')."/public/templates/".$options['form_country']."/Domain/fields/base-fields.php";
                        if(class_exists("\\Tryst\\Domain\\Main") && file_exists($file_domain_fields)){
                            include $file_domain_fields;        
                        }     
                    }
                    ?>
                    <!-- TRYST MEMBER SUPPORT -->
                </div>
                <?php if(empty($meeting)): ?>
                <div class="text-center mt-4">
                    <label for="f-security-code"><?php _e('Security code', 'tryst') ?></label>
                </div>
                <div class="row">
                    <div class="col-4 offset-md-1 col-md-4 text-right">
                        <span class="badge badge-info t-code"><?php echo substr(strtotime('now'), -3); ?></span>
                    </div>
                    <div class="col-8 col-md-4">
                        <input type="hidden" name="security_code" id="f-security-code" value="<?php echo substr(strtotime('now'), -3); ?>">
                        <input required type="text" class="form-control , validate[required, equals[f-security-code]]" name="security_code_repeat" id="f-security-code-repeat" placeholder="<?php echo __('Type the number', 'tryst') .' '.substr(strtotime('now'), -3); ?>"> 
                        <small class="form-text text-muted"><?php _e('Simple captcha to help us avoid robot spam', 'tryst')?></small>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <input type="submit" value="<?php _e('Send', 'tryst'); ?>" class="pure-button pure-button-primary btn-primary btn float-right"> 
                    </div>
                </div>
                <?php elseif(!empty($meeting->getMember())): ?>
                <div class="row mt-4">
                    <div class="col-12">
                        <button type="button" class="btn btn-secondary mx-auto my-4 d-block" data-toggle="modal" data-target="#email_repeat"><?php printf(__('Send remind e-mail to %s', 'tryst'), $meeting->getMember()->getEmail()); ?></button>
                    </div>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>
<?php if(!empty($meeting)): ?>
<!-- mail send modal -->
<!-- The Modal -->
<div class="modal" id="email_repeat">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">E-mail</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?php echo get_page_link(); ?>" method="POST" class="validate">
                <!-- Modal body -->
                <div class="modal-body">
                    <input type="hidden" value="<?php echo $meeting->getFormKey(); ?>" name="tryst_meeting_hash">
                    <input type="hidden" value="true" name="meeting_mail_repeat">
                    <div class="text-center mt-4">
                        <label for="f-security-code"><?php _e('Security code', 'tryst') ?></label>
                    </div>
                    <div class="row">
                        <div class="col-4 text-right">
                            <span class="badge badge-info t-code"><?php echo substr(strtotime('now'), -3); ?></span>
                        </div>
                        <div class="col-8">
                            <input type="hidden" name="security_code" value="<?php echo substr(strtotime('now'), -3); ?>">
                            <input required type="text" class="form-control" name="security_code_repeat" id="f-security-code" placeholder="<?php echo __('Type number', 'tryst').' '.substr(strtotime('now'), -3); ?>"> 
                            <small class="form-text text-muted"><?php _e('Simple captcha to help us avoid robot spam', 'tryst')?></small>
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success float-right">OK</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">X</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>