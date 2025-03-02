/**
 * Child Theme Navigation Module
 * 
 * Custom navigation functionality for the child theme
 * This extends/overrides parent theme navigation
 */

// Navigation module
const Navigation = {
    // DOM elements
    elements: {
      nav: null,
      menuToggle: null,
      menu: null,
      dropdownToggles: null,
      megaMenus: null
    },
  
    // Settings
    settings: {
      mobileBreakpoint: 992,
      megaMenuEnabled: true,
      animationSpeed: 300
    },
  
    /**
     * Initialize navigation functionality
     */
    init() {
      this.cacheElements();
      
      if (!this.elements.nav) return;
      
      this.getSettings();
      this.bindEvents();
      this.setupMegaMenus();
    },
  
    /**
     * Cache DOM elements
     */
    cacheElements() {
      this.elements.nav = document.querySelector('.main-navigation');
      
      if (!this.elements.nav) return;
      
      this.elements.menuToggle = this.elements.nav.querySelector('.menu-toggle');
      this.elements.menu = this.elements.nav.querySelector('.primary-menu-container');
      this.elements.dropdownToggles = this.elements.nav.querySelectorAll('.menu-item-has-children > a');
      this.elements.megaMenus = document.querySelectorAll('.mega-menu');
    },
  
    /**
     * Get settings from data attributes
     */
    getSettings() {
      if (this.elements.nav.dataset.mobileBreakpoint) {
        this.settings.mobileBreakpoint = parseInt(this.elements.nav.dataset.mobileBreakpoint, 10);
      }
      
      if (this.elements.nav.dataset.megaMenu !== undefined) {
        this.settings.megaMenuEnabled = this.elements.nav.dataset.megaMenu === 'true';
      }
      
      if (this.elements.nav.dataset.animationSpeed) {
        this.settings.animationSpeed = parseInt(this.elements.nav.dataset.animationSpeed, 10);
      }
    },
  
    /**
     * Bind event listeners
     */
    bindEvents() {
      // Use parent theme event listeners if available
      if (window.ParentTheme && window.ParentTheme.Navigation) {
        // We might want to use some parent theme functionality
        // But for now we'll implement our own to ensure full customizability
      }
      
      // Mobile menu toggle
      if (this.elements.menuToggle) {
        this.elements.menuToggle.addEventListener('click', this.toggleMenu.bind(this));
      }
      
      // Dropdown toggles
      this.elements.dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', this.handleDropdownToggle.bind(this));
      });
      
      // Close dropdowns when clicking outside
      document.addEventListener('click', this.handleOutsideClick.bind(this));
      
      // Handle scroll events for sticky header
      window.addEventListener('scroll', this.handleScroll.bind(this));
      
      // Handle resize events
      window.addEventListener('resize', this.handleResize.bind(this));
    },
  
    /**
     * Set up mega menus
     */
    setupMegaMenus() {
      if (!this.settings.megaMenuEnabled) return;
      
      this.elements.megaMenus.forEach(megaMenu => {
        // Position mega menu
        const parent = megaMenu.closest('.menu-item');
        
        if (parent) {
          const positionMegaMenu = () => {
            if (window.innerWidth < this.settings.mobileBreakpoint) return;
            
            const navWidth = this.elements.nav.offsetWidth;
            const navLeft = this.elements.nav.getBoundingClientRect().left;
            
            megaMenu.style.width = navWidth + 'px';
            megaMenu.style.left = -parent.getBoundingClientRect().left + navLeft + 'px';
          };
          
          // Position on load
          positionMegaMenu();
          
          // Reposition on resize
          window.addEventListener('resize', positionMegaMenu);
        }
      });
    },
  
    /**
     * Toggle mobile menu
     * @param {Event} event - Click event
     */
    toggleMenu(event) {
      event.preventDefault();
      
      const isOpen = this.elements.menuToggle.getAttribute('aria-expanded') === 'true';
      
      this.elements.menuToggle.setAttribute('aria-expanded', !isOpen);
      this.elements.menuToggle.classList.toggle('active');
      document.body.classList.toggle('menu-open');
      
      // Toggle menu visibility with animation
      if (isOpen) {
        this.animateMenuClose();
      } else {
        this.animateMenuOpen();
      }
    },
  
    /**
     * Animate menu opening
     */
    animateMenuOpen() {
      // Custom animation for child theme
      const menu = this.elements.menu;
      
      menu.style.display = 'block';
      menu.style.opacity = '0';
      menu.style.transform = 'translateY(-10px)';
      
      setTimeout(() => {
        menu.style.transition = `opacity ${this.settings.animationSpeed}ms ease, transform ${this.settings.animationSpeed}ms ease`;
        menu.style.opacity = '1';
        menu.style.transform = 'translateY(0)';
      }, 10);
    },
  
    /**
     * Animate menu closing
     */
    animateMenuClose() {
      // Custom animation for child theme
      const menu = this.elements.menu;
      
      menu.style.opacity = '0';
      menu.style.transform = 'translateY(-10px)';
      
      setTimeout(() => {
        menu.style.display = 'none';
        menu.style.transition = '';
      }, this.settings.animationSpeed);
    },
  
    /**
     * Handle dropdown toggle click
     * @param {Event} event - Click event
     */
    handleDropdownToggle(event) {
      const target = event.currentTarget;
      const parentLi = target.parentElement;
      
      // Only handle this in mobile view
      if (window.innerWidth < this.settings.mobileBreakpoint) {
        event.preventDefault();
        
        const isOpen = parentLi.classList.contains('dropdown-open');
        
        // Close all siblings
        const siblings = Array.from(parentLi.parentElement.children);
        siblings.forEach(sibling => {
          if (sibling !== parentLi) {
            sibling.classList.remove('dropdown-open');
            const submenu = sibling.querySelector('.sub-menu');
            if (submenu) {
              submenu.style.display = 'none';
            }
          }
        });
        
        // Toggle current dropdown
        if (isOpen) {
          parentLi.classList.remove('dropdown-open');
          const submenu = parentLi.querySelector('.sub-menu');
          if (submenu) {
            submenu.style.display = 'none';
          }
        } else {
          parentLi.classList.add('dropdown-open');
          const submenu = parentLi.querySelector('.sub-menu');
          if (submenu) {
            submenu.style.display = 'block';
          }
        }
      }
    },
  
    /**
     * Handle clicks outside the navigation
     * @param {Event} event - Click event
     */
    handleOutsideClick(event) {
      if (window.innerWidth < this.settings.mobileBreakpoint) {
        const isMenuOpen = document.body.classList.contains('menu-open');
        
        if (isMenuOpen) {
          const isMenuClick = 
            this.elements.menuToggle.contains(event.target) || 
            this.elements.menu.contains(event.target);
          
          if (!isMenuClick) {
            this.toggleMenu(event);
          }
        }
      }
    },
  
    /**
     * Handle scroll events
     */
    handleScroll() {
      const header = document.querySelector('.site-header');
      if (!header) return;
      
      const isSticky = header.classList.contains('sticky-wrapper');
      if (!isSticky) return;
      
      const scrollTop = window.scrollY || document.documentElement.scrollTop;
      
      if (scrollTop > 100) {
        header.classList.add('is-sticky');
        header.classList.add('shrink');
      } else {
        header.classList.remove('shrink');
        
        if (scrollTop === 0) {
          header.classList.remove('is-sticky');
        }
      }
    },
  
    /**
     * Handle window resize events
     */
    handleResize() {
      if (window.innerWidth >= this.settings.mobileBreakpoint) {
        // Reset mobile menu styles when viewport is desktop size
        this.elements.menu.style.display = '';
        this.elements.menu.style.opacity = '';
        this.elements.menu.style.transform = '';
        
        document.body.classList.remove('menu-open');
        this.elements.menuToggle.classList.remove('active');
        this.elements.menuToggle.setAttribute('aria-expanded', 'false');
      }
    }
  };
  
  // Initialize navigation
  Navigation.init();
  
  export default Navigation;