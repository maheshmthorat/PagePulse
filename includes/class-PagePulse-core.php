<?php
if ((!defined('ABSPATH'))) {
   die;
}
/**
 * Class PagePulse
 * The file that defines the core plugin class
 *
 * @author Mahesh Thorat
 * @link https://maheshthorat.web.app
 * @version 0.1
 * @package PagePulse
 */
class PagePulse_Core
{
   /**
    * The unique identifier of this plugin
    */
   protected $plugin_name;

   /**
    * The current version of the plugin
    */
   protected $version;

   /**
    * Define the core functionality of the plugin
    */
   public function __construct()
   {
      $this->plugin_name = PagePulse_PLUGIN_IDENTIFIER;
      $this->version = PagePulse_PLUGIN_VERSION;
   }
   public function run()
   {
      /**
       * The admin of plugin class 
       * admin related content and options
       */
      require PagePulse_PLUGIN_ABS_PATH . 'admin/class-PagePulse-admin.php';

      $plugin_admin = new PagePulse_Admin($this->get_plugin_name(), $this->get_version());
      if (is_admin()) {
         add_action('admin_menu', array($plugin_admin, 'return_admin_menu'));
         add_action('init', array($plugin_admin, 'return_update_options'));
         add_filter('plugin_action_links_pagepulse/pagepulse.php', array($plugin_admin, 'PagePulse_settings_link'));
      }

      function pagePulseCustomStyle()
      {
         wp_enqueue_style(PagePulse_PLUGIN_IDENTIFIER . 'style',  PagePulse_PLUGIN_URL . "/assets/css/style.css", array(), PagePulse_PLUGIN_VERSION, false);
      }

      function adminStyles()
      {
         add_action('admin_print_styles', 'pagePulseCustomStyle');
      }
      add_action('admin_enqueue_scripts', 'adminStyles');

      $opts = get_option('_PagePulse');
      if (isset($opts['default_PagePulse']) && $opts['default_PagePulse'] == 'on') {
         if (!is_admin()) {
            add_action('wp_enqueue_scripts', 'pagePulseCustomStyle');
            add_action('wp_enqueue_scripts', array($this, 'add_inline_script'));
            add_action('wp_head', array($this, 'call_action_add_styles'));
         }
      }
      add_action('admin_enqueue_scripts', array($this, 'my_inline_scripts_and_styles'));
   }

   public function my_inline_scripts_and_styles()
   {
      wp_register_script('pagepulse-script', '', array(), PagePulse_PLUGIN_VERSION, false);
      wp_register_style('pagepulse-style', '', array(), PagePulse_PLUGIN_VERSION, false);

      wp_enqueue_script('pagepulse-script');
      wp_enqueue_style('pagepulse-style');

      $opts = get_option('_PagePulse');
      $animationStartClass = '';
      $animationEndClass = '';
      if (isset($opts['pageLoadAnimation']) && $opts['pageLoadAnimation'] == "slideUpDown") {
         $animationStartClass = 'slideUp';
         $animationEndClass = 'slideDown';
      }
      if (isset($opts['pageLoadAnimation']) && $opts['pageLoadAnimation'] == "slideDownUp") {
         $animationStartClass = 'slideBottom';
         $animationEndClass = 'slideTop';
      }
      if (isset($opts['pageLoadAnimation']) && $opts['pageLoadAnimation'] == "fadeInOut") {
         $animationStartClass = 'fadeOut';
         $animationEndClass = 'fadeIn';
      }
      if (isset($opts['pageLoadAnimation']) && $opts['pageLoadAnimation'] == "clipFromCenter") {
         $animationStartClass = 'clipFromCenterStart';
         $animationEndClass = 'clipFromCenterEnd';
      }
      if (isset($opts['pageLoadAnimation']) && $opts['pageLoadAnimation'] == "clipLeftRight") {
         $animationStartClass = 'clipLeftRightStart';
         $animationEndClass = 'clipLeftRightEnd';
      }
      if (isset($opts['pageLoadAnimation']) && $opts['pageLoadAnimation'] == "animatedFullSlideTopBottom") {
         $animationStartClass = 'animatedFullSlideTopBottomStart';
         $animationEndClass = 'animatedFullSlideTopBottomEnd';
      }
      if (isset($opts['pageLoadAnimation']) && $opts['pageLoadAnimation'] == "animatedFullSlideBottomTop") {
         $animationStartClass = 'animatedFullSlideBottomTopStart';
         $animationEndClass = 'animatedFullSlideBottomTopEnd';
      }
      $page_background_color = defined('PagePulse_DEFAULT_COLOR') ? PagePulse_DEFAULT_COLOR : '#fff';
      $page_background_opacity = 1;
      if ((isset($opts['page_background_color']) && $opts['page_background_color'])) {
         $page_background_color = $opts['page_background_color'];
      }
      if ((isset($opts['page_background_opacity']) && $opts['page_background_opacity'])) {
         $page_background_opacity = $opts['page_background_opacity'];
      }

      // Inline JavaScript
      $inline_js = "
          const setBarColor = function() {
              let page_background_color = jQuery('#page_background_color').val();
              let scroller_color = jQuery('#scroller_color').val();
              jQuery('.loadingAnimationPreview').css('background', page_background_color);
          }
          jQuery(document).ready(function() {
              setBarColor();
              setLoadingIconOption();
              setPage_background_opacity();
          });
  
          jQuery(document).on('input', '#page_background_color', function() {
              setBarColor();
          });
  
          const setLoadingIconOption = function() {
              if (jQuery('.loading_icon_option').prop('checked') == true) {
                 jQuery('.loading_icon_option_wrap').removeClass('disabledOption');
              } else {
                 jQuery('.loading_icon_option_wrap').addClass('disabledOption');
              }
          }
  
          const setPageDefault_PagePulse = function() {
              if (jQuery('.default_PagePulse').prop('checked') == true) {
                 jQuery('.pluginOptions ').removeClass('disabledOption');
              } else {
                 jQuery('.pluginOptions ').addClass('disabledOption');
              }
          }
  
          const setPage_background_opacity = function() {
              jQuery('.opacityVal').html(jQuery('#page_background_opacity').val());
          }
  
          jQuery(document).on('change', '.loading_icon_option', function() {
              setLoadingIconOption();
          });
  
          jQuery(document).on('change', '.default_PagePulse', function() {
              setPageDefault_PagePulse();
          });
  
          jQuery(document).on('input', '#page_background_opacity', function() {
              setPage_background_opacity();
          });
  
          setInterval(() => {
              if (jQuery('.loadingAnimationPreview').hasClass('{$animationStartClass}')) {
                  jQuery('.loadingAnimationPreview').addClass('{$animationEndClass}');
                  jQuery('.loadingAnimationPreview').removeClass('{$animationStartClass}');
              } else {
                  jQuery('.loadingAnimationPreview').removeClass('{$animationEndClass}');
                  jQuery('.loadingAnimationPreview').addClass('{$animationStartClass}');
              }
          }, 1000);
      ";
      wp_add_inline_script('pagepulse-script', $inline_js);

      // Inline CSS
      $inline_css = "
          .desktop-container {
              border: 5px solid;
              border-bottom: 30px solid;
              border-radius: 10px;
              box-shadow: 10px 10px 10px #ccc;
              width: 500px;
              height: 300px;
              position: relative;
              overflow: hidden;
          }
  
          .loadingAnimationPreview {
              width: 100%;
              height: 100%;
              margin: 0 auto;
              display: flex;
              align-items: center;
              justify-content: center;
              position: absolute;
              top: 0;
              left: 0;
              z-index: 9999999;
              --bg-color: {$page_background_color};
              --bg-opacity: {$page_background_opacity};
          }
  
          .pluginOptions {
              width: 100%;
          }
  
          .disabledOption,
          .pageBackgroundWrap.disabledOption,
          .loading_icon_option_wrap.disabledOption,
          .pluginOptions.disabledOption {
              filter: blur(3px);
              pointer-events: none;
          }
      ";
      wp_add_inline_style('pagepulse-style', $inline_css);
   }

   public function add_inline_script()
   {
      wp_enqueue_script('jquery');
      wp_register_script('pagepulse-main-script', '', array(), PagePulse_PLUGIN_VERSION, false);
      wp_enqueue_script('pagepulse-main-script');

      $animationStartClass = '';
      $animationEndClass = '';
      $opts = get_option('_PagePulse');
      if (isset($opts['pageLoadAnimation'])) {
         switch ($opts['pageLoadAnimation']) {
            case "slideUpDown":
               $animationStartClass = 'slideUp';
               $animationEndClass = 'slideDown';
               break;
            case "slideDownUp":
               $animationStartClass = 'slideBottom';
               $animationEndClass = 'slideTop';
               break;
            case "fadeInOut":
               $animationStartClass = 'fadeOut';
               $animationEndClass = 'fadeIn';
               break;
            case "clipFromCenter":
               $animationStartClass = 'clipFromCenterStart';
               $animationEndClass = 'clipFromCenterEnd';
               break;
            case "clipLeftRight":
               $animationStartClass = 'clipLeftRightStart';
               $animationEndClass = 'clipLeftRightEnd';
               break;
            case "animatedFullSlideTopBottom":
               $animationStartClass = 'animatedFullSlideTopBottomStart';
               $animationEndClass = 'animatedFullSlideTopBottomEnd';
               break;
            case "animatedFullSlideBottomTop":
               $animationStartClass = 'animatedFullSlideBottomTopStart';
               $animationEndClass = 'animatedFullSlideBottomTopEnd';
               break;
         }
      }

      $inline_script = "
          document.addEventListener('readystatechange', function(event) {
              if (event.target.readyState === 'complete') {
                  setTimeout(function() {
                      jQuery('#webAppLoader').addClass('$animationStartClass');
                  }, 100);
              }
              if (event.target.readyState === 'loading') {
                  jQuery('#webAppLoader').addClass('$animationEndClass');
              }
          });
          window.onbeforeunload = function() {
              jQuery('#webAppLoader').addClass('$animationEndClass');
          }
          window.onunload = function() {
              jQuery('#webAppLoader').addClass('$animationStartClass');
          }
          document.addEventListener('submit', function(event) {
              jQuery('#webAppLoader').addClass('$animationStartClass');
          });
      ";

      wp_add_inline_script('pagepulse-main-script', $inline_script, 'after');
   }



   public function call_action_add_styles()
   {
      $opts = get_option('_PagePulse');

?>

      <?php
      $page_background_color = PagePulse_DEFAULT_COLOR;
      $page_background_opacity = 1;
      if ((isset($opts['page_background_color']) && $opts['page_background_color'])) {
         $page_background_color = $opts['page_background_color'];
      }
      if ((isset($opts['page_background_opacity']) && $opts['page_background_opacity'])) {
         $page_background_opacity = $opts['page_background_opacity'];
      }
      ?>
      <div id="webAppLoader" style="--bg-color: <?php echo esc_attr($page_background_color); ?>;--bg-opacity: <?php echo esc_attr($page_background_opacity); ?>">
      </div>
<?php
   }

   public function get_plugin_name()
   {
      return $this->plugin_name;
   }
   public function get_version()
   {
      return $this->version;
   }
}
