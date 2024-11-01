<!-- Modal -->
<div class="modal fade" id="modal-form-meeting" tabindex="-1" role="dialog" aria-labelledby="modal-meeting-title" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal-meeting-title"><?php _e('Tryst', 'tryst') ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?php require 'form-meeting.php'; ?>
      </div>
    </div>
  </div>
</div>