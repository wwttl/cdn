<?php if ( ! defined( 'ABSPATH' ) ) { die; }
/**
 *
 * Field: backup
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'CSF_Field_backup' ) ) {
  class CSF_Field_backup extends CSF_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $unique = $this->unique;
      $nonce  = wp_create_nonce( 'csf_backup_nonce' );
      $export = add_query_arg( array( 'action' => 'csf-export', 'unique' => $unique, 'nonce' => $nonce ), admin_url( 'admin-ajax.php' ) );

      echo $this->field_before();

      //echo '<textarea readonly="readonly" class="csf-export-data">'. esc_attr( json_encode( get_option( $unique ) ) ) .'</textarea>';
      echo '<a href="'. esc_url( $export ) .'" class="button button-primary csf-export" target="_blank">导出并下载</a>';
      echo '<div class="csf-field-import csf-field-content"></div>';
      echo '<textarea name="csf_import_data" class="csf-import-data" placeholder="将导出的 json 文件用记事本打开，复制数据粘贴到此，执行导入！"></textarea>';
      echo '<button type="submit" class="button button-primary csf-confirm csf-import" data-unique="'. esc_attr( $unique ) .'" data-nonce="'. esc_attr( $nonce ) .'">导入设置</button>';
      echo $this->field_after();
    }

  }
}

/**
 *
 * Field: callback
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'CSF_Field_callback' ) ) {
  class CSF_Field_callback extends CSF_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      if ( isset( $this->field['function'] ) && is_callable( $this->field['function'] ) ) {

        $args = ( isset( $this->field['args'] ) ) ? $this->field['args'] : null;

        call_user_func( $this->field['function'], $args );

      }

    }

  }
}

/**
 *
 * Field: color
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'CSF_Field_color' ) ) {
  class CSF_Field_color extends CSF_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $default_attr = ( ! empty( $this->field['default'] ) ) ? ' data-default-color="'. esc_attr( $this->field['default'] ) .'"' : '';

      echo $this->field_before();
      echo '<input type="text" name="'. esc_attr( $this->field_name() ) .'" value="'. esc_attr( $this->value ) .'" class="csf-color"'. $default_attr . $this->field_attributes() .'/>';
      echo $this->field_after();

    }

    public function output() {

      $output    = '';
      $elements  = ( is_array( $this->field['output'] ) ) ? $this->field['output'] : array_filter( (array) $this->field['output'] );
      $important = ( ! empty( $this->field['output_important'] ) ) ? '!important' : '';
      $mode      = ( ! empty( $this->field['output_mode'] ) ) ? $this->field['output_mode'] : 'color';

      if ( ! empty( $elements ) && isset( $this->value ) && $this->value !== '' ) {
        foreach ( $elements as $key_property => $element ) {
          if ( is_numeric( $key_property ) ) {
            $output = implode( ',', $elements ) .'{'. $mode .':'. $this->value . $important .';}';
            break;
          } else {
            $output .= $element .'{'. $key_property .':'. $this->value . $important .'}';
          }
        }
      }

      $this->parent->output_css .= $output;

      return $output;

    }

  }
}

/**
 *
 * Field: content
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'CSF_Field_content' ) ) {
  class CSF_Field_content extends CSF_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      if ( ! empty( $this->field['content'] ) ) {

        echo $this->field['content'];

      }

    }

  }
}

/**
 *
 * Field: fieldset
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'CSF_Field_fieldset' ) ) {
  class CSF_Field_fieldset extends CSF_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      echo $this->field_before();

      echo '<div class="csf-fieldset-content" data-depend-id="'. esc_attr( $this->field['id'] ) .'">';

      foreach ( $this->field['fields'] as $field ) {

        $field_id      = ( isset( $field['id'] ) ) ? $field['id'] : '';
        $field_default = ( isset( $field['default'] ) ) ? $field['default'] : '';
        $field_value   = ( isset( $this->value[$field_id] ) ) ? $this->value[$field_id] : $field_default;
        $unique_id     = ( ! empty( $this->unique ) ) ? $this->unique .'['. $this->field['id'] .']' : $this->field['id'];

        CSF::field( $field, $field_value, $unique_id, 'field/fieldset' );

      }

      echo '</div>';

      echo $this->field_after();

    }

  }
}

/**
 *
 * Field: number
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'CSF_Field_number' ) ) {
  class CSF_Field_number extends CSF_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $args = wp_parse_args( $this->field, array(
        'min'  => 'any',
        'max'  => 'any',
        'step' => 'any',
        'unit' => '',
      ) );

      echo $this->field_before();
      echo '<div class="csf--wrap">';
      echo '<input type="number" name="'. esc_attr( $this->field_name() ) .'" value="'. esc_attr( $this->value ) .'"'. $this->field_attributes() .' min="'. esc_attr( $args['min'] ) .'" max="'. esc_attr( $args['max'] ) .'" step="'. esc_attr( $args['step'] ) .'"/>';
      echo ( ! empty( $args['unit'] ) ) ? '<span class="csf--unit">'. esc_attr( $args['unit'] ) .'</span>' : '';
      echo '</div>';
      echo $this->field_after();

    }

    public function output() {

      $output    = '';
      $elements  = ( is_array( $this->field['output'] ) ) ? $this->field['output'] : array_filter( (array) $this->field['output'] );
      $important = ( ! empty( $this->field['output_important'] ) ) ? '!important' : '';
      $mode      = ( ! empty( $this->field['output_mode'] ) ) ? $this->field['output_mode'] : 'width';
      $unit      = ( ! empty( $this->field['unit'] ) ) ? $this->field['unit'] : 'px';

      if ( ! empty( $elements ) && isset( $this->value ) && $this->value !== '' ) {
        foreach ( $elements as $key_property => $element ) {
          if ( is_numeric( $key_property ) ) {
            if ( $mode ) {
              $output = implode( ',', $elements ) .'{'. $mode .':'. $this->value . $unit . $important .';}';
            }
            break;
          } else {
            $output .= $element .'{'. $key_property .':'. $this->value . $unit . $important .'}';
          }
        }
      }

      $this->parent->output_css .= $output;

      return $output;

    }

  }
}

/**
 *
 * Field: radio
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'CSF_Field_radio' ) ) {
  class CSF_Field_radio extends CSF_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $args = wp_parse_args( $this->field, array(
        'inline'     => false,
        'query_args' => array(),
      ) );

      $inline_class = ( $args['inline'] ) ? ' class="csf--inline-list"' : '';

      echo $this->field_before();

      if ( isset( $this->field['options'] ) ) {

        $options = $this->field['options'];
        $options = ( is_array( $options ) ) ? $options : array_filter( $this->field_data( $options, false, $args['query_args'] ) );

        if ( is_array( $options ) && ! empty( $options ) ) {

          echo '<ul'. $inline_class .'>';

          foreach ( $options as $option_key => $option_value ) {

            if ( is_array( $option_value ) && ! empty( $option_value ) ) {

              echo '<li>';
                echo '<ul>';
                  echo '<li><strong>'. esc_attr( $option_key ) .'</strong></li>';
                  foreach ( $option_value as $sub_key => $sub_value ) {
                    $checked = ( $sub_key == $this->value ) ? ' checked' : '';
                    echo '<li>';
                    echo '<label>';
                    echo '<input type="radio" name="'. esc_attr( $this->field_name() ) .'" value="'. esc_attr( $sub_key ) .'"'. $this->field_attributes() . esc_attr( $checked ) .'/>';
                    echo '<span class="csf--text">'. esc_attr( $sub_value ) .'</span>';
                    echo '</label>';
                    echo '</li>';
                  }
                echo '</ul>';
              echo '</li>';

            } else {

              $checked = ( $option_key == $this->value ) ? ' checked' : '';

              echo '<li>';
              echo '<label>';
              echo '<input type="radio" name="'. esc_attr( $this->field_name() ) .'" value="'. esc_attr( $option_key ) .'"'. $this->field_attributes() . esc_attr( $checked ) .'/>';
              echo '<span class="csf--text">'. esc_attr( $option_value ) .'</span>';
              echo '</label>';
              echo '</li>';

            }

          }

          echo '</ul>';

        } else {

          echo ( ! empty( $this->field['empty_message'] ) ) ? esc_attr( $this->field['empty_message'] ) : '没有可用数据';

        }

      } else {

        $label = ( isset( $this->field['label'] ) ) ? $this->field['label'] : '';
        echo '<label><input type="radio" name="'. esc_attr( $this->field_name() ) .'" value="1"'. $this->field_attributes() . esc_attr( checked( $this->value, 1, false ) ) .'/>';
        echo ( ! empty( $this->field['label'] ) ) ? '<span class="csf--text">'. esc_attr( $this->field['label'] ) .'</span>' : '';
        echo '</label>';

      }

      echo $this->field_after();

    }

  }
}

/**
 *
 * Field: select
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'CSF_Field_select' ) ) {
  class CSF_Field_select extends CSF_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $args = wp_parse_args( $this->field, array(
        'placeholder' => '',
        'chosen'      => false,
        'multiple'    => false,
        'sortable'    => false,
        'ajax'        => false,
        'settings'    => array(),
        'query_args'  => array(),
      ) );

      $this->value = ( is_array( $this->value ) ) ? $this->value : array_filter( (array) $this->value );

      echo $this->field_before();

      if ( isset( $this->field['options'] ) ) {

        if ( ! empty( $args['ajax'] ) ) {
          $args['settings']['data']['type']  = $args['options'];
          $args['settings']['data']['nonce'] = wp_create_nonce( 'csf_chosen_ajax_nonce' );
          if ( ! empty( $args['query_args'] ) ) {
            $args['settings']['data']['query_args'] = $args['query_args'];
          }
        }

        $chosen_rtl       = ( is_rtl() ) ? ' chosen-rtl' : '';
        $multiple_name    = ( $args['multiple'] ) ? '[]' : '';
        $multiple_attr    = ( $args['multiple'] ) ? ' multiple="multiple"' : '';
        $chosen_sortable  = ( $args['chosen'] && $args['sortable'] ) ? ' csf-chosen-sortable' : '';
        $chosen_ajax      = ( $args['chosen'] && $args['ajax'] ) ? ' csf-chosen-ajax' : '';
        $placeholder_attr = ( $args['chosen'] && $args['placeholder'] ) ? ' data-placeholder="'. esc_attr( $args['placeholder'] ) .'"' : '';
        $field_class      = ( $args['chosen'] ) ? ' class="csf-chosen'. esc_attr( $chosen_rtl . $chosen_sortable . $chosen_ajax ) .'"' : '';
        $field_name       = $this->field_name( $multiple_name );
        $field_attr       = $this->field_attributes();
        $maybe_options    = $this->field['options'];
        $chosen_data_attr = ( $args['chosen'] && ! empty( $args['settings'] ) ) ? ' data-chosen-settings="'. esc_attr( json_encode( $args['settings'] ) ) .'"' : '';

        if ( is_string( $maybe_options ) && ! empty( $args['chosen'] ) && ! empty( $args['ajax'] ) ) {
          $options = $this->field_wp_query_data_title( $maybe_options, $this->value );
        } else if ( is_string( $maybe_options ) ) {
          $options = $this->field_data( $maybe_options, false, $args['query_args'] );
        } else {
          $options = $maybe_options;
        }

        if ( ( is_array( $options ) && ! empty( $options ) ) || ( ! empty( $args['chosen'] ) && ! empty( $args['ajax'] ) ) ) {

          if ( ! empty( $args['chosen'] ) && ! empty( $args['multiple'] ) ) {

            echo '<select name="'. $field_name .'" class="csf-hide-select hidden"'. $multiple_attr . $field_attr .'>';
            foreach ( $this->value as $option_key ) {
              echo '<option value="'. esc_attr( $option_key ) .'" selected>'. esc_attr( $option_key ) .'</option>';
            }
            echo '</select>';

            $field_name = '_pseudo';
            $field_attr = '';

          }

          // These attributes has been serialized above.
          echo '<select name="'. esc_attr( $field_name ) .'"'. $field_class . $multiple_attr . $placeholder_attr . $field_attr . $chosen_data_attr .'>';

          if ( $args['placeholder'] && empty( $args['multiple'] ) ) {
            if ( ! empty( $args['chosen'] ) ) {
              echo '<option value=""></option>';
            } else {
              echo '<option value="">'. esc_attr( $args['placeholder'] ) .'</option>';
            }
          }

          foreach ( $options as $option_key => $option ) {

            if ( is_array( $option ) && ! empty( $option ) ) {

              echo '<optgroup label="'. esc_attr( $option_key ) .'">';

              foreach ( $option as $sub_key => $sub_value ) {
                $selected = ( in_array( $sub_key, $this->value ) ) ? ' selected' : '';
                echo '<option value="'. esc_attr( $sub_key ) .'" '. esc_attr( $selected ) .'>'. esc_attr( $sub_value ) .'</option>';
              }

              echo '</optgroup>';

            } else {
              $selected = ( in_array( $option_key, $this->value ) ) ? ' selected' : '';
              echo '<option value="'. esc_attr( $option_key ) .'" '. esc_attr( $selected ) .'>'. esc_attr( $option ) .'</option>';
            }

          }

          echo '</select>';

        } else {

          echo ( ! empty( $this->field['empty_message'] ) ) ? esc_attr( $this->field['empty_message'] ) : '没有可用数据';

        }

      }

      echo $this->field_after();

    }

    public function enqueue() {

      if ( ! wp_script_is( 'jquery-ui-sortable' ) ) {
        wp_enqueue_script( 'jquery-ui-sortable' );
      }

    }

  }
}

/**
 *
 * Field: subheading
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'CSF_Field_subheading' ) ) {
  class CSF_Field_subheading extends CSF_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      echo ( ! empty( $this->field['content'] ) ) ? $this->field['content'] : '';

    }

  }
}

/**
 *
 * Field: switcher
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'CSF_Field_switcher' ) ) {
  class CSF_Field_switcher extends CSF_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $active     = ( ! empty( $this->value ) ) ? ' csf--active' : '';
      $text_on    = ( ! empty( $this->field['text_on'] ) ) ? $this->field['text_on'] : esc_html__( 'On', 'csf' );
      $text_off   = ( ! empty( $this->field['text_off'] ) ) ? $this->field['text_off'] : esc_html__( 'Off', 'csf' );
      $text_width = ( ! empty( $this->field['text_width'] ) ) ? ' style="width: '. esc_attr( $this->field['text_width'] ) .'px;"': '';

      echo $this->field_before();

      echo '<div class="csf--switcher'. esc_attr( $active ) .'"'. $text_width .'>';
      echo '<span class="csf--on">'. esc_attr( $text_on ) .'</span>';
      echo '<span class="csf--off">'. esc_attr( $text_off ) .'</span>';
      echo '<span class="csf--ball"></span>';
      echo '<input type="hidden" name="'. esc_attr( $this->field_name() ) .'" value="'. esc_attr( $this->value ) .'"'. $this->field_attributes() .' />';
      echo '</div>';

      echo ( ! empty( $this->field['label'] ) ) ? '<span class="csf--label">'. esc_attr( $this->field['label'] ) . '</span>' : '';

      echo $this->field_after();

    }

  }
}

/**
 *
 * Field: text
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'CSF_Field_text' ) ) {
  class CSF_Field_text extends CSF_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $type = ( ! empty( $this->field['attributes']['type'] ) ) ? $this->field['attributes']['type'] : 'text';

      echo $this->field_before();

      echo '<input type="'. esc_attr( $type ) .'" name="'. esc_attr( $this->field_name() ) .'" value="'. esc_attr( $this->value ) .'"'. $this->field_attributes() .' />';

      echo $this->field_after();

    }

  }
}

/**
 *
 * Field: textarea
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'CSF_Field_textarea' ) ) {
  class CSF_Field_textarea extends CSF_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      echo $this->field_before();
      echo $this->shortcoder();
      echo '<textarea name="'. esc_attr( $this->field_name() ) .'"'. $this->field_attributes() .'>'. $this->value .'</textarea>';
      echo $this->field_after();

    }

    public function shortcoder() {

      if ( ! empty( $this->field['shortcoder'] ) ) {

        $instances = ( is_array( $this->field['shortcoder'] ) ) ? $this->field['shortcoder'] : array_filter( (array) $this->field['shortcoder'] );

        foreach ( $instances as $instance_key ) {

          if ( isset( CSF::$shortcode_instances[$instance_key] ) ) {

            $button_title = CSF::$shortcode_instances[$instance_key]['button_title'];

            echo '<a href="#" class="button button-primary csf-shortcode-button" data-modal-id="'. esc_attr( $instance_key ) .'">'. $button_title .'</a>';

          }

        }

      }

    }
  }
}

/**
 *
 * Field: upload
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'CSF_Field_upload' ) ) {
  class CSF_Field_upload extends CSF_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $args = wp_parse_args( $this->field, array(
        'library'        => array(),
        'preview'        => false,
        'preview_width'  => '',
        'preview_height' => '',
        'button_title'   => '上传',
        'remove_title'   => '删除',
      ) );

      echo $this->field_before();

      $library = ( is_array( $args['library'] ) ) ? $args['library'] : array_filter( (array) $args['library'] );
      $library = ( ! empty( $library ) ) ? implode(',', $library ) : '';
      $hidden  = ( empty( $this->value ) ) ? ' hidden' : '';

      if ( ! empty( $args['preview'] ) ) {

        $preview_type   = ( ! empty( $this->value ) ) ? strtolower( substr( strrchr( $this->value, '.' ), 1 ) ) : '';
        $preview_src    = ( ! empty( $preview_type ) && in_array( $preview_type, array( 'jpg', 'jpeg', 'gif', 'png', 'svg', 'webp' ) ) ) ? $this->value : '';
        $preview_width  = ( ! empty( $args['preview_width'] ) ) ? 'max-width:'. esc_attr( $args['preview_width'] ) .'px;' : '';
        $preview_height = ( ! empty( $args['preview_height'] ) ) ? 'max-height:'. esc_attr( $args['preview_height'] ) .'px;' : '';
        $preview_style  = ( ! empty( $preview_width ) || ! empty( $preview_height ) ) ? ' style="'. esc_attr( $preview_width . $preview_height ) .'"': '';
        $preview_hidden = ( empty( $preview_src ) ) ? ' hidden' : '';

        echo '<div class="csf--preview'. esc_attr( $preview_hidden ) .'">';
        echo '<div class="csf-image-preview"'. $preview_style .'>';
        echo '<i class="csf--remove dashicons dashicons-dismiss"></i><span><img src="'. esc_url( $preview_src ) .'" class="csf--src" /></span>';
        echo '</div>';
        echo '</div>';

      }

      echo '<div class="csf--wrap">';
      echo '<input type="text" name="'. esc_attr( $this->field_name() ) .'" value="'. esc_attr( $this->value ) .'"'. $this->field_attributes() .'/>';
      echo '<a href="#" class="button button-primary csf--button" data-library="'. esc_attr( $library ) .'">'. $args['button_title'] .'</a>';
      echo '<a href="#" class="button button-secondary csf-warning-primary csf--remove'. esc_attr( $hidden ) .'">'. $args['remove_title'] .'</a>';
      echo '</div>';

      echo $this->field_after();

    }
  }
}

/**
 *
 * Field: wp_editor
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'CSF_Field_wp_editor' ) ) {
  class CSF_Field_wp_editor extends CSF_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $args = wp_parse_args( $this->field, array(
        'tinymce'       => true,
        'quicktags'     => true,
        'media_buttons' => true,
        'wpautop'       => false,
        'height'        => '',
      ) );

      $attributes = array(
        'rows'         => 10,
        'class'        => 'wp-editor-area',
        'autocomplete' => 'off',
      );

      $editor_height = ( ! empty( $args['height'] ) ) ? ' style="height:'. esc_attr( $args['height'] ) .';"' : '';

      $editor_settings  = array(
        'tinymce'       => $args['tinymce'],
        'quicktags'     => $args['quicktags'],
        'media_buttons' => $args['media_buttons'],
        'wpautop'       => $args['wpautop'],
      );

      echo $this->field_before();

      echo ( csf_wp_editor_api() ) ? '<div class="csf-wp-editor" data-editor-settings="'. esc_attr( json_encode( $editor_settings ) ) .'">' : '';

      echo '<textarea name="'. esc_attr( $this->field_name() ) .'"'. $this->field_attributes( $attributes ) . $editor_height .'>'. $this->value .'</textarea>';

      echo ( csf_wp_editor_api() ) ? '</div>' : '';

      echo $this->field_after();

    }

    public function enqueue() {

      if ( csf_wp_editor_api() && function_exists( 'wp_enqueue_editor' ) ) {

        wp_enqueue_editor();

        $this->setup_wp_editor_settings();

        add_action( 'print_default_editor_scripts', array( $this, 'setup_wp_editor_media_buttons' ) );

      }

    }

    // Setup wp editor media buttons
    public function setup_wp_editor_media_buttons() {

      if ( ! function_exists( 'media_buttons' ) ) {
        return;
      }

      ob_start();
        echo '<div class="wp-media-buttons">';
          do_action( 'media_buttons' );
        echo '</div>';
      $media_buttons = ob_get_clean();

      echo '<script type="text/javascript">';
      echo 'var csf_media_buttons = '. json_encode( $media_buttons ) .';';
      echo '</script>';

    }

    // Setup wp editor settings
    public function setup_wp_editor_settings() {

      if ( csf_wp_editor_api() && class_exists( '_WP_Editors') ) {

        $defaults = apply_filters( 'csf_wp_editor', array(
          'tinymce' => array(
            'wp_skip_init' => true
          ),
        ) );

        $setup = _WP_Editors::parse_settings( 'csf_wp_editor', $defaults );

        _WP_Editors::editor_settings( 'csf_wp_editor', $setup );

      }

    }

  }
}

/**
 *
 * Field: background
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'CSF_Field_background' ) ) {
  class CSF_Field_background extends CSF_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $args                             = wp_parse_args( $this->field, array(
        'background_color'              => true,
        'background_image'              => true,
        'background_position'           => true,
        'background_repeat'             => true,
        'background_attachment'         => true,
        'background_size'               => true,
        'background_origin'             => false,
        'background_clip'               => false,
        'background_blend_mode'         => false,
        'background_gradient'           => false,
        'background_gradient_color'     => true,
        'background_gradient_direction' => true,
        'background_image_preview'      => true,
        'background_auto_attributes'    => false,
        'compact'                       => false,
        'background_image_library'      => 'image',
        'background_image_placeholder'  => '没有可用数据',
      ) );

      if ( $args['compact'] ) {
        $args['background_color']           = false;
        $args['background_auto_attributes'] = true;
      }

      $default_value                    = array(
        'background-color'              => '',
        'background-image'              => '',
        'background-position'           => '',
        'background-repeat'             => '',
        'background-attachment'         => '',
        'background-size'               => '',
        'background-origin'             => '',
        'background-clip'               => '',
        'background-blend-mode'         => '',
        'background-gradient-color'     => '',
        'background-gradient-direction' => '',
      );

      $default_value = ( ! empty( $this->field['default'] ) ) ? wp_parse_args( $this->field['default'], $default_value ) : $default_value;

      $this->value = wp_parse_args( $this->value, $default_value );

      echo $this->field_before();

      echo '<div class="csf--background-colors">';

      //
      // Background Color
      if ( ! empty( $args['background_color'] ) ) {

        echo '<div class="csf--color">';

        echo ( ! empty( $args['background_gradient'] ) ) ? '<div class="csf--title">'. esc_html__( 'From', 'csf' ) .'</div>' : '';

        CSF::field( array(
          'id'      => 'background-color',
          'type'    => 'color',
          'default' => $default_value['background-color'],
        ), $this->value['background-color'], $this->field_name(), 'field/background' );

        echo '</div>';

      }

      //
      // Background Gradient Color
      if ( ! empty( $args['background_gradient_color'] ) && ! empty( $args['background_gradient'] ) ) {

        echo '<div class="csf--color">';

        echo ( ! empty( $args['background_gradient'] ) ) ? '<div class="csf--title">'. esc_html__( 'To', 'csf' ) .'</div>' : '';

        CSF::field( array(
          'id'      => 'background-gradient-color',
          'type'    => 'color',
          'default' => $default_value['background-gradient-color'],
        ), $this->value['background-gradient-color'], $this->field_name(), 'field/background' );

        echo '</div>';

      }

      //
      // Background Gradient Direction
      if ( ! empty( $args['background_gradient_direction'] ) && ! empty( $args['background_gradient'] ) ) {

        echo '<div class="csf--color">';

        echo ( ! empty( $args['background_gradient'] ) ) ? '<div class="csf---title">'. esc_html__( 'Direction', 'csf' ) .'</div>' : '';

        CSF::field( array(
          'id'          => 'background-gradient-direction',
          'type'        => 'select',
          'options'     => array(
            ''          => esc_html__( 'Gradient Direction', 'csf' ),
            'to bottom' => esc_html__( '&#8659; top to bottom', 'csf' ),
            'to right'  => esc_html__( '&#8658; left to right', 'csf' ),
            '135deg'    => esc_html__( '&#8664; corner top to right', 'csf' ),
            '-135deg'   => esc_html__( '&#8665; corner top to left', 'csf' ),
          ),
        ), $this->value['background-gradient-direction'], $this->field_name(), 'field/background' );

        echo '</div>';

      }

      echo '</div>';

      //
      // Background Image
      if ( ! empty( $args['background_image'] ) ) {

        echo '<div class="csf--background-image">';

        CSF::field( array(
          'id'          => 'background-image',
          'type'        => 'media',
          'class'       => 'csf-assign-field-background',
          'library'     => $args['background_image_library'],
          'preview'     => $args['background_image_preview'],
          'placeholder' => $args['background_image_placeholder'],
          'attributes'  => array( 'data-depend-id' => $this->field['id'] ),
        ), $this->value['background-image'], $this->field_name(), 'field/background' );

        echo '</div>';

      }

      $auto_class   = ( ! empty( $args['background_auto_attributes'] ) ) ? ' csf--auto-attributes' : '';
      $hidden_class = ( ! empty( $args['background_auto_attributes'] ) && empty( $this->value['background-image']['url'] ) ) ? ' csf--attributes-hidden' : '';

      echo '<div class="csf--background-attributes'. esc_attr( $auto_class . $hidden_class ) .'">';

      //
      // Background Position
      if ( ! empty( $args['background_position'] ) ) {

        CSF::field( array(
          'id'              => 'background-position',
          'type'            => 'select',
          'options'         => array(
            ''              => esc_html__( 'Background Position', 'csf' ),
            'left top'      => esc_html__( 'Left Top', 'csf' ),
            'left center'   => esc_html__( 'Left Center', 'csf' ),
            'left bottom'   => esc_html__( 'Left Bottom', 'csf' ),
            'center top'    => esc_html__( 'Center Top', 'csf' ),
            'center center' => esc_html__( 'Center Center', 'csf' ),
            'center bottom' => esc_html__( 'Center Bottom', 'csf' ),
            'right top'     => esc_html__( 'Right Top', 'csf' ),
            'right center'  => esc_html__( 'Right Center', 'csf' ),
            'right bottom'  => esc_html__( 'Right Bottom', 'csf' ),
          ),
        ), $this->value['background-position'], $this->field_name(), 'field/background' );

      }

      //
      // Background Repeat
      if ( ! empty( $args['background_repeat'] ) ) {

        CSF::field( array(
          'id'          => 'background-repeat',
          'type'        => 'select',
          'options'     => array(
            ''          => esc_html__( 'Background Repeat', 'csf' ),
            'repeat'    => esc_html__( 'Repeat', 'csf' ),
            'no-repeat' => esc_html__( 'No Repeat', 'csf' ),
            'repeat-x'  => esc_html__( 'Repeat Horizontally', 'csf' ),
            'repeat-y'  => esc_html__( 'Repeat Vertically', 'csf' ),
          ),
        ), $this->value['background-repeat'], $this->field_name(), 'field/background' );

      }

      //
      // Background Attachment
      if ( ! empty( $args['background_attachment'] ) ) {

        CSF::field( array(
          'id'       => 'background-attachment',
          'type'     => 'select',
          'options'  => array(
            ''       => esc_html__( 'Background Attachment', 'csf' ),
            'scroll' => esc_html__( 'Scroll', 'csf' ),
            'fixed'  => esc_html__( 'Fixed', 'csf' ),
          ),
        ), $this->value['background-attachment'], $this->field_name(), 'field/background' );

      }

      //
      // Background Size
      if ( ! empty( $args['background_size'] ) ) {

        CSF::field( array(
          'id'        => 'background-size',
          'type'      => 'select',
          'options'   => array(
            ''        => esc_html__( 'Background Size', 'csf' ),
            'cover'   => esc_html__( 'Cover', 'csf' ),
            'contain' => esc_html__( 'Contain', 'csf' ),
            'auto'    => esc_html__( 'Auto', 'csf' ),
          ),
        ), $this->value['background-size'], $this->field_name(), 'field/background' );

      }

      //
      // Background Origin
      if ( ! empty( $args['background_origin'] ) ) {

        CSF::field( array(
          'id'            => 'background-origin',
          'type'          => 'select',
          'options'       => array(
            ''            => esc_html__( 'Background Origin', 'csf' ),
            'padding-box' => esc_html__( 'Padding Box', 'csf' ),
            'border-box'  => esc_html__( 'Border Box', 'csf' ),
            'content-box' => esc_html__( 'Content Box', 'csf' ),
          ),
        ), $this->value['background-origin'], $this->field_name(), 'field/background' );

      }

      //
      // Background Clip
      if ( ! empty( $args['background_clip'] ) ) {

        CSF::field( array(
          'id'            => 'background-clip',
          'type'          => 'select',
          'options'       => array(
            ''            => esc_html__( 'Background Clip', 'csf' ),
            'border-box'  => esc_html__( 'Border Box', 'csf' ),
            'padding-box' => esc_html__( 'Padding Box', 'csf' ),
            'content-box' => esc_html__( 'Content Box', 'csf' ),
          ),
        ), $this->value['background-clip'], $this->field_name(), 'field/background' );

      }

      //
      // Background Blend Mode
      if ( ! empty( $args['background_blend_mode'] ) ) {

        CSF::field( array(
          'id'            => 'background-blend-mode',
          'type'          => 'select',
          'options'       => array(
            ''            => esc_html__( 'Background Blend Mode', 'csf' ),
            'normal'      => esc_html__( 'Normal', 'csf' ),
            'multiply'    => esc_html__( 'Multiply', 'csf' ),
            'screen'      => esc_html__( 'Screen', 'csf' ),
            'overlay'     => esc_html__( 'Overlay', 'csf' ),
            'darken'      => esc_html__( 'Darken', 'csf' ),
            'lighten'     => esc_html__( 'Lighten', 'csf' ),
            'color-dodge' => esc_html__( 'Color Dodge', 'csf' ),
            'saturation'  => esc_html__( 'Saturation', 'csf' ),
            'color'       => esc_html__( 'Color', 'csf' ),
            'luminosity'  => esc_html__( 'Luminosity', 'csf' ),
          ),
        ), $this->value['background-blend-mode'], $this->field_name(), 'field/background' );

      }

      echo '</div>';

      echo $this->field_after();

    }

    public function output() {

      $output    = '';
      $bg_image  = array();
      $important = ( ! empty( $this->field['output_important'] ) ) ? '!important' : '';
      $element   = ( is_array( $this->field['output'] ) ) ? join( ',', $this->field['output'] ) : $this->field['output'];

      // Background image and gradient
      $background_color        = ( ! empty( $this->value['background-color']              ) ) ? $this->value['background-color']              : '';
      $background_gd_color     = ( ! empty( $this->value['background-gradient-color']     ) ) ? $this->value['background-gradient-color']     : '';
      $background_gd_direction = ( ! empty( $this->value['background-gradient-direction'] ) ) ? $this->value['background-gradient-direction'] : '';
      $background_image        = ( ! empty( $this->value['background-image']['url']       ) ) ? $this->value['background-image']['url']       : '';


      if ( $background_color && $background_gd_color ) {
        $gd_direction   = ( $background_gd_direction ) ? $background_gd_direction .',' : '';
        $bg_image[] = 'linear-gradient('. $gd_direction . $background_color .','. $background_gd_color .')';
        unset( $this->value['background-color'] );
      }

      if ( $background_image ) {
        $bg_image[] = 'url('. $background_image .')';
      }

      if ( ! empty( $bg_image ) ) {
        $output .= 'background-image:'. implode( ',', $bg_image ) . $important .';';
      }

      // Common background properties
      $properties = array( 'color', 'position', 'repeat', 'attachment', 'size', 'origin', 'clip', 'blend-mode' );

      foreach ( $properties as $property ) {
        $property = 'background-'. $property;
        if ( ! empty( $this->value[$property] ) ) {
          $output .= $property .':'. $this->value[$property] . $important .';';
        }
      }

      if ( $output ) {
        $output = $element .'{'. $output .'}';
      }

      $this->parent->output_css .= $output;

      return $output;

    }

  }
}