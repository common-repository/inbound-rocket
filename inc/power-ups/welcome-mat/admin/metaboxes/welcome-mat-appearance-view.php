<?php if(!defined('ABSPATH') || !defined('INBOUNDROCKET_PATH')) die('Security'); ?>
<div class="form-table welcome-mat-appearance">  
  <div class="new-row">
    <div>
      <label class="ir-wm-input-label" for="ir-wm-main-heading-text"><?php _e( 'Main Heading', 'inbound-rocket' ); ?></label>
      <input type="text" id="ir-wm-main-heading-text" name="ir-wm[css][main_heading_text]" value="<?php echo empty($option['css']['main_heading_text']) ? '' : esc_attr($option['css']['main_heading_text']); ?>" />
    </div>
    <div class="row-inner">
      <label class="ir-wm-input-label" for="ir-wm-main-heading-font"><?php _e( 'Font', 'inbound-rocket' ); ?></label>
      <div class="font-list">
        <a class="font-link" href="javascript:void(0);" data-value="Select font">-- <?php _e( 'Select font', 'inbound-rocket' ); ?> --</a>
        <ul class="font-items">
          <li data-value="" style=""><?php _e( 'Default', 'inbound-rocket' ); ?></li>
          <li data-value="Amaranth,sans-serif:400" style="font-family: 'Amaranth',sans-serif; font-weight: 400" class="">Amaranth</li>
          <li data-value="Antic Slab,serif:400" style="font-family: 'Antic Slab',serif; font-weight: 400" class="">Antic Slab</li>
          <li data-value="Arimo,sans-serif:400" style="font-family: 'Arimo',sans-serif; font-weight: 400" class="">Arimo</li>
          <li data-value="Bad Script,cursive:400" style="font-family: 'Bad Script',cursive; font-weight: 400" class="">Bad Script</li>
          <li data-value="Baumans,cursive:400" style="font-family: 'Baumans',cursive; font-weight: 400" class="">Baumans</li>
          <li data-value="Bevan,cursive:400" style="font-family: 'Bevan',cursive; font-weight: 400">Bevan</li>
          <li data-value="Bitter,serif:400" style="font-family: 'Bitter',serif; font-weight: 400">Bitter</li>
          <li data-value="Black Ops One,cursive:400" style="font-family: 'Black Ops One',cursive; font-weight: 400">Black Ops One</li>
          <li data-value="Bowlby One SC,cursive:400" style="font-family: 'Bowlby One SC',cursive; font-weight: 400" class="">Bowlby One SC</li>
          <li data-value="Buenard,serif:400" style="font-family: 'Buenard',serif; font-weight: 400" class="">Buenard</li>
          <li data-value="Butterfly Kids,cursive:400" style="font-family: 'Butterfly Kids',cursive; font-weight: 400" class="">Butterfly Kids</li>
          <li data-value="Cabin Sketch,cursive:400" style="font-family: 'Cabin Sketch',cursive; font-weight: 400" class="">Cabin Sketch</li>
          <li data-value="Changa One,cursive:400" style="font-family: 'Changa One',cursive; font-weight: 400" class="">Changa One</li>
          <li data-value="Chewy,cursive:400" style="font-family: 'Chewy',cursive; font-weight: 400">Chewy</li>
          <li data-value="Codystar,cursive:400" style="font-family: 'Codystar',cursive; font-weight: 400">Codystar</li>
          <li data-value="Comfortaa,cursive:400" style="font-family: 'Comfortaa',cursive; font-weight: 400">Comfortaa</li>
          <li data-value="Concert One,cursive:400" style="font-family: 'Concert One',cursive; font-weight: 400">Concert One</li>
          <li data-value="Courgette,cursive:400" style="font-family: 'Courgette',cursive; font-weight: 400">Courgette</li>
          <li data-value="Crete Round,serif:400" style="font-family: 'Crete Round',serif; font-weight: 400">Crete Round</li>
          <li data-value="Damion,cursive:400" style="font-family: 'Damion',cursive; font-weight: 400">Damion</li>
          <li data-value="Dancing Script,cursive:400" style="font-family: 'Dancing Script',cursive; font-weight: 400">Dancing Script</li>
          <li data-value="Dosis,sans-serif:400" style="font-family: 'Dosis',sans-serif; font-weight: 400">Dosis</li>
          <li data-value="Droid Sans,sans-serif:400" style="font-family: 'Droid Sans',sans-serif; font-weight: 400">Droid Sans</li>
          <li data-value="Emilys Candy,cursive:400" style="font-family: 'Emilys Candy',cursive; font-weight: 400">Emilys Candy</li>
          <li data-value="Fredoka One,cursive:400" style="font-family: 'Fredoka One',cursive; font-weight: 400">Fredoka One</li>
          <li data-value="Graduate,cursive:400" style="font-family: 'Graduate',cursive; font-weight: 400">Graduate</li>
          <li data-value="Josefin Sans,sans-serif:400" style="font-family: 'Josefin Sans',sans-serif; font-weight: 400">Josefin Sans</li>
          <li data-value="Indie Flower,cursive:400" style="font-family: 'Indie Flower',cursive; font-weight: 400">Indie Flower</li>
          <li data-value="Lato,sans-serif:300,300italic,400" style="font-family: 'Lato',sans-serif; font-weight: 300,300italic,400">Lato</li>
          <li data-value="Lilita One,cursive:400" style="font-family: 'Lilita One',cursive; font-weight: 400">Lilita One</li>
          <li data-value="Lily Script One,cursive:400" style="font-family: 'Lily Script One',cursive; font-weight: 400">Lily Script One</li>
          <li data-value="Lobster Two,cursive:400" style="font-family: 'Lobster Two',cursive; font-weight: 400">Lobster Two</li>
          <li data-value="Lora,serif:400" style="font-family: 'Lora',serif; font-weight: 400">Lora</li>
          <li data-value="Lusitana,serif:400" style="font-family: 'Lusitana',serif; font-weight: 400">Lusitana</li>
          <li data-value="Maven Pro,sans-serif:400" style="font-family: 'Maven Pro',sans-serif; font-weight: 400">Maven Pro</li>
          <li data-value="Merriweather,serif:400" style="font-family: 'Merriweather',serif; font-weight: 400">Merriweather</li>
          <li data-value="Monoton,cursive:400" style="font-family: 'Monoton',cursive; font-weight: 400">Monoton</li>
          <li data-value="Montserrat,sans-serif:400,700" style="font-family: 'Montserrat',sans-serif; font-weight: 400,700" class="">Montserrat</li>
          <li data-value="Noticia Text,serif:400" style="font-family: 'Noticia Text',serif; font-weight: 400">Noticia Text</li>
          <li data-value="Nunito,sans-serif:400,700" style="font-family: 'Nunito',sans-serif; font-weight: 400,700">Nunito</li>
          <li data-value="Open Sans,sans-serif:300,400,400italic,800" style="font-family: 'Open Sans',sans-serif; font-weight: 300,400,400italic,800">Open Sans</li>
          <li data-value="Oswald,sans-serif:400" style="font-family: 'Oswald',sans-serif; font-weight: 400">Oswald</li>
          <li data-value="Parisienne,cursive:400" style="font-family: 'Parisienne',cursive; font-weight: 400">Parisienne</li>
          <li data-value="Permanent Marker,cursive:400" style="font-family: 'Permanent Marker',cursive; font-weight: 400">Permanent Marker</li>
          <li data-value="Playfair Display,serif:400" style="font-family: 'Playfair Display',serif; font-weight: 400">Playfair Display</li>
          <li data-value="PT Mono,monospace:400" style="font-family: 'PT Mono',monospace; font-weight: 400">PT Mono</li>
          <li data-value="Quando,serif:400" style="font-family: 'Quando',serif; font-weight: 400">Quando</li>
          <li data-value="Quattrocento Sans,sans-serif:400" style="font-family: 'Quattrocento Sans',sans-serif; font-weight: 400">Quattrocento Sans</li>
          <li data-value="Quicksand,sans-serif:300,400,700" style="font-family: 'Quicksand',sans-serif; font-weight: 300,400,700">Quicksand</li>
          <li data-value="Qwigley,cursive:400" style="font-family: 'Qwigley',cursive; font-weight: 400">Qwigley</li>
          <li data-value="Raleway,sans-serif:400" style="font-family: 'Raleway',sans-serif; font-weight: 400" class="">Raleway</li>
          <li data-value="Reenie Beanie,cursive:400" style="font-family: 'Reenie Beanie',cursive; font-weight: 400" class="">Reenie Beanie</li>
          <li data-value="Roboto,serif:400,700" style="font-family: 'Roboto',serif; font-weight: 400,700">Roboto</li>
          <li data-value="Rock Salt,cursive:400" style="font-family: 'Rock Salt',cursive; font-weight: 400">Rock Salt</li>
          <li data-value="Rokkitt,serif:400" style="font-family: 'Rokkitt',serif; font-weight: 400">Rokkitt</li>
          <li data-value="Rye,cursive:400" style="font-family: 'Rye',cursive; font-weight: 400">Rye</li>
          <li data-value="Sacramento,cursive:400" style="font-family: 'Sacramento',cursive; font-weight: 400">Sacramento</li>
          <li data-value="Sansita One,cursive:400" style="font-family: 'Sansita One',cursive; font-weight: 400" class="">Sansita One</li>
          <li data-value="Satisfy,cursive:400" style="font-family: 'Satisfy',cursive; font-weight: 400">Satisfy</li>
          <li data-value="Shadows Into Light Two,cursive:400" style="font-family: 'Shadows Into Light Two',cursive; font-weight: 400">Shadows Into Light Two</li>
          <li data-value="Slabo 27px,serif:400" style="font-family: 'Slabo 27px',serif; font-weight: 400">Slabo 27px</li>
          <li data-value="Source Sans Pro,sans-serif:400" style="font-family: 'Source Sans Pro',sans-serif; font-weight: 400" class="">Source Sans Pro</li>
          <li data-value="Special Elite,cursive:400" style="font-family: 'Special Elite',cursive; font-weight: 400" class="">Special Elite</li>
          <li data-value="The Girl Next Door,cursive:400" style="font-family: 'The Girl Next Door',cursive; font-weight: 400">The Girl Next Door</li>
          <li data-value="Ubuntu,sans-serif:400" style="font-family: 'Ubuntu',sans-serif; font-weight: 400">Ubuntu</li>
          <li data-value="Yanone Kaffeesatz,sans-serif:400" style="font-family: 'Yanone Kaffeesatz',sans-serif; font-weight: 400">Yanone Kaffeesatz</li>
          <li data-value="Yellowtail,cursive:400" style="font-family: 'Yellowtail',cursive; font-weight: 400">Yellowtail</li>
        </ul>
        <input name="ir-wm[css][main_heading_font]" id="ir-wm-main-heading-font" class="sel_font_name" type="hidden" value="<?php echo empty($option['css']['main_heading_font']) ? '' : esc_attr($option['css']['main_heading_font']); ?>"/>
      </div>

      <div class="font">
        <input name="ir-wm[css][main_heading_font_size]" id="ir-wm-main-heading-font-size" type="number" min="10" max="72" step="1" value="<?php echo empty($option['css']['main_heading_font_size']) ? '16' : esc_attr($option['css']['main_heading_font_size']); ?>" />
      </div>
      <div class="style">
        <div class="input checkbox main_heading_font_weight">
          <input name="ir-wm[css][main_heading_font_weight]" id="ir-wm-main-heading-font-weight" class="check_button icon" value="bold" <?php checked( empty($option['css']['main_heading_font_weight']) ? false : esc_attr($option['css']['main_heading_font_weight']), 'bold' ); ?>  type="checkbox">
          <label class="ir-wm-input-label" for="ir-wm-main-heading-font-weight">
            <i class="dashicons dashicons-editor-bold"></i> 
          </label>
        </div>
        <div class="input checkbox main_heading_font_weight">
          <input name="ir-wm[css][main_heading_font_style]" id="ir-wm-main-heading-font-style" class="check_button icon" value="italic" <?php checked( empty($option['css']['main_heading_font_style']) ? false : esc_attr($option['css']['main_heading_font_style']), 'italic' ); ?>  type="checkbox">
          <label class="ir-wm-input-label" for="ir-wm-main-heading-font-style">
            <i class="dashicons dashicons-editor-italic"></i> 
          </label>
        </div>
      </div>
      <div class="align">
        <div class="input radio main_heading_font_align">
          <input name="ir-wm[css][main_heading_font_align]" id="ir-wm-main-heading-font-alignleft" class="check_button icon" value="left" <?php checked( empty($option['css']['main_heading_font_align']) ? false : esc_attr($option['css']['main_heading_font_align']), 'left' ); ?> type="radio">
          <label class="ir-wm-input-label" for="ir-wm-main-heading-font-alignleft">
            <i class="dashicons dashicons-editor-alignleft"></i> 
          </label>
        </div>
        <div class="input radio main_heading_font_align">
          <input name="ir-wm[css][main_heading_font_align]" id="ir-wm-main-heading-font-aligncenter" class="check_button icon" value="center" <?php checked( empty($option['css']['main_heading_font_align']) ? false : esc_attr($option['css']['main_heading_font_align']), 'center' ); ?> type="radio">
          <label class="ir-wm-input-label" for="ir-wm-main-heading-font-aligncenter">
            <i class="dashicons dashicons-editor-aligncenter"></i> 
          </label>
        </div>
        <div class="input radio main_heading_font_align">
          <input name="ir-wm[css][main_heading_font_align]" id="ir-wm-main-heading-font-alignright" class="check_button icon" value="right" <?php checked( empty($option['css']['main_heading_font_align']) ? false : esc_attr($option['css']['main_heading_font_align']), 'right' ); ?> type="radio">
          <label class="ir-wm-input-label" for="ir-wm-main-heading-font-alignright">
            <i class="dashicons dashicons-editor-alignright"></i> 
          </label>
        </div>
      </div>
      <div class="editor">
        <div class="input color main_color scope-color">
          <label class="ir-wm-input-label" for="ir-wm-main-heading-font-color"><?php _e( 'Text Color', 'inbound-rocket' ); ?></label>
          <input type="text" name="ir-wm[css][main_heading_font_color]" id="ir-wm-main-heading-font-color" class="ir-wm-color-field" value="<?php echo empty($option['css']['main_heading_font_color']) ? '' : esc_attr($option['css']['main_heading_font_color']); ?>" />
        </div>
      </div>
    </div>
  </div>
    
  <div class="new-row">
    <div>
      <label class="ir-wm-input-label" for="ir-wm-sub-heading-text"><?php _e( 'Sub Heading', 'inbound-rocket' ); ?></label>
      <input type="text" id="ir-wm-sub-heading-text" name="ir-wm[css][sub_heading_text]" value="<?php echo empty($option['css']['sub_heading_text']) ? '' : esc_attr($option['css']['sub_heading_text']); ?>" />
    </div>
    <div class="row-inner">
      <label class="ir-wm-input-label" for="ir-wm-sub-heading-font"><?php _e( 'Font', 'inbound-rocket' ); ?></label>
      <div class="font-list">
        <a class="font-link" href="javascript:void(0);" data-value="Select font">-- <?php _e( 'Select font', 'inbound-rocket' ); ?> --</a>
        <ul class="font-items">
          <li data-value="" style=""><?php _e( 'Default', 'inbound-rocket' ); ?></li>
          <li data-value="Amaranth,sans-serif:400" style="font-family: 'Amaranth',sans-serif; font-weight: 400" class="">Amaranth</li>
          <li data-value="Antic Slab,serif:400" style="font-family: 'Antic Slab',serif; font-weight: 400" class="">Antic Slab</li>
          <li data-value="Arimo,sans-serif:400" style="font-family: 'Arimo',sans-serif; font-weight: 400" class="">Arimo</li>
          <li data-value="Bad Script,cursive:400" style="font-family: 'Bad Script',cursive; font-weight: 400" class="">Bad Script</li>
          <li data-value="Baumans,cursive:400" style="font-family: 'Baumans',cursive; font-weight: 400" class="">Baumans</li>
          <li data-value="Bevan,cursive:400" style="font-family: 'Bevan',cursive; font-weight: 400">Bevan</li>
          <li data-value="Bitter,serif:400" style="font-family: 'Bitter',serif; font-weight: 400">Bitter</li>
          <li data-value="Black Ops One,cursive:400" style="font-family: 'Black Ops One',cursive; font-weight: 400">Black Ops One</li>
          <li data-value="Bowlby One SC,cursive:400" style="font-family: 'Bowlby One SC',cursive; font-weight: 400" class="">Bowlby One SC</li>
          <li data-value="Buenard,serif:400" style="font-family: 'Buenard',serif; font-weight: 400" class="">Buenard</li>
          <li data-value="Butterfly Kids,cursive:400" style="font-family: 'Butterfly Kids',cursive; font-weight: 400" class="">Butterfly Kids</li>
          <li data-value="Cabin Sketch,cursive:400" style="font-family: 'Cabin Sketch',cursive; font-weight: 400" class="">Cabin Sketch</li>
          <li data-value="Changa One,cursive:400" style="font-family: 'Changa One',cursive; font-weight: 400" class="">Changa One</li>
          <li data-value="Chewy,cursive:400" style="font-family: 'Chewy',cursive; font-weight: 400">Chewy</li>
          <li data-value="Codystar,cursive:400" style="font-family: 'Codystar',cursive; font-weight: 400">Codystar</li>
          <li data-value="Comfortaa,cursive:400" style="font-family: 'Comfortaa',cursive; font-weight: 400">Comfortaa</li>
          <li data-value="Concert One,cursive:400" style="font-family: 'Concert One',cursive; font-weight: 400">Concert One</li>
          <li data-value="Courgette,cursive:400" style="font-family: 'Courgette',cursive; font-weight: 400">Courgette</li>
          <li data-value="Crete Round,serif:400" style="font-family: 'Crete Round',serif; font-weight: 400">Crete Round</li>
          <li data-value="Damion,cursive:400" style="font-family: 'Damion',cursive; font-weight: 400">Damion</li>
          <li data-value="Dancing Script,cursive:400" style="font-family: 'Dancing Script',cursive; font-weight: 400">Dancing Script</li>
          <li data-value="Dosis,sans-serif:400" style="font-family: 'Dosis',sans-serif; font-weight: 400">Dosis</li>
          <li data-value="Droid Sans,sans-serif:400" style="font-family: 'Droid Sans',sans-serif; font-weight: 400">Droid Sans</li>
          <li data-value="Emilys Candy,cursive:400" style="font-family: 'Emilys Candy',cursive; font-weight: 400">Emilys Candy</li>
          <li data-value="Fredoka One,cursive:400" style="font-family: 'Fredoka One',cursive; font-weight: 400">Fredoka One</li>
          <li data-value="Graduate,cursive:400" style="font-family: 'Graduate',cursive; font-weight: 400">Graduate</li>
          <li data-value="Josefin Sans,sans-serif:400" style="font-family: 'Josefin Sans',sans-serif; font-weight: 400">Josefin Sans</li>
          <li data-value="Indie Flower,cursive:400" style="font-family: 'Indie Flower',cursive; font-weight: 400">Indie Flower</li>
          <li data-value="Lato,sans-serif:300,300italic,400" style="font-family: 'Lato',sans-serif; font-weight: 300,300italic,400">Lato</li>
          <li data-value="Lilita One,cursive:400" style="font-family: 'Lilita One',cursive; font-weight: 400">Lilita One</li>
          <li data-value="Lily Script One,cursive:400" style="font-family: 'Lily Script One',cursive; font-weight: 400">Lily Script One</li>
          <li data-value="Lobster Two,cursive:400" style="font-family: 'Lobster Two',cursive; font-weight: 400">Lobster Two</li>
          <li data-value="Lora,serif:400" style="font-family: 'Lora',serif; font-weight: 400">Lora</li>
          <li data-value="Lusitana,serif:400" style="font-family: 'Lusitana',serif; font-weight: 400">Lusitana</li>
          <li data-value="Maven Pro,sans-serif:400" style="font-family: 'Maven Pro',sans-serif; font-weight: 400">Maven Pro</li>
          <li data-value="Merriweather,serif:400" style="font-family: 'Merriweather',serif; font-weight: 400">Merriweather</li>
          <li data-value="Monoton,cursive:400" style="font-family: 'Monoton',cursive; font-weight: 400">Monoton</li>
          <li data-value="Montserrat,sans-serif:400,700" style="font-family: 'Montserrat',sans-serif; font-weight: 400,700" class="">Montserrat</li>
          <li data-value="Noticia Text,serif:400" style="font-family: 'Noticia Text',serif; font-weight: 400">Noticia Text</li>
          <li data-value="Nunito,sans-serif:400,700" style="font-family: 'Nunito',sans-serif; font-weight: 400,700">Nunito</li>
          <li data-value="Open Sans,sans-serif:300,400,400italic,800" style="font-family: 'Open Sans',sans-serif; font-weight: 300,400,400italic,800">Open Sans</li>
          <li data-value="Oswald,sans-serif:400" style="font-family: 'Oswald',sans-serif; font-weight: 400">Oswald</li>
          <li data-value="Parisienne,cursive:400" style="font-family: 'Parisienne',cursive; font-weight: 400">Parisienne</li>
          <li data-value="Permanent Marker,cursive:400" style="font-family: 'Permanent Marker',cursive; font-weight: 400">Permanent Marker</li>
          <li data-value="Playfair Display,serif:400" style="font-family: 'Playfair Display',serif; font-weight: 400">Playfair Display</li>
          <li data-value="PT Mono,monospace:400" style="font-family: 'PT Mono',monospace; font-weight: 400">PT Mono</li>
          <li data-value="Quando,serif:400" style="font-family: 'Quando',serif; font-weight: 400">Quando</li>
          <li data-value="Quattrocento Sans,sans-serif:400" style="font-family: 'Quattrocento Sans',sans-serif; font-weight: 400">Quattrocento Sans</li>
          <li data-value="Quicksand,sans-serif:300,400,700" style="font-family: 'Quicksand',sans-serif; font-weight: 300,400,700">Quicksand</li>
          <li data-value="Qwigley,cursive:400" style="font-family: 'Qwigley',cursive; font-weight: 400">Qwigley</li>
          <li data-value="Raleway,sans-serif:400" style="font-family: 'Raleway',sans-serif; font-weight: 400" class="">Raleway</li>
          <li data-value="Reenie Beanie,cursive:400" style="font-family: 'Reenie Beanie',cursive; font-weight: 400" class="">Reenie Beanie</li>
          <li data-value="Roboto,serif:400,700" style="font-family: 'Roboto',serif; font-weight: 400,700">Roboto</li>
          <li data-value="Rock Salt,cursive:400" style="font-family: 'Rock Salt',cursive; font-weight: 400">Rock Salt</li>
          <li data-value="Rokkitt,serif:400" style="font-family: 'Rokkitt',serif; font-weight: 400">Rokkitt</li>
          <li data-value="Rye,cursive:400" style="font-family: 'Rye',cursive; font-weight: 400">Rye</li>
          <li data-value="Sacramento,cursive:400" style="font-family: 'Sacramento',cursive; font-weight: 400">Sacramento</li>
          <li data-value="Sansita One,cursive:400" style="font-family: 'Sansita One',cursive; font-weight: 400" class="">Sansita One</li>
          <li data-value="Satisfy,cursive:400" style="font-family: 'Satisfy',cursive; font-weight: 400">Satisfy</li>
          <li data-value="Shadows Into Light Two,cursive:400" style="font-family: 'Shadows Into Light Two',cursive; font-weight: 400">Shadows Into Light Two</li>
          <li data-value="Slabo 27px,serif:400" style="font-family: 'Slabo 27px',serif; font-weight: 400">Slabo 27px</li>
          <li data-value="Source Sans Pro,sans-serif:400" style="font-family: 'Source Sans Pro',sans-serif; font-weight: 400" class="">Source Sans Pro</li>
          <li data-value="Special Elite,cursive:400" style="font-family: 'Special Elite',cursive; font-weight: 400" class="">Special Elite</li>
          <li data-value="The Girl Next Door,cursive:400" style="font-family: 'The Girl Next Door',cursive; font-weight: 400">The Girl Next Door</li>
          <li data-value="Ubuntu,sans-serif:400" style="font-family: 'Ubuntu',sans-serif; font-weight: 400">Ubuntu</li>
          <li data-value="Yanone Kaffeesatz,sans-serif:400" style="font-family: 'Yanone Kaffeesatz',sans-serif; font-weight: 400">Yanone Kaffeesatz</li>
          <li data-value="Yellowtail,cursive:400" style="font-family: 'Yellowtail',cursive; font-weight: 400">Yellowtail</li>
        </ul>
        <input name="ir-wm[css][sub_heading_font]" id="ir-wm-sub-heading-font" class="sel_font_name" type="hidden" value="<?php echo empty($option['css']['sub_heading_font']) ? '' : esc_attr($option['css']['sub_heading_font']); ?>" />
      </div>
      <div class="font">
        <input name="ir-wm[css][sub_heading_font_size]" id="ir-wm-sub-heading-font-size" type="number" min="10" max="72" step="1" value="<?php echo empty($option['css']['sub_heading_font_size']) ? '16' : esc_attr($option['css']['sub_heading_font_size']); ?>" />
      </div>
      <div class="style">
        <div class="input checkbox main_heading_font_weight">
          <input name="ir-wm[css][sub_heading_font_weight]" id="ir-wm-sub-heading-font-weight" class="check_button icon" value="bold" <?php checked( empty($option['css']['sub_heading_font_weight']) ? false : esc_attr($option['css']['sub_heading_font_weight']), 'bold' ); ?> type="checkbox">
          <label class="ir-wm-input-label" for="ir-wm-sub-heading-font-weight">
            <i class="dashicons dashicons-editor-bold"></i> 
          </label>
        </div>
        <div class="input checkbox main_heading_font_weight">
          <input name="ir-wm[css][sub_heading_font_style]" id="ir-wm-sub-heading-font-style" class="check_button icon" value="italic" <?php checked( empty($option['css']['sub_heading_font_style']) ? false : esc_attr($option['css']['sub_heading_font_style']), 'italic' ); ?> type="checkbox">
          <label class="ir-wm-input-label" for="ir-wm-sub-heading-font-style">
            <i class="dashicons dashicons-editor-italic"></i> 
          </label>
        </div>
      </div>
      <div class="align">
        <div class="input radio main_heading_font_align">
          <input name="ir-wm[css][sub_heading_font_align]" id="ir-wm-sub-heading-font-alignleft" class="check_button icon" value="left" <?php checked( empty($option['css']['sub_heading_font_align']) ? false : esc_attr($option['css']['sub_heading_font_align']), 'left' ); ?> type="radio">
          <label class="ir-wm-input-label" for="ir-wm-sub-heading-font-alignleft">
            <i class="dashicons dashicons-editor-alignleft"></i> 
          </label>
        </div>
        <div class="input radio main_heading_font_align">
          <input name="ir-wm[css][sub_heading_font_align]" id="ir-wm-sub-heading-font-aligncenter" class="check_button icon" value="center" <?php checked( empty($option['css']['sub_heading_font_align']) ? false : esc_attr($option['css']['sub_heading_font_align']), 'center' ); ?> type="radio">
          <label class="ir-wm-input-label" for="ir-wm-sub-heading-font-aligncenter">
            <i class="dashicons dashicons-editor-aligncenter"></i> 
          </label>
        </div>
        <div class="input radio main_heading_font_align">
          <input name="ir-wm[css][sub_heading_font_align]" id="ir-wm-sub-heading-font-alignright" class="check_button icon" value="right" <?php checked( empty($option['css']['sub_heading_font_align']) ? false : esc_attr($option['css']['sub_heading_font_align']), 'right' ); ?> type="radio">
          <label class="ir-wm-input-label" for="ir-wm-sub-heading-font-alignright">
            <i class="dashicons dashicons-editor-alignright"></i> 
          </label>
        </div>
      </div>
      <div class="editor">
        <div class="input color main_color scope-color">
          <label class="ir-wm-input-label" for="ir-wm-sub-heading-font-color"><?php _e( 'Text Color', 'inbound-rocket' ); ?></label>
          <input type="text" name="ir-wm[css][sub_heading_font_color]" id="ir-wm-sub-heading-font-color" class="ir-wm-color-field" value="<?php echo empty($option['css']['sub_heading_font_color']) ? '' : esc_attr($option['css']['sub_heading_font_color']); ?>" />
        </div>
      </div>
    </div> 
  </div>
     
  <div class="new-row">
    <div class="editor">
      <label class="ir-wm-input-label" for="ir-wm-background-color"><?php _e( 'Background Color and/or image', 'inbound-rocket' ); ?></label>
      <input type="text" id="ir-wm-background-color" name="ir-wm[css][background_color]" class="ir-wm-color-field" value="<?php echo empty($option['css']['background_color']) ? '' : esc_attr($option['css']['background_color']); ?>" />
      <p class="help"><?php _e( 'Choose a background color', 'inbound-rocket' ); ?></p>
    </div>
    <div class="row-inner">
      <div>
        <label class="ir-wm-input-label" for="ir-wm-image"><?php _e( 'Image', 'inbound-rocket' ); ?></label>
        <input type="text" name="ir-wm[css][background_image]" id="ir_wm_background_image" style="width:70%" value="<?php echo empty($option['css']['background_image']) ? '' : esc_attr($option['css']['background_image']); ?>" />
        <input id="ir_wm_upload_image_button" type="button" value="Upload Image" />
        <p class="help"><?php _e( 'Enter an URL or upload an image for the banner. Recommended size: 1920 x 1080 or 3840 x 2160 (for retina displays)', 'inbound-rocket' ); ?></p>
      </div>
    </div>
  </div>
  <div class="new-row">
    <div>
      <label class="ir-wm-input-label" for="ir-wm-submit-button-text"><?php _e( 'Submit Button', 'inbound-rocket' ); ?></label>
      <input type="text" id="ir-wm-submit-button-text" name="ir-wm[css][submit_button_text]" value="<?php echo empty($option['css']['submit_button_text']) ? 'Submit' : esc_attr($option['css']['submit_button_text']); ?>" />
      <p class="help"><?php _e( 'Enter submit button text', 'inbound-rocket' ); ?></p>
    </div>
    <div class="row-inner">
      <label class="ir-wm-input-label" for="ir-wm-submit-button-font"><?php _e( 'Font', 'inbound-rocket' ); ?></label>
      <div class="font-list">
        <a class="font-link" href="javascript:void(0);" data-value="Select font">-- Select font --</a>
        <ul class="font-items">
          <li data-value="">Default</li>
          <li data-value="Amaranth,sans-serif:400" style="font-family: 'Amaranth',sans-serif; font-weight: 400" class="">Amaranth</li>
          <li data-value="Antic Slab,serif:400" style="font-family: 'Antic Slab',serif; font-weight: 400" class="">Antic Slab</li>
          <li data-value="Arimo,sans-serif:400" style="font-family: 'Arimo',sans-serif; font-weight: 400" class="">Arimo</li>
          <li data-value="Bad Script,cursive:400" style="font-family: 'Bad Script',cursive; font-weight: 400" class="">Bad Script</li>
          <li data-value="Baumans,cursive:400" style="font-family: 'Baumans',cursive; font-weight: 400" class="">Baumans</li>
          <li data-value="Bevan,cursive:400" style="font-family: 'Bevan',cursive; font-weight: 400">Bevan</li>
          <li data-value="Bitter,serif:400" style="font-family: 'Bitter',serif; font-weight: 400">Bitter</li>
          <li data-value="Black Ops One,cursive:400" style="font-family: 'Black Ops One',cursive; font-weight: 400">Black Ops One</li>
          <li data-value="Bowlby One SC,cursive:400" style="font-family: 'Bowlby One SC',cursive; font-weight: 400" class="">Bowlby One SC</li>
          <li data-value="Buenard,serif:400" style="font-family: 'Buenard',serif; font-weight: 400" class="">Buenard</li>
          <li data-value="Butterfly Kids,cursive:400" style="font-family: 'Butterfly Kids',cursive; font-weight: 400" class="">Butterfly Kids</li>
          <li data-value="Cabin Sketch,cursive:400" style="font-family: 'Cabin Sketch',cursive; font-weight: 400" class="">Cabin Sketch</li>
          <li data-value="Changa One,cursive:400" style="font-family: 'Changa One',cursive; font-weight: 400" class="">Changa One</li>
          <li data-value="Chewy,cursive:400" style="font-family: 'Chewy',cursive; font-weight: 400">Chewy</li>
          <li data-value="Codystar,cursive:400" style="font-family: 'Codystar',cursive; font-weight: 400">Codystar</li>
          <li data-value="Comfortaa,cursive:400" style="font-family: 'Comfortaa',cursive; font-weight: 400">Comfortaa</li>
          <li data-value="Concert One,cursive:400" style="font-family: 'Concert One',cursive; font-weight: 400">Concert One</li>
          <li data-value="Courgette,cursive:400" style="font-family: 'Courgette',cursive; font-weight: 400">Courgette</li>
          <li data-value="Crete Round,serif:400" style="font-family: 'Crete Round',serif; font-weight: 400">Crete Round</li>
          <li data-value="Damion,cursive:400" style="font-family: 'Damion',cursive; font-weight: 400">Damion</li>
          <li data-value="Dancing Script,cursive:400" style="font-family: 'Dancing Script',cursive; font-weight: 400">Dancing Script</li>
          <li data-value="Dosis,sans-serif:400" style="font-family: 'Dosis',sans-serif; font-weight: 400">Dosis</li>
          <li data-value="Droid Sans,sans-serif:400" style="font-family: 'Droid Sans',sans-serif; font-weight: 400">Droid Sans</li>
          <li data-value="Emilys Candy,cursive:400" style="font-family: 'Emilys Candy',cursive; font-weight: 400">Emilys Candy</li>
          <li data-value="Fredoka One,cursive:400" style="font-family: 'Fredoka One',cursive; font-weight: 400">Fredoka One</li>
          <li data-value="Graduate,cursive:400" style="font-family: 'Graduate',cursive; font-weight: 400">Graduate</li>
          <li data-value="Josefin Sans,sans-serif:400" style="font-family: 'Josefin Sans',sans-serif; font-weight: 400">Josefin Sans</li>
          <li data-value="Indie Flower,cursive:400" style="font-family: 'Indie Flower',cursive; font-weight: 400">Indie Flower</li>
          <li data-value="Lato,sans-serif:300,300italic,400" style="font-family: 'Lato',sans-serif; font-weight: 300,300italic,400">Lato</li>
          <li data-value="Lilita One,cursive:400" style="font-family: 'Lilita One',cursive; font-weight: 400">Lilita One</li>
          <li data-value="Lily Script One,cursive:400" style="font-family: 'Lily Script One',cursive; font-weight: 400">Lily Script One</li>
          <li data-value="Lobster Two,cursive:400" style="font-family: 'Lobster Two',cursive; font-weight: 400">Lobster Two</li>
          <li data-value="Lora,serif:400" style="font-family: 'Lora',serif; font-weight: 400">Lora</li>
          <li data-value="Lusitana,serif:400" style="font-family: 'Lusitana',serif; font-weight: 400">Lusitana</li>
          <li data-value="Maven Pro,sans-serif:400" style="font-family: 'Maven Pro',sans-serif; font-weight: 400">Maven Pro</li>
          <li data-value="Merriweather,serif:400" style="font-family: 'Merriweather',serif; font-weight: 400">Merriweather</li>
          <li data-value="Monoton,cursive:400" style="font-family: 'Monoton',cursive; font-weight: 400">Monoton</li>
          <li data-value="Montserrat,sans-serif:400,700" style="font-family: 'Montserrat',sans-serif; font-weight: 400,700" class="">Montserrat</li>
          <li data-value="Noticia Text,serif:400" style="font-family: 'Noticia Text',serif; font-weight: 400">Noticia Text</li>
          <li data-value="Nunito,sans-serif:400,700" style="font-family: 'Nunito',sans-serif; font-weight: 400,700">Nunito</li>
          <li data-value="Open Sans,sans-serif:300,400,400italic,800" style="font-family: 'Open Sans',sans-serif; font-weight: 300,400,400italic,800">Open Sans</li>
          <li data-value="Oswald,sans-serif:400" style="font-family: 'Oswald',sans-serif; font-weight: 400">Oswald</li>
          <li data-value="Parisienne,cursive:400" style="font-family: 'Parisienne',cursive; font-weight: 400">Parisienne</li>
          <li data-value="Permanent Marker,cursive:400" style="font-family: 'Permanent Marker',cursive; font-weight: 400">Permanent Marker</li>
          <li data-value="Playfair Display,serif:400" style="font-family: 'Playfair Display',serif; font-weight: 400">Playfair Display</li>
          <li data-value="PT Mono,monospace:400" style="font-family: 'PT Mono',monospace; font-weight: 400">PT Mono</li>
          <li data-value="Quando,serif:400" style="font-family: 'Quando',serif; font-weight: 400">Quando</li>
          <li data-value="Quattrocento Sans,sans-serif:400" style="font-family: 'Quattrocento Sans',sans-serif; font-weight: 400">Quattrocento Sans</li>
          <li data-value="Quicksand,sans-serif:300,400,700" style="font-family: 'Quicksand',sans-serif; font-weight: 300,400,700">Quicksand</li>
          <li data-value="Qwigley,cursive:400" style="font-family: 'Qwigley',cursive; font-weight: 400">Qwigley</li>
          <li data-value="Raleway,sans-serif:400" style="font-family: 'Raleway',sans-serif; font-weight: 400" class="">Raleway</li>
          <li data-value="Reenie Beanie,cursive:400" style="font-family: 'Reenie Beanie',cursive; font-weight: 400" class="">Reenie Beanie</li>
          <li data-value="Roboto,serif:400,700" style="font-family: 'Roboto',serif; font-weight: 400,700">Roboto</li>
          <li data-value="Rock Salt,cursive:400" style="font-family: 'Rock Salt',cursive; font-weight: 400">Rock Salt</li>
          <li data-value="Rokkitt,serif:400" style="font-family: 'Rokkitt',serif; font-weight: 400">Rokkitt</li>
          <li data-value="Rye,cursive:400" style="font-family: 'Rye',cursive; font-weight: 400">Rye</li>
          <li data-value="Sacramento,cursive:400" style="font-family: 'Sacramento',cursive; font-weight: 400">Sacramento</li>
          <li data-value="Sansita One,cursive:400" style="font-family: 'Sansita One',cursive; font-weight: 400" class="">Sansita One</li>
          <li data-value="Satisfy,cursive:400" style="font-family: 'Satisfy',cursive; font-weight: 400">Satisfy</li>
          <li data-value="Shadows Into Light Two,cursive:400" style="font-family: 'Shadows Into Light Two',cursive; font-weight: 400">Shadows Into Light Two</li>
          <li data-value="Slabo 27px,serif:400" style="font-family: 'Slabo 27px',serif; font-weight: 400">Slabo 27px</li>
          <li data-value="Source Sans Pro,sans-serif:400" style="font-family: 'Source Sans Pro',sans-serif; font-weight: 400" class="">Source Sans Pro</li>
          <li data-value="Special Elite,cursive:400" style="font-family: 'Special Elite',cursive; font-weight: 400" class="">Special Elite</li>
          <li data-value="The Girl Next Door,cursive:400" style="font-family: 'The Girl Next Door',cursive; font-weight: 400">The Girl Next Door</li>
          <li data-value="Ubuntu,sans-serif:400" style="font-family: 'Ubuntu',sans-serif; font-weight: 400">Ubuntu</li>
          <li data-value="Yanone Kaffeesatz,sans-serif:400" style="font-family: 'Yanone Kaffeesatz',sans-serif; font-weight: 400">Yanone Kaffeesatz</li>
          <li data-value="Yellowtail,cursive:400" style="font-family: 'Yellowtail',cursive; font-weight: 400">Yellowtail</li>
        </ul>
        <input name="ir-wm[css][submit_button_font]" class="sel_font_name" id="ir-wm-submit-button-font" type="hidden" value="<?php echo empty($option['css']['submit_button_font']) ? '' : esc_attr($option['css']['submit_button_font']); ?>" />
      </div>
      <div class="font">
        <input name="ir-wm[css][submit_button_font_size]" id="ir-wm-submit-button-font-size" type="number" min="10" max="72" step="1" value="<?php echo empty($option['css']['submit_button_font_size']) ? '16' : esc_attr($option['css']['submit_button_font_size']); ?>" />
      </div>
      <div class="style">
        <div class="input checkbox main_heading_font_weight">
          <input name="ir-wm[css][submit_button_font_weight]" id="ir-wm-submit-button-font-weight" class="check_button icon" value="bold" <?php checked( empty($option['css']['submit_button_font_weight']) ? false : esc_attr($option['css']['submit_button_font_weight']), 'bold' ); ?>  type="checkbox">
          <label class="ir-wm-input-label" for="ir-wm-submit-button-font-weight">
            <i class="dashicons dashicons-editor-bold"></i> 
          </label>
        </div>
        <div class="input checkbox main_heading_font_style">
          <input name="ir-wm[css][submit_button_font_style]" id="ir-wm-submit-button-font-style" class="check_button icon" value="italic" <?php checked( empty($option['css']['submit_button_font_style']) ? false : esc_attr($option['css']['submit_button_font_style']), 'italic' ); ?>  type="checkbox">
          <label class="ir-wm-input-label" for="ir-wm-submit-button-font-style">
            <i class="dashicons dashicons-editor-italic"></i> 
          </label>
        </div>
      </div>
      <div class="align">
        <div class="input radio main_heading_font_align">
          <input name="ir-wm[css][submit_button_font_align]" id="ir-wm-submit-button-font-alignleft" class="check_button icon" value="left" <?php checked( empty($option['css']['submit_button_font_align']) ? false : esc_attr($option['css']['submit_button_font_align']), 'left' ); ?>  type="radio">
          <label class="ir-wm-input-label" for="ir-wm-submit-button-font-alignleft">
            <i class="dashicons dashicons-editor-alignleft"></i> 
          </label>
        </div>
        <div class="input radio main_heading_font_align">
          <input name="ir-wm[css][submit_button_font_align]" id="ir-wm-submit-button-font-aligncenter" class="check_button icon" value="center" <?php checked( empty($option['css']['submit_button_font_align']) ? false : esc_attr($option['css']['submit_button_font_align']), 'center' ); ?> type="radio">
          <label class="ir-wm-input-label" for="ir-wm-submit-button-font-aligncenter">
            <i class="dashicons dashicons-editor-aligncenter"></i> 
          </label>
        </div>
        <div class="input radio main_heading_font_align">
          <input name="ir-wm[css][submit_button_font_align]" id="ir-wm-submit-button-font-alignright" class="check_button icon" value="right" <?php checked( empty($option['css']['submit_button_font_align']) ? false : esc_attr($option['css']['submit_button_font_align']), 'right' ); ?> type="radio">
          <label class="ir-wm-input-label" for="ir-wm-submit-button-font-alignright">
            <i class="dashicons dashicons-editor-alignright"></i> 
          </label>
        </div>
      </div>
      <div class="editor">
        <div class="input color main_color scope-color">
          <label class="ir-wm-input-label" for="ir-wm-submit-button-text-color"><?php _e( 'Text Color', 'inbound-rocket' ); ?></label>
          <input type="text" name="ir-wm[css][submit_button_text_color]" id="ir-wm-submit-button-text-color" class="ir-wm-color-field" value="<?php echo empty($option['css']['submit_button_text_color']) ? '' : esc_attr($option['css']['submit_button_text_color']); ?>" />
        </div>
        <div class="input color main_color scope-color">
          <label class="ir-wm-input-label" for="ir-wm-submit-button-text-hover-color"><?php _e( 'Text Hover', 'inbound-rocket' ); ?></label>
          <input type="text" name="ir-wm[css][submit_button_text_hover_color]" id="ir-wm-submit-button-text-hover-color" class="ir-wm-color-field" value="<?php echo empty($option['css']['submit_button_text_hover_color']) ? '' : esc_attr($option['css']['submit_button_text_hover_color']); ?>" />
        </div>
        <div class="input color main_color scope-color">
          <label class="ir-wm-input-label" for="ir-wm-submit-button-background-color"><?php _e( 'Background Color', 'inbound-rocket' ); ?></label>
          <input type="text" name="ir-wm[css][submit_button_background_color]" id="ir-wm-submit-button-background-color" class="ir-wm-color-field" value="<?php echo empty($option['css']['submit_button_background_color']) ? '' : esc_attr($option['css']['submit_button_background_color']); ?>" />
        </div>
        <div class="input color main_color scope-color">
          <label class="ir-wm-input-label" for="ir-wm-submit-button-hover-background-color"><?php _e( 'Background Hover Color', 'inbound-rocket' ); ?></label>
          <input type="text" name="ir-wm[css][submit_button_hover_background_color]" id="ir-wm-submit-button-hover-background-color" class="ir-wm-color-field" value="<?php echo empty($option['css']['submit_button_hover_background_color']) ? '' : esc_attr($option['css']['submit_button_hover_background_color']); ?>" />
        </div>
      </div>
    </div>
  </div>

  <div class="new-row">
    <div>
      <label class="ir-wm-input-label" for="ir-wm-no-thanks-button-text"><?php _e( 'No, thanks Button', 'inbound-rocket' ); ?></label>
      <input type="text" id="ir-wm-no-thanks-button-text" name="ir-wm[css][no_thanks_button_text]" value="<?php echo empty($option['css']['no_thanks_button_text']) ? 'No, Thanks' : esc_attr($option['css']['no_thanks_button_text']); ?>" />
    </div>
    <div class="row-inner">
      <label class="ir-wm-input-label" for="ir-wm-no-thanks-button-font"><?php _e( 'Font', 'inbound-rocket' ); ?></label>
      <div class="font-list">
        <a class="font-link" href="javascript:void(0);" data-value="Select font">-- <?php _e( 'Select font', 'inbound-rocket' ); ?> --</a>
        <ul class="font-items">
          <li data-value="" style=""><?php _e( 'Default', 'inbound-rocket' ); ?></li>
          <li data-value="Amaranth,sans-serif:400" style="font-family: 'Amaranth',sans-serif; font-weight: 400" class="">Amaranth</li>
          <li data-value="Antic Slab,serif:400" style="font-family: 'Antic Slab',serif; font-weight: 400" class="">Antic Slab</li>
          <li data-value="Arimo,sans-serif:400" style="font-family: 'Arimo',sans-serif; font-weight: 400" class="">Arimo</li>
          <li data-value="Bad Script,cursive:400" style="font-family: 'Bad Script',cursive; font-weight: 400" class="">Bad Script</li>
          <li data-value="Baumans,cursive:400" style="font-family: 'Baumans',cursive; font-weight: 400" class="">Baumans</li>
          <li data-value="Bevan,cursive:400" style="font-family: 'Bevan',cursive; font-weight: 400">Bevan</li>
          <li data-value="Bitter,serif:400" style="font-family: 'Bitter',serif; font-weight: 400">Bitter</li>
          <li data-value="Black Ops One,cursive:400" style="font-family: 'Black Ops One',cursive; font-weight: 400">Black Ops One</li>
          <li data-value="Bowlby One SC,cursive:400" style="font-family: 'Bowlby One SC',cursive; font-weight: 400" class="">Bowlby One SC</li>
          <li data-value="Buenard,serif:400" style="font-family: 'Buenard',serif; font-weight: 400" class="">Buenard</li>
          <li data-value="Butterfly Kids,cursive:400" style="font-family: 'Butterfly Kids',cursive; font-weight: 400" class="">Butterfly Kids</li>
          <li data-value="Cabin Sketch,cursive:400" style="font-family: 'Cabin Sketch',cursive; font-weight: 400" class="">Cabin Sketch</li>
          <li data-value="Changa One,cursive:400" style="font-family: 'Changa One',cursive; font-weight: 400" class="">Changa One</li>
          <li data-value="Chewy,cursive:400" style="font-family: 'Chewy',cursive; font-weight: 400">Chewy</li>
          <li data-value="Codystar,cursive:400" style="font-family: 'Codystar',cursive; font-weight: 400">Codystar</li>
          <li data-value="Comfortaa,cursive:400" style="font-family: 'Comfortaa',cursive; font-weight: 400">Comfortaa</li>
          <li data-value="Concert One,cursive:400" style="font-family: 'Concert One',cursive; font-weight: 400">Concert One</li>
          <li data-value="Courgette,cursive:400" style="font-family: 'Courgette',cursive; font-weight: 400">Courgette</li>
          <li data-value="Crete Round,serif:400" style="font-family: 'Crete Round',serif; font-weight: 400">Crete Round</li>
          <li data-value="Damion,cursive:400" style="font-family: 'Damion',cursive; font-weight: 400">Damion</li>
          <li data-value="Dancing Script,cursive:400" style="font-family: 'Dancing Script',cursive; font-weight: 400">Dancing Script</li>
          <li data-value="Dosis,sans-serif:400" style="font-family: 'Dosis',sans-serif; font-weight: 400">Dosis</li>
          <li data-value="Droid Sans,sans-serif:400" style="font-family: 'Droid Sans',sans-serif; font-weight: 400">Droid Sans</li>
          <li data-value="Emilys Candy,cursive:400" style="font-family: 'Emilys Candy',cursive; font-weight: 400">Emilys Candy</li>
          <li data-value="Fredoka One,cursive:400" style="font-family: 'Fredoka One',cursive; font-weight: 400">Fredoka One</li>
          <li data-value="Graduate,cursive:400" style="font-family: 'Graduate',cursive; font-weight: 400">Graduate</li>
          <li data-value="Josefin Sans,sans-serif:400" style="font-family: 'Josefin Sans',sans-serif; font-weight: 400">Josefin Sans</li>
          <li data-value="Indie Flower,cursive:400" style="font-family: 'Indie Flower',cursive; font-weight: 400">Indie Flower</li>
          <li data-value="Lato,sans-serif:300,300italic,400" style="font-family: 'Lato',sans-serif; font-weight: 300,300italic,400">Lato</li>
          <li data-value="Lilita One,cursive:400" style="font-family: 'Lilita One',cursive; font-weight: 400">Lilita One</li>
          <li data-value="Lily Script One,cursive:400" style="font-family: 'Lily Script One',cursive; font-weight: 400">Lily Script One</li>
          <li data-value="Lobster Two,cursive:400" style="font-family: 'Lobster Two',cursive; font-weight: 400">Lobster Two</li>
          <li data-value="Lora,serif:400" style="font-family: 'Lora',serif; font-weight: 400">Lora</li>
          <li data-value="Lusitana,serif:400" style="font-family: 'Lusitana',serif; font-weight: 400">Lusitana</li>
          <li data-value="Maven Pro,sans-serif:400" style="font-family: 'Maven Pro',sans-serif; font-weight: 400">Maven Pro</li>
          <li data-value="Merriweather,serif:400" style="font-family: 'Merriweather',serif; font-weight: 400">Merriweather</li>
          <li data-value="Monoton,cursive:400" style="font-family: 'Monoton',cursive; font-weight: 400">Monoton</li>
          <li data-value="Montserrat,sans-serif:400,700" style="font-family: 'Montserrat',sans-serif; font-weight: 400,700" class="">Montserrat</li>
          <li data-value="Noticia Text,serif:400" style="font-family: 'Noticia Text',serif; font-weight: 400">Noticia Text</li>
          <li data-value="Nunito,sans-serif:400,700" style="font-family: 'Nunito',sans-serif; font-weight: 400,700">Nunito</li>
          <li data-value="Open Sans,sans-serif:300,400,400italic,800" style="font-family: 'Open Sans',sans-serif; font-weight: 300,400,400italic,800">Open Sans</li>
          <li data-value="Oswald,sans-serif:400" style="font-family: 'Oswald',sans-serif; font-weight: 400">Oswald</li>
          <li data-value="Parisienne,cursive:400" style="font-family: 'Parisienne',cursive; font-weight: 400">Parisienne</li>
          <li data-value="Permanent Marker,cursive:400" style="font-family: 'Permanent Marker',cursive; font-weight: 400">Permanent Marker</li>
          <li data-value="Playfair Display,serif:400" style="font-family: 'Playfair Display',serif; font-weight: 400">Playfair Display</li>
          <li data-value="PT Mono,monospace:400" style="font-family: 'PT Mono',monospace; font-weight: 400">PT Mono</li>
          <li data-value="Quando,serif:400" style="font-family: 'Quando',serif; font-weight: 400">Quando</li>
          <li data-value="Quattrocento Sans,sans-serif:400" style="font-family: 'Quattrocento Sans',sans-serif; font-weight: 400">Quattrocento Sans</li>
          <li data-value="Quicksand,sans-serif:300,400,700" style="font-family: 'Quicksand',sans-serif; font-weight: 300,400,700">Quicksand</li>
          <li data-value="Qwigley,cursive:400" style="font-family: 'Qwigley',cursive; font-weight: 400">Qwigley</li>
          <li data-value="Raleway,sans-serif:400" style="font-family: 'Raleway',sans-serif; font-weight: 400" class="">Raleway</li>
          <li data-value="Reenie Beanie,cursive:400" style="font-family: 'Reenie Beanie',cursive; font-weight: 400" class="">Reenie Beanie</li>
          <li data-value="Roboto,serif:400,700" style="font-family: 'Roboto',serif; font-weight: 400,700">Roboto</li>
          <li data-value="Rock Salt,cursive:400" style="font-family: 'Rock Salt',cursive; font-weight: 400">Rock Salt</li>
          <li data-value="Rokkitt,serif:400" style="font-family: 'Rokkitt',serif; font-weight: 400">Rokkitt</li>
          <li data-value="Rye,cursive:400" style="font-family: 'Rye',cursive; font-weight: 400">Rye</li>
          <li data-value="Sacramento,cursive:400" style="font-family: 'Sacramento',cursive; font-weight: 400">Sacramento</li>
          <li data-value="Sansita One,cursive:400" style="font-family: 'Sansita One',cursive; font-weight: 400" class="">Sansita One</li>
          <li data-value="Satisfy,cursive:400" style="font-family: 'Satisfy',cursive; font-weight: 400">Satisfy</li>
          <li data-value="Shadows Into Light Two,cursive:400" style="font-family: 'Shadows Into Light Two',cursive; font-weight: 400">Shadows Into Light Two</li>
          <li data-value="Slabo 27px,serif:400" style="font-family: 'Slabo 27px',serif; font-weight: 400">Slabo 27px</li>
          <li data-value="Source Sans Pro,sans-serif:400" style="font-family: 'Source Sans Pro',sans-serif; font-weight: 400" class="">Source Sans Pro</li>
          <li data-value="Special Elite,cursive:400" style="font-family: 'Special Elite',cursive; font-weight: 400" class="">Special Elite</li>
          <li data-value="The Girl Next Door,cursive:400" style="font-family: 'The Girl Next Door',cursive; font-weight: 400">The Girl Next Door</li>
          <li data-value="Ubuntu,sans-serif:400" style="font-family: 'Ubuntu',sans-serif; font-weight: 400">Ubuntu</li>
          <li data-value="Yanone Kaffeesatz,sans-serif:400" style="font-family: 'Yanone Kaffeesatz',sans-serif; font-weight: 400">Yanone Kaffeesatz</li>
          <li data-value="Yellowtail,cursive:400" style="font-family: 'Yellowtail',cursive; font-weight: 400">Yellowtail</li>
        </ul>
        <input name="ir-wm[css][no_thanks_button_font]" class="sel_font_name" id="ir-wm-no-thanks-button-font" type="hidden" value="<?php echo empty($option['css']['no_thanks_button_font']) ? '' : esc_attr($option['css']['no_thanks_button_font']); ?>" />
      </div>
      <div class="font">
        <input name="ir-wm[css][no_thanks_button_font_size]" id="ir-wm-no-thanks-button-font-size" type="number" min="10" max="72" step="1" value="<?php echo empty($option['css']['no_thanks_button_font_size']) ? '16' : esc_attr($option['css']['no_thanks_button_font_size']); ?>" />
      </div>
      <div class="style">
        <div class="input checkbox no_thanks_button_font_weight">
          <input name="ir-wm[css][no_thanks_button_font_weight]" id="ir-wm-no-thanks-button-font-weight" class="check_button icon" value="bold" <?php checked( empty($option['css']['no_thanks_button_font_weight']) ? false : esc_attr($option['css']['no_thanks_button_font_weight']), 'bold' ); ?>  type="checkbox">
          <label class="ir-wm-input-label" for="ir-wm-no-thanks-button-font-weight">
            <i class="dashicons dashicons-editor-bold"></i> 
          </label>
        </div>
        <div class="input checkbox no_thanks_button_font_style">
          <input name="ir-wm[css][no_thanks_button_font_style]" id="ir-wm-no-thanks-button-font-style" class="check_button icon" value="italic" <?php checked( empty($option['css']['no_thanks_button_font_style']) ? false : esc_attr($option['css']['no_thanks_button_font_style']), 'italic' ); ?> type="checkbox">
          <label class="ir-wm-input-label" for="ir-wm-no-thanks-button-font-style">
            <i class="dashicons dashicons-editor-italic"></i> 
          </label>
        </div>
      </div>
      <div class="align">
        <div class="input radio no_thanks_button_font_align">
          <input name="ir-wm[css][no_thanks_button_font_align]" id="ir-wm-no-thanks-button-font-alignleft" class="check_button icon" value="left" <?php checked( empty($option['css']['no_thanks_button_font_align']) ? false : esc_attr($option['css']['no_thanks_button_font_align']), 'left' ); ?>  type="radio">
          <label class="ir-wm-input-label" for="ir-wm-no-thanks-button-font-alignleft">
            <i class="dashicons dashicons-editor-alignleft"></i> 
          </label>
        </div>
        <div class="input radio no_thanks_button_font_align">
          <input name="ir-wm[css][no_thanks_button_font_align]" id="ir-wm-no-thanks-button-font-aligncenter" class="check_button icon" value="center" <?php checked( empty($option['css']['no_thanks_button_font_align']) ? false : esc_attr($option['css']['no_thanks_button_font_align']), 'center' ); ?>  type="radio">
          <label class="ir-wm-input-label" for="ir-wm-no-thanks-button-font-aligncenter">
            <i class="dashicons dashicons-editor-aligncenter"></i> 
          </label>
        </div>
        <div class="input radio no_thanks_button_font_align">
          <input name="ir-wm[css][no_thanks_button_font_align]" id="ir-wm-no-thanks-button-font-alignright" class="check_button icon" value="right" <?php checked( empty($option['css']['no_thanks_button_font_align']) ? false : esc_attr($option['css']['no_thanks_button_font_align']), 'right' ); ?>  type="radio">
          <label class="ir-wm-input-label" for="ir-wm-no-thanks-button-font-alignright">
            <i class="dashicons dashicons-editor-alignright"></i> 
          </label>
        </div>
      </div>
      <div class="editor">
        <div class="input color main_color scope-color">
          <label class="ir-wm-input-label" for="ir-wm-no-thanks-button-text-color"><?php _e( 'Text Color', 'inbound-rocket' ); ?></label>
          <input type="text" name="ir-wm[css][no_thanks_button_text_color]" id="ir-wm-no-thanks-button-text-color" class="ir-wm-color-field" value="<?php echo empty($option['css']['no_thanks_button_text_color']) ? '' : esc_attr($option['css']['no_thanks_button_text_color']); ?>" />
        </div>
        <div class="input color main_color scope-color">
          <label class="ir-wm-input-label" for="ir-wm-no-thanks-button-text-hover-color"><?php _e( 'Text Hover', 'inbound-rocket' ); ?></label>
          <input type="text" name="ir-wm[css][no_thanks_button_text_hover_color]" id="ir-wm-no-thanks-button-text-hover-color" class="ir-wm-color-field" value="<?php echo empty($option['css']['no_thanks_button_text_hover_color']) ? '' : esc_attr($option['css']['no_thanks_button_text_hover_color']); ?>" />
        </div>
        <div class="input color main_color scope-color">
          <label class="ir-wm-input-label" for="ir-wm-no-thanks-button-background-color"><?php _e( 'Background Color', 'inbound-rocket' ); ?></label>
          <input type="text" name="ir-wm[css][no_thanks_button_background_color]" id="ir-wm-no-thanks-button-background-color" class="ir-wm-color-field" value="<?php echo empty($option['css']['no_thanks_button_background_color']) ? '' : esc_attr($option['css']['no_thanks_button_background_color']); ?>" />
        </div>
        <div class="input color main_color scope-color">
          <label class="ir-wm-input-label" for="ir-wm-no-thanks-button-hover-background-color"><?php _e( 'Background Hover Color', 'inbound-rocket' ); ?></label>
          <input type="text" name="ir-wm[css][no_thanks_button_hover_background_color]" id="ir-wm-no-thanks-button-hover-background-color" class="ir-wm-color-field" value="<?php echo empty($option['css']['no_thanks_button_hover_background_color']) ? '' : esc_attr($option['css']['no_thanks_button_hover_background_color']); ?>" />
        </div>
      </div>
    </div>
  </div>

  <div class="new-row">
    <div class="row-inner">
      <div class="icon-container-wrap">
          <label class="ir-wm-input-label" for="ir-wm-arrow-color"><?php _e( 'Arrow Icon', 'inbound-rocket' ); ?></label>
        <div class="icon-container">
          <label class="ir-wm-input-label icon-round icon-down" for="ir-wm-arrow-round-down">
            <input name="ir-wm[css][arrow_icon]" id="ir-wm-arrow-round-down" <?php checked( empty($option['css']['arrow_icon']) ? 'round-arrdown' : esc_attr($option['css']['arrow_icon']), 'round-arrdown' ); ?>  value="round-arrdown" type="radio"> 
          </label>
        </div>
        <div class="icon-container"> 
          <label class="ir-wm-input-label icon-square icon-down" for="ir-wm-arrow-square-down">
            <input name="ir-wm[css][arrow_icon]" id="ir-wm-arrow-square-down" <?php checked( empty($option['css']['arrow_icon']) ? false : esc_attr($option['css']['arrow_icon']), 'square-arrdow' ); ?> value="square-arrdow" type="radio">
          </label>
        </div>
        <div class="icon-container">
          <label class="ir-wm-input-label icon-round icon-cross" for="ir-wm-arrow-round-cross">
            <input name="ir-wm[css][arrow_icon]" id="ir-wm-arrow-round-cross" <?php checked( empty($option['css']['arrow_icon']) ? false : esc_attr($option['css']['arrow_icon']), 'round-cross' ); ?> value="round-cross" type="radio"> 
          </label>
        </div>
        <div class="icon-container"> 
          <label class="ir-wm-input-label icon-square icon-cross" for="ir-wm-arrow-square-cross">
            <input name="ir-wm[css][arrow_icon]" id="ir-wm-arrow-square-cross" <?php checked( empty($option['css']['arrow_icon']) ? false : esc_attr($option['css']['arrow_icon']), 'square-cross' ); ?> value="square-cross" type="radio">
          </label>
        </div>
      </div>
    </div>
    <div class="row-inner">
      <div class="editor">
        <div class="input color main_color scope-color">
          <label class="ir-wm-input-label" for="ir-wm-arrow-color"><?php _e( 'Arrow color', 'inbound-rocket' ); ?></label>
          <input type="text" name="ir-wm[css][arrow_color]" id="ir-wm-arrow-color" class="ir-wm-color-field" value="<?php echo empty($option['css']['arrow_color']) ? '#FFFFFF' : esc_attr($option['css']['arrow_color']); ?>" />
        </div>
        <div class="input color main_color scope-color">
          <label class="ir-wm-input-label" for="ir-wm-arrow-border-color"><?php _e( 'Arrow border color', 'inbound-rocket' ); ?></label>
          <input type="text" name="ir-wm[css][submit_arrow_border_color]" id="ir-wm-arrow-border-color" class="ir-wm-color-field" value="<?php echo empty($option['css']['submit_arrow_border_color']) ? '#FFFFFF' : esc_attr($option['css']['submit_arrow_border_color']); ?>" />
        </div>
      </div>
    </div>
  </div>

  <div class="new-row">
    <div>
      <label class="ir-wm-label"><?php _e( 'Manual CSS', 'inbound-rocket' ); ?></label>
      <textarea id="ir-wm-manual-css" name="ir-wm[css][extra_css]" class="widefat" rows="5" placeholder=".ir-wm-<?php echo $post->ID; ?> { ... }"><?php echo empty($option['css']['extra_css']) ? '' : esc_textarea( $option['css']['extra_css'] ); ?></textarea>
    </div>
  </div>
</div>