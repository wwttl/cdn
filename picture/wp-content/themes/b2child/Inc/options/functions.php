<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Get icons from admin ajax
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'csf_get_icons' ) ) {
  function csf_get_icons() {

    $nonce = ( ! empty( $_POST[ 'nonce' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ 'nonce' ] ) ) : '';

    if ( ! wp_verify_nonce( $nonce, 'csf_icon_nonce' ) ) {
      wp_send_json_error( array( 'error' => esc_html__( 'Error: Invalid nonce verification.', 'csf' ) ) );
    }

    ob_start();

    $icon_library = ( apply_filters( 'csf_fa4', false ) ) ? 'fa4' : 'fa5';

    CSF::include_plugin_file( 'fields/icon/'. $icon_library .'-icons.php' );

    $icon_lists = apply_filters( 'csf_field_icon_add_icons', csf_get_default_icons() );

    if ( ! empty( $icon_lists ) ) {

      foreach ( $icon_lists as $list ) {

        echo ( count( $icon_lists ) >= 2 ) ? '<div class="csf-icon-title">'. esc_attr( $list['title'] ) .'</div>' : '';

        foreach ( $list['icons'] as $icon ) {
          echo '<i title="'. esc_attr( $icon ) .'" class="'. esc_attr( $icon ) .'"></i>';
        }

      }

    } else {

      echo '<div class="csf-error-text">'. esc_html__( 'No data available.', 'csf' ) .'</div>';

    }

    $content = ob_get_clean();

    wp_send_json_success( array( 'content' => $content ) );

  }
  add_action( 'wp_ajax_csf-get-icons', 'csf_get_icons' );
}

/**
 *
 * Export
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'csf_export' ) ) {
  function csf_export() {

    $nonce  = ( ! empty( $_GET[ 'nonce' ] ) ) ? sanitize_text_field( wp_unslash( $_GET[ 'nonce' ] ) ) : '';
    $unique = ( ! empty( $_GET[ 'unique' ] ) ) ? sanitize_text_field( wp_unslash( $_GET[ 'unique' ] ) ) : '';

    if ( ! wp_verify_nonce( $nonce, 'csf_backup_nonce' ) ) {
      die( esc_html__( 'Error: Invalid nonce verification.', 'csf' ) );
    }

    if ( empty( $unique ) ) {
      die( esc_html__( 'Error: Invalid key.', 'csf' ) );
    }

    // Export
    header('Content-Type: application/json');
    header('Content-disposition: attachment; filename=backup-'. gmdate( 'd-m-Y' ) .'.json');
    header('Content-Transfer-Encoding: binary');
    header('Pragma: no-cache');
    header('Expires: 0');

    echo json_encode( get_option( $unique ) );

    die();

  }
  add_action( 'wp_ajax_csf-export', 'csf_export' );
}

/**
 *
 * Import Ajax
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'csf_import_ajax' ) ) {
  function csf_import_ajax() {

    $nonce  = ( ! empty( $_POST[ 'nonce' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ 'nonce' ] ) ) : '';
    $unique = ( ! empty( $_POST[ 'unique' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ 'unique' ] ) ) : '';
    $data   = ( ! empty( $_POST[ 'data' ] ) ) ? wp_kses_post_deep( json_decode( wp_unslash( trim( $_POST[ 'data' ] ) ), true ) ) : array();

    if ( ! wp_verify_nonce( $nonce, 'csf_backup_nonce' ) ) {
      wp_send_json_error( array( 'error' => esc_html__( 'Error: Invalid nonce verification.', 'csf' ) ) );
    }

    if ( empty( $unique ) ) {
      wp_send_json_error( array( 'error' => esc_html__( 'Error: Invalid key.', 'csf' ) ) );
    }

    if ( empty( $data ) || ! is_array( $data ) ) {
      wp_send_json_error( array( 'error' => esc_html__( 'Error: The response is not a valid JSON response.', 'csf' ) ) );
    }

    // Success
    update_option( $unique, $data );

    wp_send_json_success();

  }
  add_action( 'wp_ajax_csf-import', 'csf_import_ajax' );
}

/**
 *
 * Reset Ajax
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'csf_reset_ajax' ) ) {
  function csf_reset_ajax() {

    $nonce  = ( ! empty( $_POST[ 'nonce' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ 'nonce' ] ) ) : '';
    $unique = ( ! empty( $_POST[ 'unique' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ 'unique' ] ) ) : '';

    if ( ! wp_verify_nonce( $nonce, 'csf_backup_nonce' ) ) {
      wp_send_json_error( array( 'error' => esc_html__( 'Error: Invalid nonce verification.', 'csf' ) ) );
    }

    // Success
    delete_option( $unique );

    wp_send_json_success();

  }
  add_action( 'wp_ajax_csf-reset', 'csf_reset_ajax' );
}

/**
 *
 * Chosen Ajax
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'csf_chosen_ajax' ) ) {
  function csf_chosen_ajax() {

    $nonce = ( ! empty( $_POST[ 'nonce' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ 'nonce' ] ) ) : '';
    $type  = ( ! empty( $_POST[ 'type' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ 'type' ] ) ) : '';
    $term  = ( ! empty( $_POST[ 'term' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ 'term' ] ) ) : '';
    $query = ( ! empty( $_POST[ 'query_args' ] ) ) ? wp_kses_post_deep( $_POST[ 'query_args' ] ) : array();

    if ( ! wp_verify_nonce( $nonce, 'csf_chosen_ajax_nonce' ) ) {
      wp_send_json_error( array( 'error' => esc_html__( 'Error: Invalid nonce verification.', 'csf' ) ) );
    }

    if ( empty( $type ) || empty( $term ) ) {
      wp_send_json_error( array( 'error' => esc_html__( 'Error: Invalid term ID.', 'csf' ) ) );
    }

    $capability = apply_filters( 'csf_chosen_ajax_capability', 'manage_options' );

    if ( ! current_user_can( $capability ) ) {
      wp_send_json_error( array( 'error' => esc_html__( 'Error: You do not have permission to do that.', 'csf' ) ) );
    }

    // Success
    $options = CSF_Fields::field_data( $type, $term, $query );

    wp_send_json_success( $options );

  }
  add_action( 'wp_ajax_csf-chosen', 'csf_chosen_ajax' );
}

/**
 *
 * WP Customize custom panel
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'WP_Customize_Panel_CSF' ) && class_exists( 'WP_Customize_Panel' ) ) {
  class WP_Customize_Panel_CSF extends WP_Customize_Panel {
    public $type = 'csf';
  }
}

/**
 *
 * WP Customize custom section
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'WP_Customize_Section_CSF' ) && class_exists( 'WP_Customize_Section' ) ) {
  class WP_Customize_Section_CSF extends WP_Customize_Section {
    public $type = 'csf';
  }
}

/**
 *
 * WP Customize custom control
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'WP_Customize_Control_CSF' ) && class_exists( 'WP_Customize_Control' ) ) {
  class WP_Customize_Control_CSF extends WP_Customize_Control {

    public $type   = 'csf';
    public $field  = '';
    public $unique = '';

    public function render() {

      $depend  = '';
      $visible = '';

      if ( ! empty( $this->field['dependency'] ) ) {

        $dependency      = $this->field['dependency'];
        $depend_visible  = '';
        $data_controller = '';
        $data_condition  = '';
        $data_value      = '';
        $data_global     = '';

        if ( is_array( $dependency[0] ) ) {
          $data_controller = implode( '|', array_column( $dependency, 0 ) );
          $data_condition  = implode( '|', array_column( $dependency, 1 ) );
          $data_value      = implode( '|', array_column( $dependency, 2 ) );
          $data_global     = implode( '|', array_column( $dependency, 3 ) );
          $depend_visible  = implode( '|', array_column( $dependency, 4 ) );
        } else {
          $data_controller = ( ! empty( $dependency[0] ) ) ? $dependency[0] : '';
          $data_condition  = ( ! empty( $dependency[1] ) ) ? $dependency[1] : '';
          $data_value      = ( ! empty( $dependency[2] ) ) ? $dependency[2] : '';
          $data_global     = ( ! empty( $dependency[3] ) ) ? $dependency[3] : '';
          $depend_visible  = ( ! empty( $dependency[4] ) ) ? $dependency[4] : '';
        }

        $depend .= ' data-controller="'. esc_attr( $data_controller ) .'"';
        $depend .= ' data-condition="'. esc_attr( $data_condition ) .'"';
        $depend .= ' data-value="'. esc_attr( $data_value ) .'"';
        $depend .= ( ! empty( $data_global ) ) ? ' data-depend-global="true"' : '';

        $visible  = ' csf-dependency-control';
        $visible .= ( ! empty( $depend_visible ) ) ? ' csf-depend-visible' : ' csf-depend-hidden';

      }

      $id    = 'customize-control-' . str_replace( array( '[', ']' ), array( '-', '' ), $this->id );
      $class = 'customize-control customize-control-'. $this->type . $visible;

      echo '<li id="'. esc_attr( $id ) .'" class="'. esc_attr( $class ) .'"'. $depend .'>';
      $this->render_field_content();
      echo '</li>';

    }

    public function render_field_content() {

      $complex = apply_filters( 'csf_customize_complex_fields', array(
        'accordion',
        'background',
        'border',
        'button_set',
        'checkbox',
        'color_group',
        'dimensions',
        'fieldset',
        'group',
        'image_select',
        'link',
        'link_color',
        'media',
        'palette',
        'repeater',
        'sortable',
        'sorter',
        'spacing',
        'switcher',
        'tabbed',
      ) );

      $field_id   = ( ! empty( $this->field['id'] ) ) ? $this->field['id'] : '';
      $custom     = ( ! empty( $this->field['customizer'] ) ) ? true : false;
      $is_complex = ( in_array( $this->field['type'], $complex ) ) ? true : false;
      $class      = ( $is_complex || $custom ) ? ' csf-customize-complex' : '';
      $atts       = ( $is_complex || $custom ) ? ' data-unique-id="'. esc_attr( $this->unique ) .'" data-option-id="'. esc_attr( $field_id ) .'"' : '';

      if ( ! $is_complex && ! $custom ) {
        $this->field['attributes']['data-customize-setting-link'] = $this->settings['default']->id;
      }

      $this->field['name'] = $this->settings['default']->id;

      $this->field['dependency'] = array();

      echo '<div class="csf-customize-field'. esc_attr( $class ) .'"'. $atts .'>';

      CSF::field( $this->field, $this->value(), $this->unique, 'customize' );

      echo '</div>';

    }

  }
}

/**
 *
 * Array search key & value
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'csf_array_search' ) ) {
  function csf_array_search( $array, $key, $value ) {

    $results = array();

    if ( is_array( $array ) ) {
      if ( isset( $array[$key] ) && $array[$key] == $value ) {
        $results[] = $array;
      }

      foreach ( $array as $sub_array ) {
        $results = array_merge( $results, csf_array_search( $sub_array, $key, $value ) );
      }

    }

    return $results;

  }
}

/**
 *
 * Between Microtime
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'csf_timeout' ) ) {
  function csf_timeout( $timenow, $starttime, $timeout = 30 ) {
    return ( ( $timenow - $starttime ) < $timeout ) ? true : false;
  }
}

/**
 *
 * Check for wp editor api
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'csf_wp_editor_api' ) ) {
  function csf_wp_editor_api() {
    global $wp_version;
    return version_compare( $wp_version, '4.8', '>=' );
  }
}

/**
 *
 * Sanitize
 * Replace letter a to letter b
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'csf_sanitize_replace_a_to_b' ) ) {
  function csf_sanitize_replace_a_to_b( $value ) {
    return str_replace( 'a', 'b', $value );
  }
}

/**
 *
 * Sanitize title
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'csf_sanitize_title' ) ) {
  function csf_sanitize_title( $value ) {
    return sanitize_title( $value );
  }
}

/**
 *
 * Email validate
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'csf_validate_email' ) ) {
  function csf_validate_email( $value ) {

    if ( ! filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
      return esc_html__( 'Please enter a valid email address.', 'csf' );
    }

  }
}

/**
 *
 * Numeric validate
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'csf_validate_numeric' ) ) {
  function csf_validate_numeric( $value ) {

    if ( ! is_numeric( $value ) ) {
      return esc_html__( 'Please enter a valid number.', 'csf' );
    }

  }
}

/**
 *
 * Required validate
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'csf_validate_required' ) ) {
  function csf_validate_required( $value ) {

    if ( empty( $value ) ) {
      return esc_html__( 'This field is required.', 'csf' );
    }

  }
}

/**
 *
 * URL validate
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'csf_validate_url' ) ) {
  function csf_validate_url( $value ) {

    if ( ! filter_var( $value, FILTER_VALIDATE_URL ) ) {
      return esc_html__( 'Please enter a valid URL.', 'csf' );
    }

  }
}

/**
 *
 * Email validate for Customizer
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'csf_customize_validate_email' ) ) {
  function csf_customize_validate_email( $validity, $value, $wp_customize ) {

    if ( ! sanitize_email( $value ) ) {
      $validity->add( 'required', esc_html__( 'Please enter a valid email address.', 'csf' ) );
    }

    return $validity;

  }
}

/**
 *
 * Numeric validate for Customizer
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'csf_customize_validate_numeric' ) ) {
  function csf_customize_validate_numeric( $validity, $value, $wp_customize ) {

    if ( ! is_numeric( $value ) ) {
      $validity->add( 'required', esc_html__( 'Please enter a valid number.', 'csf' ) );
    }

    return $validity;

  }
}

/**
 *
 * Required validate for Customizer
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'csf_customize_validate_required' ) ) {
  function csf_customize_validate_required( $validity, $value, $wp_customize ) {

    if ( empty( $value ) ) {
      $validity->add( 'required', esc_html__( 'This field is required.', 'csf' ) );
    }

    return $validity;

  }
}

/**
 *
 * URL validate for Customizer
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'csf_customize_validate_url' ) ) {
  function csf_customize_validate_url( $validity, $value, $wp_customize ) {

    if ( ! filter_var( $value, FILTER_VALIDATE_URL ) ) {
      $validity->add( 'required', esc_html__( 'Please enter a valid URL.', 'csf' ) );
    }

    return $validity;

  }
}

/**
 *
 * Custom Walker for Nav Menu Edit
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'CSF_Walker_Nav_Menu_Edit' ) && class_exists( 'Walker_Nav_Menu_Edit' ) ) {
  class CSF_Walker_Nav_Menu_Edit extends Walker_Nav_Menu_Edit {

    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

      $html = '';

      parent::start_el( $html, $item, $depth, $args, $id );

      ob_start();
      do_action( 'wp_nav_menu_item_custom_fields', $item->ID, $item, $depth, $args );
      $custom_fields = ob_get_clean();

      $output .= preg_replace( '/(?=<(fieldset|p)[^>]+class="[^"]*field-move)/', $custom_fields, $html );

    }

  }
}