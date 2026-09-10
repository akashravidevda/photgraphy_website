/**
 * Swamini Photography - Core UI Controller
 */

document.addEventListener('DOMContentLoaded', () => {
  const header = document.querySelector('.site-header-upgrade') || document.querySelector('.site-header');
  const navLinks = document.querySelectorAll('.nav-item-link, .nav-link');
  const sections = document.querySelectorAll('section[id]');
  const mobileToggle = document.querySelector('.nav-hamburger-btn, .mobile-menu-toggle');
  const mobileDrawer = document.querySelector('.nav-mobile-drawer-upgrade, .mobile-nav-drawer');
  const mobileNavLinks = document.querySelectorAll('.nav-mobile-item-link, .mobile-nav-link');

  // 1. Sticky Header Blur on Scroll
  function handleScroll() {
    if (!header) return;
    if (window.scrollY > 30) {
      header.classList.add('scrolled');
    } else {
      header.classList.remove('scrolled');
    }

    // 2. ScrollSpy for Active Nav Link
    let current = '';
    const scrollPos = window.scrollY + 140;

    sections.forEach((section) => {
      const top = section.offsetTop;
      const height = section.offsetHeight;
      if (scrollPos >= top && scrollPos < top + height) {
        current = section.getAttribute('id');
      }
    });

    navLinks.forEach((link) => {
      link.classList.remove('active');
      if (link.getAttribute('href') === `#${current}`) {
        link.classList.add('active');
      }
    });
  }

  window.addEventListener('scroll', handleScroll, { passive: true });
  handleScroll();

  // 3. Mobile Drawer Toggle & Morphing Hamburger
  if (mobileToggle && mobileDrawer) {
    function closeDrawer() {
      mobileDrawer.classList.remove('open');
      mobileToggle.classList.remove('is-active');
      mobileToggle.setAttribute('aria-expanded', 'false');
      mobileDrawer.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
    }

    function openDrawer() {
      mobileDrawer.classList.add('open');
      mobileToggle.classList.add('is-active');
      mobileToggle.setAttribute('aria-expanded', 'true');
      mobileDrawer.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    }

    mobileToggle.addEventListener('click', () => {
      const isOpen = mobileDrawer.classList.contains('open');
      if (isOpen) {
        closeDrawer();
      } else {
        openDrawer();
      }
    });

    // Close on link click
    mobileNavLinks.forEach((link) => {
      link.addEventListener('click', closeDrawer);
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && mobileDrawer.classList.contains('open')) {
        closeDrawer();
      }
    });
  }

  // 4. Dynamic WhatsApp Service Deep Linking
  const WHATSAPP_BASE = 'https://wa.me/918432582511';
  const WHATSAPP_TEMPLATES = {
    general: 'Hi Swamini Photography, I would like to enquire about a shoot.',
    wedding: 'Hi Swamini Photography, I would like to enquire about your Wedding Photography & Cinematography packages.',
    maternity: 'Hi Swamini Photography, I would like to enquire about a Maternity Shoot.',
    baby: 'Hi Swamini Photography, I would like to enquire about a Baby Shoot.',
    pre_wedding: 'Hi Swamini Photography, I would like to enquire about a Pre-Wedding Shoot.',
    events: 'Hi Swamini Photography, I would like to enquire about Birthday & Event Photography.',
    couple: 'Hi Swamini Photography, I would like to enquire about a Couple Shoot.',
    corporate: 'Hi Swamini Photography, I would like to enquire about Corporate Photography.',
    albums: 'Hi Swamini Photography, I would like to enquire about Premium Photo Frames & Albums.'
  };

  document.querySelectorAll('[data-whatsapp-service]').forEach((elem) => {
    const serviceKey = elem.getAttribute('data-whatsapp-service') || 'general';
    const text = WHATSAPP_TEMPLATES[serviceKey] || WHATSAPP_TEMPLATES.general;
    elem.href = `${WHATSAPP_BASE}?text=${encodeURIComponent(text)}`;
  });

  // 5. Scroll to contact when "Book Your Shoot" is clicked
  const toggleEnquiryFormBtn = document.getElementById('toggleEnquiryFormBtn');
  const nameInput = document.getElementById('nameInput');
  if (toggleEnquiryFormBtn && nameInput) {
    toggleEnquiryFormBtn.addEventListener('click', () => {
      nameInput.focus();
      nameInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
  }
});
