<?php

class Creasync_MapProperties_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'Creasync_MapProperties_Widget',
            __('Aviators: Map Properties', 'aviators'),
            array(
                 'classname' => 'properties',
                 'description' => __('Map Properties', 'aviators'),
            ));
    }

    public function form($instance) {
        if (isset($instance['latitude'])) {
            $latitude = $instance['latitude'];
        } else {
            $latitude = '34.019000';
        }

        if (isset($instance['longitude'])) {
            $longitude = $instance['longitude'];
        } else {
            $longitude = '-118.455458';
        }

        if (isset($instance['zoom'])) {
            $zoom = $instance['zoom'];
        } else {
            $zoom = '14';
        }

        if (isset($instance['height'])) {
            $height = $instance['height'];
        } else {
            $height = '485px';
        }

        if (isset($instance['enable_geolocation'])) {
            $enable_geolocation = $instance['enable_geolocation'];
        } else {
            $enable_geolocation = FALSE;
        }

        if (isset($instance['show_filter'])) {
            $show_filter = $instance['show_filter'];
        } else {
            $show_filter = TRUE;
        }

        ?>

        <p>
            <label for="<?php echo $this->get_field_id('latitude'); ?>"><?php echo __('Latitude', 'aviators'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('latitude'); ?>" name="<?php echo $this->get_field_name( 'latitude' ); ?>" type="text" value="<?php echo esc_attr($latitude); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('longitude'); ?>"><?php echo __('Longitude', 'aviators'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('longitude'); ?>" name="<?php echo $this->get_field_name( 'longitude' ); ?>" type="text" value="<?php echo esc_attr($longitude); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('zoom'); ?>"><?php echo __('Zoom', 'aviators'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('zoom'); ?>" name="<?php echo $this->get_field_name( 'zoom' ); ?>" type="text" value="<?php echo esc_attr($zoom); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('height'); ?>"><?php echo __('Height', 'aviators'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" type="text" value="<?php echo esc_attr($height); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('show_filter'); ?>"><?php echo __('Show filter', 'aviators'); ?></label>
            <input type="checkbox" id="<?php echo $this->get_field_id('show_filter'); ?>" name="<?php echo $this->get_field_name( 'show_filter' ); ?>" value="1" <?php checked($show_filter); ?>>            
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('enable_geolocation'); ?>"><?php echo __('Enable geolocation', 'aviators'); ?></label>
            <input type="checkbox" id="<?php echo $this->get_field_id('enable_geolocation'); ?>" name="<?php echo $this->get_field_name( 'enable_geolocation' ); ?>" value="1" <?php checked($enable_geolocation); ?>>
        </p>
    <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['latitude'] = strip_tags($new_instance['latitude']);
        $instance['longitude'] = strip_tags($new_instance['longitude']);
        $instance['zoom'] = strip_tags($new_instance['zoom']);
        $instance['height'] = strip_tags($new_instance['height']);
        $instance['show_filter'] = strip_tags($new_instance['show_filter']);
        $instance['enable_geolocation'] = strip_tags($new_instance['enable_geolocation']);

        return $instance;
    }

    public function widget($args, $instance) {
        extract($args);

        $price_from = array();
        $price_from_parts = explode("\n", aviators_settings_get_value('properties', 'filter', 'from'));
        foreach($price_from_parts as $price) {
            $price_from[] = trim($price);
        }

        $price_to = array();
        $price_to_parts = explode("\n", aviators_settings_get_value('properties', 'filter', 'to'));
        foreach($price_to_parts as $price) {
            $price_to[] = trim($price);
        }

        echo View::render('properties/map.twig', array(
             'latitude' => $instance['latitude'],
             'longitude' => $instance['longitude'],
             'zoom' => $instance['zoom'],
             'height' => $instance['height'],
             'properties' => creasync_aviators_properties_get_most_recent(9999),
             'price_from' => $price_from,
             'price_to' => $price_to,
             'show_filter' => $instance['show_filter'],
             'enable_geolocation' => !empty($instance['enable_geolocation']) ? $instance['enable_geolocation'] : FALSE,
             'before_widget' => $before_widget,
             'after_widget' => $after_widget,
             'before_title' => $before_title,
             'after_title' => $after_title,
        ));
    }
}


class Creasync_FeaturedProperties_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'Creasync_FeaturedProperties_Widget',
            __('Aviators: Featured Properties', 'aviators'),
            array(
                 'classname' => 'properties',
                 'description' => __( 'Featured Properties', 'aviators' ),
            ));
    }

    public function form($instance) {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('Featured Properties', 'aviators' );
        }

        if (isset( $instance['count'])) {
            $count = $instance['count'];
        } else {
            $count = 3;
        }

        if (isset($instance['shuffle'])) {
            $shuffle = $instance['shuffle'];
        } else {
            $shuffle = FALSE;
        }
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title', 'aviators'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('count'); ?>"><?php echo __('Count', 'aviators'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo esc_attr($count); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('shuffle'); ?>"><?php echo __('Shuffle', 'aviators'); ?></label>
            <input type="checkbox" id="<?php echo $this->get_field_id('shuffle'); ?>" name="<?php echo $this->get_field_name('shuffle'); ?>" value="1" <?php checked($shuffle); ?>>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['count'] = strip_tags($new_instance['count']);
        $instance['shuffle'] = strip_tags($new_instance['shuffle']);
        return $instance;
    }

    public function widget($args, $instance) {
        extract($args);

        $do_shuffle = FALSE;
        if (!empty($instance['shuffle']) && $instance['shuffle']) {
            $do_shuffle = TRUE;
        }

        echo View::render('properties/widget.twig', array(
            'title' => apply_filters('widget_title', $instance['title']),
            'count' => $instance['count'],
            'properties' => creasync_aviators_properties_get_featured($instance['count'], $do_shuffle),
            'before_widget' => $before_widget,
            'after_widget' => $after_widget,
            'before_title' => $before_title,
            'after_title' => $after_title,
        ));
    }
}

class Creasync_FeaturedPropertiesLarge_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'FeaturedPropertiesLarge_Widget',
            __('Aviators: Featured Properties Large', 'aviators'),
            array(
                'classname' => 'properties-large',
                'description' => __( 'Featured Properties Large', 'aviators' ),
            ));
    }

    public function form($instance) {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('Featured Properties', 'aviators' );
        }

        if (isset( $instance['count'])) {
            $count = $instance['count'];
        } else {
            $count = 3;
        }

        if (isset($instance['shuffle'])) {
            $shuffle = $instance['shuffle'];
        } else {
            $shuffle = FALSE;
        }
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title', 'aviators'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>


        <p>
            <label for="<?php echo $this->get_field_id('shuffle'); ?>"><?php echo __('Shuffle', 'aviators'); ?></label>
            <input type="checkbox" id="<?php echo $this->get_field_id('shuffle'); ?>" name="<?php echo $this->get_field_name('shuffle'); ?>" value="1" <?php checked($shuffle); ?>>
        </p>
    <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['shuffle'] = strip_tags($new_instance['shuffle']);
        return $instance;
    }

    public function widget($args, $instance) {
        extract($args);

        $do_shuffle = FALSE;
        if (!empty($instance['shuffle']) && $instance['shuffle']) {
            $do_shuffle = TRUE;
        }

        echo View::render('properties/widget-large.twig', array(
            'title' => apply_filters('widget_title', $instance['title']),
            'properties' => creasync_aviators_properties_get_featured(3, $do_shuffle),
            'before_widget' => $before_widget,
            'after_widget' => $after_widget,
            'before_title' => $before_title,
            'after_title' => $after_title,
        ));
    }
}

class Creasync_MostRecentProperties_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'Creasync_MostRecentProperties_Widget',
            __('Aviators: Most Recent Properties', 'aviators'),
            array(
                 'classname' => 'properties',
                 'description' => __( 'Most Recent Properties', 'aviators' ),
            ));
    }

    public function form($instance) {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('Most Recent Properties', 'aviators' );
        }

        if (isset( $instance['count'])) {
            $count = $instance['count'];
        } else {
            $count = 3;
        }

        if (isset($instance['shuffle'])) {
            $shuffle = $instance['shuffle'];
        } else {
            $shuffle = FALSE;
        }
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title', 'aviators'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('count'); ?>"><?php echo __('Count', 'aviators'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo esc_attr($count); ?>" />
        </p>

    <p>
        <label for="<?php echo $this->get_field_id('shuffle'); ?>"><?php echo __('Shuffle', 'aviators'); ?></label>
        <input type="checkbox" id="<?php echo $this->get_field_id('shuffle'); ?>" name="<?php echo $this->get_field_name('shuffle'); ?>" value="1" <?php checked($shuffle); ?>>
    </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['count'] = strip_tags($new_instance['count']);
        $instance['shuffle'] = strip_tags($new_instance['shuffle']);
        return $instance;
    }

    public function widget($args, $instance) {
        extract($args);

        $do_shuffle = FALSE;
        if (!empty($instance['shuffle']) && $instance['shuffle']) {
            $do_shuffle = TRUE;
        }

        echo View::render('properties/widget.twig', array(
             'title' => apply_filters('widget_title', $instance['title']),
             'count' => $instance['count'],
             'properties' => creasync_aviators_properties_get_most_recent($instance['count'], $do_shuffle),
             'before_widget' => $before_widget,
             'after_widget' => $after_widget,
             'before_title' => $before_title,
             'after_title' => $after_title,
        ));
    }
}

class Creasync_ReducedProperties_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'Creasync_ReducedProperties_Widget',
            __('Aviators: Reduced Properties', 'aviators'),
            array(
                 'classname' => 'properties',
                 'description' => __('Reduced Properties', 'aviators'),
            ));
    }

    public function form($instance) {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('Reduced Properties', 'aviators' );
        }

        if (isset( $instance['count'])) {
            $count = $instance['count'];
        } else {
            $count = 3;
        }

        if (isset($instance['shuffle'])) {
            $shuffle = $instance['shuffle'];
        } else {
            $shuffle = FALSE;
        }
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title', 'aviators'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('count'); ?>"><?php echo __('Count', 'aviators'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo esc_attr($count); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('shuffle'); ?>"><?php echo __('Shuffle', 'aviators'); ?></label>
            <input type="checkbox" id="<?php echo $this->get_field_id('shuffle'); ?>" name="<?php echo $this->get_field_name('shuffle'); ?>" value="1" <?php checked($shuffle); ?>>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['count'] = strip_tags($new_instance['count']);
        $instance['shuffle'] = strip_tags($new_instance['shuffle']);
        return $instance;
    }

    public function widget($args, $instance) {
        extract($args);

        $do_shuffle = FALSE;
        if (!empty($instance['shuffle']) && $instance['shuffle']) {
            $do_shuffle = TRUE;
        }

        echo View::render('properties/widget.twig', array(
             'title' => apply_filters('widget_title', $instance['title']),
             'count' => $instance['count'],
             'properties' => creasync_aviators_properties_get_reduced($instance['count'], $do_shuffle),
             'before_widget' => $before_widget,
             'after_widget' => $after_widget,
             'before_title' => $before_title,
             'after_title' => $after_title,
        ));
    }
}

class Creasync_CarouselProperties_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'Creasync_CarouselProperties_Widget',
            __('Aviators: Carousel Properties', 'aviators'),
            array(
                 'classname' => '',
                 'description' => __('Carousel Properties', 'aviators'),
            ));
    }

    public function form($instance) {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('Carousel Properties', 'aviators' );
        }

        if (isset($instance['count'])) {
            $count = $instance['count'];
        } else {
            $count = 10;
        }
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title', 'aviators'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('count'); ?>"><?php echo __('Count', 'aviators'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="text" value="<?php echo esc_attr($count); ?>" />
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['count'] = strip_tags($new_instance['count']);
        return $instance;
    }

    public function widget($args, $instance) {
        extract($args);

        echo View::render('properties/carousel.twig', array(
             'title' => apply_filters('widget_title', $instance['title']),
             'properties' => creasync_aviators_properties_get_most_recent($instance['count']),
             'before_widget' => $before_widget,
             'after_widget' => $after_widget,
             'before_title' => $before_title,
             'after_title' => $after_title,
        ));
    }
}

class Creasync_PropertyFilter_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'Creasync_PropertyFilter_Widget',
            __('Aviators:Property Filter', 'aviators'),
            array(
                 'classname' => 'enquire',
                 'description' => __('Property Filter', 'aviators'),
            ));
    }

    public function form($instance) {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('Property Filter', 'aviators' );
        }        
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title', 'aviators'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <?php       
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    public function widget($args, $instance) {
        global $post;

        extract($args);

        $price_from = array();
        $price_from_parts = explode("\n", aviators_settings_get_value('properties', 'filter', 'from'));
        foreach($price_from_parts as $price) {
            $price_from[] = trim($price);
        }

        $price_to = array();
        $price_to_parts = explode("\n", aviators_settings_get_value('properties', 'filter', 'to'));
        foreach($price_to_parts as $price) {
            $price_to[] = trim($price);
        }

        echo View::render('properties/filter.twig', array(
            'id' => $this->id,
            'title' => apply_filters('widget_title', $instance['title']),
            'price_from' => $price_from,
            'price_to' => $price_to,
            'before_widget' => $before_widget,
            'after_widget' => $after_widget,
            'before_title' => $before_title,
            'after_title' => $after_title,
        ));
    }
}

class Creasync_EnquireProperties_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'Creasync_EnquireProperties_Widget',
            __('Aviators: Enquire Property', 'aviators'),
            array(
                 'classname' => 'enquire',
                 'description' => __('Enquire', 'aviators'),
            ));
    }

    public function form($instance) {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('Enquire Now', 'aviators' );
        }
        ?>

    <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title', 'aviators'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
    </p>

    <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    public function widget($args, $instance) {
        global $post;

        extract($args);

        echo View::render('properties/enquire.twig', array(
           'title' => apply_filters('widget_title', $instance['title']),
           'post_id' => $post->ID,
           'before_widget' => $before_widget,
           'after_widget' => $after_widget,
           'before_title' => $before_title,
           'after_title' => $after_title,
      ));
    }
}

class Creasync_SliderProperties_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'Creasync_SliderProperties_Widget',
            __('Aviators: Property Slider', 'aviators'),
            array(
                 'classname' => 'property-slider',
                 'description' => __('Property slider', 'aviators'),
            ));
    }

    public function form($instance) {
        if (isset($instance['properties'])) {
            $properties = $instance['properties'];
        }

        if (isset($instance['show_filter'])) {
            $show_filter = $instance['show_filter'];
        } else {
            $show_filter = TRUE;
        }
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('properties'); ?>"><?php echo __('Properties - property IDs (eg. 11,25,36)', 'aviators'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('properties'); ?>" name="<?php echo $this->get_field_name( 'properties' ); ?>" type="text" value="<?php echo esc_attr($properties); ?>" />
        </p>

    <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['properties'] = strip_tags($new_instance['properties']);
        $instance['show_filter'] = strip_tags($new_instance['show_filter']);
        return $instance;
    }

    public function widget($args, $instance) {
        global $post;
        extract($args);

        $posts = array();
        $parts = explode(',', $instance['properties']);

        foreach($parts as $part) {
            $posts[] = trim($part);
        }

        $args = array(
            'post__in' => $posts,
            'post_type' => 'property',
            'posts_per_page' => -1,
        );

        $price_from = array();
        $price_from_parts = explode("\n", aviators_settings_get_value('properties', 'filter', 'from'));
        foreach($price_from_parts as $price) {
            $price_from[] = trim($price);
        }

        $price_to = array();
        $price_to_parts = explode("\n", aviators_settings_get_value('properties', 'filter', 'to'));
        foreach($price_to_parts as $price) {
            $price_to[] = trim($price);
        }

        echo View::render('properties/slider-large.twig', array(
            'id' => $this->id,
            'properties' => _creasync_aviators_properties_prepare(new WP_Query($args)),
            'price_to' => $price_to,
            'price_from' => $price_from,
            'before_widget' => $before_widget,
            'after_widget' => $after_widget,
            'before_title' => $before_title,
            'after_title' => $after_title,
        ));
    }
}