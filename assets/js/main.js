/**
 * Child Theme Main JavaScript
 * 
 * Main entry point for all child theme JS functionality
 */

// Import SCSS file for Vite processing
import '../scss/main.scss';

// Import modules
import './modules/navigation';
import './modules/animations';
import customSlider from './modules/slider';
import lazyLoad from './modules/lazy-load';

// Child Theme namespace
const ChildTheme = {
  /**
   * Initialize theme
   */
  init() {
    // Initialize parent theme functionality if available
    if (window.ParentTheme) {
      // You can access parent theme modules like this:
      // window.ParentTheme.Navigation 
      // window.ParentTheme.Animations
      
      // But generally we want to override with our own functionality
      // So we don't call parent theme's init directly
    }
    
    // Initialize our own components
    this.initComponents();
    
    // Handle DOM ready events
    this.domReady(() => {
      this.setupCustomFunctionality();
    });
  },
  
  /**
   * Initialize theme components
   */
  initComponents() {
    // Initialize custom slider if element exists
    if (document.querySelector('.custom-slider')) {
      customSlider.init({
        selector: '.custom-slider',
        slidesToShow: 3,
        autoplay: true
      });
    }
    
    // Initialize lazy loading
    lazyLoad.init();
  },
  
  /**
   * DOM ready callback
   * @param {Function} callback - Function to execute when DOM is ready
   */
  domReady(callback) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', callback);
    } else {
      callback();
    }
  },
  
  /**
   * Setup custom functionality specific to child theme
   */
  setupCustomFunctionality() {
    // Setup smooth scroll for anchor links
    this.setupSmoothScroll();
    
    // Setup form enhancements
    this.enhanceForms();
    
    // Setup any third-party integrations
    this.setupThirdPartyIntegrations();
  },
  
  /**
   * Setup smooth scroll for anchor links
   */
  setupSmoothScroll() {
    document.querySelectorAll('a[href^="#"]:not([href="#"])').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        
        const targetId = this.getAttribute('href');
        const targetElement = document.querySelector(targetId);
        
        if (targetElement) {
          // Add offset for fixed header if needed
          const headerHeight = document.querySelector('.site-header') ? 
            document.querySelector('.site-header').offsetHeight : 0;
          
          const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset;
          const offsetPosition = targetPosition - headerHeight;
          
          window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
          });
        }
      });
    });
  },
  
  /**
   * Enhance forms with custom functionality
   */
  enhanceForms() {
    // Add floating labels to form fields
    document.querySelectorAll('.form-floating input, .form-floating textarea').forEach(field => {
      const updateLabel = () => {
        const label = field.parentNode.querySelector('label');
        if (label) {
          if (field.value !== '') {
            label.classList.add('active');
          } else {
            label.classList.remove('active');
          }
        }
      };
      
      // Initial state
      updateLabel();
      
      // Event listeners
      field.addEventListener('focus', updateLabel);
      field.addEventListener('blur', updateLabel);
      field.addEventListener('input', updateLabel);
    });
    
    // Add form validation
    document.querySelectorAll('form.validate').forEach(form => {
      form.addEventListener('submit', e => {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
          if (!field.value.trim()) {
            isValid = false;
            field.classList.add('is-invalid');
          } else {
            field.classList.remove('is-invalid');
          }
        });
        
        if (!isValid) {
          e.preventDefault();
        }
      });
    });
  },
  
  /**
   * Setup third-party integrations
   */
  setupThirdPartyIntegrations() {
    // Initialize AOS (Animate On Scroll) if library is loaded
    if (typeof AOS !== 'undefined') {
      AOS.init({
        duration: 800,
        once: true,
        offset: 100
      });
    }
    
    // Initialize custom lightbox for gallery
    const galleries = document.querySelectorAll('.gallery');
    if (galleries.length && typeof GLightbox !== 'undefined') {
      GLightbox({
        selector: '.gallery a',
        touchNavigation: true,
        loop: true
      });
    }
  }
};

// Initialize the child theme
document.addEventListener('DOMContentLoaded', () => {
  ChildTheme.init();
});

// Make components available globally
window.ChildTheme = ChildTheme;

export default ChildTheme;