<div class="control-group">
    <label class="control-label">
        <?php echo __('Price', 'aviators'); ?>
    </label>

    <div class="controls">
        <?php $mb->the_field('price'); ?>
        <div class="input-append">
            <input type="number" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>">
            <span class="add-on"><?php print aviators_settings_get_value('money', 'currency', 'sign'); ?></span>
        </div>
    </div>
</div>

<div class="control-group">
    <label class="control-label">
        <?php echo __('Bathrooms', 'aviators'); ?>
    </label>

    <div class="controls">
        <?php $mb->the_field('bathrooms'); ?>
        <input type="number" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>">
    </div>
</div>

<div class="control-group">
    <label class="control-label">
        <?php echo __('Bedrooms', 'aviators'); ?>
    </label>

    <div class="controls">
        <?php $mb->the_field('bedrooms'); ?>
        <input type="number" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>">
    </div>
</div>

<div class="control-group">
    <label class="control-label">
        <?php echo __('Area', 'aviators'); ?>
    </label>

    <div class="controls">
        <?php $mb->the_field('area'); ?>
        <div class="input-append">
            <input type="number" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>">
            <span class="add-on"><?php print aviators_settings_get_value('properties', 'units', 'area'); ?></span>
        </div>
    </div>
</div>

<div class="control-group">
    <label class="control-label">
        <?php echo __('GPS', 'aviators'); ?>
    </label>

    <div class="controls">
        <?php $mb->the_field('latitude'); ?>
        <input class="latitude" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" placeholder="<?php echo __('Latitude', 'aviators'); ?>"/>

        <?php $mb->the_field('longitude'); ?>
        <input class="longitude" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" placeholder="<?php echo __('Longitude', 'aviators'); ?>"/>
    </div>
</div>

<div class="control-group">
    <label class="control-label">
        <?php echo __('Contract type', 'aviators'); ?>
    </label>

    <div class="controls">
        

        <ul class="unstyled">
            <li>
                <label>
                    <input type="radio" name="<?php $mb->the_name(); ?>" value="rent" <?php if ($mb->get_the_value() == 'rent'): ?>checked="checked"<?php endif; ?>/>
                    &nbsp;&nbsp;<?php echo __('For rent', 'aviators'); ?>
                </label>
            </li>

            <li>
                <label>
                    <input type="radio" name="<?php $mb->the_name(); ?>" value="sale" <?php if ($mb->get_the_value() == 'sale'): ?>checked="checked"<?php endif; ?> />
                    &nbsp;&nbsp;<?php echo __('Sale', 'aviators'); ?>
                </label>
            </li>
        </ul>
    </div>
</div>

