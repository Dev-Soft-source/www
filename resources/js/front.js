import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';

// Initialize tooltips with dynamic theme support
// Reads theme from data-theme attribute, defaults to 'custom' if not specified
function initTooltips() {
    document.querySelectorAll('[data-tippy-content]').forEach(element => {
        const theme = element.getAttribute('data-theme') || 'custom';
        const isPaymentMethodTooltip = element.classList.contains('payment-method-tooltip');
        tippy(element, {
            theme: theme,
            animation: 'fade',
            delay: [100, 50],
            ...(isPaymentMethodTooltip && { maxWidth: 300 }),
        });
    });
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTooltips);
} else {
    initTooltips();
}

// Available Default Themes:
// light - Light background with dark text
// light-border - Light with border
// material - Material Design style
// translucent - Semi-transparent background
// dark - Dark background with light text
