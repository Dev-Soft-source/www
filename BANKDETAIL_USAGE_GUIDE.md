# BankDetail Model - Web Frontend Usage Guide

## Overview
The `BankDetail` model is used to store user payout information including bank transfer details, Interac e-Transfer email, and PayPal email addresses.

## Model Structure

**Location:** `app/Models/BankDetail.php`

**Table:** `bank_details`

**Key Fields:**
- `user_id` - Foreign key to users table
- `bank_id` - Foreign key to banks table (for bank transfers)
- `bank_title` - Account holder name
- `acc_no` - Account number (7-12 digits)
- `branch` - Branch name
- `branch_number` - Transit number (5 digits)
- `branch_address` - Branch address
- `institution_number` - Institution number (3 digits)
- `address` - Account holder address
- `interac_email` - Email for Interac e-Transfer
- `paypal_email` - PayPal email address
- `status` - Status: 'pending', 'sent_amount', 'verify', 'verified', 'admin_verify'
- `set_default` - Default payout method: 'interac', 'bank', or 'paypal'
- `admin_verify_amount` - Amount sent by admin for verification
- `user_verify_amount` - Amount entered by user for verification
- `random_id` - Auto-generated unique identifier (format: XXXX-ID)

**Relationships:**
- `belongsTo(Bank::class)` - Relationship to Bank model

---

## Usage in Web Frontend (Blade Templates)

### 1. Server-Side Rendering (Traditional Web)

#### Controller: `app/Http/Controllers/PayoutController.php`

**Getting BankDetail Data:**
```php
// In PayoutController@index
$user_id = auth()->user()->id;
$userBankDetail = BankDetail::where('user_id', $user_id)->first();

// Pass to view
return view('payout', [
    'userBankDetail' => $userBankDetail,
    'banks' => $banks,
    // ... other data
]);
```

**View: `resources/views/payout.blade.php`**

**Displaying BankDetail in Blade:**
```blade
{{-- Check if bank detail exists --}}
@if(optional($userBankDetail)->interac_email)
    {{-- Display existing Interac email (readonly) --}}
    <input 
        type="email" 
        value="{{ $userBankDetail->interac_email }}" 
        readonly
    />
@endif

{{-- Bank transfer fields --}}
<input 
    type="text" 
    name="account_holder_name" 
    value="{{ old('account_holder_name', $userBankDetail->bank_title ?? '') }}"
    {{ optional($userBankDetail)->bank_title ? 'readonly' : '' }}
/>

<input 
    type="text" 
    name="branch_number" 
    value="{{ old('branch_number', $userBankDetail->branch_number ?? '') }}"
    {{ optional($userBankDetail)->branch_number ? 'readonly' : '' }}
/>

<input 
    type="text" 
    name="institution_number" 
    value="{{ old('institution_number', $userBankDetail->institution_number ?? '') }}"
    {{ optional($userBankDetail)->institution_number ? 'readonly' : '' }}
/>

<input 
    type="text" 
    name="account_holder_number" 
    value="{{ old('account_holder_number', $userBankDetail->acc_no ?? '') }}"
    {{ optional($userBankDetail)->acc_no ? 'readonly' : '' }}
/>

{{-- PayPal email --}}
<input 
    type="email" 
    name="paypal_email" 
    value="{{ old('paypal_email', $userBankDetail->paypal_email ?? '') }}"
/>

{{-- Check status for verification --}}
@if (optional($userBankDetail)->status === 'admin_verify')
    <button type="submit">Verify Bank</button>
@endif

{{-- Set default payout method --}}
<input 
    type="radio" 
    name="set_default" 
    value="bank" 
    {{ optional($userBankDetail)->set_default == 'bank' ? 'checked' : '' }}
/>
```

**Saving BankDetail (Form Submission):**
```php
// In PayoutController@store
$getBankDetail = BankDetail::where('user_id', $user_id)->first();

if(isset($getBankDetail) && !is_null($getBankDetail)){
    // Update existing
} else {
    // Create new
    $getBankDetail = new BankDetail();
}

// Set fields based on payout method
if($request->payout_method == "paypal"){
    $getBankDetail->paypal_email = $request->paypal_email;
} else {
    $getBankDetail->bank_id = $request->bank_name;
    $getBankDetail->bank_title = $request->account_holder_name;
    $getBankDetail->acc_no = $request->account_holder_number;
    $getBankDetail->branch = $request->branch;
    $getBankDetail->address = $request->account_holder_address;
    $getBankDetail->institution_number = $request->institution_number;
    $getBankDetail->branch_address = $request->branch_address;
    $getBankDetail->branch_number = $request->branch_number;
}

$getBankDetail->user_id = $user_id;
$getBankDetail->status = "pending";
$getBankDetail->set_default = $request->set_default ?? "bank";
$getBankDetail->save();
```

---

## Usage via API (For SPA/Mobile Apps)

### API Endpoints

**Routes:** `routes/app.php`
- `GET /api/bank-detail` - Get user's bank detail
- `POST /api/bank-detail/storeUpdateBankDetail` - Create/Update bank detail
- `POST /api/bank-detail/verifyBank` - Verify bank account

### Controller: `app/Http/Controllers/Api/App/BankDetailController.php`

#### 1. Get Bank Detail
```javascript
// Frontend API call
const response = await fetch('/api/bank-detail?lang_id=1', {
    headers: {
        'Authorization': 'Bearer ' + token,
        'Accept': 'application/json'
    }
});

const data = await response.json();
// Response structure:
// {
//     "data": {
//         "userBankDetail": {
//             "id": 1,
//             "user_id": 1,
//             "bank_id": 2,
//             "bank_title": "John Doe",
//             "acc_no": "1234567890",
//             "branch": "Main Branch",
//             "branch_number": "12345",
//             "institution_number": "004",
//             "interac_email": "user@example.com",
//             "paypal_email": "paypal@example.com",
//             "status": "pending",
//             "set_default": "bank"
//         },
//         "banks": [...], // List of available banks
//         "payoutOptionPage": {...}, // Settings
//         "validationMessages": {...}
//     }
// }
```

#### 2. Store/Update Bank Detail
```javascript
// Frontend API call
const formData = {
    type: 'bank', // or 'paypal'
    bank_name: 2, // bank_id (for bank type)
    account_holder_name: 'John Doe',
    account_holder_number: '1234567890',
    branch: 'Main Branch',
    branch_number: '12345',
    institution_number: '004',
    account_holder_address: '123 Main St',
    branch_address: '456 Bank St',
    set_default: 'bank', // or 'interac' or 'paypal'
    // OR for PayPal:
    // type: 'paypal',
    // paypal_email: 'user@paypal.com'
};

const response = await fetch('/api/bank-detail/storeUpdateBankDetail', {
    method: 'POST',
    headers: {
        'Authorization': 'Bearer ' + token,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    body: JSON.stringify(formData)
});

const data = await response.json();
```

#### 3. Verify Bank
```javascript
// Frontend API call
const response = await fetch('/api/bank-detail/verifyBank', {
    method: 'POST',
    headers: {
        'Authorization': 'Bearer ' + token,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    body: JSON.stringify({
        user_verify_amount: 0.01 // Amount sent by admin
    })
});
```

---

## Validation Rules

### Bank Transfer Validation:
- `bank_name` - Required (when type is 'bank')
- `account_holder_name` - Required (when type is 'bank')
- `account_holder_number` - Required, 7-12 digits (when type is 'bank')
- `branch` - Required (when type is 'bank')
- `branch_number` - Required, exactly 5 digits (when type is 'bank')
- `institution_number` - Required, exactly 3 digits (when type is 'bank')
- `account_holder_address` - Required (when type is 'bank')
- `branch_address` - Required (when type is 'bank')

### PayPal Validation:
- `paypal_email` - Required, valid email (when type is 'paypal')

---

## Status Flow

1. **pending** - Initial status when bank detail is created
2. **sent_amount** - Admin has sent verification amount
3. **admin_verify** - Waiting for user to verify the amount
4. **verify** - User has verified (temporary status)
5. **verified** - Bank account is fully verified

---

## Common Patterns

### Check if Bank Detail Exists
```blade
@if(optional($userBankDetail))
    {{-- Bank detail exists --}}
@else
    {{-- No bank detail yet --}}
@endif
```

### Check Status
```blade
@if(optional($userBankDetail)->status === 'admin_verify')
    {{-- Show verification form --}}
@elseif(optional($userBankDetail)->status === 'verified')
    {{-- Show verified message --}}
@endif
```

### Make Fields Readonly if Already Set
```blade
<input 
    value="{{ $userBankDetail->acc_no ?? '' }}"
    {{ optional($userBankDetail)->acc_no ? 'readonly' : '' }}
/>
```

### Access Related Bank Model
```php
// In controller
$userBankDetail = BankDetail::with('bank')->where('user_id', $user_id)->first();
$bankName = $userBankDetail->bank->name;

// In Blade
{{ $userBankDetail->bank->name ?? '' }}
```

---

## Web Routes

**Location:** `routes/web.php`

```php
// View payout page
Route::get('{lang?}/payout-options', [PayoutController::class, 'index'])->name('payout');

// Store/Update bank detail
Route::post('payout/store', [PayoutController::class, 'store'])->name('payout.store');

// Verify bank
Route::post('payout/verifyBank', [PayoutController::class, 'verifyBank'])->name('payout.verifyBank');
```

---

## Example: Complete Form Implementation

```blade
<form method="POST" action="{{ route('payout.store') }}">
    @csrf
    
    {{-- Payout Method Selection --}}
    <input type="radio" name="payout_method" value="interac" checked>
    <input type="radio" name="payout_method" value="bank">
    <input type="radio" name="payout_method" value="paypal">
    
    {{-- Bank Transfer Fields (shown when bank is selected) --}}
    <div id="bank_fields">
        <input name="bank_name" value="{{ old('bank_name', $userBankDetail->bank_id ?? '') }}">
        <input name="account_holder_name" value="{{ old('account_holder_name', $userBankDetail->bank_title ?? '') }}">
        <input name="account_holder_number" value="{{ old('account_holder_number', $userBankDetail->acc_no ?? '') }}">
        <input name="branch_number" value="{{ old('branch_number', $userBankDetail->branch_number ?? '') }}">
        <input name="institution_number" value="{{ old('institution_number', $userBankDetail->institution_number ?? '') }}">
    </div>
    
    {{-- PayPal Fields (shown when paypal is selected) --}}
    <div id="paypal_fields">
        <input name="paypal_email" value="{{ old('paypal_email', $userBankDetail->paypal_email ?? '') }}">
    </div>
    
    {{-- Set Default --}}
    <input type="radio" name="set_default" value="bank" 
           {{ optional($userBankDetail)->set_default == 'bank' ? 'checked' : '' }}>
    
    <button type="submit">Save</button>
</form>
```

---

---

## Usage in Admin Panel

The admin panel uses BankDetail for two main purposes:
1. **Verify User Banks** - Admin verifies user bank accounts by sending verification amounts
2. **Bank Settings** - Admin configures the company's own bank details for payouts

### 1. Verify User Banks

**Controller:** `app/Http/Controllers/Api/Admin/VerifyBanksController.php`

**Vue Component:** `resources/js/admin/Verify_Banks/VerifyBanks.vue`

**Vuex Store:** `resources/js/store/admin/verify_banks.js`

**API Routes:**
- `GET /api/admin/verify-banks` - List all user bank details pending verification
- `PUT /api/admin/verify-bank-request/{id}` - Send verification amount to user

#### Getting User Bank Details (Admin):
```javascript
// Vuex Action
this.$store.dispatch("verify_banks/fetchVerifyBanks", { url: page_url });

// API Endpoint
GET /api/admin/verify-banks?q=1&limit=10&sortBy=id&sortType=desc&searchParam=
```

**Response Structure:**
```json
{
    "status": "Success",
    "data": [
        {
            "id": 1,
            "random_id": "ABCD-1",
            "bank_id": 2,
            "bank_title": "John Doe",
            "acc_no": "1234567890",
            "iban": "004",
            "branch": "12345",
            "address": "123 Main St",
            "status": "pending",
            "paypal_email": null,
            "set_default": "bank",
            "bank": {
                "id": 2,
                "name": "TD Bank"
            }
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        ...
    }
}
```

#### Verifying a Bank (Admin):
```javascript
// Vuex Action
this.$store.dispatch("verify_banks/rejectWithdrawal", {
    id: bankDetailId,
    admin_verify_amount: 0.01
});

// API Endpoint
PUT /api/admin/verify-bank-request/{id}
Body: {
    "admin_verify_amount": 0.01
}
```

**Controller Logic:**
```php
// In VerifyBanksController@verifyRequest
BankDetail::whereId($id)->update([
    'status' => 'admin_verify',
    'admin_verify_amount' => $request->admin_verify_amount,
]);
```

#### Vue Component Example:
```vue
<template>
    <div>
        <table>
            <tr v-for="verify_bank in verify_banks" :key="verify_bank.id">
                <td>{{ verify_bank.random_id }}</td>
                <td>{{ verify_bank.bank_title }}</td>
                <td>{{ verify_bank.acc_no }}</td>
                <td>{{ verify_bank.branch }}</td>
                <td>{{ verify_bank.status }}</td>
                <td v-if="verify_bank.status === 'pending'">
                    <button @click="toggleModal2(verify_bank)">Verify</button>
                </td>
            </tr>
        </table>
        
        <!-- Verification Modal -->
        <div v-if="showModal2">
            <input type="number" v-model="rejectionReason" />
            <button @click="rejectWithdrawal(selectedWithdrawal)">Submit</button>
        </div>
    </div>
</template>

<script>
export default {
    computed: {
        ...mapState({
            verify_banks: (state) => state.verify_banks.verify_banks,
            pagination: (state) => state.verify_banks.pagination,
        }),
    },
    methods: {
        rejectWithdrawal(withdrawal) {
            this.$store.dispatch("verify_banks/rejectWithdrawal", {
                id: withdrawal.id,
                admin_verify_amount: this.rejectionReason,
            });
        },
    },
    created() {
        this.$store.dispatch("verify_banks/fetchVerifyBanks");
    },
};
</script>
```

### 2. Bank Settings (Admin Company Bank)

**Controller:** `app/Http/Controllers/Api/Admin/BankSettingController.php`

**Vue Component:** `resources/js/admin/General_Settings/BankSettings.vue`

**Vuex Store:** `resources/js/store/admin/bank_settings.js`

**API Routes:**
- `GET /api/admin/bank-settings` - Get admin bank settings
- `POST /api/admin/bank-settings` - Create/Update admin bank settings

#### Key Difference:
- Admin bank settings use `type = 'admin'` (not `type = 'user'`)
- Only one admin bank setting record exists
- Used for company payouts to users

#### Getting Admin Bank Settings:
```javascript
// Vuex Action
this.$store.dispatch("bank_settings/fetchSetting");

// API Endpoint
GET /api/admin/bank-settings?q=1
```

**Response Structure:**
```json
{
    "status": "Success",
    "data": {
        "id": 1,
        "random_id": "ADMN-1",
        "bank_id": 2,
        "bank_title": "Company Name",
        "acc_no": "123456",
        "iban": "004",
        "branch": "12345",
        "address": "Company Address",
        "status": "verified",
        "paypal_email": "company@paypal.com",
        "set_default": "bank",
        "bank": {
            "id": 2,
            "name": "TD Bank"
        }
    }
}
```

#### Saving Admin Bank Settings:
```javascript
// Vuex Action
this.$store.dispatch("bank_settings/addUpdateForm");

// API Endpoint
POST /api/admin/bank-settings
Body: {
    "bank_id": 2,
    "bank_title": "Company Name",
    "acc_no": "123456",
    "iban": "004",
    "branch": "12345",
    "address": "Company Address",
    "paypal_email": "company@paypal.com",
    "set_default": "bank" // or "paypal"
}
```

**Validation Rules:**
- `bank_id` - Required
- `set_default` - Required
- If `set_default === 'bank'`:
  - `bank_title` - Required
  - `acc_no` - Required, numeric, exactly 6 digits
  - `iban` - Required, numeric, exactly 3 digits
  - `branch` - Required, numeric, exactly 5 digits
  - `address` - Required
- If `set_default === 'paypal'`:
  - `paypal_email` - Required

#### Vue Component Example:
```vue
<template>
    <form @submit.prevent="addUpdateForm()">
        <select v-model="form.bank_id" @change="updateForm('bank_id', $event.target.value)">
            <option v-for="bank in banks" :key="bank.id" :value="bank.id">
                {{ bank.name }}
            </option>
        </select>
        
        <input v-model="form.bank_title" @input="updateForm('bank_title', $event.target.value)" />
        <input v-model="form.acc_no" @input="updateForm('acc_no', $event.target.value)" />
        <input v-model="form.iban" @input="updateForm('iban', $event.target.value)" />
        <input v-model="form.branch" @input="updateForm('branch', $event.target.value)" />
        <input v-model="form.address" @input="updateForm('address', $event.target.value)" />
        <input v-model="form.paypal_email" @input="updateForm('paypal_email', $event.target.value)" />
        
        <div>
            <input type="radio" v-model="form.set_default" value="bank" />
            <input type="radio" v-model="form.set_default" value="paypal" />
        </div>
        
        <button type="submit">Save changes</button>
    </form>
</template>

<script>
export default {
    computed: {
        ...mapState({
            form: (state) => state.bank_settings.form,
            validationErros: (state) => state.bank_settings.validationErros,
        }),
    },
    methods: {
        updateForm(field, value) {
            this.$store.commit("bank_settings/setForm", {
                [field]: value,
            });
        },
        addUpdateForm() {
            this.$store.dispatch("bank_settings/addUpdateForm");
        },
    },
    created() {
        this.$store.dispatch("bank_settings/fetchSetting");
    },
};
</script>
```

### 3. Withdrawal Requests (Using BankDetail)

**Controller:** `app/Http/Controllers/Api/Admin/WithdrawalRequestController.php`

**Usage:** BankDetail is accessed through withdrawal requests to get user payout information.

#### Getting Withdrawal Requests with Bank Details:
```php
// In WithdrawalRequestController@index
$withdrawals = Payout::query()
    ->selectRaw('user_id, SUM(amount) as total_amount')
    ->where('status', 'request')
    ->groupBy('user_id')
    ->with('driver.bankDetail', 'driver.bankDetail.bank')
    ->get();
```

#### Accessing BankDetail in Withdrawal:
```php
// When processing withdrawal
$bankDetail = BankDetail::where('user_id', $id)->first();

$emailData = [
    'payment_method' => $bankDetail->set_default === 'paypal' ? 'paypal' : 'bank',
    'paypal_email' => $bankDetail->paypal_email ?? null,
    'account_number' => $bankDetail->account_number ?? null,
    'bank_title' => $bankDetail->bank_title ?? null,
];
```

---

## Admin API Resource

**Location:** `app/Http/Resources/Admin/BankDetailResource.php`

**Purpose:** Formats BankDetail data for admin API responses

**Fields Returned:**
- `id`
- `random_id`
- `bank_id`
- `bank_title`
- `acc_no`
- `iban`
- `branch`
- `address`
- `status`
- `paypal_email`
- `set_default`
- `bank` (relationship)

**Usage:**
```php
// In controllers
return $this->apiSuccessResponse(
    new BankDetailResource($bankDetail), 
    'Data Get Successfully!'
);

// For collections
return $this->apiSuccessResponse(
    BankDetailResource::collection($bankDetails), 
    'Data Get Successfully!'
);
```

---

## Admin Routes

**Location:** `routes/admin.php`

```php
// Bank Settings
Route::apiResource('bank-settings', BankSettingController::class);

// Verify Banks
Route::get('verify-banks', [VerifyBanksController::class, 'index']);
Route::put('verify-bank-request/{id}', [VerifyBanksController::class, 'verifyRequest']);

// Withdrawal Requests (uses BankDetail)
Route::get('withdrawal-requests', [WithdrawalRequestController::class, 'index']);
Route::put('accept-withdrawal-request/{id}', [WithdrawalRequestController::class, 'acceptRequest']);
Route::put('reject-withdrawal-request/{id}', [WithdrawalRequestController::class, 'rejectRequest']);
```

---

## Admin Workflow

### Bank Verification Workflow:
1. User submits bank details → Status: `pending`
2. Admin views pending banks in Verify Banks page
3. Admin clicks "Verify" and enters verification amount
4. System updates: `status = 'admin_verify'`, `admin_verify_amount = [amount]`
5. User receives notification and enters the amount
6. If amount matches → Status: `verified`
7. If amount doesn't match → User can retry

### Withdrawal Processing Workflow:
1. User requests withdrawal
2. Admin views withdrawal requests with user's BankDetail
3. Admin processes payment using BankDetail information:
   - If `set_default === 'paypal'` → Use `paypal_email`
   - If `set_default === 'bank'` → Use bank account details
4. Admin marks withdrawal as completed
5. System updates `BankDetail.payment_status = 'completed'`

---

## Notes

1. **Readonly Fields**: Once bank details are saved, certain fields become readonly to prevent accidental changes
2. **Status Management**: The status field controls the verification workflow
3. **Random ID**: Auto-generated on save (format: XXXX-ID) for unique identification
4. **Default Method**: Users can set a default payout method (interac, bank, or paypal)
5. **Multi-language Support**: The system supports multiple languages for error messages and labels
6. **Type Field**: BankDetail has a `type` field:
   - `'user'` - User bank details (for receiving payouts)
   - `'admin'` - Admin/company bank details (for sending payouts)
7. **Admin Bank Settings**: Only one admin bank setting record should exist (type = 'admin')
8. **Verification Amount**: Admin sends a small amount (e.g., $0.01) to verify bank account ownership
