<?php if(!defined('ABSPATH') || !defined('INBOUNDROCKET_PATH')) die('Security'); ?>

<table class="form-table scroll-box-appearance">  
  <tr valign="top">
    <td>
      <label class="ir-sb-input-label" for="ir-sb-background-color"><?php _e( 'Background color', 'inbound-rocket' ); ?></label>
      <input type="text" id="ir-sb-background-color" name="ir-sb[css][background_color]" class="ir-sb-color-field" value="<?php echo empty($option['css']['background_color']) ? false : esc_attr($option['css']['background_color']); ?>" />
      <p class="help"><?php _e( 'Choose a color', 'inbound-rocket' ); ?></p>
    </td>
    <td>
      <label class="ir-sb-input-label" for="ir-sb-text-color"><?php _e( 'Text color', 'inbound-rocket' ); ?></label>
      <input type="text" id="ir-sb-text-color" name="ir-sb[css][text_color]" class="ir-sb-color-field" value="<?php echo empty($option['css']['text_color']) ? false : esc_attr($option['css']['text_color']); ?>" />
      <p class="help"><?php _e( 'Choose a color', 'inbound-rocket' ); ?></p>
    </td>
    <td>
      <label class="ir-sb-input-label" for="ir-sb-width"><?php _e( 'Box width', 'inbound-rocket' ); ?></label>
      <input type="number" id="ir-sb-width" name="ir-sb[css][box_width]" min="0" max="3200" step="1" value="<?php echo empty($option['css']['box_width']) ? false : esc_attr($option['css']['box_width']); ?>"  />
      <p class="help"><?php _e( 'Enter value in px', 'inbound-rocket' ); ?></p>
	</td>
  </tr>
  <tr valign="top">
    <td>
      <label class="ir-sb-input-label" for="ir-sb-border-color"><?php _e( 'Border color', 'inbound-rocket' ); ?></label>
      <input type="text" id="ir-sb-border-color" name="ir-sb[css][border_color]" class="ir-sb-color-field" value="<?php echo empty($option['css']['border_color']) ? false : esc_attr($option['css']['border_color']); ?>" />
      <p class="help"><?php _e( 'Choose a color', 'inbound-rocket' ); ?></p>
    </td>
    <td>
      <label class="ir-sb-input-label" for="ir-sb-border-width"><?php _e( 'Border width', 'inbound-rocket' ); ?></label>
      <input name="ir-sb[css][border_width]" id="ir-sb-border-width" type="number" min="0" max="25" step="1" value="<?php echo empty($option['css']['border_width']) ? false : esc_attr($option['css']['border_width']); ?>" />
      <p class="help"><?php _e( 'Enter value in px', 'inbound-rocket' ); ?></p>
    </td>
    <td>
      <label class="ir-sb-input-label" for="ir-sb-border-style"><?php _e( 'Border style', 'inbound-rocket' ); ?></label>
      <select name="ir-sb[css][border_style]" id="ir-sb-border-style">
        <option value="" <?php selected( empty($option['css']['border_style']) ? false : esc_attr($option['css']['border_style']), '' ); ?>>
          <?php _e( 'Default', 'inbound-rocket' ); ?>
        </option>
        <option value="solid" <?php selected( empty($option['css']['border_style']) ? false : esc_attr($option['css']['border_style']), 'solid' ); ?>>
          <?php _e( 'Solid', 'inbound-rocket' ); ?>
        </option>
        <option value="dashed" <?php selected( empty($option['css']['border_style']) ? false : esc_attr($option['css']['border_style']), 'dashed' ); ?>>
          <?php _e( 'Dashed', 'inbound-rocket' ); ?>
        </option>
        <option value="dotted" <?php selected( empty($option['css']['border_style']) ? false : esc_attr($option['css']['border_style']), 'dotted' ); ?>>
          <?php _e( 'Dotted', 'inbound-rocket' ); ?>
        </option>
        <option value="double" <?php selected( empty($option['css']['border_style']) ? false : esc_attr($option['css']['border_style']), 'double' ); ?>>
          <?php _e( 'Double', 'inbound-rocket' ); ?>
        </option>
      </select>
      <p class="help"><?php _e( 'Select from option', 'inbound-rocket' ); ?></p>
	</td>
  </tr>
  <tr valign="top">
    <td colspan="3">
      <label class="ir-sb-label"><?php _e( 'Manual CSS', 'inbound-rocket' ); ?></label>
      <textarea id="ir-sb-manual-css" name="ir-sb[css][extra_css]" class="widefat" rows="5" placeholder=".ir-sb-<?php echo $post->ID; ?> { ... }"><?php echo empty($option['css']['extra_css']) ? false : esc_textarea( $option['css']['extra_css'] ); ?></textarea>
    </td>
  </tr>
</table>