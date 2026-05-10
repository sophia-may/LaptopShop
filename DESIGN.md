---
name: High-Velocity Tech Commerce
colors:
  surface: '#f9f9f9'
  surface-dim: '#dadada'
  surface-bright: '#f9f9f9'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f3f3f3'
  surface-container: '#eeeeee'
  surface-container-high: '#e8e8e8'
  surface-container-highest: '#e2e2e2'
  on-surface: '#1a1c1c'
  on-surface-variant: '#5d3f3c'
  inverse-surface: '#2f3131'
  inverse-on-surface: '#f1f1f1'
  outline: '#926e6b'
  outline-variant: '#e7bdb8'
  surface-tint: '#c00014'
  primary: '#b70013'
  on-primary: '#ffffff'
  primary-container: '#e11b22'
  on-primary-container: '#fff7f6'
  inverse-primary: '#ffb4ab'
  secondary: '#5e5e5e'
  on-secondary: '#ffffff'
  secondary-container: '#e3e2e2'
  on-secondary-container: '#646464'
  tertiary: '#005e93'
  on-tertiary: '#ffffff'
  tertiary-container: '#0078b9'
  on-tertiary-container: '#f7f9ff'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#ffdad6'
  primary-fixed-dim: '#ffb4ab'
  on-primary-fixed: '#410002'
  on-primary-fixed-variant: '#93000d'
  secondary-fixed: '#e3e2e2'
  secondary-fixed-dim: '#c7c6c6'
  on-secondary-fixed: '#1b1c1c'
  on-secondary-fixed-variant: '#464747'
  tertiary-fixed: '#cde5ff'
  tertiary-fixed-dim: '#95ccff'
  on-tertiary-fixed: '#001d32'
  on-tertiary-fixed-variant: '#004a75'
  background: '#f9f9f9'
  on-background: '#1a1c1c'
  surface-variant: '#e2e2e2'
typography:
  display-lg:
    fontFamily: Inter
    fontSize: 32px
    fontWeight: '700'
    lineHeight: '1.2'
    letterSpacing: -0.02em
  headline-md:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: '1.3'
  title-sm:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '600'
    lineHeight: '1.4'
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: '1.5'
  body-sm:
    fontFamily: Inter
    fontSize: 13px
    fontWeight: '400'
    lineHeight: '1.4'
  label-bold:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '700'
    lineHeight: '1'
    letterSpacing: 0.05em
  price-lg:
    fontFamily: Inter
    fontSize: 20px
    fontWeight: '700'
    lineHeight: '1'
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 4px
  container-max: 1240px
  gutter: 12px
  margin-page: 24px
  card-padding: 16px
  stack-tight: 8px
  stack-loose: 20px
---

## Brand & Style

The design system is engineered for a high-energy, retail-intensive environment. It targets tech-savvy consumers and inventory managers who require speed, density, and immediate clarity. The brand personality is "Professional Kinetic"—it feels authoritative and trustworthy like a flagship corporate store, yet pulses with the urgency of a fast-paced marketplace.

The visual style follows a **Corporate / Modern** movement with a heavy emphasis on **density-rich hierarchy**. It avoids unnecessary whitespace in favor of informative proximity, ensuring that users can compare specs, prices, and availability at a glance. The use of vibrant red accents against a clinical neutral backdrop creates a high-contrast environment that directs attention to calls-to-action and critical alerts.

## Colors

The palette is anchored by a tech-focused Primary Red, used strategically for branding, primary buttons, and promotional urgency. 

- **Primary Red (#e11b22):** The heartbeat of the system. Used for headers, primary actions, and "Hot Deal" indicators.
- **Deep Charcoal (#2d2e2e):** Provides a solid foundation for typography, ensuring maximum legibility and a serious, technical tone.
- **Surface Grays:** A range of light grays are used to differentiate content sections (cards vs. background) without breaking the clean, white aesthetic.
- **Success/Info Accents:** Subtle blues and greens are reserved for status indicators and technical specifications to provide a secondary layer of meaning without competing with the primary red.

## Typography

This design system utilizes **Inter** for its exceptional performance in data-dense interfaces. The typographic scale is optimized for small-to-medium sizes to allow for multi-column layouts.

- **Emphasis:** Bold weights are used frequently for product names and prices to ensure they pop against a busy background.
- **Technical Specs:** Use `body-sm` for secondary metadata and technical descriptions to maintain high information density.
- **Price Treatment:** Prices should always use a bold weight and, when used in product listings, the Primary Red color.

## Layout & Spacing

The system uses a **Fixed Grid** model for desktop, centered on a 1240px container. 

- **Density:** Inspired by Image 2, the layout prioritizes a "Shelf" approach. Product grids should utilize a 5-column or 6-column layout to maximize the number of items visible above the fold.
- **Sidebars:** Persistent left-hand navigation is used for category browsing, utilizing a high-density list style with 8px vertical spacing between items.
- **Rhythm:** A 4px baseline grid ensures alignment across technical specs and pricing tables. Gutters are kept tight (12px) to maintain the "fast-paced" retail feel.

## Elevation & Depth

To maintain a professional and trustworthy feel, depth is achieved through **Tonal Layers** supplemented by very crisp, **Subtle Shadows**.

- **Level 0 (Background):** Used for the main page canvas, typically `#f4f4f4`.
- **Level 1 (Cards/Sheets):** White surfaces with a 1px border (`#e0e0e0`) or a very soft shadow (0px 2px 4px rgba(0,0,0,0.05)).
- **Level 2 (Hover/Interaction):** When a user hovers over a product card, the shadow should deepen and the card should slightly lift, providing immediate feedback.
- **Level 3 (Modals/Popovers):** Standard diffused shadows to focus user attention on management tasks or cart previews.

## Shapes

The design system employs a **Rounded** (0.5rem) shape language to soften the technical density and make the platform feel modern and accessible.

- **Small Elements:** Tooltips and tags use the base 0.5rem radius.
- **Standard Elements:** Buttons and Input fields use 0.5rem.
- **Large Elements:** Product cards and promotional banners use the `rounded-lg` (1rem) setting to create clear visual containment.
- **Search Bars:** The primary search bar in the header should use a pill-shape (round-xl or full) to distinguish it as the primary navigation tool.

## Components

- **Buttons:** Primary buttons are solid Primary Red with white text. Secondary buttons use a charcoal outline or a light gray fill. Use 10px 20px padding for standard actions.
- **Product Cards:** Must contain an image area, brand label, product name (`title-sm`), price (`price-lg`), and a "Add to Cart" quick-action icon.
- **Chips/Badges:** Used for "Installment 0%", "New", or "Sale". These should be small, capitalized labels with 4px corner radius and high-contrast background fills.
- **Input Fields:** Clean white backgrounds with 1px charcoal borders that turn Primary Red on focus.
- **Density Lists:** Used in the sidebar; icons should be simplified line art (20px) paired with `body-md` text.
- **Progressive Disclosure:** For management platforms, use collapsible accordions for complex specification sets to manage visual load.