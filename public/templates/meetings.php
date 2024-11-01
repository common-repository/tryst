<div class="container my-4">
    <div class="row">
        <div class="col-md-12">
            <h1><?php _e('Meetings', 'tryst'); ?></h1>            
            <ul class="list-group">
                <?php foreach($meetings as $x => $day): ?>
                <?php foreach($date as $k => $meeting): ?>
                    <li class="list-group-item"><a href="<?php echo get_option('tryst_option')['tryst_meeting_request'].'?tryst_meeting_hash='.$meeting->getFormKey(); ?>"><?php echo $meeting->getPost()->post_title; ?></a></li>
                <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
</div>