<div class="control-group">
    <?php if(osc_form_element_type()=='checkbox') { ?>
        <div class="controls checkbox">
            <?php osc_print_form_field(); ?>
            <label class="control-label" for="<?php echo osc_esc_html(osc_form_element_name()); ?>"><?php echo osc_form_element_label(); ?></label>
        </div>
    <?php } else { ?>
        <label class="control-label" for="<?php echo osc_esc_html(osc_form_element_name()); ?>"><?php echo osc_form_element_label(); ?></label>
        <div class="controls">
            <?php osc_print_form_field(); ?>
        </div>
    <?php }; ?>
</div>
