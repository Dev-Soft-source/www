# Hardcoded Non-Multilingual Text Report

This report lists all hardcoded English text found in blade files that should be made translatable.

## search_ride.blade.php

### Button Text (Lines 44, 118, 151, 1624, 1661, 1717)
- **Line 44**: `Close` - Success modal close button
- **Line 118**: `Close` - Failure modal close button  
- **Line 151**: `Close` - Message modal close button
- **Line 1624**: `Close` - Modal close button (modal-id1)
- **Line 1661**: `Close` - Modal close button (modal-id2)
- **Line 1717**: `Close` - Phone verification modal close button

### Label/Text (Lines 172, 849, 1139, 1145, 1466, 1702, 1705-1706, 1713)
- **Line 172**: `Search filters` - Filter toggle button text
- **Line 849**: `Search` - Search button text (mobile)
- **Line 1139**: `Instant booking` - Booking button text
- **Line 1145**: `Request to book` - Booking button text
- **Line 1466**: `No ride for this location exist` - No results message (fallback)
- **Line 1702**: `Phone Verification Required` - Modal title
- **Lines 1705-1706**: `To maintain a safe and reliable community, you must have a verified phone number before booking or posting a ride.` - Modal description
- **Line 1713**: `Verify My Number` - Verification button text

## Other Blade Files Found

### ride_detail.blade.php
- **Line 90**: `Close` - Modal close button
- **Line 142**: `Close` - Modal close button
- **Line 182**: `Close` - Modal close button
- **Line 231**: `Close` - Modal close button
- **Line 285**: `Close` - Modal close button
- Uses fallback values with `??` operator (e.g., `'Yes'`, `'No'`, `'Heading'`) - These should use translation keys

### pink_ride.blade.php
- **Line 149**: `Close` - Modal close button
- **Line 181**: `Close` - Modal close button

### my_trips.blade.php
- **Line 44**: `Close` - Modal close button

### my_ride_detail.blade.php
- **Line 102**: `Close` - Modal close button

### index.blade.php
- **Line 41**: `Close` - Modal close button
- **Line 75**: `Close` - Modal close button
- **Line 105**: `Close` - Modal close button
- **Line 112**: `Close` - Modal close button
- **Line 119**: `Close` - Modal close button
- **Line 209**: `Search` - Search button text (mobile)

### folk_ride.blade.php
- **Line 159**: `Close` - Modal close button
- **Line 191**: `Close` - Modal close button

### edit_ride.blade.php
- **Line 169**: `Close` - Modal close button
- **Line 508**: `No` - Delete confirmation button
- **Line 509**: `Yes` - Delete confirmation button

### login.blade.php
- **Line 58**: `Close` - Modal close button
- **Line 96**: `Close` - Modal close button

### proximalocal_ride.blade.php
- **Line 161**: `Close` - Modal close button
- **Line 193**: `Close` - Modal close button

### post_ride.blade.php
- **Line 148**: `Close` - Modal close button (commented out)

### pink_ride.blade.php
- **Line 221**: `Search filters` - Filter toggle button text

### folk_ride.blade.php
- Uses fallback: `'Search Filters'` - Should use translation key

### proximalocal_ride.blade.php
- Uses fallback: `'Search Filters'` - Should use translation key

### booking.blade.php
- **Line 1098**: `Pay and Request to Book` - Button text

### edit_booking.blade.php
- **Line 857**: `Pay and Request to Book` - Button text

## Summary Statistics

- **Total files with hardcoded text**: 12+
- **Most common hardcoded text**: "Close" (appears in 30+ locations)
- **Other common texts**: "Search", "Yes", "No", "Instant booking", "Request to book"

## Recommendations

1. **Create translation keys** for all hardcoded text
2. **Use `@lang()` or `{{ __() }}`** helper functions
3. **Store translations** in language files (e.g., `resources/lang/en/messages.php`)
4. **Replace all instances** of hardcoded text with translation keys
5. **Use fallback values** from database settings when available (like `$findRidePage->*` properties)

## Example Fix

**Before:**
```blade
<button>Close</button>
```

**After:**
```blade
<button>{{ __('common.close') ?? 'Close' }}</button>
```

Or using site text:
```blade
<button>{{ $siteText['close_button'] ?? __('common.close') ?? 'Close' }}</button>
```
