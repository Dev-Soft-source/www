# Tooltip Implementation Guide

This document explains all tooltip types and patterns used throughout the project.

## Overview

The project uses **custom CSS-based tooltips** with Tailwind CSS classes. Tooltips are implemented in:
- **Web (Laravel Blade templates)**: ~50+ view files
- **Flutter Mobile App**: Custom widget implementations

---

## Web Tooltip Types (Blade Templates)

### 1. **Simple Error Tooltip** (Most Common)
Used for form validation errors that appear on hover or on page load.

**Pattern:**
```html
<div class="relative tooltip -bottom-4 group-hover:flex">
    <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
    </div>
</div>
```

**Usage:**
- Parent element needs `group` class
- Tooltip shows on `group-hover:flex`
- Hidden by default with `hidden` class (if needed)
- **Files:** `post_ride.blade.php`, `signup.blade.php`, `login.blade.php`, etc.

---

### 2. **Info Icon Tooltip with Peer Pattern**
Used for informational tooltips triggered by an info icon.

**Pattern:**
```html
<div class="sups relative inline-flex">
    <svg class="bi bi-info-circle-fill text-black peer" ...>
        <!-- Info icon -->
    </svg>
    <div class="absolute tooltip right-32 -top-12 group-hover:flex hidden peer-hover:flex">
        <div role="tooltip" class="absolute payment_tooltiptext -left-1/2 -top-2 z-10 ...">
            <p class="text-white font-normal text-start text-sm lg:text-base">
                {{ $tooltipText }}
            </p>
        </div>
    </div>
</div>
```

**Usage:**
- Icon has `peer` class
- Tooltip uses `peer-hover:flex` to show on icon hover
- **Files:** `post_ride.blade.php` (payment methods, booking options)

---

### 3. **Animated Error Tooltip (JavaScript Generated)**
Dynamically created tooltips with fade-in animation for AJAX form validation.

**Pattern:**
```javascript
const tooltip = $(`
    <div class="relative tooltip tooltip-error tooltip-init -bottom-4">
        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
            <p class="text-white leading-none text-sm lg:text-base">${errorMessage}</p>
        </div>
    </div>
`);

input.parent().append(tooltip);
setTimeout(() => {
    tooltip.removeClass('tooltip-init').addClass('tooltip-show');
}, 10);
```

**CSS Classes:**
- `.tooltip-init` - Initial hidden state
- `.tooltip-show` - Visible with animation
- `.tooltip-hide` - Hiding animation
- **Files:** `login.blade.php`, `forgot_password.blade.php`

---

### 4. **Password Validation Tooltip**
Special tooltip for password requirements with left-pointing arrow.

**Pattern:**
```html
<div class="password-tooltip">
    <!-- Password requirements list -->
</div>
```

**CSS:**
```css
.password-tooltip {
    position: relative;
    background-color: #c75b5b;
    border-radius: 0.5rem;
    padding: 0.75rem;
    width: 28rem;
}

.password-tooltip::after {
    content: "";
    position: absolute;
    left: -10px;
    top: 50%;
    transform: translateY(-50%);
    border-top: 8px solid transparent;
    border-bottom: 8px solid transparent;
    border-right: 10px solid #c75b5b;
}
```

**Files:** `signup.blade.php`, `payout.blade.php`

---

### 5. **Custom Arrow Tooltips**
Tooltips with custom arrow indicators using `::after` pseudo-elements.

**Types:**
- `.features_tooltiptext` - Blue arrow (#3b82f6)
- `.luggage_tooltiptext` - Blue arrow (#3b82f6)
- `.payment_tooltiptext` - Red arrow (#ef4444)

**CSS Pattern:**
```css
.features_tooltiptext::after {
    content: "";
    border-width: 10px;
    border-style: solid;
    border-color: #3b82f6 transparent transparent transparent;
    position: absolute;
    bottom: -20px;
}
```

**Files:** `post_ride.blade.php`, `edit_ride.blade.php`, `signup.blade.php`

---

## Tooltip Positioning Classes

### Common Tailwind Positioning:
- `-bottom-4` - Below element
- `-top-2` - Above element
- `right-32` - Right side positioning
- `absolute` - Absolute positioning
- `relative` - Relative container

### Custom Positioning Classes:
- `.tooltip_position` - Custom positioning for small screens
- `.payment_tooltiptext_position` - Payment tooltip specific
- `.tooltip_width` - Responsive width control

---

## Tooltip Styling Classes

### Base Classes:
- `bg-red-500` - Red background (errors)
- `bg-blue-500` or `#3b82f6` - Blue background (info)
- `text-white` - White text
- `rounded` - Rounded corners
- `shadow-lg` - Drop shadow
- `p-2` - Padding
- `z-10` or `z-50` - Z-index layering

### Responsive Classes:
- `w-full md:w-1/2` - Full width on mobile, half on desktop
- `text-sm lg:text-base` - Responsive text sizing

---

## Tooltip Animation Patterns

### 1. **Hover-based (Group Pattern)**
```css
.group:hover .tooltip:not(.hidden) {
    display: flex !important;
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}
```

### 2. **Hover-based (Peer Pattern)**
```css
.peer:hover ~ .tooltip:not(.hidden) {
    display: flex !important;
    opacity: 1;
    visibility: visible;
}
```

### 3. **JavaScript Animation**
```css
.tooltip-error.tooltip-init {
    opacity: 0;
    visibility: hidden;
    transform: translateY(-5px);
}

.tooltip-error.tooltip-show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
    transition: opacity 0.2s ease-in-out, visibility 0.2s ease-in-out, transform 0.2s ease-in-out;
}
```

---

## Flutter Mobile Tooltips

### Widget: `tool_tip.dart`

**Main Functions:**
1. `toolTip()` - General purpose tooltip
2. `toolTipPassword()` - Password validation tooltip
3. `toolTipEmptyPassword()` - Empty password error

**Pattern:**
```dart
Widget toolTip({
  double fontSize = 20.0,
  dynamic tip,
  String type = 'normal',
  int position = 0,
  double width = 0,
}) {
  return Column(
    children: [
      const ClippedTriangleWidget(), // Arrow indicator
      Container(
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(5.0),
          color: Colors.red,
        ),
        child: Padding(
          padding: const EdgeInsets.all(5.0),
          child: Column(
            children: [
              // Tooltip content
            ],
          ),
        ),
      ),
    ],
  );
}
```

**Usage in Flutter:**
- Used in signup, password, and form validation screens
- Red background with white text
- Triangle arrow indicator at top

---

## Files Using Tooltips

### Web Views (Blade):
- `post_ride.blade.php` - 65+ tooltip instances
- `edit_ride.blade.php` - 48+ instances
- `signup.blade.php` - 23+ instances
- `login.blade.php` - 13+ instances
- `forgot_password.blade.php` - 13+ instances
- Plus 40+ other view files

### Flutter:
- `tool_tip.dart` - Main tooltip widget
- Used in: signup, password, post_ride, trip_detail, and 30+ other screens

---

## Best Practices

### 1. **Error Tooltips:**
- Use red background (`bg-red-500`)
- Show immediately on validation error
- Position below input field

### 2. **Info Tooltips:**
- Use blue background or red for consistency
- Trigger on hover of info icon
- Position relative to trigger element

### 3. **Accessibility:**
- Always include `role="tooltip"` attribute
- Use semantic HTML
- Ensure sufficient color contrast

### 4. **Responsive Design:**
- Use Tailwind responsive classes (`md:`, `lg:`)
- Adjust positioning for mobile
- Consider touch targets on mobile

### 5. **Performance:**
- Use CSS transitions for animations
- Avoid JavaScript for simple hover tooltips
- Lazy load tooltip content if heavy

---

## Quick Reference

### Create a Simple Error Tooltip:
```html
<div class="group">
    <input type="text" name="field">
    <div class="relative tooltip -bottom-4 hidden group-hover:flex">
        <div role="tooltip" class="relative tooltiptext -top-2 z-10 shadow-lg p-2 flex bg-red-500 rounded">
            <p class="text-white text-sm">Error message</p>
        </div>
    </div>
</div>
```

### Create an Info Icon Tooltip:
```html
<div class="relative inline-flex">
    <svg class="peer" ...>Info Icon</svg>
    <div class="absolute tooltip hidden peer-hover:flex -top-12">
        <div role="tooltip" class="tooltiptext bg-red-500 text-white p-2 rounded">
            Info text here
        </div>
    </div>
</div>
```

---

## Customization

### Change Tooltip Color:
- Error: `bg-red-500` → `bg-red-600`
- Info: `bg-blue-500` → `bg-primary` (if defined)

### Change Arrow Color:
Modify the `border-color` in `::after` pseudo-element:
```css
.features_tooltiptext::after {
    border-color: #YOUR_COLOR transparent transparent transparent;
}
```

### Change Animation Speed:
Modify `transition` duration:
```css
transition: opacity 0.2s ease-in-out; /* Change 0.2s to desired duration */
```

---

## Troubleshooting

### Tooltip Not Showing:
1. Check if parent has `group` class (for group-hover)
2. Check if trigger has `peer` class (for peer-hover)
3. Verify `z-index` is high enough
4. Check if `hidden` class is removed on hover

### Tooltip Positioning Issues:
1. Ensure parent container has `relative` positioning
2. Adjust Tailwind positioning classes (`-top-2`, `-bottom-4`, etc.)
3. Use custom CSS for complex positioning

### Animation Not Working:
1. Verify CSS transition properties are set
2. Check JavaScript classes (`tooltip-init`, `tooltip-show`)
3. Ensure no conflicting CSS rules

---

*Last Updated: Based on project analysis*
