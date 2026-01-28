# Hybrid Phone Verification Implementation

## Overview
This implementation adds a hybrid verification flow to reduce SMS costs for international numbers while maintaining SMS for North American numbers. International numbers can use WhatsApp via Twilio Verify API.

## Implementation Details

### 1. Rate Limiting
- **Maximum Attempts**: 3 verification attempts per phone number
- **Time Window**: 24 hours (rolling window)
- **Enforcement**: Backend validation in `Step5to5Controller@sendVerificationCode`
- **Response**: Returns HTTP 429 status with error message when limit exceeded

### 2. Verification Flow

#### North American Numbers (+1)
- **Method**: Standard Twilio SMS Messages API
- **Code Storage**: Stored in `phone_verifications` table
- **Channel**: `sms`
- **Cost**: Standard SMS rates

#### International Numbers (Non +1)
- **Method**: Twilio Verify API
- **Channel Options**: 
  - SMS (default, tried first)
  - WhatsApp (if user clicks "Send code via WhatsApp")
- **Code Storage**: Managed by Twilio (not stored in DB)
- **Tracking**: `twilio_verify_sid` stored in `phone_verifications` table
- **Cost**: Lower rates via Twilio Verify API

### 3. Frontend Changes

#### Main Form (`step5to5.blade.php`)
- **WhatsApp Button**: Automatically shown for international numbers (country code ≠ +1)
- **Visibility**: Hidden for North American numbers
- **Location**: Next to "Verify" button

#### Verification Modal
- **WhatsApp Resend Button**: Shown for international numbers after initial SMS attempt
- **Location**: Inside verification modal, below code input fields
- **Text**: "Send code via WhatsApp" with WhatsApp icon

### 4. Backend Changes

#### Controller: `Step5to5Controller`
- **Method**: `sendVerificationCode()` - Updated with hybrid logic
- **New Method**: `sendVerificationCodeWhatsApp()` - Forces WhatsApp channel
- **Rate Limiting**: Checks attempts in last 24 hours before sending
- **Phone Detection**: Uses `isNorthAmericanNumber()` helper function

#### Routes
- **Existing**: `POST /{lang}/step5-5-send-verification` (supports channel parameter)
- **New**: `POST /{lang}/step5-5-send-verification-whatsapp` (forces WhatsApp)

### 5. Database Schema
The `phone_verifications` table includes:
- `channel`: 'sms' or 'whatsapp'
- `twilio_verify_sid`: Stores Twilio Verify SID for international numbers
- `verification_code`: Empty for Twilio Verify, contains code for SMS

### 6. Environment Variables Required
```env
TWILIO_ACCOUNT_SID=your_account_sid
TWILIO_AUTH_TOKEN=your_auth_token
TWILIO_PHONE_NUMBER=your_twilio_number
TWILIO_VERIFY_SERVICE_SID=VAxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### 7. Twilio Setup Required

1. **Create Verify Service**:
   - Go to Twilio Console → Verify → Services
   - Create a new Verify Service
   - Copy the Service SID to `TWILIO_VERIFY_SERVICE_SID`

2. **Enable WhatsApp Channel** (Optional but Recommended):
   - In Verify Service settings, enable WhatsApp
   - Register WhatsApp sender (requires Meta Business Account approval)
   - Configure WhatsApp template messages
   - **Note**: If WhatsApp is not enabled, the system will automatically fall back to SMS when WhatsApp is requested

3. **Test Configuration**:
   - Test with North American number (+1) - should use SMS
   - Test with international number - should use Twilio Verify
   - Test WhatsApp option - should send via WhatsApp (or SMS if WhatsApp not enabled)

### 8. User Experience Flow

#### For North American Numbers (+1):
1. User enters phone number
2. Clicks "Verify" button
3. Receives SMS with 4-digit code
4. Enters code in modal
5. Phone verified

#### For International Numbers:
1. User enters phone number (non +1 country code)
2. Sees "Verify" button AND "Send code via WhatsApp" button
3. **Option A**: Clicks "Verify" → Receives SMS via Twilio Verify
4. **Option B**: Clicks "Send code via WhatsApp" → Receives code via WhatsApp
5. If SMS sent first, sees "Send code via WhatsApp" option in verification modal
6. Enters code in modal
7. Phone verified

### 9. Rate Limiting Behavior

- **First 3 Attempts**: Allowed within 24 hours
- **4th+ Attempt**: Blocked with message: "Maximum verification attempts (3) reached for this number. Please try again after 24 hours."
- **Reset**: After 24 hours from first attempt, counter resets
- **Tracking**: Based on `phone_number_id` and `created_at` timestamp

### 10. Error Handling

- **Rate Limit Exceeded**: HTTP 429 with clear message
- **WhatsApp Channel Disabled**: Automatically falls back to SMS, returns success with `whatsapp_unavailable: true` flag
- **Twilio Verify Failure**: HTTP 500 with error details
- **Invalid Phone**: HTTP 422 with validation message
- **Suspended Account**: HTTP 400 with account status message

### 10.1. WhatsApp Fallback Behavior

If WhatsApp channel is disabled in Twilio Verify Service:
- System automatically retries with SMS channel
- User receives notification: "WhatsApp is not available for this number. Verification code has been sent via SMS instead."
- WhatsApp buttons are hidden from UI
- Verification proceeds normally via SMS

### 11. Security Features

- **Rate Limiting**: Prevents SMS pumping fraud
- **Backend Validation**: All checks performed server-side
- **Phone Normalization**: Ensures consistent phone format
- **Attempt Tracking**: Per phone number, not per user

### 12. Testing Checklist

- [ ] North American number (+1) uses SMS
- [ ] International number shows WhatsApp button
- [ ] WhatsApp button sends code via WhatsApp
- [ ] Rate limiting blocks after 3 attempts
- [ ] Rate limit resets after 24 hours
- [ ] Existing phone numbers can resend codes
- [ ] Error messages display correctly
- [ ] Verification modal shows WhatsApp option for international numbers

### 13. Code Locations

**Backend**:
- `app/Http/Controllers/Step5to5Controller.php` - Main verification logic
- `app/helper.php` - `isNorthAmericanNumber()` helper function
- `routes/web.php` - Route definitions

**Frontend**:
- `resources/views/step5to5.blade.php` - Main form and verification modal

**Database**:
- `database/migrations/2026_01_11_155746_add_verification_fields_to_phone_verifications_table.php` - Schema updates

### 14. Notes

- WhatsApp channel requires Meta Business Account approval
- Twilio Verify API manages codes automatically (no manual code storage for international)
- Rate limiting is per phone number, allowing multiple users to verify different numbers
- The system gracefully falls back if Twilio Verify is not configured (shows error message)
