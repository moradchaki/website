# Styling Guide — Color Palettes & Card Variants

## Pre-Built Color Palettes

### 🔴 Flash Sale (default)
```css
--accent:      #e53e3e;
--accent-dark: #c53030;
```

### 🟠 Warm Deals
```css
--accent:      #dd6b20;
--accent-dark: #c05621;
```

### 🟡 Summer Sale
```css
--accent:      #d69e2e;
--accent-dark: #b7791f;
```

### 🟢 Eco / Green
```css
--accent:      #276749;  /* was: #38a169 */
--accent-dark: #276749;
```

### 🔵 Tech / Modern
```css
--accent:      #2b6cb0;
--accent-dark: #2c5282;
```

### 🟣 Luxury / Premium
```css
--accent:      #6b46c1;
--accent-dark: #553c9a;
```

### 🖤 Minimal / Dark
```css
--accent:      #1a202c;
--accent-dark: #171923;
--bg:          #f7fafc;
```

---

## Card Variants

### Default Card (standard)
Already defined in base template. Use for general products.

### Featured Card (highlighted)
Add `.featured` class for one hero product at the top:
```css
.product-card.featured {
  grid-column: 1 / -1;       /* Full width */
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0;
}
.product-card.featured img {
  height: 300px;
}
@media (max-width: 640px) {
  .product-card.featured { grid-template-columns: 1fr; }
}
```

### Horizontal Card (list style)
```css
.product-card.horizontal {
  display: flex;
  flex-direction: row;
}
.product-card.horizontal img {
  width: 140px;
  height: 140px;
  flex-shrink: 0;
  object-fit: cover;
}
```

### Minimal Card (no image)
```css
.product-card.minimal img { display: none; }
.product-card.minimal { padding: 1.25rem; }
```

---

## Badge Color Variants

Override the default badge per-product:
```html
<div class="badge badge--new">NEW</div>
<div class="badge badge--hot">HOT</div>
<div class="badge badge--sold-out">SOLD OUT</div>
```

```css
.badge--new      { background: #2b6cb0; }
.badge--hot      { background: #dd6b20; }
.badge--sold-out { background: #718096; }
.badge--free     { background: #276749; }
```

---

## Hero Variants

### Gradient (default)
```css
.hero { background: linear-gradient(135deg, var(--accent), var(--accent-dark)); }
```

### Dark overlay on image
```css
.hero {
  background: linear-gradient(rgba(0,0,0,.55), rgba(0,0,0,.55)),
              url('HERO_IMAGE_URL') center/cover no-repeat;
}
```

### Split layout (text left, image right)
```css
.hero {
  display: grid;
  grid-template-columns: 1fr 1fr;
  text-align: left;
  padding: 3rem;
}
```

---

## Typography Scale

```css
/* Headings */
.hero-title     { font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 900; }
.section-title  { font-size: 1.75rem; font-weight: 800; }
.product-name   { font-size: 1rem;    font-weight: 700; }

/* Body */
.product-desc   { font-size: .85rem; line-height: 1.4; }
.price-original { font-size: .875rem; }
.price-sale     { font-size: 1.3rem;  font-weight: 800; }
```

---

## Animations (optional)

Add to `<style>` for entrance animation on cards:
```css
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(16px); }
  to   { opacity: 1; transform: translateY(0); }
}
.product-card {
  animation: fadeUp .4s ease both;
}
.product-card:nth-child(2) { animation-delay: .07s; }
.product-card:nth-child(3) { animation-delay: .14s; }
.product-card:nth-child(4) { animation-delay: .21s; }
```
