<label for="f-observation"><?= __('Observation', 'tryst') ?></label>
<textarea class="form-control" name="meet[observation]" id="f-observation"><?php if(isset($meeting)) echo $meeting->getMeta('observation'); ?></textarea>