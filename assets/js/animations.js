/**
 * Swamini Photography - GSAP & Motion Animations
 */

document.addEventListener('DOMContentLoaded', () => {
  // Check if reduced motion is requested
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (prefersReducedMotion) return;

  // Ensure GSAP is loaded
  if (typeof gsap === 'undefined') return;

  // Register ScrollTrigger if available
  if (typeof ScrollTrigger !== 'undefined') {
    gsap.registerPlugin(ScrollTrigger);
  }

  // 1. Hero Entrance Sequence
  const heroTimeline = gsap.timeline({ defaults: { ease: 'power3.out', duration: 1 } });
  
  heroTimeline
    .from('.site-header', { y: -50, opacity: 0, duration: 0.8 })
    .from('.hero-meta-strip', { y: 20, opacity: 0, duration: 0.6 }, '-=0.4')
    .from('.hero-headline', { y: 30, opacity: 0, duration: 0.9 }, '-=0.4')
    .from('.hero-supporting', { y: 20, opacity: 0, duration: 0.7 }, '-=0.5')
    .from('.hero-actions', { y: 20, opacity: 0, duration: 0.7 }, '-=0.5')
    .from('.hero-trust-bar', { y: 20, opacity: 0, duration: 0.7 }, '-=0.5')
    .from('.hero-scroll-cue', { opacity: 0, duration: 0.8 }, '-=0.3');

  // 2. ScrollTrigger Section Reveals
  if (typeof ScrollTrigger !== 'undefined') {
    // Reveal Section Headers
    gsap.utils.toArray('.section-header').forEach((header) => {
      gsap.from(header, {
        scrollTrigger: {
          trigger: header,
          start: 'top 85%',
          toggleActions: 'play none none none'
        },
        y: 40,
        opacity: 0,
        duration: 0.8,
        ease: 'power2.out'
      });
    });

    // Reveal Service Cards Staggered
    const serviceCards = gsap.utils.toArray('.service-card');
    if (serviceCards.length > 0) {
      gsap.from(serviceCards, {
        scrollTrigger: {
          trigger: '#services .grid-2',
          start: 'top 80%',
          toggleActions: 'play none none none'
        },
        y: 40,
        opacity: 0,
        duration: 0.7,
        stagger: 0.15,
        ease: 'power2.out'
      });
    }

    // Reveal Why Swamini Points
    const whyPoints = gsap.utils.toArray('.why-point-item');
    if (whyPoints.length > 0) {
      gsap.from(whyPoints, {
        scrollTrigger: {
          trigger: '.why-points-list',
          start: 'top 80%',
          toggleActions: 'play none none none'
        },
        x: -30,
        opacity: 0,
        duration: 0.6,
        stagger: 0.12,
        ease: 'power2.out'
      });
    }

    // Reveal Experience Timeline Steps
    const timelineSteps = gsap.utils.toArray('.timeline-step');
    if (timelineSteps.length > 0) {
      gsap.from(timelineSteps, {
        scrollTrigger: {
          trigger: '.timeline',
          start: 'top 80%',
          toggleActions: 'play none none none'
        },
        y: 35,
        opacity: 0,
        duration: 0.7,
        stagger: 0.18,
        ease: 'power2.out'
      });
    }

    // Reveal Package Cards
    const packageCards = gsap.utils.toArray('.package-card');
    if (packageCards.length > 0) {
      gsap.from(packageCards, {
        scrollTrigger: {
          trigger: '#packages .grid-3',
          start: 'top 80%',
          toggleActions: 'play none none none'
        },
        y: 40,
        opacity: 0,
        duration: 0.8,
        stagger: 0.15,
        ease: 'power2.out'
      });
    }
  }
});
