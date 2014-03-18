<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('.obtain-gps').on({
            click: function(e) {
                e.preventDefault();
                var title = $('input#title').val();
                var locations = $('#locationschecklist');
                if (title == '') {
                    alert('<?php echo __('Please fill the address (Title field).', 'aviators'); ?>');
                    return;
                }

                var checked_locations = $('input[type=checkbox]:checked', locations);

                if (checked_locations.length === 0) {
                    alert('<?php echo __('Please check the location (Locations checkboxes).', 'aviators'); ?>');
                    return;
                }

                if (checked_locations.length > 1) {
                    alert('<?php echo __('Please check just one location (Locations checkbox).', 'aviators'); ?>');
                    return;
                }

                var location = $('input[type=checkbox]:checked', locations).parent().text();
                var geocoder = new google.maps.Geocoder();
                geocoder.geocode( { 'address': title + ', '+ location}, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        $('.latitude').attr('value', results[0].geometry.location.jb);
                        $('.longitude').attr('value', results[0].geometry.location.kb);
                    } else {
                        alert("<?php echo __('Geocode was not successful for the following reason: ', 'aviators'); ?>" + status);
                    }
                });
            }
        })
    });
</script>

<table class="aviators-options">
    <tr>
        <th>
            <label><?php echo __('ID', 'aviators'); ?></label>
        </th>
        <td>
            <?php $mb->the_field('id'); ?>
            <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/>

            <p class="description">
                <?php echo __('Leave empty for default ID.', 'aviators'); ?>
            </p>
        </td>
    </tr>

    <tr>
        <th>
            <label><?php echo __('Optional Title', 'aviators'); ?></label>
        </th>
        <td>
            <?php $mb->the_field('title'); ?>
            <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/>

            <p class="description">
                <?php echo __('It will be used in widgets and properties grid & rows layout.', 'aviators'); ?>
            </p>
        </td>
    </tr>

    <?php if (function_exists('aviators_landlords_create_post_type')): ?>
        <tr>
            <th>
                <label><?php echo __('Landlord', 'aviators'); ?></label>
            </th>
            <td>
                <?php $mb->the_field('landlord'); ?>

                <select name="<?php $mb->the_name(); ?>">
                    <option value="">---</option>
                    <?php foreach (aviators_landlords_get_list() as $landlord): ?>
                        <option value="<?php echo $landlord->ID; ?>" <?php $mb->the_select_state($landlord->ID); ?>><?php echo $landlord->post_title ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    <?php endif ?>

    <?php if (function_exists('creasync_aviators_agents_create_post_type')): ?>
        <tr>
            <th>
                <label><?php echo __('Agents', 'aviators'); ?></label>
            </th>
            <td>
                <?php $mb->the_field('agents', WPALCHEMY_FIELD_HINT_SELECT_MULTI); ?>

                <select multiple="multiple" name="<?php $mb->the_name(); ?>">
                    <?php foreach (creasync_aviators_agents_get(9999) as $agent): ?>
                        <option value="<?php echo $agent->ID; ?>" <?php $mb->the_select_state($agent->ID); ?>><?php echo $agent->post_title ?></option>
                    <?php endforeach; ?>
               </select>
            </td>
        </tr>
    <?php endif ?>

    <tr>
        <th>
            <label><?php echo __('Price', 'aviators'); ?></label>
        </th>
        <td>
            <?php $mb->the_field('price'); ?>
            <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/>
        </td>
    </tr>

    <tr>
        <th>
            <label><?php echo __('Bathrooms', 'aviators'); ?></label>
        </th>
        <td>
            <?php $mb->the_field('bathrooms'); ?>
            <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/>
        </td>
    </tr>

    <tr>
        <th>
            <label><?php echo __('Bedrooms', 'aviators'); ?></label>
        </th>
        <td>
            <?php $mb->the_field('bedrooms'); ?>
            <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/>
        </td>
    </tr>

    <tr>
        <th>
            <label><?php echo __('Area', 'aviators'); ?></label>
        </th>
        <td>
            <?php $mb->the_field('area'); ?>
            <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/>
        </td>
    </tr>

    <tr>
        <th>
            <label><?php echo __('GPS', 'aviators'); ?></label>
        </th>
        <td>
            <?php $mb->the_field('latitude'); ?>
            <input class="latitude" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" placeholder="<?php echo __('Latitude', 'aviators'); ?>"/>

            <?php $mb->the_field('longitude'); ?>
            <input class="longitude" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" placeholder="<?php echo __('Longitude', 'aviators'); ?>"/>

            <input type="submit" class="button obtain-gps" value="<?php echo __('Obtain from Google Maps', 'aviators'); ?>">
        </td>
    </tr>

    <tr>
        <th>
            <label><?php echo __('Contract type', 'aviators'); ?></label>
        </th>
        <td>
            <?php $mb->the_field('contract_type'); ?>

            <ul class="categorychecklist form-no-clear">
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
        </td>
    </tr>

    <tr>
        <th>
            <label><?php echo __('Featured', 'aviators'); ?></label>
        </th>
        <td>
            <?php $mb->the_field('featured'); ?>
            <input type="checkbox" name="<?php $mb->the_name(); ?>" value="1" <?php checked($mb->get_the_value()); ?>/>
        </td>
    </tr>

    
    
    <tr>
        <th>
            <label><?php echo __('Reduced', 'aviators'); ?></label>
        </th>
        <td>
            <?php $mb->the_field('sold'); ?>
            <input type="checkbox" name="<?php $mb->the_name(); ?>" value="1" <?php checked($mb->get_the_value()); ?>/>
        </td>
    </tr>

<tr>
        <th>
            <label><?php echo __('Sold', 'aviators'); ?></label>
        </th>
        <td>
            <?php $mb->the_field('reduced'); ?>
            <input type="checkbox" name="<?php $mb->the_name(); ?>" value="1" <?php checked($mb->get_the_value()); ?>/>
        </td>
    </tr>

    <tr>
        <th><label><?php echo __('Slider image', 'aviators'); ?></label></th>
        <td>
            <?php $property_slider_media_metabox = new Creasync_WPAlchemy_MediaAccess(); ?>

            <?php $mb->the_field('slider_image'); ?>
            <?php echo $property_slider_media_metabox->getField(array('name' => $mb->get_the_name(), 'value' => $mb->get_the_value())); ?>
            <?php echo $property_slider_media_metabox->getButton(); ?>
        </td>
    </tr>

    <tr>
        <th><label><?php echo __('Images', 'aviators'); ?></label></th>
        <td>
            <?php $property_media_metabox = new Creasync_WPAlchemy_MediaAccess(); ?>


            <?php while($mb->have_fields_and_multi('slides')): ?>
                <?php $mb->the_group_open(); ?>


                <?php $mb->the_field('imgurl'); ?>
                <?php $property_media_metabox->setGroupName('img-n'. $mb->get_the_index())->setInsertButtonLabel(__('Insert', 'aviators')); ?>

                <p>
                    <?php echo $property_media_metabox->getField(array('name' => $mb->get_the_name(), 'value' => $mb->get_the_value())); ?>
                    <?php echo $property_media_metabox->getButton(); ?>
                    <a href="#" class="dodelete button"><?php echo __('Remove', 'aviators'); ?></a>
                </p>

                <?php $mb->the_group_close(); ?>
            <?php endwhile; ?>

            <p>
                <a href="#" class="docopy-slides button button-primary"><?php echo __('Add', 'aviators'); ?></a>
                <a href="#" class="dodelete-docs button"><?php echo __('Remove all', 'aviators')?></a>
            </p>
        </td>
    </tr>
</table>

