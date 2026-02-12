@props([
    'text',
    'position' => 'top', // top | bottom | left | right | auto
])

<span class="tooltip-wrapper" data-tooltip-auto="{{ $position === 'auto' ? 'true' : 'false' }}">
    {{ $slot }}
    <span class="tooltip-box tooltip-{{ $position === 'auto' ? 'top' : $position }}" data-tooltip-position="{{ $position === 'auto' ? 'top' : $position }}">
        {{ $text }}
    </span>
</span>

@once
<script>
(function() {
    // Only initialize once
    if (window.tooltipAutoPositionInitialized) return;
    window.tooltipAutoPositionInitialized = true;
    
    function updateTooltipPosition(wrapper) {
        const autoPosition = wrapper.getAttribute('data-tooltip-auto') === 'true';
        if (!autoPosition) return;
        
        const tooltip = wrapper.querySelector('.tooltip-box');
        if (!tooltip) return;
        
        // Store original styles
        const originalVisibility = tooltip.style.visibility;
        const originalOpacity = tooltip.style.opacity;
        const originalDisplay = tooltip.style.display;
        const originalLeft = tooltip.style.left;
        const originalRight = tooltip.style.right;
        const originalTransform = tooltip.style.transform;
        
        // Ensure tooltip is visible for measurement (CSS hover might have already made it visible)
        // Temporarily override visibility to measure, but keep it visually hidden if needed
        const computedStyle = window.getComputedStyle(tooltip);
        const isCurrentlyVisible = computedStyle.visibility !== 'hidden' && computedStyle.opacity !== '0';
        
        // If not visible, make it temporarily visible for measurement
        if (!isCurrentlyVisible) {
            tooltip.style.visibility = 'hidden';
            tooltip.style.opacity = '0';
        }
        tooltip.style.display = 'block';
        tooltip.style.pointerEvents = 'none';
        
        // Reset positioning to get accurate measurements
        tooltip.style.left = '';
        tooltip.style.right = '';
        tooltip.style.transform = '';
        
        // Force a reflow to ensure dimensions are calculated
        void tooltip.offsetWidth;
        
        // Get wrapper and tooltip dimensions
        const wrapperRect = wrapper.getBoundingClientRect();
        const tooltipRect = tooltip.getBoundingClientRect();
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;
        
        const tooltipWidth = tooltipRect.width || 300;
        const tooltipHeight = tooltipRect.height || 100;
        const spacing = 15;
        
        // Calculate available space on each side
        const spaceTop = wrapperRect.top;
        const spaceBottom = viewportHeight - wrapperRect.bottom;
        const spaceLeft = wrapperRect.left;
        const spaceRight = viewportWidth - wrapperRect.right;
        
        const positions = {
            top: spaceTop - tooltipHeight - spacing,
            bottom: spaceBottom - tooltipHeight - spacing,
            left: spaceLeft - tooltipWidth - spacing,
            right: spaceRight - tooltipWidth - spacing
        };
        
        // Find the position with the most available space, but avoid positions that would cause overflow
        let bestPosition = 'top';
        let maxSpace = positions.top;
        
        // Check each position and ensure it won't cause overflow
        for (const [pos, space] of Object.entries(positions)) {
            // Skip positions that would definitely cause overflow
            if (pos === 'left' && spaceLeft < tooltipWidth + 20) {
                continue; // Not enough space on left
            }
            if (pos === 'right' && spaceRight < tooltipWidth + 20) {
                continue; // Not enough space on right
            }
            if (pos === 'top' && spaceTop < tooltipHeight + 20) {
                continue; // Not enough space on top
            }
            if (pos === 'bottom' && spaceBottom < tooltipHeight + 20) {
                continue; // Not enough space on bottom
            }
            
            if (space > maxSpace) {
                maxSpace = space;
                bestPosition = pos;
            }
        }
        
        // If the best position doesn't have enough space, try alternatives
        // Prefer top/bottom over left/right to avoid horizontal overflow issues
        if (maxSpace < 50) {
            if (spaceTop > 100) {
                bestPosition = 'top';
            } else if (spaceBottom > 100) {
                bestPosition = 'bottom';
            } else if (spaceRight > spaceLeft && spaceRight > 100) {
                bestPosition = 'right';
            } else if (spaceLeft > 100) {
                bestPosition = 'left';
            }
        }
        
        // Remove all position classes
        tooltip.classList.remove('tooltip-top', 'tooltip-bottom', 'tooltip-left', 'tooltip-right');
        // Add the best position class
        tooltip.classList.add('tooltip-' + bestPosition);
        tooltip.setAttribute('data-tooltip-position', bestPosition);
        
        // Force another reflow after class change
        void tooltip.offsetWidth;
        
        // Get updated tooltip dimensions after class change
        const updatedTooltipRect = tooltip.getBoundingClientRect();
        const actualTooltipWidth = updatedTooltipRect.width || tooltipWidth;
        
        // Handle edge cases for top/bottom positions
        if (bestPosition === 'top' || bestPosition === 'bottom') {
            // Calculate where tooltip would be centered
            const tooltipCenterX = wrapperRect.left + (wrapperRect.width / 2);
            const tooltipLeftEdge = tooltipCenterX - (actualTooltipWidth / 2);
            const tooltipRightEdge = tooltipCenterX + (actualTooltipWidth / 2);
            
            // Check for left edge overflow - use more aggressive detection
            if (tooltipLeftEdge < 10) {
                tooltip.style.setProperty('left', '0', 'important');
                tooltip.style.setProperty('right', 'auto', 'important');
                tooltip.style.setProperty('transform', 'translateX(10px)', 'important');
            }
            // Check for right edge overflow
            else if (tooltipRightEdge > viewportWidth - 10) {
                tooltip.style.setProperty('left', 'auto', 'important');
                tooltip.style.setProperty('right', '0', 'important');
                tooltip.style.setProperty('transform', 'translateX(0)', 'important');
            } else {
                // Center it normally - reset to default
                tooltip.style.left = '';
                tooltip.style.right = '';
                tooltip.style.transform = '';
            }
        } else if (bestPosition === 'left' || bestPosition === 'right') {
            // For left/right positions, check if tooltip would overflow vertically
            const tooltipTop = wrapperRect.top + (wrapperRect.height / 2) - (tooltipHeight / 2);
            const tooltipBottom = tooltipTop + tooltipHeight;
            
            // If tooltip would overflow top, align to top of viewport
            if (tooltipTop < 10) {
                tooltip.style.top = '10px';
                tooltip.style.bottom = 'auto';
                tooltip.style.transform = 'translateY(0)';
            }
            // If tooltip would overflow bottom, align to bottom of viewport
            else if (tooltipBottom > viewportHeight - 10) {
                tooltip.style.top = 'auto';
                tooltip.style.bottom = '10px';
                tooltip.style.transform = 'translateY(0)';
            } else {
                // Center vertically normally
                tooltip.style.top = '';
                tooltip.style.bottom = '';
                tooltip.style.transform = '';
            }
        }
        
        // Force one more reflow to ensure styles are applied
        void tooltip.offsetWidth;
        
        // Force one more reflow and final check - if tooltip is still off-screen, adjust it
        void tooltip.offsetWidth;
        const finalRect = tooltip.getBoundingClientRect();
        
        // Final horizontal overflow check - applies to all positions
        if (finalRect.left < 10) {
            // Tooltip is off the left edge - force it to stay within viewport
            tooltip.style.setProperty('left', '10px', 'important');
            tooltip.style.setProperty('right', 'auto', 'important');
            if (bestPosition === 'top' || bestPosition === 'bottom') {
                tooltip.style.setProperty('transform', 'translateX(0)', 'important');
            } else {
                tooltip.style.setProperty('transform', '', 'important');
            }
        } else if (finalRect.right > viewportWidth - 10) {
            // Tooltip is off the right edge - force it to stay within viewport
            tooltip.style.setProperty('left', 'auto', 'important');
            tooltip.style.setProperty('right', '10px', 'important');
            if (bestPosition === 'top' || bestPosition === 'bottom') {
                tooltip.style.setProperty('transform', 'translateX(0)', 'important');
            } else {
                tooltip.style.setProperty('transform', '', 'important');
            }
        }
        
        // Final vertical overflow check
        if (finalRect.top < 10) {
            tooltip.style.setProperty('top', '10px', 'important');
            tooltip.style.setProperty('bottom', 'auto', 'important');
        } else if (finalRect.bottom > viewportHeight - 10) {
            tooltip.style.setProperty('top', 'auto', 'important');
            tooltip.style.setProperty('bottom', '10px', 'important');
        }
        
        // Restore original visibility state (only if we changed it and it wasn't visible)
        if (!isCurrentlyVisible) {
            tooltip.style.visibility = originalVisibility || '';
            tooltip.style.opacity = originalOpacity || '';
        }
        tooltip.style.display = originalDisplay || '';
        tooltip.style.pointerEvents = '';
        
        // Always adjust arrow position after positioning is complete
        // Force a reflow and then adjust arrow
        void tooltip.offsetWidth;
        adjustArrowPosition(wrapper, tooltip);
        
        // Also adjust after a small delay to catch any final CSS transitions
        setTimeout(() => {
            adjustArrowPosition(wrapper, tooltip);
        }, 50);
    }
    
    function adjustArrowPosition(wrapper, tooltip) {
        // Adjust arrow to point to the trigger element
        const tooltipRect = tooltip.getBoundingClientRect();
        
        // Get the trigger element (the slot content, usually the icon)
        // Try to find the actual trigger - it's usually the first child that's not the tooltip
        let triggerElement = null;
        for (let child of wrapper.children) {
            if (!child.classList.contains('tooltip-box')) {
                triggerElement = child;
                break;
            }
        }
        
        if (!triggerElement) {
            // Fallback: use wrapper center if no specific trigger found
            triggerElement = wrapper;
        }
        
        const triggerRect = triggerElement.getBoundingClientRect();
        
        // Calculate trigger center
        const triggerCenterX = triggerRect.left + (triggerRect.width / 2);
        const triggerCenterY = triggerRect.top + (triggerRect.height / 2);
        
        // Calculate arrow position relative to tooltip's left/top edge
        const arrowOffsetX = triggerCenterX - tooltipRect.left;
        const arrowOffsetY = triggerCenterY - tooltipRect.top;
        
        // Apply arrow positioning based on tooltip position class
        if (tooltip.classList.contains('tooltip-top') || tooltip.classList.contains('tooltip-bottom')) {
            // For top/bottom tooltips, adjust horizontal arrow position
            // Keep arrow within tooltip bounds (10px margin from edges to prevent arrow from being too close to edge)
            const minArrowPos = 10;
            const maxArrowPos = tooltipRect.width - 10;
            const arrowLeft = Math.max(minArrowPos, Math.min(arrowOffsetX, maxArrowPos));
            tooltip.style.setProperty('--arrow-left', arrowLeft + 'px');
        } else if (tooltip.classList.contains('tooltip-left') || tooltip.classList.contains('tooltip-right')) {
            // For left/right tooltips, adjust vertical arrow position
            const minArrowPos = 10;
            const maxArrowPos = tooltipRect.height - 10;
            const arrowTop = Math.max(minArrowPos, Math.min(arrowOffsetY, maxArrowPos));
            tooltip.style.setProperty('--arrow-top', arrowTop + 'px');
        }
    }
    
    function preventTooltipOverflow(wrapper) {
        // This function works for ALL tooltips, not just auto-positioned ones
        const tooltip = wrapper.querySelector('.tooltip-box');
        if (!tooltip) return;
        
        // Wait for tooltip to be visible via CSS hover
        const checkOverflow = () => {
            const computedStyle = window.getComputedStyle(tooltip);
            if (computedStyle.visibility === 'hidden' || computedStyle.opacity === '0') {
                return; // Tooltip not visible yet
            }
            
            const tooltipRect = tooltip.getBoundingClientRect();
            const viewportWidth = window.innerWidth;
            const viewportHeight = window.innerHeight;
            let tooltipWasRepositioned = false;
            
            // Check horizontal overflow
            if (tooltipRect.left < 10) {
                // Tooltip is off the left edge
                tooltip.style.setProperty('left', '10px', 'important');
                tooltip.style.setProperty('right', 'auto', 'important');
                // Remove transform if it's centering the tooltip
                if (tooltip.classList.contains('tooltip-top') || tooltip.classList.contains('tooltip-bottom')) {
                    tooltip.style.setProperty('transform', 'translateX(0)', 'important');
                }
                tooltipWasRepositioned = true;
            } else if (tooltipRect.right > viewportWidth - 10) {
                // Tooltip is off the right edge
                tooltip.style.setProperty('left', 'auto', 'important');
                tooltip.style.setProperty('right', '10px', 'important');
                if (tooltip.classList.contains('tooltip-top') || tooltip.classList.contains('tooltip-bottom')) {
                    tooltip.style.setProperty('transform', 'translateX(0)', 'important');
                }
                tooltipWasRepositioned = true;
            }
            
            // Check vertical overflow
            if (tooltipRect.top < 10) {
                tooltip.style.setProperty('top', '10px', 'important');
                tooltip.style.setProperty('bottom', 'auto', 'important');
                tooltipWasRepositioned = true;
            } else if (tooltipRect.bottom > viewportHeight - 10) {
                tooltip.style.setProperty('top', 'auto', 'important');
                tooltip.style.setProperty('bottom', '10px', 'important');
                tooltipWasRepositioned = true;
            }
            
            // ALWAYS adjust arrow position to point to trigger element
            // This ensures arrow is correct whether tooltip was repositioned or not
            // Force a reflow first to ensure tooltip position is updated
            void tooltip.offsetWidth;
            adjustArrowPosition(wrapper, tooltip);
        };
        
        // Check multiple times to catch CSS transitions
        setTimeout(checkOverflow, 10);
        setTimeout(checkOverflow, 50);
        setTimeout(checkOverflow, 150);
        setTimeout(checkOverflow, 300);
        
        // Final arrow adjustment after all checks are done
        setTimeout(() => {
            const computedStyle = window.getComputedStyle(tooltip);
            if (computedStyle.visibility !== 'hidden' && computedStyle.opacity !== '0') {
                adjustArrowPosition(wrapper, tooltip);
            }
        }, 350);
    }
    
    function initTooltips() {
        // Handle auto-positioned tooltips
        const tooltipWrappers = document.querySelectorAll('.tooltip-wrapper[data-tooltip-auto="true"]');
        
        tooltipWrappers.forEach(wrapper => {
            // Update position on hover - use both mouseenter and a small delay to catch CSS transitions
            wrapper.addEventListener('mouseenter', function() {
                // First update immediately
                updateTooltipPosition(this);
                
                // Then update again after a short delay to catch any CSS transitions
                setTimeout(() => {
                    updateTooltipPosition(this);
                }, 50);
                
                // Final check after tooltip should be fully visible
                setTimeout(() => {
                    updateTooltipPosition(this);
                }, 150);
            });
        });
        
        // Handle ALL tooltips for overflow prevention (including default 'top' position)
        const allTooltipWrappers = document.querySelectorAll('.tooltip-wrapper');
        
        allTooltipWrappers.forEach(wrapper => {
            wrapper.addEventListener('mouseenter', function() {
                preventTooltipOverflow(this);
            });
        });
        
        // Also update on window resize
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                // Update auto-positioned tooltips
                const autoTooltipWrappers = document.querySelectorAll('.tooltip-wrapper[data-tooltip-auto="true"]');
                autoTooltipWrappers.forEach(wrapper => {
                    if (wrapper.matches(':hover')) {
                        updateTooltipPosition(wrapper);
                    }
                });
                
                // Prevent overflow for all tooltips
                const allTooltipWrappers = document.querySelectorAll('.tooltip-wrapper');
                allTooltipWrappers.forEach(wrapper => {
                    if (wrapper.matches(':hover')) {
                        preventTooltipOverflow(wrapper);
                    }
                });
            }, 100);
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTooltips);
    } else {
        initTooltips();
    }
})();
</script>
@endonce
