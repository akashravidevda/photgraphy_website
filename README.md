# Swamini Photography & Cinematography Website

A luxury editorial one-page photography portfolio and lead generation web application for **Swamini Photography & Cinematography** founded by **Navanit Patil**.

## 🌟 Key Highlights & Design Aesthetic

- **Design Aesthetic**: High-end luxury editorial photography magazine feeling with rich charcoal (`#171514`), deep brown (`#2A211D`), warm cream (`#F8F2EA`), and muted gold (`#B58A58`) accents.
- **Story-Driven Copywriting**: Focuses on emotional storytelling (*"Your Moments. Our Passion. Memories Forever."*, *"Photographs let you return to a feeling."*) rather than technical camera specs.
- **Conversion Architecture**:
  - Direct WhatsApp deep-linking dynamically tailored to each service package.
  - One-tap phone calling (`+91 7276505046`).
  - Floating sticky action bar for mobile users.
  - Interactive AJAX contact and package enquiry form with validation, CSRF security, and honeypot spam protection.
- **Visual Excellence**:
  - Full-bleed cinematic Hero with subtle Ken Burns motion.
  - Editorial masonry portfolio with category filtering (Wedding, Pre-Wedding, Maternity, Baby & Newborn, Couple, Events).
  - Touch-friendly & keyboard accessible Lightbox modal with direct "Enquire For This Style" conversion hook.
  - 4-step experience timeline (`01 CONNECT`, `02 PLAN`, `03 CAPTURE`, `04 RELIVE`).
  - Transparent pricing cards for Wedding, Baby Shoot, and Maternity.
  - Founder introduction of Navanit Patil.

## 📂 Project Structure

```
photgraphy_website/
├── index.php                      # Main one-page PHP website
├── index.html                     # Instant static HTML5 preview
├── api/
│   └── contact.php                # Secure AJAX API endpoint with CSRF, Honeypot & Rate-limiting
├── includes/
│   ├── config.php                 # Core settings, phone numbers & mail configuration
│   ├── mailer.php                 # HTML email templates and mail delivery
│   └── csrf.php                   # CSRF token generator & validator
├── assets/
│   ├── css/
│   │   ├── variables.css          # Design tokens & color palette
│   │   ├── reset.css              # Base CSS reset & accessibility
│   │   ├── layout.css             # Grid systems & containers
│   │   ├── components.css         # Luxury buttons, cards, lightbox, form controls
│   │   └── style.css              # Section styles & responsive refinements
│   ├── js/
│   │   ├── main.js                # Sticky header blur, scrollspy, dynamic WhatsApp generator
│   │   ├── animations.js          # GSAP & ScrollTrigger motion reveals
│   │   ├── gallery.js             # Portfolio category filter & accessible lightbox
│   │   └── contact.js             # Form validation, AJAX submit & toast notifications
│   └── images/                    # Curated art-directed photography assets
```

## 🚀 How to Run Locally

### With PHP:
```bash
php -S localhost:8000
```
Open `http://localhost:8000` in your browser.

### With Python (Static Preview):
```bash
python -m http.server 8080
```
Open `http://localhost:8080` in your browser.
