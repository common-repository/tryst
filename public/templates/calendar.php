<div class="container-fluid my-4" id="tryst-calendar">
  <header>
    <!-- PREV -->
    <div class="row d-flex  d-md-none">
      <div class="col-6 text-center">
        <a href="<?php echo $agenda->getPreviousMonthLink() ?>">< <?php _e( 'Previous month', 'tryst' ); ?></a>
      </div>
      <!-- NEXT -->
      <div class="col-6 text-center">
        <a href="<?php echo $agenda->getNextMonthLink() ?>"><?php _e( 'Next month', 'tryst' ); ?> ></a>
      </div>
    </div>
    <div class="row">
      <!-- PREV -->
      <div class="col-md-3 d-none d-md-block text-center">
        <a  href="<?php echo $agenda->getPreviousMonthLink() ?>" class="btn btn-outline-secondary btn-sm"><span class="dashicons dashicons-arrow-left-alt"></span> <?php _e( 'Previous month', 'tryst' ); ?></a>
      </div>
      <div class="col-md-6 text-center">
        <h4 class="display-4"><?php _e( $agenda->getTimestampStart()->format('F'), 'tryst' ); ?> <?php echo $agenda->getTimestampStart()->format('Y') ?></h4>
      </div>
      <!-- NEXT -->
      <div class="col-md-3 d-none d-md-block text-center">
        <a href="<?php echo $agenda->getNextMonthLink() ?>" class="btn btn-outline-secondary btn-sm"> <?php _e( 'Next month', 'tryst' ); ?><span class="dashicons dashicons-arrow-right-alt"></span></a>
      </div>
    </div>
    <div class="row d-none d-sm-flex p-1 bg-dark text-white">
      <h5 class="col-sm p-1 text-center"><?php _e( 'Sunday', 'tryst' ); ?></h5> 
      <h5 class="col-sm p-1 text-center"><?php _e( 'Monday', 'tryst' ); ?></h5> 
      <h5 class="col-sm p-1 text-center"><?php _e( 'Tuesday', 'tryst' ); ?></h5> 
      <h5 class="col-sm p-1 text-center"><?php _e( 'Wednesday', 'tryst' ); ?></h5>
      <h5 class="col-sm p-1 text-center"><?php _e( 'Thursday', 'tryst' ); ?></h5>
      <h5 class="col-sm p-1 text-center"><?php _e( 'Friday', 'tryst' ); ?></h5>
      <h5 class="col-sm p-1 text-center"><?php _e( 'Saturday', 'tryst' ); ?></h5> 
    </div>
  </header>
  <main>
    <div class="row border border-right-0 border-bottom-0">
      <?php 
      $week_start = $agenda->getWeekFirstDay();
      $week_last = $agenda->getWeekLastDay();
      $week_days_count = 0;
      ?>
      <?php 
      $agenda_date = $agenda->getDateTime();
     $dt = $agenda_date->sub(new \DateInterval('P'.($week_start).'D'));

      ?>
      <!-- PREVIOUS MONTH DAYS -->
      <?php for($c = 1; $c < $week_start; $c++): ?>    
      <?php $dt->add(new \DateInterval('P1D')); ?>
      <?php $week_days_count++; ?>  
      <div class="day col-sm p-2 border border-left-0 border-top-0 text-truncate  bg-light text-muted d-none d-md-block">
        <a  href="<?php echo $agenda->getPreviousMonthLink(); ?>">
          <h5 class="row align-items-center">
            <span class="date col-1"><?php echo $dt->format('d'); ?></span>
            <small class="col d-sm-none text-center text-muted"><?php _e( $dt->format('l'), 'tryst' ); ?></small>
            <span class="col-1"></span>
          </h5>
        </a>
      </div>
 
      <?php endfor; ?>
      <?php foreach($agenda->getSchedule() as $k => $day): ?>
      <?php $day_class = $day->getTimestamp()->format('d') == date('d') && $agenda->getMonth() == date('m') ? 'today' : ''; ?>
      <div <?php if($day_class != '') { echo 'data-toggle="tooltip" title="'.__( 'Today', 'tryst' ).'"'; } ?>  class="day col-sm p-2 border border-left-0 border-top-0 text-truncate bg-light <?php echo $day_class; ?>">
        <h5 class="row align-items-center">
          <span class="date col-1"><?php echo $day->getTimestamp()->format('d'); ?></span>
          <small class="col d-sm-none text-center text-muted"><?php _e( $day->getTimestamp()->format('l'), 'tryst' ); ?></small>
          <span class="col-1"></span>
        </h5> 
        <?php 
        $meetings = is_array($day->getMeetings()) ? $day->getMeetings() : [];
        $available = $day->getAvailable();
        ?>
        <?php if(!$day->isDayOff()): ?>
        <?php foreach($available as $k => $time): ?> 
        <?php $slot_class = isset($meetings[$time]) ? 'bg-danger unavailable' : 'bg-success available'; ?>
        <a href="#" data-toggle="tooltip" title="<?php echo isset($meetings[$time]) ? __( 'Slot unavailable', 'tryst' ) : __( 'Slot available', 'tryst' ) ?>" class="d-block text-truncate small <?php echo $slot_class; ?> text-white" data-date="<?php echo $day->getTimestamp()->format('d/m/Y'); ?>" data-time="<?php echo $time; ?>">
          <?php echo $time; ?>
        </a>
        <?php endforeach; ?>
        <?php endif; ?>
        <?php if(empty($available)): ?>
        <p class="d-sm-none"><?php _e( 'Unavailable', 'tryst' ); ?></p>
        <?php endif;?>
      </div>
      <?php if($week_days_count % 6 == 0 && $week_days_count != 0): ?>
      <?php $week_days_count = 0; ?>
      <div class="w-100"></div>
      <?php else: $week_days_count++; ?>
      <?php endif; ?>
      <?php endforeach; ?>
      <!-- NEXT MONTH DAYS -->
      <?php  $dt = new DateTime(); $dt->add(new \DateInterval('P1M')); $dt->setDate($dt->format('Y'), $dt->format('m'), 1);  ?>
      <?php for($c = 1; $c <= 7; $c++): ?>
      <?php if($week_days_count % 7 == 0 && $week_days_count != 0): ?>
      <?php break; ?>
      <?php else: $week_days_count++; ?>
      <?php endif; ?>
      <div class="day col-sm p-2 border border-left-0 border-top-0 text-truncate  bg-light text-muted d-none d-md-block">
        <a  href="<?php echo $agenda->getNextMonthLink(); ?>">
          <h5 class="row align-items-center">
            <span class="date col-1"><?php echo $dt->format('d'); ?></span>
            <small class="col d-sm-none text-center text-muted"><?php _e($dt->format('l') , 'tryst' ); ?></small>
            <span class="col-1"></span>
          </div>
        </h5>
      </a>
      <?php $dt->add(new \DateInterval('P1D')); ?>
      <?php endfor; ?>
    </div>
  </main>
</div>
