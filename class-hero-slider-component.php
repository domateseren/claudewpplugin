<?php

/**
 * Kreatus Hero Slider Component V3 (Full-Screen Editor)
 *
 * YENİ ÖZELLİKLER (V3):
 * - ✅ Direkt Tam Ekran Düzenleme Modu (Ara modal yok)
 * - ✅ Split View: %25 Ayarlar + %75 Canlı Önizleme
 * - ✅ Sürükle-Bırak ile Katman Konumlandırma
 * - ✅ Resize Handles ile Katman Boyutlandırma
 * - ✅ Slayt Yönetimi Tam Ekran İçinde
 * - ✅ Gradient Overlay Desteği
 * - ✅ Katman Görselleri için Hover Efektleri
 * - ✅ Katman Görselleri için Link/URL Desteği
 * - ✅ Klavye Kısayolları (Ok tuşları, Delete, Ctrl+Z)
 * - ✅ Layer Visibility Toggle
 * - ✅ Layer Lock/Unlock
 *
 * @package     Kreatus
 * @subpackage  Components
 * @author      Alparslan Yazar
 * @version     3.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class ClassHeroSliderComponent extends KreatusComponentBase {

    protected function init() {
        $this->type = 'hero-slider';
        $this->name = '🚀 Hero Slider V3 (Full-Screen)';
        $this->icon = '🎬';
        $this->description = 'Tam ekran düzenleme modlu slider. Sürükle-bırak + resize ile katman yönetimi!';

        $this->fields = array(
            'slider_data' => array(
                'type' => 'textarea',
                'label' => '🎬 Slider Verisi',
                'placeholder' => 'Tam ekran editör ile düzenle'
            ),
        );
    }

    protected function render_field_editor($field_name, $field, $value) {
        if ($field_name === 'slider_data') {
            $data = $this->decode_json_string($value);
            $slideCount = isset($data['slides']) ? count($data['slides']) : 0;
            
            $output = '<div class="kreatus-form-group">';
            $output .= '<label>' . esc_html($field['label']) . '</label>';
            $output .= '<input type="hidden" name="' . esc_attr($field_name) . '" value="' . esc_attr($value) . '" class="hsc-slider-data">';
            $output .= '<button type="button" class="kreatus-btn kreatus-btn-primary hsc-open-fullscreen-editor" ';
            $output .= 'style="width: 100%; margin-bottom: 0.5rem; background: #8B5CF6; border-color: #8B5CF6; padding: 14px; font-size: 1.1rem; font-weight: 600;">';
            $output .= '🖥️ TAM EKRAN DÜZENLEME MODUNU AÇ';
            $output .= '</button>';

            if ($slideCount > 0) {
                $output .= '<div class="current-settings"><small style="color: #10B981; font-weight: 600;">';
                $output .= '✓ ' . $slideCount . ' slayt tanımlandı</small></div>';
            } else {
                $output .= '<div class="current-settings"><small style="color: #94A3B8;">';
                $output .= 'Henüz slayt eklenmedi. Tam ekran editörü açarak başlayın!</small></div>';
            }
            $output .= '</div>';
            return $output;
        }

        return parent::render_field_editor($field_name, $field, $value);
    }

    public function render($content, $settings = array()) {
        $component_id = 'hsc-' . uniqid();
        
        $data = $this->decode_json_string($content['slider_data'] ?? '');
        $global = $data['global'] ?? array();
        $slides = $data['slides'] ?? array();

        $slider_height = $global['slider_height'] ?? '600px';
        $autoplay_speed = !empty($global['autoplay']) ? intval($global['autoplay_speed'] ?? 7000) : 0;
        $show_arrows = !empty($global['nav_arrows']);
        $show_bullets = !empty($global['nav_bullets']);
        $enable_parallax = !empty($global['mouse_parallax']);
        $parallax_amount = intval($global['parallax_amount'] ?? 30);
        $global_transition = $global['global_transition'] ?? 'fade';

        $wrapper_classes = 'hsc-slider-wrapper';
        if ($enable_parallax) {
            $wrapper_classes .= ' hsc-parallax-enabled';
        }
        if (count($slides) > 1) {
            $wrapper_classes .= ' hsc-has-multiple-slides';
        }

        ob_start();
        ?>
        <div id="<?php echo esc_attr($component_id); ?>" 
             class="<?php echo $wrapper_classes; ?>"
             style="--hsc-height: <?php echo esc_attr($slider_height); ?>; height: var(--hsc-height);"
             data-autoplay-speed="<?php echo esc_attr($autoplay_speed); ?>"
             data-parallax-amount="<?php echo esc_attr($parallax_amount); ?>"
             data-global-transition="<?php echo esc_attr($global_transition); ?>">
            
            <div class="hsc-perspective-wrap">
                <div class="hsc-slides-container">
                    <?php if (empty($slides)): ?>
                        <div class="hsc-slide active">
                            <div class="hsc-slide-background" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                            <div class="hsc-slide-layers">
                                <div class="hsc-layer" style="top: 50%; left: 50%; transform: translate(-50%, -50%); color: #fff; font-size: 24px; text-align: center; font-weight: 600;">
                                    🎬 Hero Slider V3<br>
                                    <small style="font-size: 16px; opacity: 0.9;">Admin panelden "Tam Ekran Düzenleme" butonuna tıklayarak slayt ekleyin!</small>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($slides as $index => $slide): ?>
                            <?php
                            $ken_burns_class = !empty($slide['bg_ken_burns']) ? 'hsc-ken-burns' : '';
                            $slide_transition = $slide['slide_transition'] ?? $global_transition;
                            ?>
                            <div class="hsc-slide <?php echo $index === 0 ? 'active' : ''; ?>" 
                                 data-slide-index="<?php echo $index; ?>"
                                 data-transition="<?php echo esc_attr($slide_transition); ?>">
                                
                                <?php 
                                $bg_style = '';
                                $bg_type = $slide['background_type'] ?? 'color';
                                if ($bg_type === 'color') {
                                    $bg_style = 'background-color: ' . esc_attr($slide['background_color'] ?? '#333');
                                } elseif ($bg_type === 'image') {
                                    $bg_style = 'background-image: url(' . esc_url($slide['background_image'] ?? '') . ');';
                                }
                                ?>
                                <div class="hsc-slide-background <?php echo $ken_burns_class; ?>" style="<?php echo $bg_style; ?>"></div>
                                
                                <?php if ($bg_type === 'video' && !empty($slide['background_video'])): ?>
                                    <div class="hsc-slide-video-background">
                                        <video autoplay muted loop playsinline poster="<?php echo esc_url($slide['background_image'] ?? ''); ?>">
                                            <source src="<?php echo esc_url($slide['background_video']); ?>" type="video/mp4">
                                        </video>
                                    </div>
                                <?php endif; ?>

                                <?php $this->render_particles($slide['particle_effect'] ?? 'none'); ?>

                                <?php if (!empty($slide['bg_overlay'])): ?>
                                    <?php
                                    $overlay_type = $slide['bg_overlay_type'] ?? 'color';
                                    $overlay_opacity = $slide['bg_overlay_opacity'] ?? 0.5;
                                    $overlay_style = '';

                                    if ($overlay_type === 'gradient') {
                                        $gradient_start = $slide['bg_overlay_gradient_start'] ?? '#000000';
                                        $gradient_end = $slide['bg_overlay_gradient_end'] ?? '#000000';
                                        $gradient_direction = $slide['bg_overlay_gradient_direction'] ?? 'to top';
                                        $overlay_style = "background: linear-gradient({$gradient_direction}, {$gradient_start}, {$gradient_end}); opacity: {$overlay_opacity};";
                                    } else {
                                        $overlay_color = $slide['bg_overlay_color'] ?? '#000000';
                                        $overlay_style = "background-color: {$overlay_color}; opacity: {$overlay_opacity};";
                                    }
                                    ?>
                                    <div class="hsc-slide-overlay" style="<?php echo esc_attr($overlay_style); ?>"></div>
                                <?php endif; ?>

                                <div class="hsc-slide-layers">
                                    <?php 
                                    $layers = $slide['layers'] ?? array();
                                    foreach ($layers as $layer): 
                                        if (!empty($layer['hidden'])) continue; // Gizli katmanları render etme
                                        
                                        $layer_id = 'layer-' . uniqid();
                                        $styles = $this->generate_layer_styles($layer);
                                        $anim_in = $layer['animation_in'] ?? 'fade-in';
                                        $anim_out = $layer['animation_out'] ?? 'fade-out';
                                        $delay_in = intval($layer['animation_delay_in'] ?? 0);
                                        $duration_in = intval($layer['animation_duration_in'] ?? 1000);
                                        $delay_out = intval($layer['animation_delay_out'] ?? 0);
                                        $duration_out = intval($layer['animation_duration_out'] ?? 500);
                                        
                                        $hover_class = '';
                                        if ($layer['type'] === 'image' && !empty($layer['hover_effect'])) {
                                            $hover_class = 'hsc-hover-' . esc_attr($layer['hover_effect']);
                                        }
                                    ?>
                                        <div id="<?php echo esc_attr($layer_id); ?>" 
                                             class="hsc-layer hsc-layer-<?php echo esc_attr($layer['type'] ?? 'text'); ?> <?php echo $hover_class; ?>"
                                             style="<?php echo esc_attr($styles); ?>"
                                             data-anim-in="<?php echo esc_attr($anim_in); ?>"
                                             data-delay-in="<?php echo esc_attr($delay_in); ?>"
                                             data-duration-in="<?php echo esc_attr($duration_in); ?>ms"
                                             data-anim-out="<?php echo esc_attr($anim_out); ?>"
                                             data-delay-out="<?php echo esc_attr($delay_out); ?>"
                                             data-duration-out="<?php echo esc_attr($duration_out); ?>ms">
                                            
                                            <?php if ($layer['type'] === 'text'): ?>
                                                <?php echo do_shortcode(wp_kses_post($layer['content'] ?? '')); ?>
                                                
                                            <?php elseif ($layer['type'] === 'image'): ?>
                                                <?php 
                                                $image_html = '<img src="' . esc_url($layer['image_url'] ?? '') . '" alt="' . esc_attr($layer['alt_text'] ?? 'layer') . '">';
                                                
                                                if (!empty($layer['link_url'])): 
                                                ?>
                                                    <a href="<?php echo esc_url($layer['link_url']); ?>" 
                                                       target="<?php echo !empty($layer['link_new_tab']) ? '_blank' : '_self'; ?>"
                                                       rel="noopener noreferrer"
                                                       class="hsc-layer-image-link">
                                                        <?php echo $image_html; ?>
                                                    </a>
                                                <?php else: ?>
                                                    <?php echo $image_html; ?>
                                                <?php endif; ?>
                                                
                                            <?php elseif ($layer['type'] === 'button'): ?>
                                                <a href="<?php echo esc_url($layer['link_url'] ?? '#'); ?>" 
                                                   class="hsc-layer-button"
                                                   target="<?php echo !empty($layer['link_new_tab']) ? '_blank' : '_self'; ?>"
                                                   rel="noopener noreferrer">
                                                    <?php echo esc_html($layer['content'] ?? 'Click Me'); ?>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($show_arrows && count($slides) > 1): ?>
                <button type="button" class="hsc-nav-arrow hsc-nav-prev" aria-label="Previous Slide">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" /></svg>
                </button>
                <button type="button" class="hsc-nav-arrow hsc-nav-next" aria-label="Next Slide">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" /></svg>
                </button>
            <?php endif; ?>

            <?php if ($show_bullets && count($slides) > 1): ?>
                <div class="hsc-nav-bullets">
                    <?php foreach ($slides as $index => $slide): ?>
                        <button type="button" 
                                class="hsc-nav-bullet <?php echo $index === 0 ? 'active' : ''; ?>" 
                                data-slide-to="<?php echo $index; ?>"
                                aria-label="Go to slide <?php echo $index + 1; ?>">
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
        
        <?php
        echo $this->generate_frontend_styles();
        echo $this->generate_frontend_scripts();
        return ob_get_clean();
    }
    
    private function generate_layer_styles($layer) {
        $styles = '';
        $styles .= 'top: ' . esc_attr($layer['pos_y'] ?? '10') . 'px;';
        $styles .= 'left: ' . esc_attr($layer['pos_x'] ?? '10') . 'px;';
        
        // Width ve Height desteği
        if (!empty($layer['width']) && $layer['width'] !== 'auto') {
            $styles .= 'width: ' . esc_attr($layer['width']) . 'px;';
        }
        if (!empty($layer['height']) && $layer['height'] !== 'auto') {
            $styles .= 'height: ' . esc_attr($layer['height']) . 'px;';
        }
        
        $styles .= 'z-index: ' . esc_attr($layer['z_index'] ?? '10') . ';';
        $styles .= 'transform: translate3d(0,0,0);';
        
        if ($layer['type'] === 'text' || $layer['type'] === 'button') {
            $styles .= 'font-size: ' . esc_attr($layer['font_size'] ?? '16') . 'px;';
            $styles .= 'color: ' . esc_attr($layer['color'] ?? '#000000') . ';';
            $styles .= 'font-weight: ' . esc_attr($layer['font_weight'] ?? '400') . ';';
            $styles .= 'text-align: ' . esc_attr($layer['text_align'] ?? 'left') . ';';
            $styles .= 'line-height: ' . esc_attr($layer['line_height'] ?? '1.4') . ';';
            $styles .= 'letter-spacing: ' . esc_attr($layer['letter_spacing'] ?? '0') . 'px;';
        }
        
        if ($layer['type'] === 'button') {
            $styles .= 'background-color: ' . esc_attr($layer['bg_color'] ?? '#0073aa') . ';';
            $styles .= 'padding: ' . esc_attr($layer['padding'] ?? '10px 20px') . ';';
            $styles .= 'border-radius: ' . esc_attr($layer['border_radius'] ?? '4') . 'px;';
        }
        
        return $styles;
    }

    private function render_particles($effect_type) {
        if ($effect_type === 'none' || empty($effect_type)) return;

        $output = '<div class="hsc-particles-container">';
        $count = 50;
        
        if ($effect_type === 'snow') {
            for ($i = 0; $i < $count; $i++) {
                $output .= '<div class="hsc-particle hsc-snow"></div>';
            }
        } elseif ($effect_type === 'bubbles') {
            for ($i = 0; $i < 20; $i++) {
                $output .= '<div class="hsc-particle hsc-bubble"></div>';
            }
        }
        $output .= '</div>';
        echo $output;
    }

    private function generate_frontend_styles() {
        ob_start();
        ?>
        <style>
        .hsc-slider-wrapper {
            position: relative;
            width: 100%;
            overflow: hidden;
            background: #111;
            transform: translate3d(0,0,0);
        }
        .hsc-perspective-wrap {
            width: 100%;
            height: 100%;
            perspective: 1500px;
            overflow: hidden;
        }
        .hsc-parallax-enabled .hsc-slides-container {
            transition: transform 0.1s ease-out;
            will-change: transform;
        }
        .hsc-slides-container {
            position: relative;
            width: 100%;
            height: 100%;
            transform-style: preserve-3d;
        }
        .hsc-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            visibility: hidden;
            overflow: hidden;
            transform: translate3d(0,0,0);
        }
        .hsc-slide.active {
            opacity: 1;
            visibility: visible;
            z-index: 10;
        }
        
        .hsc-slide[data-transition="fade"] {
            transition: opacity 1s ease;
        }
        .hsc-slide[data-transition="slide-left"] {
            transition: transform 1s cubic-bezier(0.45, 0.05, 0.55, 0.95);
            transform: translateX(100%);
        }
        .hsc-slide[data-transition="slide-right"] {
            transition: transform 1s cubic-bezier(0.45, 0.05, 0.55, 0.95);
            transform: translateX(-100%);
        }
        .hsc-slide[data-transition="slide-up"] {
            transition: transform 1s cubic-bezier(0.45, 0.05, 0.55, 0.95);
            transform: translateY(100%);
        }
        .hsc-slide[data-transition="slide-down"] {
            transition: transform 1s cubic-bezier(0.45, 0.05, 0.55, 0.95);
            transform: translateY(-100%);
        }
        .hsc-slide[data-transition="zoom"] {
            transition: transform 1s ease, opacity 1s ease;
            transform: scale(1.2);
            opacity: 0;
        }
        .hsc-slide.active[data-transition^="slide-"] {
            transform: translateX(0) translateY(0);
        }
        .hsc-slide.active[data-transition="zoom"] {
            transform: scale(1);
            opacity: 1;
        }

        .hsc-slide-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center center;
            z-index: 1;
        }
        .hsc-slide-video-background {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            transform: translateX(-50%) translateY(-50%);
            z-index: 2;
        }
        .hsc-slide-video-background video {
            width: 100%;
            height: auto;
        }
        .hsc-slide-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 3;
            pointer-events: none;
        }
        .hsc-slide-layers {
            position: relative;
            width: 100%;
            height: 100%;
            z-index: 5;
            max-width: 1200px;
            margin: 0 auto;
            left: 0;
            right: 0;
        }
        .hsc-layer {
            position: absolute;
            opacity: 0;
            will-change: transform, opacity;
            white-space: nowrap;
        }
        .hsc-layer-text {
            white-space: normal;
        }
        .hsc-layer-image img {
            max-width: 100%;
            height: auto;
            display: block;
        }
        .hsc-layer-image-link {
            display: inline-block;
        }
        
        .hsc-hover-zoom img {
            transition: transform 0.5s ease;
        }
        .hsc-hover-zoom:hover img {
            transform: scale(1.1);
        }
        .hsc-hover-float {
            transition: transform 0.5s ease;
        }
        .hsc-hover-float:hover {
            transform: translateY(-10px);
        }
        .hsc-hover-glow img {
            transition: filter 0.5s ease, transform 0.5s ease;
        }
        .hsc-hover-glow:hover img {
            filter: drop-shadow(0 0 20px rgba(255, 255, 255, 0.8));
            transform: scale(1.05);
        }
        .hsc-hover-rotate img {
            transition: transform 0.5s ease;
        }
        .hsc-hover-rotate:hover img {
            transform: rotate(5deg) scale(1.05);
        }
        
        .hsc-layer-button {
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .hsc-layer-button:hover {
            opacity: 0.8;
            transform: scale(1.05);
        }

        .hsc-nav-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 20;
            background: rgba(0,0,0,0.3);
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            transition: background 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.3s ease;
        }
        .hsc-has-multiple-slides:hover .hsc-nav-arrow {
            opacity: 1;
        }
        .hsc-nav-arrow:hover { background: rgba(0,0,0,0.7); }
        .hsc-nav-arrow svg { width: 24px; height: 24px; }
        .hsc-nav-prev { left: 20px; }
        .hsc-nav-next { right: 20px; }
        .hsc-nav-bullets {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 20;
            display: flex;
            gap: 10px;
            opacity: 0;
            transition: all 0.3s ease;
        }
        .hsc-has-multiple-slides:hover .hsc-nav-bullets {
            opacity: 1;
        }
        .hsc-nav-bullet {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255,255,255,0.5);
            border: none;
            padding: 0;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .hsc-nav-bullet:hover, .hsc-nav-bullet.active {
            background: white;
            transform: scale(1.2);
        }
        
        @keyframes hscKenBurns {
            0% { transform: scale(1) translate(0, 0); }
            50% { transform: scale(1.15) translate(5px, 5px); }
            100% { transform: scale(1) translate(0, 0); }
        }
        .hsc-ken-burns {
            animation: hscKenBurns 20s ease-in-out infinite;
        }

        /* Motion Animations */
        @keyframes hsc-motion-float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes hsc-motion-pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.9; }
        }
        @keyframes hsc-motion-swing {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(5deg); }
            75% { transform: rotate(-5deg); }
        }
        @keyframes hsc-motion-tilt {
            0%, 100% { transform: perspective(400px) rotateY(0deg); }
            50% { transform: perspective(400px) rotateY(10deg); }
        }
        @keyframes hsc-motion-glitch {
            0%, 100% { transform: translate(0); }
            20% { transform: translate(-2px, 2px); }
            40% { transform: translate(-2px, -2px); }
            60% { transform: translate(2px, 2px); }
            80% { transform: translate(2px, -2px); }
        }

        .hsc-particles-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 4;
            overflow: hidden;
            pointer-events: none;
        }
        .hsc-particle {
            position: absolute;
            background: white;
            border-radius: 50%;
            opacity: 0;
        }
        .hsc-snow {
            width: 5px;
            height: 5px;
            animation: hscSnowfall 10s linear infinite;
        }
        @keyframes hscSnowfall {
            0% { transform: translateY(-100%); opacity: 0; }
            10% { opacity: 0.8; }
            90% { opacity: 0.8; }
            100% { transform: translateY(100vh) translateX(50px); opacity: 0; }
        }
        .hsc-bubble {
            width: 20px;
            height: 20px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            animation: hscBubbleRise 15s ease-in-out infinite;
        }
        @keyframes hscBubbleRise {
            0% { transform: translateY(100vh) scale(0); opacity: 0; }
            10% { opacity: 0.7; transform: scale(0.5); }
            90% { opacity: 0.7; }
            100% { transform: translateY(-100%) scale(1); opacity: 0; }
        }
        
        <?php for ($i = 0; $i < 50; $i++): ?>
        .hsc-snow:nth-child(<?php echo $i; ?>) {
            left: <?php echo rand(0, 100); ?>%;
            animation-duration: <?php echo rand(8, 15); ?>s;
            animation-delay: <?php echo rand(0, 10); ?>s;
            width: <?php echo rand(2, 6); ?>px;
            height: <?php echo rand(2, 6); ?>px;
            opacity: <?php echo rand(3, 8) / 10; ?>;
        }
        <?php endfor; ?>
        <?php for ($i = 0; $i < 20; $i++): ?>
        .hsc-bubble:nth-child(<?php echo $i; ?>) {
            left: <?php echo rand(0, 100); ?>%;
            animation-duration: <?php echo rand(10, 20); ?>s;
            animation-delay: <?php echo rand(0, 15); ?>s;
            width: <?php echo rand(5, 25); ?>px;
            height: <?php echo rand(5, 25); ?>px;
        }
        <?php endfor; ?>

        .hsc-slide.active .hsc-layer {
            transition-property: opacity, transform;
        }
        
        .hsc-layer[data-anim-in="fade-in"] { transform: scale(1); }
        .hsc-layer[data-anim-in="fade-in-up"] { transform: translateY(50px); }
        .hsc-layer[data-anim-in="fade-in-down"] { transform: translateY(-50px); }
        .hsc-layer[data-anim-in="fade-in-left"] { transform: translateX(50px); }
        .hsc-layer[data-anim-in="fade-in-right"] { transform: translateX(-50px); }
        .hsc-layer[data-anim-in="slide-from-top"] { transform: translateY(-100%); }
        .hsc-layer[data-anim-in="slide-from-bottom"] { transform: translateY(100%); }
        .hsc-layer[data-anim-in="slide-from-left"] { transform: translateX(-100%); }
        .hsc-layer[data-anim-in="slide-from-right"] { transform: translateX(100%); }
        .hsc-layer[data-anim-in="scale-up"] { transform: scale(0.5); }
        .hsc-layer[data-anim-in="scale-down"] { transform: scale(1.5); }
        .hsc-layer[data-anim-in="rotate-in"] { transform: rotate(-90deg) scale(0.5); }

        .hsc-slide.active .hsc-layer[data-anim-in] {
            opacity: 1;
            transform: translate(0, 0) scale(1) rotate(0);
        }
        
        .hsc-slide.slide-out-active .hsc-layer[data-anim-out="fade-out"] { opacity: 0; }
        .hsc-slide.slide-out-active .hsc-layer[data-anim-out="fade-out-up"] { opacity: 0; transform: translateY(-50px); }
        .hsc-slide.slide-out-active .hsc-layer[data-anim-out="fade-out-down"] { opacity: 0; transform: translateY(50px); }
        .hsc-slide.slide-out-active .hsc-layer[data-anim-out="fade-out-left"] { opacity: 0; transform: translateX(-50px); }
        .hsc-slide.slide-out-active .hsc-layer[data-anim-out="fade-out-right"] { opacity: 0; transform: translateX(50px); }
        .hsc-slide.slide-out-active .hsc-layer[data-anim-out="slide-to-top"] { opacity: 0; transform: translateY(-100%); }
        .hsc-slide.slide-out-active .hsc-layer[data-anim-out="slide-to-bottom"] { opacity: 0; transform: translateY(100%); }
        .hsc-slide.slide-out-active .hsc-layer[data-anim-out="slide-to-left"] { opacity: 0; transform: translateX(-100%); }
        .hsc-slide.slide-out-active .hsc-layer[data-anim-out="slide-to-right"] { opacity: 0; transform: translateX(100%); }
        .hsc-slide.slide-out-active .hsc-layer[data-anim-out="scale-down"] { opacity: 0; transform: scale(0.5); }
        .hsc-slide.slide-out-active .hsc-layer[data-anim-out="scale-up"] { opacity: 0; transform: scale(1.5); }
        .hsc-slide.slide-out-active .hsc-layer[data-anim-out="rotate-out"] { opacity: 0; transform: rotate(90deg) scale(0.5); }

        @media (max-width: 768px) {
            .hsc-slider-wrapper { --hsc-height: 500px; }
            .hsc-nav-arrow { width: 40px; height: 40px; }
            .hsc-nav-prev { left: 10px; }
            .hsc-nav-next { right: 10px; }
            .hsc-nav-bullet { width: 10px; height: 10px; }
        }
        
        </style>
        <?php
        return ob_get_clean();
    }

    private function generate_frontend_scripts() {
        ob_start();
        ?>
        <script>
        (function() {
            function initHeroSlider(sliderWrapper) {
                const container = sliderWrapper.querySelector('.hsc-slides-container');
                const slides = Array.from(sliderWrapper.querySelectorAll('.hsc-slide'));
                const bullets = Array.from(sliderWrapper.querySelectorAll('.hsc-nav-bullet'));
                const nextBtn = sliderWrapper.querySelector('.hsc-nav-next');
                const prevBtn = sliderWrapper.querySelector('.hsc-nav-prev');
                const autoplaySpeed = parseInt(sliderWrapper.dataset.autoplaySpeed, 10);
                const enableParallax = sliderWrapper.classList.contains('hsc-parallax-enabled');
                const parallaxAmount = parseInt(sliderWrapper.dataset.parallaxAmount, 10);
                
                let currentIndex = 0;
                let autoplayInterval = null;
                let isAnimating = false;
                let touchStartX = 0;
                let touchEndX = 0;

                if (slides.length <= 1) return;

                function animateLayers(slide, direction) {
                    const layers = slide.querySelectorAll('.hsc-layer');
                    layers.forEach(layer => {
                        if (direction === 'in') {
                            const anim = layer.dataset.animIn;
                            layer.style.transition = 'none';
                            layer.style.opacity = '0';
                            
                            if (anim === 'fade-in-up') layer.style.transform = 'translateY(50px)';
                            else if (anim === 'fade-in-down') layer.style.transform = 'translateY(-50px)';
                            else if (anim === 'fade-in-left') layer.style.transform = 'translateX(50px)';
                            else if (anim === 'fade-in-right') layer.style.transform = 'translateX(-50px)';
                            else if (anim === 'slide-from-top') layer.style.transform = 'translateY(-100%)';
                            else if (anim === 'slide-from-bottom') layer.style.transform = 'translateY(100%)';
                            else if (anim === 'slide-from-left') layer.style.transform = 'translateX(-100%)';
                            else if (anim === 'slide-from-right') layer.style.transform = 'translateX(100%)';
                            else if (anim === 'scale-up') layer.style.transform = 'scale(0.5)';
                            else if (anim === 'scale-down') layer.style.transform = 'scale(1.5)';
                            else if (anim === 'rotate-in') layer.style.transform = 'rotate(-90deg) scale(0.5)';
                            else layer.style.transform = 'none';

                            setTimeout(() => {
                                layer.style.transition = `all ${layer.dataset.durationIn || '1000ms'} ease ${layer.dataset.delayIn || '0'}ms`;
                                layer.style.opacity = '1';
                                layer.style.transform = 'translate(0, 0) scale(1) rotate(0)';
                            }, 50);

                        } else {
                            const anim = layer.dataset.animOut;
                            layer.style.transition = `all ${layer.dataset.durationOut || '500ms'} ease ${layer.dataset.delayOut || '0'}ms`;

                            if (anim === 'fade-out') layer.style.opacity = '0';
                            else if (anim === 'fade-out-up') { layer.style.opacity = '0'; layer.style.transform = 'translateY(-50px)'; }
                            else if (anim === 'fade-out-down') { layer.style.opacity = '0'; layer.style.transform = 'translateY(50px)'; }
                            else if (anim === 'fade-out-left') { layer.style.opacity = '0'; layer.style.transform = 'translateX(-50px)'; }
                            else if (anim === 'fade-out-right') { layer.style.opacity = '0'; layer.style.transform = 'translateX(50px)'; }
                            else if (anim === 'slide-to-top') { layer.style.opacity = '0'; layer.style.transform = 'translateY(-100%)'; }
                            else if (anim === 'slide-to-bottom') { layer.style.opacity = '0'; layer.style.transform = 'translateY(100%)'; }
                            else if (anim === 'slide-to-left') { layer.style.opacity = '0'; layer.style.transform = 'translateX(-100%)'; }
                            else if (anim === 'slide-to-right') { layer.style.opacity = '0'; layer.style.transform = 'translateX(100%)'; }
                            else if (anim === 'scale-down') { layer.style.opacity = '0'; layer.style.transform = 'scale(0.5)'; }
                            else if (anim === 'scale-up') { layer.style.opacity = '0'; layer.style.transform = 'scale(1.5)'; }
                            else if (anim === 'rotate-out') { layer.style.opacity = '0'; layer.style.transform = 'rotate(90deg) scale(0.5)'; }
                            else layer.style.opacity = '0';
                        }
                    });
                }
                
                function goToSlide(index, direction) {
                    if (isAnimating || index === currentIndex) return;
                    isAnimating = true;

                    const oldSlide = slides[currentIndex];
                    const newSlide = slides[index];
                    
                    animateLayers(oldSlide, 'out');
                    oldSlide.classList.add('slide-out-active');

                    newSlide.classList.add('active');
                    
                    if (bullets[currentIndex]) bullets[currentIndex].classList.remove('active');
                    if (bullets[index]) bullets[index].classList.add('active');
                    
                    currentIndex = index;

                    const transitionDuration = 1000;
                    
                    setTimeout(() => {
                        oldSlide.classList.remove('active', 'slide-out-active');
                        animateLayers(newSlide, 'in');
                        
                        isAnimating = false;
                    }, transitionDuration);
                }

                function nextSlide() {
                    let nextIndex = (currentIndex + 1) % slides.length;
                    goToSlide(nextIndex, 'next');
                }

                function prevSlide() {
                    let prevIndex = (currentIndex - 1 + slides.length) % slides.length;
                    goToSlide(prevIndex, 'prev');
                }

                function startAutoplay() {
                    if (autoplaySpeed > 0) {
                        stopAutoplay();
                        autoplayInterval = setInterval(nextSlide, autoplaySpeed);
                    }
                }

                function stopAutoplay() {
                    if (autoplayInterval) {
                        clearInterval(autoplayInterval);
                    }
                }
                
                if (nextBtn) nextBtn.addEventListener('click', () => {
                    nextSlide();
                    stopAutoplay();
                    startAutoplay();
                });
                
                if (prevBtn) prevBtn.addEventListener('click', () => {
                    prevSlide();
                    stopAutoplay();
                    startAutoplay();
                });

                bullets.forEach(bullet => {
                    bullet.addEventListener('click', () => {
                        const newIndex = parseInt(bullet.dataset.slideTo, 10);
                        goToSlide(newIndex, newIndex > currentIndex ? 'next' : 'prev');
                        stopAutoplay();
                        startAutoplay();
                    });
                });
                
                sliderWrapper.addEventListener('mouseenter', stopAutoplay);
                sliderWrapper.addEventListener('mouseleave', startAutoplay);

                if (enableParallax) {
                    sliderWrapper.addEventListener('mousemove', (e) => {
                        if (isAnimating) return;
                        const rect = sliderWrapper.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const y = e.clientY - rect.top;
                        
                        const percentX = (x / rect.width - 0.5) * 2;
                        const percentY = (y / rect.height - 0.5) * 2;
                        
                        const rotateX = -percentY * (parallaxAmount / 10);
                        const rotateY = percentX * (parallaxAmount / 10);
                        
                        container.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.05)`;
                    });
                    
                    sliderWrapper.addEventListener('mouseleave', () => {
                        container.style.transform = 'rotateX(0deg) rotateY(0deg) scale(1)';
                    });
                }
                
                sliderWrapper.addEventListener('touchstart', (e) => {
                    touchStartX = e.changedTouches[0].screenX;
                }, { passive: true });
                
                sliderWrapper.addEventListener('touchend', (e) => {
                    touchEndX = e.changedTouches[0].screenX;
                    handleSwipe();
                }, { passive: true });
                
                function handleSwipe() {
                    if (isAnimating) return;
                    const deltaX = touchEndX - touchStartX;
                    if (Math.abs(deltaX) > 50) {
                        if (deltaX < 0) {
                            nextSlide();
                        } else {
                            prevSlide();
                        }
                        stopAutoplay();
                        startAutoplay();
                    }
                }

                animateLayers(slides[currentIndex], 'in');
                startAutoplay();
            }

            document.querySelectorAll('.hsc-slider-wrapper').forEach(initHeroSlider);
        })();
        </script>
        <?php
        return ob_get_clean();
    }
    
    public function render_editor($content, $settings = array()) {
        wp_enqueue_media();

        $output = '<form class="kreatus-component-form hsc-editor-form" onsubmit="return false;">';

        $output .= '<div class="csc-panel" style="border-left: 4px solid #8B5CF6;">';
        $output .= '<h3>🚀 Hero Slider V3 (Full-Screen Editor)</h3>';
        $output .= '<p style="color: #64748B; font-size: 0.9rem; margin: 0.5rem 0 1.5rem 0;">Sürükle-bırak + resize ile tam ekran düzenleme deneyimi!</p>';
        
        $output .= $this->render_field_editor('slider_data', $this->fields['slider_data'], $content['slider_data'] ?? '');
        
        $output .= '</div>';

        $output .= '<div class="kreatus-component-actions">';
        $output .= '<button type="button" class="kreatus-btn kreatus-btn-primary" onclick="kreatusUpdateComponent(this)">💾 Kaydet ve Çık</button>';
        $output .= '</div>';
        $output .= '</form>';

        $output .= $this->render_fullscreen_modal();
        $output .= $this->render_editor_styles();
        $output .= $this->render_editor_scripts();

        return $output;
    }

    private function render_fullscreen_modal() {
        ob_start();
        ?>
        <!-- TAM EKRAN DÜZENLEME MODALI -->
        <div id="hsc-fullscreen-modal" class="kreatus-modal hsc-fullscreen-modal" style="display: none;">
            <div class="hsc-fullscreen-container">
                
                <!-- SOL PANEL: Ayarlar ve Kontroller (%25) -->
                <div class="hsc-fullscreen-left">
                    <div class="hsc-fullscreen-header">
                        <div>
                            <h3 style="margin: 0;">✨ Hero Slider Editor</h3>
                            <small style="color: #64748B;">Tam Ekran Düzenleme Modu</small>
                        </div>
                        <button type="button" class="kreatus-btn kreatus-btn-danger hsc-close-fullscreen" style="padding: 8px 16px;">
                            ✕ Kapat
                        </button>
                    </div>
                    
                    <!-- Tabs -->
                    <div class="hsc-tabs">
                        <button type="button" class="hsc-tab active" data-tab="global">⚙️ Global</button>
                        <button type="button" class="hsc-tab" data-tab="slides">🎬 Slaytlar</button>
                        <button type="button" class="hsc-tab" data-tab="layers">✨ Katmanlar</button>
                    </div>
                    
                    <div class="hsc-fullscreen-content">
                        
                        <!-- GLOBAL SETTINGS TAB -->
                        <div class="hsc-tab-content active" data-tab="global">
                            <div class="hsc-section">
                                <h4>Slider Ayarları</h4>
                                <div class="hsc-form-grid">
                                    <div class="kreatus-form-group">
                                        <label>Yükseklik</label>
                                        <input type="text" id="hsc-slider-height" class="kreatus-input" value="600px" placeholder="600px veya 100vh">
                                    </div>
                                    <div class="kreatus-form-group">
                                        <label>Geçiş Efekti</label>
                                        <select id="hsc-global-transition" class="kreatus-select">
                                            <option value="fade">Fade</option>
                                            <option value="slide-left">Soldan Kay</option>
                                            <option value="slide-right">Sağdan Kay</option>
                                            <option value="slide-up">Alttan Kay</option>
                                            <option value="slide-down">Üstten Kay</option>
                                            <option value="zoom">Zoom</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="hsc-section">
                                <h4>Navigasyon</h4>
                                <div class="hsc-form-grid">
                                    <div class="kreatus-form-group">
                                        <label><input type="checkbox" id="hsc-autoplay" class="kreatus-checkbox"> Otomatik Oynat</label>
                                    </div>
                                    <div class="kreatus-form-group">
                                        <label>Hız (ms)</label>
                                        <input type="number" id="hsc-autoplay-speed" class="kreatus-input" value="7000">
                                    </div>
                                    <div class="kreatus-form-group">
                                        <label><input type="checkbox" id="hsc-nav-arrows" class="kreatus-checkbox" checked> Oklar</label>
                                    </div>
                                    <div class="kreatus-form-group">
                                        <label><input type="checkbox" id="hsc-nav-bullets" class="kreatus-checkbox" checked> Noktalar</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="hsc-section">
                                <h4>Efektler</h4>
                                <div class="hsc-form-grid">
                                    <div class="kreatus-form-group">
                                        <label><input type="checkbox" id="hsc-mouse-parallax" class="kreatus-checkbox"> Parallax</label>
                                    </div>
                                    <div class="kreatus-form-group">
                                        <label>Parallax Miktarı</label>
                                        <input type="number" id="hsc-parallax-amount" class="kreatus-input" value="30">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- SLIDES TAB -->
                        <div class="hsc-tab-content" data-tab="slides">
                            <div class="hsc-section">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                    <h4 style="margin: 0;">Slaytlar</h4>
                                    <button type="button" class="kreatus-btn kreatus-btn-primary" onclick="hscAddSlide()">+ Slayt Ekle</button>
                                </div>
                                <div id="hsc-slides-list" class="hsc-items-list"></div>
                            </div>
                            
                            <div id="hsc-slide-settings" class="hsc-section" style="display: none;">
                                <h4>Slayt Ayarları</h4>
                                <div class="hsc-form-grid">
                                    <div class="kreatus-form-group hsc-grid-full">
                                        <label>Slayt Adı</label>
                                        <input type="text" id="hsc-slide-label" class="kreatus-input" placeholder="Örn: Ana Hero">
                                    </div>
                                    
                                    <div class="kreatus-form-group">
                                        <label>Arka Plan Tipi</label>
                                        <select id="hsc-slide-bg-type" class="kreatus-select">
                                            <option value="color">Renk</option>
                                            <option value="image">Görsel</option>
                                            <option value="video">Video</option>
                                        </select>
                                    </div>
                                    
                                    <div class="kreatus-form-group">
                                        <label>BG Rengi</label>
                                        <input type="color" id="hsc-slide-bg-color" class="kreatus-color" value="#333333">
                                    </div>
                                    
                                    <div class="kreatus-form-group hsc-grid-full">
                                        <label>BG Görsel URL</label>
                                        <input type="text" id="hsc-slide-bg-image" class="kreatus-input">
                                        <button type="button" class="kreatus-btn kreatus-btn-secondary hsc-select-media" data-target="bg-image" style="width: 100%; margin-top: 5px;">Görsel Seç</button>
                                    </div>
                                    
                                    <div class="kreatus-form-group hsc-grid-full">
                                        <label>BG Video URL</label>
                                        <input type="text" id="hsc-slide-bg-video" class="kreatus-input">
                                        <button type="button" class="kreatus-btn kreatus-btn-secondary hsc-select-media" data-target="bg-video" data-media-type="video" style="width: 100%; margin-top: 5px;">Video Seç</button>
                                    </div>
                                    
                                    <div class="kreatus-form-group">
                                        <label><input type="checkbox" id="hsc-slide-overlay" class="kreatus-checkbox"> Overlay Aktif</label>
                                    </div>

                                    <div class="kreatus-form-group">
                                        <label>Overlay Opaklık</label>
                                        <input type="range" id="hsc-slide-overlay-opacity" class="kreatus-range" min="0" max="1" step="0.05" value="0.5">
                                        <span id="hsc-overlay-opacity-value">0.5</span>
                                    </div>

                                    <div class="kreatus-form-group">
                                        <label>Overlay Tipi</label>
                                        <select id="hsc-slide-overlay-type" class="kreatus-select">
                                            <option value="color">Renk</option>
                                            <option value="gradient">Gradient</option>
                                        </select>
                                    </div>

                                    <div class="kreatus-form-group hsc-grid-full" id="hsc-overlay-color-group">
                                        <label>Overlay Rengi</label>
                                        <input type="color" id="hsc-slide-overlay-color" class="kreatus-color" value="#000000">
                                    </div>

                                    <div class="kreatus-form-group" id="hsc-overlay-gradient-start-group" style="display: none;">
                                        <label>Gradient Başlangıç</label>
                                        <input type="color" id="hsc-slide-overlay-grad-start" class="kreatus-color" value="#000000">
                                    </div>

                                    <div class="kreatus-form-group" id="hsc-overlay-gradient-end-group" style="display: none;">
                                        <label>Gradient Bitiş</label>
                                        <input type="color" id="hsc-slide-overlay-grad-end" class="kreatus-color" value="#000000">
                                    </div>

                                    <div class="kreatus-form-group" id="hsc-overlay-gradient-dir-group" style="display: none;">
                                        <label>Gradient Yön</label>
                                        <select id="hsc-slide-overlay-grad-dir" class="kreatus-select">
                                            <option value="to top">Yukarı</option>
                                            <option value="to bottom">Aşağı</option>
                                            <option value="to left">Sola</option>
                                            <option value="to right">Sağa</option>
                                            <option value="to top right">Yukarı Sağ</option>
                                            <option value="to top left">Yukarı Sol</option>
                                            <option value="to bottom right">Aşağı Sağ</option>
                                            <option value="to bottom left">Aşağı Sol</option>
                                        </select>
                                    </div>
                                    
                                    <div class="kreatus-form-group">
                                        <label>Slayt Geçişi</label>
                                        <select id="hsc-slide-transition" class="kreatus-select">
                                            <option value="fade">Fade</option>
                                            <option value="slide-left">Soldan Kay</option>
                                            <option value="slide-right">Sağdan Kay</option>
                                            <option value="slide-up">Alttan Kay</option>
                                            <option value="slide-down">Üstten Kay</option>
                                            <option value="zoom">Zoom</option>
                                        </select>
                                    </div>
                                    
                                    <div class="kreatus-form-group">
                                        <label>Parçacık Efekti</label>
                                        <select id="hsc-slide-particle" class="kreatus-select">
                                            <option value="none">Yok</option>
                                            <option value="snow">Kar</option>
                                            <option value="bubbles">Baloncuk</option>
                                        </select>
                                    </div>
                                    
                                    <div class="kreatus-form-group">
                                        <label><input type="checkbox" id="hsc-slide-ken-burns" class="kreatus-checkbox"> Ken Burns</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- LAYERS TAB -->
                        <div class="hsc-tab-content" data-tab="layers">
                            <div class="hsc-section">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                    <h4 style="margin: 0;">Katmanlar</h4>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <button type="button" class="kreatus-btn kreatus-btn-secondary" onclick="hscAddLayer('text')" title="Metin Ekle">📝</button>
                                        <button type="button" class="kreatus-btn kreatus-btn-secondary" onclick="hscAddLayer('image')" title="Görsel Ekle">🖼️</button>
                                        <button type="button" class="kreatus-btn kreatus-btn-secondary" onclick="hscAddLayer('button')" title="Buton Ekle">🔘</button>
                                    </div>
                                </div>
                                <div id="hsc-layers-list" class="hsc-items-list"></div>
                            </div>
                            
                            <div id="hsc-layer-settings" class="hsc-section" style="display: none;">
                                <h4>Katman Ayarları</h4>
                                <div class="hsc-form-grid" id="hsc-layer-settings-content">
                                    <!-- Dinamik olarak doldurulacak -->
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                    <!-- Footer Actions -->
                    <div class="hsc-fullscreen-footer">
                        <button type="button" class="kreatus-btn kreatus-btn-secondary hsc-close-fullscreen" style="flex: 1;">İptal</button>
                        <button type="button" class="kreatus-btn kreatus-btn-primary" onclick="hscSaveFullscreenData()" style="flex: 2;">💾 Kaydet</button>
                    </div>
                </div>
                
                <!-- SAĞ PANEL: Canlı Önizleme (%75) -->
                <div class="hsc-fullscreen-right">
                    <div class="hsc-fullscreen-preview-header">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <h4 style="margin: 0;">🎨 Canlı Önizleme</h4>
                            <div id="hsc-current-slide-indicator" style="background: rgba(255,255,255,0.1); padding: 4px 12px; border-radius: 6px; font-size: 0.85rem;">
                                Slayt 1 / 1
                            </div>
                        </div>
                        <div class="hsc-preview-controls">
                            <button type="button" class="kreatus-btn kreatus-btn-secondary" onclick="hscPrevPreviewSlide()" style="padding: 8px 12px;">◀</button>
                            <button type="button" class="kreatus-btn kreatus-btn-secondary" onclick="hscNextPreviewSlide()" style="padding: 8px 12px;">▶</button>
                        </div>
                    </div>
                    <div class="hsc-fullscreen-preview-info">
                        <small>💡 Katmanları sürükleyerek konumlandırın • Köşelerden sürükleyerek boyutlandırın • Ok tuşları ile ince ayar (Shift = 10px) • Del ile sil</small>
                    </div>
                    <div id="hsc-fullscreen-preview" class="hsc-fullscreen-preview-container">
                        <div id="hsc-preview-slide">
                            <div class="hsc-preview-placeholder">
                                <div style="text-align: center; color: #94A3B8;">
                                    <div style="font-size: 3rem; margin-bottom: 1rem;">🎬</div>
                                    <div style="font-size: 1.2rem; font-weight: 600; margin-bottom: 0.5rem;">Slayt Eklenmedi</div>
                                    <div>Sol menüden "Slayt Ekle" butonuna tıklayarak başlayın!</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function render_editor_styles() {
        ob_start();
        ?>
        <style>
        /* TAM EKRAN MODAL */
        .hsc-fullscreen-modal {
            background: rgba(0, 0, 0, 0.95) !important;
            z-index: 100000 !important;
        }
        .hsc-fullscreen-modal .kreatus-modal-content {
            max-width: 100% !important;
            height: 100vh !important;
            margin: 0 !important;
            border-radius: 0 !important;
            padding: 0 !important;
        }
        .hsc-fullscreen-container {
            display: flex;
            height: 100vh;
            width: 100%;
        }
        
        /* SOL PANEL */
        .hsc-fullscreen-left {
            width: 25%;
            min-width: 350px;
            max-width: 450px;
            background: #F8FAFC;
            border-right: 1px solid #E2E8F0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .hsc-fullscreen-header {
            padding: 1.5rem;
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
            background: white;
        }
        .hsc-fullscreen-header h3 {
            font-size: 1.25rem;
            color: #1E293B;
        }
        
        /* TABS */
        .hsc-tabs {
            display: flex;
            background: white;
            border-bottom: 2px solid #E2E8F0;
            flex-shrink: 0;
        }
        .hsc-tab {
            flex: 1;
            padding: 1rem;
            border: none;
            background: transparent;
            color: #64748B;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border-bottom: 3px solid transparent;
        }
        .hsc-tab:hover {
            background: #F8FAFC;
            color: #475569;
        }
        .hsc-tab.active {
            color: #8B5CF6;
            border-bottom-color: #8B5CF6;
            background: #F8FAFC;
        }
        
        .hsc-fullscreen-content {
            flex-grow: 1;
            overflow-y: auto;
            padding: 1.5rem;
        }
        
        .hsc-tab-content {
            display: none;
        }
        .hsc-tab-content.active {
            display: block;
        }
        
        .hsc-section {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .hsc-section h4 {
            margin: 0 0 1rem 0;
            color: #1E293B;
            font-size: 1rem;
            font-weight: 600;
        }
        
        .hsc-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .hsc-form-grid .hsc-grid-full {
            grid-column: 1 / -1;
        }
        
        .hsc-items-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            max-height: 300px;
            overflow-y: auto;
        }
        .hsc-item {
            background: white;
            border: 2px solid #E2E8F0;
            border-radius: 6px;
            padding: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .hsc-item:hover {
            border-color: #CBD5E1;
            background: #F8FAFC;
        }
        .hsc-item.active {
            border-color: #8B5CF6;
            background: #F5F3FF;
        }
        .hsc-item-title {
            font-weight: 500;
            color: #1E293B;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .hsc-item-actions {
            display: flex;
            gap: 0.5rem;
        }
        .hsc-item-action {
            padding: 4px 8px;
            border: none;
            background: transparent;
            cursor: pointer;
            color: #64748B;
            transition: color 0.2s;
            font-size: 1.1rem;
        }
        .hsc-item-action:hover {
            color: #1E293B;
        }
        .hsc-item-action.danger:hover {
            color: #EF4444;
        }
        
        .hsc-fullscreen-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #E2E8F0;
            display: flex;
            gap: 1rem;
            flex-shrink: 0;
            background: white;
        }
        
        /* SAĞ PANEL */
        .hsc-fullscreen-right {
            flex: 1;
            background: #1F2937;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .hsc-fullscreen-preview-header {
            padding: 1rem 2rem;
            background: #111827;
            border-bottom: 1px solid #374151;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }
        .hsc-fullscreen-preview-header h4 {
            color: white;
            font-size: 1.125rem;
            margin: 0;
        }
        .hsc-preview-controls {
            display: flex;
            gap: 0.5rem;
        }
        .hsc-fullscreen-preview-info {
            padding: 0.75rem 2rem;
            background: #1F2937;
            border-bottom: 1px solid #374151;
            flex-shrink: 0;
        }
        .hsc-fullscreen-preview-info small {
            color: #9CA3AF;
        }
        .hsc-fullscreen-preview-container {
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            overflow: hidden;
        }
        #hsc-preview-slide {
            position: relative;
            width: 100%;
            max-width: 1400px;
            aspect-ratio: 16 / 9;
            background: #000;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            overflow: hidden;
            border-radius: 8px;
        }
        .hsc-preview-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        /* PREVIEW KATMANLARI */
        .hsc-preview-layer {
            position: absolute;
            cursor: move;
            user-select: none;
            transition: box-shadow 0.2s;
            border: 2px solid transparent;
        }
        .hsc-preview-layer:hover {
            border-color: rgba(16, 185, 129, 0.5);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }
        .hsc-preview-layer.selected {
            border-color: #8B5CF6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.3);
        }
        .hsc-preview-layer.locked {
            cursor: not-allowed;
            opacity: 0.7;
        }
        .hsc-preview-layer img {
            max-width: 100%;
            height: auto;
            display: block;
            pointer-events: none;
        }
        
        /* RESIZE HANDLES */
        .hsc-resize-handle {
            position: absolute;
            width: 10px;
            height: 10px;
            background: #8B5CF6;
            border: 2px solid white;
            border-radius: 50%;
            opacity: 0;
            transition: opacity 0.2s;
            z-index: 10;
        }
        .hsc-preview-layer.selected .hsc-resize-handle {
            opacity: 1;
        }
        .hsc-resize-handle.nw { top: -5px; left: -5px; cursor: nw-resize; }
        .hsc-resize-handle.ne { top: -5px; right: -5px; cursor: ne-resize; }
        .hsc-resize-handle.sw { bottom: -5px; left: -5px; cursor: sw-resize; }
        .hsc-resize-handle.se { bottom: -5px; right: -5px; cursor: se-resize; }
        
        /* LAYER INFO BADGE */
        .hsc-layer-badge {
            position: absolute;
            top: -30px;
            left: 0;
            background: rgba(139, 92, 246, 0.95);
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            pointer-events: none;
            white-space: nowrap;
            z-index: 100;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        
        /* VISIBILITY TOGGLE */
        .hsc-visibility-toggle {
            font-size: 1.2rem;
            transition: opacity 0.2s;
        }
        .hsc-visibility-toggle.hidden {
            opacity: 0.3;
        }
        
        /* LOCK TOGGLE */
        .hsc-lock-toggle {
            font-size: 1.1rem;
            transition: color 0.2s;
        }
        .hsc-lock-toggle.locked {
            color: #EF4444;
        }
        
        /* SCROLLBAR */
        .hsc-fullscreen-content::-webkit-scrollbar,
        .hsc-items-list::-webkit-scrollbar {
            width: 8px;
        }
        .hsc-fullscreen-content::-webkit-scrollbar-track,
        .hsc-items-list::-webkit-scrollbar-track {
            background: #F1F5F9;
        }
        .hsc-fullscreen-content::-webkit-scrollbar-thumb,
        .hsc-items-list::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 4px;
        }
        .hsc-fullscreen-content::-webkit-scrollbar-thumb:hover,
        .hsc-items-list::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }
        </style>
        <?php
        return ob_get_clean();
    }

    private function render_editor_scripts() {
        ob_start();
        ?>
        <script>
        (function($) {
            'use strict';
            
            // GLOBAL VARIABLES
            let hscSliderData = {
                global: {
                    slider_height: '600px',
                    global_transition: 'fade',
                    autoplay: true,
                    autoplay_speed: 7000,
                    nav_arrows: true,
                    nav_bullets: true,
                    mouse_parallax: false,
                    parallax_amount: 30
                },
                slides: []
            };
            let hscCurrentSlideIndex = -1;
            let hscCurrentLayerIndex = -1;
            let hscMediaUploader = null;
            let hscDragData = null;
            let hscResizeData = null;
            
            // OPEN FULLSCREEN EDITOR
            $(document).on('click', '.hsc-open-fullscreen-editor', function() {
                const $form = $(this).closest('.hsc-editor-form');
                const dataString = $form.find('.hsc-slider-data').val();
                
                if (dataString) {
                    try {
                        hscSliderData = JSON.parse(dataString);
                        if (!hscSliderData.slides) hscSliderData.slides = [];
                    } catch(e) {
                        hscSliderData = {
                            global: {
                                slider_height: '600px',
                                global_transition: 'fade',
                                autoplay: true,
                                autoplay_speed: 7000,
                                nav_arrows: true,
                                nav_bullets: true,
                                mouse_parallax: false,
                                parallax_amount: 30
                            },
                            slides: []
                        };
                    }
                } else {
                    // İlk slide ekle
                    hscSliderData.slides = [{
                        admin_label: 'Slayt 1',
                        background_type: 'color',
                        background_color: '#667eea',
                        background_image: '',
                        background_video: '',
                        bg_overlay: false,
                        bg_overlay_opacity: 0.5,
                        bg_overlay_type: 'color',
                        bg_overlay_color: '#000000',
                        bg_overlay_gradient_start: '#000000',
                        bg_overlay_gradient_end: '#000000',
                        bg_overlay_gradient_direction: 'to top',
                        slide_transition: 'fade',
                        particle_effect: 'none',
                        bg_ken_burns: false,
                        layers: []
                    }];
                }
                
                hscLoadGlobalSettings();
                hscRenderSlidesList();
                hscRenderLayersList();
                
                if (hscSliderData.slides.length > 0) {
                    hscSelectSlide(0);
                }
                
                $('#hsc-fullscreen-modal').fadeIn(300);
            });
            
            // CLOSE FULLSCREEN EDITOR
            $(document).on('click', '.hsc-close-fullscreen', function() {
                if (confirm('Kaydedilmemiş değişiklikler kaybolabilir. Çıkmak istediğinizden emin misiniz?')) {
                    $('#hsc-fullscreen-modal').fadeOut(300);
                }
            });
            
            // TAB SWITCHING
            $(document).on('click', '.hsc-tab', function() {
                const tab = $(this).data('tab');
                $('.hsc-tab').removeClass('active');
                $(this).addClass('active');
                $('.hsc-tab-content').removeClass('active');
                $(`.hsc-tab-content[data-tab="${tab}"]`).addClass('active');
            });
            
            // LOAD GLOBAL SETTINGS
            function hscLoadGlobalSettings() {
                const g = hscSliderData.global;
                $('#hsc-slider-height').val(g.slider_height || '600px');
                $('#hsc-global-transition').val(g.global_transition || 'fade');
                $('#hsc-autoplay').prop('checked', g.autoplay !== false);
                $('#hsc-autoplay-speed').val(g.autoplay_speed || 7000);
                $('#hsc-nav-arrows').prop('checked', g.nav_arrows !== false);
                $('#hsc-nav-bullets').prop('checked', g.nav_bullets !== false);
                $('#hsc-mouse-parallax').prop('checked', g.mouse_parallax === true);
                $('#hsc-parallax-amount').val(g.parallax_amount || 30);
            }
            
            // SAVE GLOBAL SETTINGS
            $(document).on('change', '#hsc-slider-height, #hsc-global-transition, #hsc-autoplay, #hsc-autoplay-speed, #hsc-nav-arrows, #hsc-nav-bullets, #hsc-mouse-parallax, #hsc-parallax-amount', function() {
                hscSliderData.global = {
                    slider_height: $('#hsc-slider-height').val(),
                    global_transition: $('#hsc-global-transition').val(),
                    autoplay: $('#hsc-autoplay').is(':checked'),
                    autoplay_speed: parseInt($('#hsc-autoplay-speed').val()) || 7000,
                    nav_arrows: $('#hsc-nav-arrows').is(':checked'),
                    nav_bullets: $('#hsc-nav-bullets').is(':checked'),
                    mouse_parallax: $('#hsc-mouse-parallax').is(':checked'),
                    parallax_amount: parseInt($('#hsc-parallax-amount').val()) || 30
                };
            });
            
            // ADD SLIDE
            window.hscAddSlide = function() {
                const newSlide = {
                    admin_label: `Slayt ${hscSliderData.slides.length + 1}`,
                    background_type: 'color',
                    background_color: '#667eea',
                    background_image: '',
                    background_video: '',
                    bg_overlay: false,
                    bg_overlay_opacity: 0.5,
                    bg_overlay_type: 'color',
                    bg_overlay_color: '#000000',
                    bg_overlay_gradient_start: '#000000',
                    bg_overlay_gradient_end: '#000000',
                    bg_overlay_gradient_direction: 'to top',
                    slide_transition: 'fade',
                    particle_effect: 'none',
                    bg_ken_burns: false,
                    layers: []
                };
                hscSliderData.slides.push(newSlide);
                hscRenderSlidesList();
                hscSelectSlide(hscSliderData.slides.length - 1);
            };
            
            // RENDER SLIDES LIST
            function hscRenderSlidesList() {
                const $list = $('#hsc-slides-list').empty();
                hscSliderData.slides.forEach((slide, index) => {
                    const $item = $(`
                        <div class="hsc-item" data-index="${index}">
                            <div class="hsc-item-title">
                                <span>🎬</span>
                                <span>${hscEscape(slide.admin_label || `Slayt ${index + 1}`)}</span>
                            </div>
                            <div class="hsc-item-actions">
                                <button type="button" class="hsc-item-action hsc-duplicate-slide" data-index="${index}" title="Kopyala">📋</button>
                                <button type="button" class="hsc-item-action danger hsc-delete-slide" data-index="${index}" title="Sil">🗑️</button>
                            </div>
                        </div>
                    `);
                    $list.append($item);
                });
            }
            
            // SELECT SLIDE
            $(document).on('click', '#hsc-slides-list .hsc-item', function(e) {
                if ($(e.target).closest('.hsc-item-action').length) return;
                const index = $(this).data('index');
                hscSelectSlide(index);
            });
            
            function hscSelectSlide(index) {
                hscCurrentSlideIndex = index;
                hscCurrentLayerIndex = -1;
                
                $('#hsc-slides-list .hsc-item').removeClass('active');
                $(`#hsc-slides-list .hsc-item[data-index="${index}"]`).addClass('active');
                
                const slide = hscSliderData.slides[index];
                if (!slide) return;
                
                // Load slide settings
                $('#hsc-slide-label').val(slide.admin_label || '');
                $('#hsc-slide-bg-type').val(slide.background_type || 'color');
                $('#hsc-slide-bg-color').val(slide.background_color || '#333333');
                $('#hsc-slide-bg-image').val(slide.background_image || '');
                $('#hsc-slide-bg-video').val(slide.background_video || '');
                $('#hsc-slide-overlay').prop('checked', slide.bg_overlay === true);
                $('#hsc-slide-overlay-opacity').val(slide.bg_overlay_opacity || 0.5);
                $('#hsc-overlay-opacity-value').text(slide.bg_overlay_opacity || 0.5);
                $('#hsc-slide-overlay-type').val(slide.bg_overlay_type || 'color');
                $('#hsc-slide-overlay-color').val(slide.bg_overlay_color || '#000000');
                $('#hsc-slide-overlay-grad-start').val(slide.bg_overlay_gradient_start || '#000000');
                $('#hsc-slide-overlay-grad-end').val(slide.bg_overlay_gradient_end || '#000000');
                $('#hsc-slide-overlay-grad-dir').val(slide.bg_overlay_gradient_direction || 'to top');
                $('#hsc-slide-transition').val(slide.slide_transition || 'fade');
                $('#hsc-slide-particle').val(slide.particle_effect || 'none');
                $('#hsc-slide-ken-burns').prop('checked', slide.bg_ken_burns === true);

                // Toggle overlay type visibility
                if (slide.bg_overlay_type === 'gradient') {
                    $('#hsc-overlay-color-group').hide();
                    $('#hsc-overlay-gradient-start-group, #hsc-overlay-gradient-end-group, #hsc-overlay-gradient-dir-group').show();
                } else {
                    $('#hsc-overlay-color-group').show();
                    $('#hsc-overlay-gradient-start-group, #hsc-overlay-gradient-end-group, #hsc-overlay-gradient-dir-group').hide();
                }

                $('#hsc-slide-settings').show();
                
                hscRenderLayersList();
                hscRenderPreview();
                hscUpdateSlideIndicator();
            }
            
            // SAVE SLIDE SETTINGS
            $(document).on('change input', '#hsc-slide-label, #hsc-slide-bg-type, #hsc-slide-bg-color, #hsc-slide-bg-image, #hsc-slide-bg-video, #hsc-slide-overlay, #hsc-slide-overlay-opacity, #hsc-slide-overlay-type, #hsc-slide-overlay-color, #hsc-slide-overlay-grad-start, #hsc-slide-overlay-grad-end, #hsc-slide-overlay-grad-dir, #hsc-slide-transition, #hsc-slide-particle, #hsc-slide-ken-burns', function() {
                if (hscCurrentSlideIndex === -1) return;

                const slide = hscSliderData.slides[hscCurrentSlideIndex];
                slide.admin_label = $('#hsc-slide-label').val();
                slide.background_type = $('#hsc-slide-bg-type').val();
                slide.background_color = $('#hsc-slide-bg-color').val();
                slide.background_image = $('#hsc-slide-bg-image').val();
                slide.background_video = $('#hsc-slide-bg-video').val();
                slide.bg_overlay = $('#hsc-slide-overlay').is(':checked');
                slide.bg_overlay_opacity = parseFloat($('#hsc-slide-overlay-opacity').val()) || 0.5;
                slide.bg_overlay_type = $('#hsc-slide-overlay-type').val();
                slide.bg_overlay_color = $('#hsc-slide-overlay-color').val();
                slide.bg_overlay_gradient_start = $('#hsc-slide-overlay-grad-start').val();
                slide.bg_overlay_gradient_end = $('#hsc-slide-overlay-grad-end').val();
                slide.bg_overlay_gradient_direction = $('#hsc-slide-overlay-grad-dir').val();
                slide.slide_transition = $('#hsc-slide-transition').val();
                slide.particle_effect = $('#hsc-slide-particle').val();
                slide.bg_ken_burns = $('#hsc-slide-ken-burns').is(':checked');

                hscRenderSlidesList();
                hscRenderPreview(true); // Force full render for slide bg changes
            });

            // Overlay opacity slider value display
            $(document).on('input', '#hsc-slide-overlay-opacity', function() {
                $('#hsc-overlay-opacity-value').text($(this).val());
            });

            // Overlay type toggle
            $(document).on('change', '#hsc-slide-overlay-type', function() {
                const type = $(this).val();
                if (type === 'gradient') {
                    $('#hsc-overlay-color-group').hide();
                    $('#hsc-overlay-gradient-start-group, #hsc-overlay-gradient-end-group, #hsc-overlay-gradient-dir-group').show();
                } else {
                    $('#hsc-overlay-color-group').show();
                    $('#hsc-overlay-gradient-start-group, #hsc-overlay-gradient-end-group, #hsc-overlay-gradient-dir-group').hide();
                }
            });
            
            // DELETE SLIDE
            $(document).on('click', '.hsc-delete-slide', function(e) {
                e.stopPropagation();
                const index = $(this).data('index');
                if (!confirm('Bu slaytı silmek istediğinizden emin misiniz?')) return;
                
                hscSliderData.slides.splice(index, 1);
                hscRenderSlidesList();
                
                if (hscSliderData.slides.length > 0) {
                    hscSelectSlide(Math.min(index, hscSliderData.slides.length - 1));
                } else {
                    $('#hsc-slide-settings').hide();
                    hscRenderPreview();
                }
            });
            
            // DUPLICATE SLIDE
            $(document).on('click', '.hsc-duplicate-slide', function(e) {
                e.stopPropagation();
                const index = $(this).data('index');
                const slide = JSON.parse(JSON.stringify(hscSliderData.slides[index]));
                slide.admin_label += ' (Kopya)';
                hscSliderData.slides.splice(index + 1, 0, slide);
                hscRenderSlidesList();
                hscSelectSlide(index + 1);
            });
            
            // ADD LAYER
            window.hscAddLayer = function(type) {
                if (hscCurrentSlideIndex === -1) {
                    alert('Önce bir slayt seçin!');
                    return;
                }
                
                const slide = hscSliderData.slides[hscCurrentSlideIndex];
                const newLayer = {
                    type: type,
                    pos_x: 100,
                    pos_y: 100,
                    width: 'auto',
                    height: 'auto',
                    z_index: 10 + slide.layers.length,
                    animation_in: 'fade-in',
                    animation_delay_in: 0,
                    animation_duration_in: 1000,
                    animation_out: 'fade-out',
                    animation_delay_out: 0,
                    animation_duration_out: 500,
                    hidden: false,
                    locked: false
                };
                
                if (type === 'text') {
                    newLayer.content = 'Yeni Metin';
                    newLayer.font_size = 24;
                    newLayer.color = '#ffffff';
                    newLayer.font_weight = 400;
                    newLayer.text_align = 'left';
                    newLayer.line_height = 1.4;
                    newLayer.letter_spacing = 0;
                } else if (type === 'image') {
                    newLayer.image_url = 'https://via.placeholder.com/300x200';
                    newLayer.alt_text = 'Image';
                    newLayer.hover_effect = 'zoom';
                    newLayer.link_url = '';
                    newLayer.link_new_tab = false;
                    newLayer.width = 300;
                } else if (type === 'button') {
                    newLayer.content = 'Buton';
                    newLayer.font_size = 16;
                    newLayer.color = '#ffffff';
                    newLayer.font_weight = 600;
                    newLayer.text_align = 'center';
                    newLayer.bg_color = '#8B5CF6';
                    newLayer.padding = '12px 24px';
                    newLayer.border_radius = 6;
                    newLayer.link_url = '#';
                    newLayer.link_new_tab = false;
                }
                
                slide.layers.push(newLayer);
                hscRenderLayersList();
                hscSelectLayer(slide.layers.length - 1);
                $('.hsc-tab[data-tab="layers"]').click();
            };
            
            // RENDER LAYERS LIST
            function hscRenderLayersList() {
                const $list = $('#hsc-layers-list').empty();
                
                if (hscCurrentSlideIndex === -1) {
                    $list.html('<div style="color: #94A3B8; text-align: center; padding: 2rem;">Önce bir slayt seçin</div>');
                    return;
                }
                
                const slide = hscSliderData.slides[hscCurrentSlideIndex];
                if (!slide.layers || slide.layers.length === 0) {
                    $list.html('<div style="color: #94A3B8; text-align: center; padding: 2rem;">Henüz katman yok. Yukarıdaki butonlardan ekleyin!</div>');
                    return;
                }
                
                slide.layers.forEach((layer, index) => {
                    let icon = '📝';
                    if (layer.type === 'image') icon = '🖼️';
                    if (layer.type === 'button') icon = '🔘';
                    
                    const $item = $(`
                        <div class="hsc-item" data-index="${index}">
                            <div class="hsc-item-title">
                                <span>${icon}</span>
                                <span>${hscGetLayerTitle(layer)}</span>
                            </div>
                            <div class="hsc-item-actions">
                                <button type="button" class="hsc-item-action hsc-toggle-layer-visibility" data-index="${index}" title="Görünürlük">
                                    <span class="hsc-visibility-toggle ${layer.hidden ? 'hidden' : ''}">${layer.hidden ? '👁️‍🗨️' : '👁️'}</span>
                                </button>
                                <button type="button" class="hsc-item-action hsc-toggle-layer-lock" data-index="${index}" title="Kilitle">
                                    <span class="hsc-lock-toggle ${layer.locked ? 'locked' : ''}">${layer.locked ? '🔒' : '🔓'}</span>
                                </button>
                                <button type="button" class="hsc-item-action hsc-duplicate-layer" data-index="${index}" title="Kopyala">📋</button>
                                <button type="button" class="hsc-item-action danger hsc-delete-layer" data-index="${index}" title="Sil">🗑️</button>
                            </div>
                        </div>
                    `);
                    $list.append($item);
                });
            }
            
            function hscGetLayerTitle(layer) {
                if (layer.type === 'text') {
                    const text = layer.content ? layer.content.replace(/<[^>]*>/g, '').substring(0, 20) : 'Metin';
                    return text + (layer.content && layer.content.length > 20 ? '...' : '');
                } else if (layer.type === 'image') {
                    return 'Görsel';
                } else if (layer.type === 'button') {
                    return layer.content || 'Buton';
                }
                return 'Katman';
            }
            
            // SELECT LAYER
            $(document).on('click', '#hsc-layers-list .hsc-item', function(e) {
                if ($(e.target).closest('.hsc-item-action').length) return;
                const index = $(this).data('index');
                hscSelectLayer(index);
            });
            
            function hscSelectLayer(index) {
                hscCurrentLayerIndex = index;
                
                $('#hsc-layers-list .hsc-item').removeClass('active');
                $(`#hsc-layers-list .hsc-item[data-index="${index}"]`).addClass('active');
                
                const slide = hscSliderData.slides[hscCurrentSlideIndex];
                const layer = slide.layers[index];
                
                hscLoadLayerSettings(layer);
                hscRenderPreview();
                $('#hsc-layer-settings').show();
            }
            
            // LOAD LAYER SETTINGS
            function hscLoadLayerSettings(layer) {
                let html = '';
                
                // Pozisyon ve Boyut
                html += `
                    <div class="kreatus-form-group">
                        <label>X (px)</label>
                        <input type="number" id="hsc-layer-pos-x" class="kreatus-input" value="${layer.pos_x || 0}">
                    </div>
                    <div class="kreatus-form-group">
                        <label>Y (px)</label>
                        <input type="number" id="hsc-layer-pos-y" class="kreatus-input" value="${layer.pos_y || 0}">
                    </div>
                    <div class="kreatus-form-group">
                        <label>Genişlik</label>
                        <input type="text" id="hsc-layer-width" class="kreatus-input" value="${layer.width || 'auto'}" placeholder="auto veya px">
                    </div>
                    <div class="kreatus-form-group">
                        <label>Yükseklik</label>
                        <input type="text" id="hsc-layer-height" class="kreatus-input" value="${layer.height || 'auto'}" placeholder="auto veya px">
                    </div>
                    <div class="kreatus-form-group">
                        <label>Z-Index</label>
                        <input type="number" id="hsc-layer-z-index" class="kreatus-input" value="${layer.z_index || 10}">
                    </div>
                `;
                
                // İçerik ayarları
                if (layer.type === 'text') {
                    html += `
                        <div class="kreatus-form-group hsc-grid-full">
                            <label>İçerik (HTML)</label>
                            <textarea id="hsc-layer-content" class="kreatus-textarea" rows="3">${hscEscape(layer.content || '')}</textarea>
                        </div>
                        <div class="kreatus-form-group">
                            <label>Yazı Boyutu (px)</label>
                            <input type="number" id="hsc-layer-font-size" class="kreatus-input" value="${layer.font_size || 16}">
                        </div>
                        <div class="kreatus-form-group">
                            <label>Yazı Rengi</label>
                            <input type="color" id="hsc-layer-color" class="kreatus-color" value="${layer.color || '#ffffff'}">
                        </div>
                        <div class="kreatus-form-group">
                            <label>Yazı Kalınlığı</label>
                            <select id="hsc-layer-font-weight" class="kreatus-select">
                                <option value="300" ${layer.font_weight == 300 ? 'selected' : ''}>İnce</option>
                                <option value="400" ${layer.font_weight == 400 ? 'selected' : ''}>Normal</option>
                                <option value="600" ${layer.font_weight == 600 ? 'selected' : ''}>Yarı Kalın</option>
                                <option value="700" ${layer.font_weight == 700 ? 'selected' : ''}>Kalın</option>
                                <option value="900" ${layer.font_weight == 900 ? 'selected' : ''}>Çok Kalın</option>
                            </select>
                        </div>
                        <div class="kreatus-form-group">
                            <label>Hizalama</label>
                            <select id="hsc-layer-text-align" class="kreatus-select">
                                <option value="left" ${layer.text_align === 'left' ? 'selected' : ''}>Sol</option>
                                <option value="center" ${layer.text_align === 'center' ? 'selected' : ''}>Orta</option>
                                <option value="right" ${layer.text_align === 'right' ? 'selected' : ''}>Sağ</option>
                            </select>
                        </div>
                        <div class="kreatus-form-group">
                            <label>Satır Yüksekliği</label>
                            <input type="number" id="hsc-layer-line-height" class="kreatus-input" value="${layer.line_height || 1.5}" step="0.1" min="0.5" max="3">
                        </div>
                        <div class="kreatus-form-group">
                            <label>Harf Aralığı (px)</label>
                            <input type="number" id="hsc-layer-letter-spacing" class="kreatus-input" value="${layer.letter_spacing || 0}" step="0.5">
                        </div>
                        <div class="kreatus-form-group">
                            <label><input type="checkbox" id="hsc-layer-text-glow" class="kreatus-checkbox" ${layer.text_glow ? 'checked' : ''}> Glow Efekti</label>
                        </div>
                        <div class="kreatus-form-group" id="hsc-glow-settings" style="display: ${layer.text_glow ? 'block' : 'none'};">
                            <label>Glow Rengi</label>
                            <input type="color" id="hsc-layer-text-glow-color" class="kreatus-color" value="${layer.text_glow_color || '#8B5CF6'}">
                        </div>
                        <div class="kreatus-form-group" style="display: ${layer.text_glow ? 'block' : 'none'};">
                            <label>Glow Bulanıklık (px)</label>
                            <input type="number" id="hsc-layer-text-glow-blur" class="kreatus-input" value="${layer.text_glow_blur || 20}" min="0" max="100">
                        </div>
                        <div class="kreatus-form-group">
                            <label>Motion Animasyon</label>
                            <select id="hsc-layer-motion-animation" class="kreatus-select">
                                <option value="none" ${(layer.motion_animation || 'none') === 'none' ? 'selected' : ''}>Yok</option>
                                <option value="float" ${layer.motion_animation === 'float' ? 'selected' : ''}>Float (Yüzen)</option>
                                <option value="pulse" ${layer.motion_animation === 'pulse' ? 'selected' : ''}>Pulse (Nabız)</option>
                                <option value="swing" ${layer.motion_animation === 'swing' ? 'selected' : ''}>Swing (Sallanma)</option>
                                <option value="glitch" ${layer.motion_animation === 'glitch' ? 'selected' : ''}>Glitch</option>
                            </select>
                        </div>
                    `;
                } else if (layer.type === 'image') {
                    html += `
                        <div class="kreatus-form-group hsc-grid-full">
                            <label>Görsel URL</label>
                            <input type="text" id="hsc-layer-image-url" class="kreatus-input" value="${hscEscape(layer.image_url || '')}">
                            <button type="button" class="kreatus-btn kreatus-btn-secondary hsc-select-media" data-target="layer-image" style="width: 100%; margin-top: 5px;">Görsel Seç</button>
                        </div>
                        <div class="kreatus-form-group">
                            <label>Alt Metin</label>
                            <input type="text" id="hsc-layer-alt-text" class="kreatus-input" value="${hscEscape(layer.alt_text || '')}">
                        </div>
                        <div class="kreatus-form-group">
                            <label>Border Radius (px)</label>
                            <input type="number" id="hsc-layer-border-radius" class="kreatus-input" value="${layer.border_radius || 0}" min="0">
                        </div>
                        <div class="kreatus-form-group">
                            <label>Opaklık</label>
                            <input type="range" id="hsc-layer-opacity" class="kreatus-range" min="0" max="1" step="0.1" value="${layer.opacity !== undefined ? layer.opacity : 1}">
                            <span id="hsc-layer-opacity-value">${layer.opacity !== undefined ? layer.opacity : 1}</span>
                        </div>
                        <div class="kreatus-form-group">
                            <label>Hover Efekti</label>
                            <select id="hsc-layer-hover-effect" class="kreatus-select">
                                <option value="none" ${layer.hover_effect === 'none' ? 'selected' : ''}>Yok</option>
                                <option value="zoom" ${layer.hover_effect === 'zoom' ? 'selected' : ''}>Zoom</option>
                                <option value="float" ${layer.hover_effect === 'float' ? 'selected' : ''}>Float</option>
                                <option value="glow" ${layer.hover_effect === 'glow' ? 'selected' : ''}>Glow</option>
                                <option value="rotate" ${layer.hover_effect === 'rotate' ? 'selected' : ''}>Rotate</option>
                            </select>
                        </div>
                        <div class="kreatus-form-group">
                            <label>Motion Animasyon</label>
                            <select id="hsc-layer-motion-animation" class="kreatus-select">
                                <option value="none" ${(layer.motion_animation || 'none') === 'none' ? 'selected' : ''}>Yok</option>
                                <option value="float" ${layer.motion_animation === 'float' ? 'selected' : ''}>Float (Yüzen)</option>
                                <option value="pulse" ${layer.motion_animation === 'pulse' ? 'selected' : ''}>Pulse (Nabız)</option>
                                <option value="swing" ${layer.motion_animation === 'swing' ? 'selected' : ''}>Swing (Sallanma)</option>
                                <option value="tilt" ${layer.motion_animation === 'tilt' ? 'selected' : ''}>Tilt (Eğilme)</option>
                            </select>
                        </div>
                        <div class="kreatus-form-group hsc-grid-full">
                            <label>Link URL</label>
                            <input type="url" id="hsc-layer-link-url" class="kreatus-input" value="${hscEscape(layer.link_url || '')}" placeholder="https://...">
                        </div>
                        <div class="kreatus-form-group">
                            <label><input type="checkbox" id="hsc-layer-link-new-tab" class="kreatus-checkbox" ${layer.link_new_tab ? 'checked' : ''}> Yeni sekmede aç</label>
                        </div>
                    `;
                } else if (layer.type === 'button') {
                    html += `
                        <div class="kreatus-form-group hsc-grid-full">
                            <label>Buton Metni</label>
                            <input type="text" id="hsc-layer-content" class="kreatus-input" value="${hscEscape(layer.content || '')}">
                        </div>
                        <div class="kreatus-form-group">
                            <label>Yazı Boyutu</label>
                            <input type="number" id="hsc-layer-font-size" class="kreatus-input" value="${layer.font_size || 16}">
                        </div>
                        <div class="kreatus-form-group">
                            <label>Yazı Rengi</label>
                            <input type="color" id="hsc-layer-color" class="kreatus-color" value="${layer.color || '#ffffff'}">
                        </div>
                        <div class="kreatus-form-group">
                            <label>Arka Plan Rengi</label>
                            <input type="color" id="hsc-layer-bg-color" class="kreatus-color" value="${layer.bg_color || '#8B5CF6'}">
                        </div>
                        <div class="kreatus-form-group">
                            <label>Padding</label>
                            <input type="text" id="hsc-layer-padding" class="kreatus-input" value="${layer.padding || '12px 24px'}">
                        </div>
                        <div class="kreatus-form-group">
                            <label>Border Radius</label>
                            <input type="number" id="hsc-layer-border-radius" class="kreatus-input" value="${layer.border_radius || 6}">
                        </div>
                        <div class="kreatus-form-group hsc-grid-full">
                            <label>Link URL</label>
                            <input type="url" id="hsc-layer-link-url" class="kreatus-input" value="${hscEscape(layer.link_url || '#')}">
                        </div>
                        <div class="kreatus-form-group">
                            <label><input type="checkbox" id="hsc-layer-link-new-tab" class="kreatus-checkbox" ${layer.link_new_tab ? 'checked' : ''}> Yeni sekmede aç</label>
                        </div>
                    `;
                }
                
                // Animasyon ayarları
                html += `
                    <div class="kreatus-form-group">
                        <label>Giriş Animasyonu</label>
                        <select id="hsc-layer-anim-in" class="kreatus-select">
                            ${hscGetAnimOptions('in', layer.animation_in)}
                        </select>
                    </div>
                    <div class="kreatus-form-group">
                        <label>Gecikme (ms)</label>
                        <input type="number" id="hsc-layer-delay-in" class="kreatus-input" value="${layer.animation_delay_in || 0}">
                    </div>
                    <div class="kreatus-form-group">
                        <label>Çıkış Animasyonu</label>
                        <select id="hsc-layer-anim-out" class="kreatus-select">
                            ${hscGetAnimOptions('out', layer.animation_out)}
                        </select>
                    </div>
                `;
                
                $('#hsc-layer-settings-content').html(html);
            }
            
            function hscGetAnimOptions(type, selected) {
                const anims = {
                    'in': {
                        'fade-in': 'Fade In',
                        'fade-in-up': 'Fade In (Alttan)',
                        'fade-in-down': 'Fade In (Üstten)',
                        'fade-in-left': 'Fade In (Sağdan)',
                        'fade-in-right': 'Fade In (Soldan)',
                        'slide-from-top': 'Slide (Üstten)',
                        'slide-from-bottom': 'Slide (Alttan)',
                        'slide-from-left': 'Slide (Soldan)',
                        'slide-from-right': 'Slide (Sağdan)',
                        'scale-up': 'Scale Up',
                        'scale-down': 'Scale Down'
                    },
                    'out': {
                        'fade-out': 'Fade Out',
                        'fade-out-up': 'Fade Out (Üste)',
                        'fade-out-down': 'Fade Out (Alta)',
                        'slide-to-top': 'Slide (Üste)',
                        'slide-to-bottom': 'Slide (Alta)',
                        'scale-down': 'Scale Down'
                    }
                };
                
                const list = anims[type];
                let html = '';
                for (let key in list) {
                    html += `<option value="${key}" ${key === selected ? 'selected' : ''}>${list[key]}</option>`;
                }
                return html;
            }
            
            // SAVE LAYER SETTINGS
            $(document).on('change input', '#hsc-layer-settings-content input, #hsc-layer-settings-content select, #hsc-layer-settings-content textarea', function() {
                if (hscCurrentSlideIndex === -1 || hscCurrentLayerIndex === -1) return;
                
                const slide = hscSliderData.slides[hscCurrentSlideIndex];
                const layer = slide.layers[hscCurrentLayerIndex];
                
                // Ortak alanlar
                layer.pos_x = parseInt($('#hsc-layer-pos-x').val()) || 0;
                layer.pos_y = parseInt($('#hsc-layer-pos-y').val()) || 0;
                
                const width = $('#hsc-layer-width').val();
                layer.width = (width === 'auto' || width === '') ? 'auto' : parseInt(width);
                
                const height = $('#hsc-layer-height').val();
                layer.height = (height === 'auto' || height === '') ? 'auto' : parseInt(height);
                
                layer.z_index = parseInt($('#hsc-layer-z-index').val()) || 10;
                layer.animation_in = $('#hsc-layer-anim-in').val();
                layer.animation_delay_in = parseInt($('#hsc-layer-delay-in').val()) || 0;
                layer.animation_out = $('#hsc-layer-anim-out').val();
                
                // Tip-specific alanlar
                if (layer.type === 'text') {
                    layer.content = $('#hsc-layer-content').val();
                    layer.font_size = parseInt($('#hsc-layer-font-size').val()) || 16;
                    layer.color = $('#hsc-layer-color').val();
                    layer.font_weight = parseInt($('#hsc-layer-font-weight').val()) || 400;
                    layer.text_align = $('#hsc-layer-text-align').val();
                    layer.line_height = parseFloat($('#hsc-layer-line-height').val()) || 1.5;
                    layer.letter_spacing = parseFloat($('#hsc-layer-letter-spacing').val()) || 0;
                    layer.text_glow = $('#hsc-layer-text-glow').is(':checked');
                    layer.text_glow_color = $('#hsc-layer-text-glow-color').val();
                    layer.text_glow_blur = parseInt($('#hsc-layer-text-glow-blur').val()) || 20;
                    layer.motion_animation = $('#hsc-layer-motion-animation').val();
                } else if (layer.type === 'image') {
                    layer.image_url = $('#hsc-layer-image-url').val();
                    layer.alt_text = $('#hsc-layer-alt-text').val();
                    layer.border_radius = parseInt($('#hsc-layer-border-radius').val()) || 0;
                    layer.opacity = parseFloat($('#hsc-layer-opacity').val()) || 1;
                    layer.hover_effect = $('#hsc-layer-hover-effect').val();
                    layer.motion_animation = $('#hsc-layer-motion-animation').val();
                    layer.link_url = $('#hsc-layer-link-url').val();
                    layer.link_new_tab = $('#hsc-layer-link-new-tab').is(':checked');
                } else if (layer.type === 'button') {
                    layer.content = $('#hsc-layer-content').val();
                    layer.font_size = parseInt($('#hsc-layer-font-size').val()) || 16;
                    layer.color = $('#hsc-layer-color').val();
                    layer.bg_color = $('#hsc-layer-bg-color').val();
                    layer.padding = $('#hsc-layer-padding').val();
                    layer.border_radius = parseInt($('#hsc-layer-border-radius').val()) || 6;
                    layer.link_url = $('#hsc-layer-link-url').val();
                    layer.link_new_tab = $('#hsc-layer-link-new-tab').is(':checked');
                }

                hscRenderLayersList();
                hscRenderPreview();
            });

            // Glow checkbox toggle
            $(document).on('change', '#hsc-layer-text-glow', function() {
                const isChecked = $(this).is(':checked');
                $('#hsc-glow-settings').toggle(isChecked);
                $(this).closest('.kreatus-form-group').next().toggle(isChecked);
                $(this).closest('.kreatus-form-group').next().next().toggle(isChecked);
            });

            // Opacity slider value display
            $(document).on('input', '#hsc-layer-opacity', function() {
                $('#hsc-layer-opacity-value').text($(this).val());
            });

            // DELETE LAYER
            $(document).on('click', '.hsc-delete-layer', function(e) {
                e.stopPropagation();
                const index = $(this).data('index');
                if (!confirm('Bu katmanı silmek istediğinizden emin misiniz?')) return;
                
                const slide = hscSliderData.slides[hscCurrentSlideIndex];
                slide.layers.splice(index, 1);
                hscRenderLayersList();
                
                if (slide.layers.length > 0) {
                    hscSelectLayer(Math.min(index, slide.layers.length - 1));
                } else {
                    $('#hsc-layer-settings').hide();
                    hscRenderPreview();
                }
            });
            
            // DUPLICATE LAYER
            $(document).on('click', '.hsc-duplicate-layer', function(e) {
                e.stopPropagation();
                const index = $(this).data('index');
                const slide = hscSliderData.slides[hscCurrentSlideIndex];
                const layer = JSON.parse(JSON.stringify(slide.layers[index]));
                layer.pos_x += 20;
                layer.pos_y += 20;
                slide.layers.splice(index + 1, 0, layer);
                hscRenderLayersList();
                hscSelectLayer(index + 1);
            });
            
            // TOGGLE LAYER VISIBILITY
            $(document).on('click', '.hsc-toggle-layer-visibility', function(e) {
                e.stopPropagation();
                const index = $(this).data('index');
                const slide = hscSliderData.slides[hscCurrentSlideIndex];
                const layer = slide.layers[index];
                layer.hidden = !layer.hidden;
                hscRenderLayersList();
                hscRenderPreview();
            });
            
            // TOGGLE LAYER LOCK
            $(document).on('click', '.hsc-toggle-layer-lock', function(e) {
                e.stopPropagation();
                const index = $(this).data('index');
                const slide = hscSliderData.slides[hscCurrentSlideIndex];
                const layer = slide.layers[index];
                layer.locked = !layer.locked;
                hscRenderLayersList();
                hscRenderPreview();
            });
            
            // MEDIA UPLOADER
            $(document).on('click', '.hsc-select-media', function() {
                const target = $(this).data('target');
                const mediaType = $(this).data('media-type') || 'image';
                
                if (hscMediaUploader) {
                    hscMediaUploader.options.library.type = mediaType;
                    hscMediaUploader.open();
                    return;
                }
                
                hscMediaUploader = wp.media({
                    title: mediaType === 'video' ? 'Video Seç' : 'Görsel Seç',
                    button: { text: mediaType === 'video' ? 'Videoyu Kullan' : 'Görseli Kullan' },
                    multiple: false,
                    library: { type: mediaType }
                });
                
                hscMediaUploader.on('select', function() {
                    const attachment = hscMediaUploader.state().get('selection').first().toJSON();
                    
                    if (target === 'bg-image') {
                        $('#hsc-slide-bg-image').val(attachment.url).trigger('change');
                    } else if (target === 'bg-video') {
                        $('#hsc-slide-bg-video').val(attachment.url).trigger('change');
                    } else if (target === 'layer-image') {
                        $('#hsc-layer-image-url').val(attachment.url).trigger('change');
                    }
                });
                
                hscMediaUploader.open();
            });
            
            // RENDER PREVIEW
            function hscRenderPreview(forceFullRender = false) {
                const $preview = $('#hsc-preview-slide');

                if (hscCurrentSlideIndex === -1 || !hscSliderData.slides[hscCurrentSlideIndex]) {
                    $preview.html(`
                        <div class="hsc-preview-placeholder">
                            <div style="text-align: center; color: #94A3B8;">
                                <div style="font-size: 3rem; margin-bottom: 1rem;">🎬</div>
                                <div style="font-size: 1.2rem; font-weight: 600; margin-bottom: 0.5rem;">Slayt Eklenmedi</div>
                                <div>Sol menüden "Slayt Ekle" butonuna tıklayarak başlayın!</div>
                            </div>
                        </div>
                    `);
                    return;
                }

                const slide = hscSliderData.slides[hscCurrentSlideIndex];

                // Check if we can do partial update instead of full render
                if (!forceFullRender && $preview.find('.hsc-preview-layer').length > 0) {
                    hscUpdateLayersOnly();
                    return;
                }

                // Background
                let bgStyle = '';
                if (slide.background_type === 'color') {
                    bgStyle = `background-color: ${slide.background_color || '#333'};`;
                } else if (slide.background_type === 'image' && slide.background_image) {
                    bgStyle = `background-image: url(${slide.background_image}); background-size: cover; background-position: center;`;
                } else if (slide.background_type === 'video' && slide.background_video) {
                    bgStyle = `background-color: ${slide.background_color || '#000'};`;
                }

                // Overlay
                let overlayHTML = '';
                if (slide.bg_overlay) {
                    let overlayStyle = '';
                    const opacity = slide.bg_overlay_opacity || 0.5;
                    if (slide.bg_overlay_type === 'gradient') {
                        const startColor = slide.bg_overlay_gradient_start || '#000000';
                        const endColor = slide.bg_overlay_gradient_end || '#000000';
                        overlayStyle = `background: linear-gradient(${slide.bg_overlay_gradient_direction || 'to top'}, ${startColor}, ${endColor});`;
                    } else {
                        const color = slide.bg_overlay_color || '#000000';
                        overlayStyle = `background-color: ${color};`;
                    }
                    overlayHTML = `<div class="hsc-preview-overlay" style="position: absolute; inset: 0; ${overlayStyle} opacity: ${opacity}; z-index: 1; pointer-events: none;"></div>`;
                }

                // Video
                let videoHTML = '';
                if (slide.background_type === 'video' && slide.background_video) {
                    videoHTML = `
                        <div class="hsc-preview-video" style="position: absolute; top: 50%; left: 50%; min-width: 100%; min-height: 100%; transform: translate(-50%, -50%); z-index: 0;">
                            <video autoplay muted loop playsinline style="width: 100%; height: auto;">
                                <source src="${slide.background_video}" type="video/mp4">
                            </video>
                        </div>
                    `;
                }

                // Particle effects
                let particleHTML = '';
                if (slide.particle_effect && slide.particle_effect !== 'none') {
                    particleHTML = `<div class="hsc-particles hsc-particles-${slide.particle_effect}" style="position: absolute; inset: 0; z-index: 5; pointer-events: none;"></div>`;
                }

                // Layers
                let layersHTML = '';
                slide.layers.forEach((layer, index) => {
                    if (layer.hidden) return;

                    const styles = hscGetPreviewLayerStyles(layer);
                    const content = hscGetPreviewLayerContent(layer);
                    const isSelected = (index === hscCurrentLayerIndex);
                    const isLocked = layer.locked;

                    layersHTML += `
                        <div class="hsc-preview-layer ${isSelected ? 'selected' : ''} ${isLocked ? 'locked' : ''}"
                             data-layer-index="${index}"
                             style="${styles}">
                            <div class="hsc-layer-badge">
                                ${hscGetLayerTitle(layer)} • ${layer.pos_x}px, ${layer.pos_y}px
                            </div>
                            ${content}
                            ${isSelected && !isLocked ? `
                                <div class="hsc-resize-handle nw"></div>
                                <div class="hsc-resize-handle ne"></div>
                                <div class="hsc-resize-handle sw"></div>
                                <div class="hsc-resize-handle se"></div>
                            ` : ''}
                        </div>
                    `;
                });

                $preview.html(`
                    <div class="hsc-preview-bg" style="position: absolute; inset: 0; ${bgStyle} z-index: 0;"></div>
                    ${videoHTML}
                    ${overlayHTML}
                    ${particleHTML}
                    <div class="hsc-preview-layers-container" style="position: relative; width: 100%; height: 100%; z-index: 2;">
                        ${layersHTML}
                    </div>
                `);

                // Init particles
                if (slide.particle_effect && slide.particle_effect !== 'none') {
                    hscInitParticles(slide.particle_effect);
                }

                // Init drag & resize
                hscInitDragResize();
            }

            // FAST UPDATE - Only update layers without re-rendering video/background
            function hscUpdateLayersOnly() {
                if (hscCurrentSlideIndex === -1) return;

                const slide = hscSliderData.slides[hscCurrentSlideIndex];
                const $layersContainer = $('#hsc-preview-slide .hsc-preview-layers-container');

                if ($layersContainer.length === 0) {
                    hscRenderPreview(true);
                    return;
                }

                // Update layers
                let layersHTML = '';
                slide.layers.forEach((layer, index) => {
                    if (layer.hidden) return;

                    const styles = hscGetPreviewLayerStyles(layer);
                    const content = hscGetPreviewLayerContent(layer);
                    const isSelected = (index === hscCurrentLayerIndex);
                    const isLocked = layer.locked;

                    layersHTML += `
                        <div class="hsc-preview-layer ${isSelected ? 'selected' : ''} ${isLocked ? 'locked' : ''}"
                             data-layer-index="${index}"
                             style="${styles}">
                            <div class="hsc-layer-badge">
                                ${hscGetLayerTitle(layer)} • ${layer.pos_x}px, ${layer.pos_y}px
                            </div>
                            ${content}
                            ${isSelected && !isLocked ? `
                                <div class="hsc-resize-handle nw"></div>
                                <div class="hsc-resize-handle ne"></div>
                                <div class="hsc-resize-handle sw"></div>
                                <div class="hsc-resize-handle se"></div>
                            ` : ''}
                        </div>
                    `;
                });

                $layersContainer.html(layersHTML);

                // Update overlay if needed
                const $overlay = $('#hsc-preview-slide .hsc-preview-overlay');
                if (slide.bg_overlay && $overlay.length > 0) {
                    let overlayStyle = '';
                    if (slide.bg_overlay_type === 'gradient') {
                        overlayStyle = `linear-gradient(${slide.bg_overlay_gradient_direction}, ${slide.bg_overlay_gradient_start}, ${slide.bg_overlay_gradient_end})`;
                        $overlay.css('background', overlayStyle);
                    } else {
                        $overlay.css('background-color', slide.bg_overlay_color);
                    }
                    $overlay.css('opacity', slide.bg_overlay_opacity || 0.5);
                }

                // Re-init drag & resize
                hscInitDragResize();
            }
            
            function hscGetPreviewLayerStyles(layer) {
                let styles = `
                    left: ${layer.pos_x || 0}px !important;
                    top: ${layer.pos_y || 0}px !important;
                    z-index: ${layer.z_index || 10} !important;
                `;

                if (layer.width !== 'auto' && layer.width) {
                    styles += `width: ${layer.width}px !important;`;
                }
                if (layer.height !== 'auto' && layer.height) {
                    styles += `height: ${layer.height}px !important;`;
                }

                if (layer.type === 'text' || layer.type === 'button') {
                    styles += `
                        font-size: ${layer.font_size || 16}px !important;
                        color: ${layer.color || '#fff'} !important;
                        font-weight: ${layer.font_weight || 400} !important;
                        text-align: ${layer.text_align || 'left'} !important;
                        line-height: ${layer.line_height || 1.5} !important;
                        letter-spacing: ${layer.letter_spacing || 0}px !important;
                    `;

                    // Text shadow/glow effect
                    if (layer.text_glow && layer.text_glow_color) {
                        styles += `text-shadow: 0 0 ${layer.text_glow_blur || 20}px ${layer.text_glow_color} !important;`;
                    }
                }

                if (layer.type === 'button') {
                    styles += `
                        background-color: ${layer.bg_color || '#8B5CF6'} !important;
                        padding: ${layer.padding || '12px 24px'} !important;
                        border-radius: ${layer.border_radius || 6}px !important;
                        display: inline-block !important;
                        text-decoration: none !important;
                        cursor: pointer !important;
                    `;
                }

                // Image specific styles
                if (layer.type === 'image') {
                    if (layer.border_radius) {
                        styles += `border-radius: ${layer.border_radius}px !important; overflow: hidden !important;`;
                    }
                    if (layer.opacity !== undefined && layer.opacity !== 1) {
                        styles += `opacity: ${layer.opacity} !important;`;
                    }
                }

                // Motion animation classes
                if (layer.motion_animation && layer.motion_animation !== 'none') {
                    styles += `animation: hsc-motion-${layer.motion_animation} ${layer.motion_duration || 3}s ${layer.motion_timing || 'ease-in-out'} infinite !important;`;
                }

                return styles;
            }

            function hscGetPreviewLayerContent(layer) {
                if (layer.type === 'text') {
                    const textStyles = `
                        font-size: ${layer.font_size || 16}px !important;
                        color: ${layer.color || '#fff'} !important;
                        font-weight: ${layer.font_weight || 400} !important;
                        text-align: ${layer.text_align || 'left'} !important;
                        line-height: ${layer.line_height || 1.5} !important;
                        letter-spacing: ${layer.letter_spacing || 0}px !important;
                        ${layer.text_glow && layer.text_glow_color ? `text-shadow: 0 0 ${layer.text_glow_blur || 20}px ${layer.text_glow_color} !important;` : ''}
                    `;
                    return `<div style="${textStyles}">${layer.content || 'Metin'}</div>`;
                } else if (layer.type === 'image') {
                    const imgStyles = `
                        max-width: 100% !important;
                        display: block !important;
                        pointer-events: none !important;
                        ${layer.border_radius ? `border-radius: ${layer.border_radius}px !important;` : ''}
                        ${layer.opacity !== undefined && layer.opacity !== 1 ? `opacity: ${layer.opacity} !important;` : ''}
                    `;
                    const hoverClass = layer.hover_effect ? `hsc-hover-${layer.hover_effect}` : '';
                    return `<img src="${layer.image_url || ''}" class="${hoverClass}" style="${imgStyles}" alt="${layer.alt_text || ''}">`;
                } else if (layer.type === 'button') {
                    const btnStyles = `
                        font-size: ${layer.font_size || 16}px !important;
                        color: ${layer.color || '#fff'} !important;
                        font-weight: ${layer.font_weight || 700} !important;
                    `;
                    return `<span style="${btnStyles}">${layer.content || 'Buton'}</span>`;
                }
                return '';
            }
            
            // DRAG & RESIZE
            function hscInitDragResize() {
                let activeLayer = null;
                let startX = 0;
                let startY = 0;
                let startLeft = 0;
                let startTop = 0;
                let startWidth = 0;
                let startHeight = 0;
                let isResizing = false;
                let resizeDirection = '';
                
                // Layer click - select
                $(document).on('mousedown', '.hsc-preview-layer', function(e) {
                    const index = $(this).data('layer-index');
                    const slide = hscSliderData.slides[hscCurrentSlideIndex];
                    const layer = slide.layers[index];
                    
                    if (layer.locked) return;
                    
                    // Eğer resize handle'a tıklandıysa drag başlatma
                    if ($(e.target).hasClass('hsc-resize-handle')) {
                        isResizing = true;
                        resizeDirection = $(e.target).attr('class').split(' ')[1];
                        activeLayer = this;
                        startX = e.clientX;
                        startY = e.clientY;
                        startLeft = parseInt($(this).css('left'));
                        startTop = parseInt($(this).css('top'));
                        startWidth = $(this).outerWidth();
                        startHeight = $(this).outerHeight();
                        e.preventDefault();
                        return;
                    }
                    
                    // Normal drag
                    hscSelectLayer(index);
                    activeLayer = this;
                    startX = e.clientX;
                    startY = e.clientY;
                    startLeft = parseInt($(this).css('left'));
                    startTop = parseInt($(this).css('top'));
                    
                    $(this).css('cursor', 'grabbing');
                    e.preventDefault();
                });
                
                // Mouse move
                $(document).on('mousemove', function(e) {
                    if (!activeLayer) return;
                    
                    const deltaX = e.clientX - startX;
                    const deltaY = e.clientY - startY;
                    
                    if (isResizing) {
                        // Resize
                        const $layer = $(activeLayer);
                        let newWidth = startWidth;
                        let newHeight = startHeight;
                        let newLeft = startLeft;
                        let newTop = startTop;
                        
                        if (resizeDirection.includes('e')) {
                            newWidth = startWidth + deltaX;
                        }
                        if (resizeDirection.includes('w')) {
                            newWidth = startWidth - deltaX;
                            newLeft = startLeft + deltaX;
                        }
                        if (resizeDirection.includes('s')) {
                            newHeight = startHeight + deltaY;
                        }
                        if (resizeDirection.includes('n')) {
                            newHeight = startHeight - deltaY;
                            newTop = startTop + deltaY;
                        }
                        
                        newWidth = Math.max(50, newWidth);
                        newHeight = Math.max(30, newHeight);
                        
                        $layer.css({
                            width: newWidth + 'px',
                            height: newHeight + 'px',
                            left: newLeft + 'px',
                            top: newTop + 'px'
                        });
                        
                        $layer.find('.hsc-layer-badge').text(`${newWidth}×${newHeight}px • ${newLeft}px, ${newTop}px`);
                        
                    } else {
                        // Drag
                        let newLeft = startLeft + deltaX;
                        let newTop = startTop + deltaY;
                        
                        const container = $('#hsc-preview-slide')[0].getBoundingClientRect();
                        const layerWidth = $(activeLayer).outerWidth();
                        const layerHeight = $(activeLayer).outerHeight();
                        
                        newLeft = Math.max(0, Math.min(newLeft, container.width - layerWidth));
                        newTop = Math.max(0, Math.min(newTop, container.height - layerHeight));
                        
                        $(activeLayer).css({ left: newLeft + 'px', top: newTop + 'px' });
                        $(activeLayer).find('.hsc-layer-badge').text(`${hscGetLayerTitle(hscSliderData.slides[hscCurrentSlideIndex].layers[hscCurrentLayerIndex])} • ${Math.round(newLeft)}px, ${Math.round(newTop)}px`);
                    }
                });
                
                // Mouse up
                $(document).on('mouseup', function() {
                    if (!activeLayer) return;
                    
                    const $layer = $(activeLayer);
                    const layerIndex = $layer.data('layer-index');
                    const slide = hscSliderData.slides[hscCurrentSlideIndex];
                    const layer = slide.layers[layerIndex];
                    
                    layer.pos_x = parseInt($layer.css('left'));
                    layer.pos_y = parseInt($layer.css('top'));
                    
                    if (isResizing) {
                        layer.width = $layer.outerWidth();
                        layer.height = $layer.outerHeight();
                    }
                    
                    // Update form inputs
                    $('#hsc-layer-pos-x').val(layer.pos_x);
                    $('#hsc-layer-pos-y').val(layer.pos_y);
                    if (isResizing) {
                        $('#hsc-layer-width').val(layer.width);
                        $('#hsc-layer-height').val(layer.height);
                    }
                    
                    $layer.css('cursor', 'move');
                    activeLayer = null;
                    isResizing = false;
                    resizeDirection = '';
                });
            }
            
            // Keyboard shortcuts
            $(document).on('keydown', function(e) {
                if (!$('#hsc-fullscreen-modal').is(':visible')) return;
                if (hscCurrentSlideIndex === -1 || hscCurrentLayerIndex === -1) return;
                
                const slide = hscSliderData.slides[hscCurrentSlideIndex];
                const layer = slide.layers[hscCurrentLayerIndex];
                
                if (layer.locked) return;
                
                const step = e.shiftKey ? 10 : 1;
                let changed = false;
                
                if (e.key === 'ArrowLeft') {
                    layer.pos_x = Math.max(0, layer.pos_x - step);
                    changed = true;
                    e.preventDefault();
                } else if (e.key === 'ArrowRight') {
                    layer.pos_x += step;
                    changed = true;
                    e.preventDefault();
                } else if (e.key === 'ArrowUp') {
                    layer.pos_y = Math.max(0, layer.pos_y - step);
                    changed = true;
                    e.preventDefault();
                } else if (e.key === 'ArrowDown') {
                    layer.pos_y += step;
                    changed = true;
                    e.preventDefault();
                } else if (e.key === 'Delete' || e.key === 'Backspace') {
                    if (!$(e.target).is('input, textarea')) {
                        if (confirm('Bu katmanı silmek istediğinizden emin misiniz?')) {
                            slide.layers.splice(hscCurrentLayerIndex, 1);
                            hscRenderLayersList();
                            if (slide.layers.length > 0) {
                                hscSelectLayer(Math.min(hscCurrentLayerIndex, slide.layers.length - 1));
                            } else {
                                $('#hsc-layer-settings').hide();
                                hscRenderPreview();
                            }
                        }
                        e.preventDefault();
                    }
                }
                
                if (changed) {
                    $('#hsc-layer-pos-x').val(layer.pos_x);
                    $('#hsc-layer-pos-y').val(layer.pos_y);
                    hscRenderPreview();
                }
            });
            
            // Preview slide navigation
            window.hscPrevPreviewSlide = function() {
                if (hscCurrentSlideIndex > 0) {
                    hscSelectSlide(hscCurrentSlideIndex - 1);
                }
            };
            
            window.hscNextPreviewSlide = function() {
                if (hscCurrentSlideIndex < hscSliderData.slides.length - 1) {
                    hscSelectSlide(hscCurrentSlideIndex + 1);
                }
            };
            
            function hscUpdateSlideIndicator() {
                const total = hscSliderData.slides.length;
                const current = hscCurrentSlideIndex + 1;
                $('#hsc-current-slide-indicator').text(`Slayt ${current} / ${total}`);
            }
            
            // SAVE DATA
            window.hscSaveFullscreenData = function() {
                const $form = $('.hsc-editor-form');
                $form.find('.hsc-slider-data').val(JSON.stringify(hscSliderData)).trigger('change');
                $('#hsc-fullscreen-modal').fadeOut(300);
                
                // Update button
                const slideCount = hscSliderData.slides.length;
                $form.find('.current-settings').html(`<small style="color: #10B981; font-weight: 600;">✓ ${slideCount} slayt tanımlandı</small>`);
            };
            
            // PARTICLE EFFECTS
            function hscInitParticles(effect) {
                const $container = $('#hsc-preview-slide .hsc-particles');
                if ($container.length === 0 || effect === 'none') return;

                $container.empty();

                if (effect === 'snow') {
                    for (let i = 0; i < 50; i++) {
                        const $particle = $('<div class="hsc-particle hsc-snow"></div>');
                        $particle.css({
                            left: Math.random() * 100 + '%',
                            animationDuration: (Math.random() * 7 + 8) + 's',
                            animationDelay: Math.random() * 10 + 's',
                            width: (Math.random() * 4 + 2) + 'px',
                            height: (Math.random() * 4 + 2) + 'px',
                            opacity: Math.random() * 0.5 + 0.3
                        });
                        $container.append($particle);
                    }
                } else if (effect === 'bubbles') {
                    for (let i = 0; i < 20; i++) {
                        const $particle = $('<div class="hsc-particle hsc-bubble"></div>');
                        $particle.css({
                            left: Math.random() * 100 + '%',
                            animationDuration: (Math.random() * 10 + 10) + 's',
                            animationDelay: Math.random() * 15 + 's',
                            width: (Math.random() * 20 + 5) + 'px',
                            height: (Math.random() * 20 + 5) + 'px'
                        });
                        $container.append($particle);
                    }
                }
            }

            // UTILITIES
            function hscEscape(str) {
                if (typeof str !== 'string') return '';
                return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
            }
            
        })(jQuery);
        </script>
        <?php
        return ob_get_clean();
    }
    
    public function get_default_content() {
        $defaultSlide = array(
            'admin_label' => 'Epic Mountain & Dragon',
            'background_type' => 'video',
            'background_color' => '#1a1a1a',
            'background_image' => '',
            'background_video' => 'https://videocdn.cdnpk.net/videos/8dac444b-996e-4f7c-a8b9-e0dd1a76eff1/horizontal/previews/watermarked/large.mp4',
            'bg_overlay' => true,
            'bg_overlay_type' => 'gradient',
            'bg_overlay_color' => 'rgba(0,0,0,0.5)',
            'bg_overlay_gradient_start' => 'rgba(0,0,0,0.7)',
            'bg_overlay_gradient_end' => 'rgba(0,0,0,0.0)',
            'bg_overlay_gradient_direction' => 'to top',
            'slide_transition' => 'fade',
            'particle_effect' => 'none',
            'bg_ken_burns' => false,
            'layers' => array(
                array(
                    'type' => 'image',
                    'image_url' => 'https://www.pngall.com/wp-content/uploads/2/Dragon-PNG-Picture.png',
                    'alt_text' => 'Dragon',
                    'width' => 400,
                    'height' => 'auto',
                    'pos_x' => 100,
                    'pos_y' => 150,
                    'z_index' => 15,
                    'hover_effect' => 'zoom',
                    'link_url' => '',
                    'link_new_tab' => false,
                    'animation_in' => 'fade-in-right',
                    'animation_delay_in' => 500,
                    'animation_duration_in' => 1500,
                    'animation_out' => 'fade-out-left',
                    'animation_delay_out' => 0,
                    'animation_duration_out' => 800,
                    'hidden' => false,
                    'locked' => false
                ),
                array(
                    'type' => 'text',
                    'content' => '<h1>Epic Mountain Adventure</h1>',
                    'pos_x' => 700,
                    'pos_y' => 200,
                    'width' => 500,
                    'height' => 'auto',
                    'z_index' => 10,
                    'font_size' => 56,
                    'color' => '#FFFFFF',
                    'font_weight' => 900,
                    'text_align' => 'left',
                    'line_height' => 1.2,
                    'letter_spacing' => -1,
                    'animation_in' => 'slide-from-top',
                    'animation_delay_in' => 300,
                    'animation_duration_in' => 1000,
                    'animation_out' => 'fade-out-up',
                    'animation_delay_out' => 0,
                    'animation_duration_out' => 500,
                    'hidden' => false,
                    'locked' => false
                ),
                array(
                    'type' => 'button',
                    'content' => 'Keşfet',
                    'link_url' => '#explore',
                    'link_new_tab' => false,
                    'pos_x' => 700,
                    'pos_y' => 350,
                    'width' => 'auto',
                    'height' => 'auto',
                    'z_index' => 10,
                    'font_size' => 18,
                    'color' => '#FFFFFF',
                    'font_weight' => 700,
                    'text_align' => 'center',
                    'bg_color' => '#10B981',
                    'padding' => '16px 36px',
                    'border_radius' => 8,
                    'animation_in' => 'fade-in-up',
                    'animation_delay_in' => 900,
                    'animation_duration_in' => 1000,
                    'animation_out' => 'fade-out-down',
                    'animation_delay_out' => 0,
                    'animation_duration_out' => 500,
                    'hidden' => false,
                    'locked' => false
                )
            )
        );

        return array(
            'slider_data' => json_encode(array(
                'global' => array(
                    'slider_height' => '600px',
                    'global_transition' => 'fade',
                    'autoplay' => true,
                    'autoplay_speed' => 7000,
                    'nav_arrows' => true,
                    'nav_bullets' => true,
                    'mouse_parallax' => true,
                    'parallax_amount' => 30
                ),
                'slides' => array($defaultSlide)
            ))
        );
    }

    private function decode_json_string($value) {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded))) ? $decoded : array();
        }
        return (is_array($value) || is_object($value)) ? $value : array();
    }

    public function get_category() {
        return 'structure';
    }
}
