<?php 
   use Elementor\Icons_Manager;
   $_rand = wp_rand(6);
?>

<?php if($settings['style'] == 'style-1'){ ?>
   <div class="tabs-contact-one__single">
      <ul class="nav tabs-contact-one__nav" id="tabs-<?php echo esc_attr($_rand) ?>" role="tablist">
         <?php
            $i = 0;
            foreach ($settings['forms_content'] as $form){
               $i++;
               $active = $i == 1 ? ' active' : '';
               echo '<li class="tabs-contact-one__nav-item" role="presentation">';
                  echo '<a class="tabs-contact-one__nav-link' . $active . '" data-bs-toggle="tab" href="#" data-bs-target="#tab-item-'. $_rand . $form['form_id'] . $i . '" role="tab">';
                     if( !empty($form['selected_icon']['value']) ){
                        Icons_Manager::render_icon($form['selected_icon'], [ 'aria-hidden' => 'true' ]); 
                     }
                     echo esc_html($form['title']);
                  echo '</a>';
               echo '</li>';
            }
         ?>
      </ul>   
      <div class="tabs-contact-one__content tab-content">
         <?php 
            $i = 0;
            foreach ($settings['forms_content'] as $form){
               $i++;
               $active = $i == 1 ? ' show active' : '';
               echo '<div class="tabs-contact-one__panel tab-pane fade ' . $active . '" id="tab-item-'. $_rand . $form['form_id'] . $i . '" tabindex="' . esc_attr($i) . '">';
                  echo do_shortcode('[contact-form-7 id="' . $form['form_id'] . '" title="' . esc_attr($form['title']) . '"]');
               echo '</div>';
            }
         ?>
      </div>
   </div>
<?php } ?>

<?php if($settings['style'] == 'style-2'){ ?>
   <div class="tabs-contact-two__single">
      <div class="tabs-contact-two__wrapper">
         <div class="tabs-contact-two__forms">
            
            <div class="align-left style-1 widget gsc-heading auto-responsive tabs-contact-two__heading">
               <div class="content-inner">
                  <?php 
                     if($settings['sub_title']){ 
                        echo '<div class="sub-title"><span class="tagline">' . esc_html($settings['sub_title']) . '</span></div>';
                     }
                     if($settings['title_text']){  
                        echo '<h2 class="title">';
                           echo '<span>' . esc_html($settings['title_text']) . '</span>';
                        echo '</h2>';
                     }
                  ?>
               </div>
            </div>
            <div class="tabs-contact-two__forms-inner">
               <ul class="nav tabs-contact-two__nav" id="tabs-<?php echo esc_attr($_rand) ?>" role="tablist">
                  <?php
                     $i = 0;
                     foreach ($settings['forms_content'] as $form){
                        $i++;
                        $active = $i == 1 ? ' active' : '';
                        echo '<li class="tabs-contact-two__nav-item" role="presentation">';
                           echo '<a class="tabs-contact-two__nav-link' . $active . '" data-bs-toggle="tab" href="#" data-bs-target="#tab-item-'. $_rand . $form['form_id'] . $i . '" role="tab">';
                              if( !empty($form['selected_icon']['value']) ){
                                 Icons_Manager::render_icon($form['selected_icon'], [ 'aria-hidden' => 'true' ]); 
                              }
                              echo esc_html($form['title']);
                           echo '</a>';
                        echo '</li>';
                     }
                  ?>
               </ul>   
               <div class="tabs-contact-two__content tab-content">
                  <?php 
                     $i = 0;
                     foreach ($settings['forms_content'] as $form){
                        $i++;
                        $active = $i == 1 ? ' show active' : '';
                        echo '<div class="tabs-contact-two__panel tab-pane fade ' . $active . '" id="tab-item-'. $_rand . $form['form_id'] . $i . '" tabindex="' . esc_attr($i) . '">';
                           echo do_shortcode('[contact-form-7 id="' . $form['form_id'] . '" title="' . esc_attr($form['title']) . '"]');
                        echo '</div>';
                     }
                  ?>
               </div>
            </div>   
         </div>
         <?php 
            if( $settings['image']['url'] ){
               echo '<div class="tabs-contact-two__image">';
                  echo '<div class="tabs-contact-two__image-inner">';
                     echo '<img src="' . esc_url($settings['image']['url']) . '" alt="' . esc_attr($settings['title_text']) . '"/>';
                  echo '</div>';
               echo '</div>';
            }
         ?> 
      </div>   
   </div>
<?php } ?>