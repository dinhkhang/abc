<?php
if (isset($rating) && is_array($rating) && array_key_exists('score', $rating) && array_key_exists('count', $rating)) {
        ?>
        <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo __('rating_score') ?></label>
                <div class="col-sm-10">
                        <input class="form-control" type="text" value="<?php echo $rating['score']; ?>" readonly>
                </div>
        </div>
        <div class="hr-line-dashed"></div>
        <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo __('rating_count') ?></label>
                <div class="col-sm-10">
                        <input class="form-control" type="text" value="<?php echo $rating['count']; ?>" readonly>
                </div>
        </div>
        <div class="hr-line-dashed"></div>
<?php } ?>