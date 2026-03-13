import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';

// Initialize tooltips with dynamic theme support
// Reads theme from data-theme attribute, defaults to 'custom' if not specified
function initTooltips() {
    document.querySelectorAll('[data-tippy-content]').forEach(element => {
        const theme = element.getAttribute('data-theme') || 'custom';
        
        tippy(element, {
            theme: theme,
            animation: 'fade',
            delay: [100, 50],
        });
    });
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTooltips);
} else {
    initTooltips();
}


document.addEventListener("DOMContentLoaded", function () {
    // Scroll to first error on page load if validation errors exist
    const firstError = document.querySelector('.tooltip-error');
    if (firstError) {
        // Find the parent container that contains the error (usually a form field wrapper)
        let errorContainer = firstError.closest('div');

        // Walk up to find a meaningful container (section or field wrapper)
        while (errorContainer) {
            // Check if this container is a section or has a meaningful structure
            if (errorContainer.tagName === 'SECTION' ||
                errorContainer.querySelector('input, select, textarea, label')) {
                break;
            }
            errorContainer = errorContainer.parentElement;
        }

        // Scroll to the error container or the error itself
        const scrollTarget = errorContainer || firstError;
        scrollTarget.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }
});

function findTooltipInScope(startNode, root) {
    if (!(startNode instanceof HTMLElement) || !(root instanceof HTMLElement)) {
        return null;
    }

    let node = startNode.closest('div, section, label');

    while (node && node !== root) {
        const tooltipInChildren = Array.from(node.children).find((child) =>
            child instanceof HTMLElement && child.classList.contains('tooltip-error')
        );
        if (tooltipInChildren) {
            return tooltipInChildren;
        }

        if (node.parentElement) {
            const tooltipSibling = Array.from(node.parentElement.children).find((sibling) =>
                sibling instanceof HTMLElement &&
                sibling.classList.contains('tooltip-error') &&
                sibling !== node
            );
            if (tooltipSibling) {
                return tooltipSibling;
            }
        }

        node = node.parentElement?.closest('div, section, label') || null;
    }

    return null;
}

function hideTooltipOnInteraction(event) {
    if (!(event.target instanceof HTMLElement)) {
        return;
    }

    const form = event.target.closest('form');
    if (!form) {
        return;
    }

    const tooltip = findTooltipInScope(event.target, form);
    if (tooltip) {
        tooltip.remove();
    }
}

document.addEventListener('click', hideTooltipOnInteraction);
document.addEventListener('focusin', hideTooltipOnInteraction);


// Available Default Themes:
// light - Light background with dark text
// light-border - Light with border
// material - Material Design style
// translucent - Semi-transparent background
// dark - Dark background with light text
