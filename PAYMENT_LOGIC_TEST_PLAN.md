# Payment Logic Test Plan

## Overview
This document outlines the test scenarios for the payment logic based on the business requirements.

## Key Concepts

### 1. Booking Fee
- **Definition**: 10% of the booking price
- **Recipient**: Goes to Proxima Ride (the platform)
- **Applies to**: All passengers EXCEPT students with valid student cards

### 2. Booking Price
- **Definition**: The price set by the driver (can be any amount)
- **Recipient**: Goes to the driver
- **Applies to**: All passengers (students still pay booking price)

### 3. Student Discount
- **Condition**: Student must have uploaded a valid student card
- **Benefit**: Booking fee (10%) is waived
- **Expiry**: When student card expiry date approaches, automated email is sent. If not renewed, student starts paying booking fee again.

### 4. Payment Methods (Chosen by Driver)

#### A. Cash Payment
- **What passenger pays online**: Booking fee only (e.g., $6 for $60 ride)
- **What passenger pays to driver**: Booking price in cash (e.g., $60)
- **Advantage for passenger**: If passenger doesn't show up, they only lose the booking fee, not the full amount
- **Disadvantage for driver**: Driver has no guarantee passenger will pay

#### B. Online Payment
- **What passenger pays online**: Booking fee + Booking price (e.g., $6 + $60 = $66)
- **What passenger pays to driver**: Nothing (already paid online)
- **Advantage for driver**: Guaranteed payment
- **Disadvantage for passenger**: Loses full amount if they don't show up

#### C. Secured Cash Payment
- **Details**: (To be clarified based on implementation)

---

## Test Scenarios

### Scenario 1: Regular User - Cash Payment
**Setup**:
- User: Regular (not a student)
- Ride Price: $60
- Payment Method: Cash
- Seats: 1

**Expected Results**:
- Booking Fee: $6 (10% of $60)
- Booking Price: $60
- **Online Payment**: $6 (booking fee only)
- **Cash to Driver**: $60

**Test Steps**:
1. Login as regular user
2. Find a ride with Cash payment method
3. Book 1 seat for $60 ride
4. Verify booking fee shows as $6
5. Verify total online payment is $6
6. Complete payment
7. Verify transaction shows booking fee of $6
8. Verify booking price of $60 is NOT charged online

---

### Scenario 2: Regular User - Online Payment
**Setup**:
- User: Regular (not a student)
- Ride Price: $60
- Payment Method: Online
- Seats: 1

**Expected Results**:
- Booking Fee: $6 (10% of $60)
- Booking Price: $60
- **Online Payment**: $66 (booking fee + booking price)
- **Cash to Driver**: $0

**Test Steps**:
1. Login as regular user
2. Find a ride with Online payment method
3. Book 1 seat for $60 ride
4. Verify booking fee shows as $6
5. Verify booking price shows as $60
6. Verify total online payment is $66
7. Complete payment
8. Verify transaction shows booking fee of $6 and booking price of $60

---

### Scenario 3: Student with Valid Card - Cash Payment
**Setup**:
- User: Student (student = '1', valid student card, charge_booking = '2')
- Ride Price: $60
- Payment Method: Cash
- Seats: 1

**Expected Results**:
- Booking Fee: $0 (waived for students)
- Booking Price: $60
- **Online Payment**: $0 (no booking fee charged)
- **Cash to Driver**: $60

**Test Steps**:
1. Login as student with valid student card
2. Find a ride with Cash payment method
3. Book 1 seat for $60 ride
4. Verify booking fee shows as $0 (or hidden)
5. Verify total online payment is $0
6. Complete booking
7. Verify no booking fee transaction is created
8. Verify booking price of $60 is NOT charged online

---

### Scenario 4: Student with Valid Card - Online Payment
**Setup**:
- User: Student (student = '1', valid student card, charge_booking = '2')
- Ride Price: $60
- Payment Method: Online
- Seats: 1

**Expected Results**:
- Booking Fee: $0 (waived for students)
- Booking Price: $60
- **Online Payment**: $60 (only booking price, no booking fee)
- **Cash to Driver**: $0

**Test Steps**:
1. Login as student with valid student card
2. Find a ride with Online payment method
3. Book 1 seat for $60 ride
4. Verify booking fee shows as $0 (or hidden)
5. Verify booking price shows as $60
6. Verify total online payment is $60 (not $66)
7. Complete payment
8. Verify transaction shows booking fee of $0 and booking price of $60

---

### Scenario 5: Student with Expired Card - Cash Payment
**Setup**:
- User: Student (student = '1', expired student card, charge_booking = '1')
- Ride Price: $60
- Payment Method: Cash
- Seats: 1

**Expected Results**:
- Booking Fee: $6 (NOT waived - card expired)
- Booking Price: $60
- **Online Payment**: $6 (booking fee only)
- **Cash to Driver**: $60

**Test Steps**:
1. Login as student with expired student card
2. Find a ride with Cash payment method
3. Book 1 seat for $60 ride
4. Verify booking fee shows as $6 (not waived)
5. Verify total online payment is $6
6. Complete payment
7. Verify transaction shows booking fee of $6

---

### Scenario 6: Student Pending Verification - Cash Payment
**Setup**:
- User: Student (student = '2', pending verification)
- Ride Price: $60
- Payment Method: Cash
- Seats: 1

**Expected Results**:
- Booking Fee: $6 (charged initially)
- Booking Price: $60
- **Online Payment**: $6 (booking fee only)
- **Note**: If student is later approved, booking fee should be refunded/credited

**Test Steps**:
1. Login as student with pending verification
2. Find a ride with Cash payment method
3. Book 1 seat for $60 ride
4. Verify booking fee shows as $6
5. Verify message about pending verification and refund
6. Complete payment
7. Verify transaction shows booking fee of $6
8. After admin approves student, verify booking fee is refunded/credited

---

### Scenario 7: Multiple Seats - Regular User - Cash Payment
**Setup**:
- User: Regular (not a student)
- Ride Price: $60 per seat
- Payment Method: Cash
- Seats: 2

**Expected Results**:
- Booking Fee: $12 (10% of $120 = $6 per seat × 2 seats)
- Booking Price: $120 ($60 × 2 seats)
- **Online Payment**: $12 (booking fee only)
- **Cash to Driver**: $120

**Test Steps**:
1. Login as regular user
2. Find a ride with Cash payment method
3. Book 2 seats for $60 ride
4. Verify booking fee shows as $12 ($6 × 2)
5. Verify booking price shows as $120 ($60 × 2)
6. Verify total online payment is $12
7. Complete payment
8. Verify transaction shows booking fee of $12

---

### Scenario 8: Multiple Seats - Student - Cash Payment (Limit Test)
**Setup**:
- User: Student (student = '1', valid student card)
- Ride Price: $60 per seat
- Payment Method: Cash
- Seats: 2 (maximum allowed for students on Cash rides)

**Expected Results**:
- Booking Fee: $0 (waived for students)
- Booking Price: $120 ($60 × 2 seats)
- **Online Payment**: $0
- **Cash to Driver**: $120
- **Validation**: Should allow 2 seats, block 3+ seats

**Test Steps**:
1. Login as student with valid student card
2. Find a ride with Cash payment method
3. Try to book 2 seats
4. Verify booking is allowed
5. Verify booking fee shows as $0
6. Try to book 3 seats
7. Verify booking is blocked with error message
8. Complete booking with 2 seats
9. Verify no booking fee transaction

---

### Scenario 9: Student - Online Payment (No Limit)
**Setup**:
- User: Student (student = '1', valid student card)
- Ride Price: $60 per seat
- Payment Method: Online
- Seats: 3 (no limit for Online payment)

**Expected Results**:
- Booking Fee: $0 (waived for students)
- Booking Price: $180 ($60 × 3 seats)
- **Online Payment**: $180 (only booking price, no booking fee)
- **Validation**: Should allow any number of seats for Online payment

**Test Steps**:
1. Login as student with valid student card
2. Find a ride with Online payment method
3. Book 3 seats
4. Verify booking is allowed (no limit for Online)
5. Verify booking fee shows as $0
6. Verify booking price shows as $180
7. Verify total online payment is $180
8. Complete payment
9. Verify transaction shows booking fee of $0 and booking price of $180

---

### Scenario 10: Edge Case - Ride Price ≤ $15
**Setup**:
- User: Regular (not a student)
- Ride Price: $15
- Payment Method: Cash
- Seats: 1

**Expected Results**:
- Booking Fee: $0 (based on code: price ≤ 15, booking fee = 0)
- Booking Price: $15
- **Online Payment**: $0
- **Cash to Driver**: $15

**Test Steps**:
1. Login as regular user
2. Find/create a ride with price = $15
3. Book 1 seat
4. Verify booking fee shows as $0
5. Verify total online payment is $0
6. Complete booking
7. Verify no booking fee transaction

---

### Scenario 11: Edge Case - Ride Price $15-$30
**Setup**:
- User: Regular (not a student)
- Ride Price: $25
- Payment Method: Cash
- Seats: 1

**Expected Results**:
- Booking Fee: $2.50 (10% of $25)
- Booking Price: $25
- **Online Payment**: $2.50
- **Cash to Driver**: $25

**Test Steps**:
1. Login as regular user
- Find/create a ride with price = $25
3. Book 1 seat
4. Verify booking fee shows as $2.50
5. Verify total online payment is $2.50
6. Complete payment
7. Verify transaction shows booking fee of $2.50

---

### Scenario 12: Secured Cash Payment
**Setup**:
- User: Regular (not a student)
- Ride Price: $60
- Payment Method: Secured Cash
- Seats: 1

**Expected Results**:
- (To be determined based on implementation)
- Need to verify how Secured Cash differs from regular Cash

**Test Steps**:
1. Login as regular user
2. Find a ride with Secured Cash payment method
3. Book 1 seat
4. Verify payment flow
5. Document actual behavior

---

## Code Locations to Verify

### Frontend (booking.blade.php)
1. **Booking Fee Calculation** (Line ~1359-1372):
   - Price ≤ $15: bookingPrice = 0
   - Price $15-$30: bookingPrice = 10% of price
   - Price > $30: bookingPrice = setting->booking_price

2. **Student Discount Application**:
   - Check if `charge_booking` field is used
   - Verify booking fee is set to 0 for students with valid cards

3. **Payment Method Handling** (Line ~1560-1574):
   - Cash: Only booking fee charged online
   - Online: Booking fee + booking price charged online

### Backend (BookingController.php)
1. **Transaction Creation** (Line ~1173-1184):
   - Verify `booking_fee` field is set correctly
   - Verify `price` field contains correct amount

2. **Student Validation**:
   - Check `user->student` field
   - Check `user->charge_booking` field
   - Check `user->student_card_exp_date` field

---

## Test Checklist

- [ ] Regular user - Cash payment - 1 seat
- [ ] Regular user - Cash payment - Multiple seats
- [ ] Regular user - Online payment - 1 seat
- [ ] Regular user - Online payment - Multiple seats
- [ ] Student (valid card) - Cash payment - 1 seat
- [ ] Student (valid card) - Cash payment - 2 seats (max)
- [ ] Student (valid card) - Cash payment - 3 seats (should block)
- [ ] Student (valid card) - Online payment - 1 seat
- [ ] Student (valid card) - Online payment - Multiple seats (no limit)
- [ ] Student (expired card) - Cash payment
- [ ] Student (pending) - Cash payment
- [ ] Edge case - Price ≤ $15
- [ ] Edge case - Price $15-$30
- [ ] Secured Cash payment flow

---

## Notes

1. **Student Card Expiry**: Automated email should be sent when expiry approaches. Need to verify this functionality.

2. **Student Verification Refund**: When student with pending status is approved, booking fees paid before approval should be refunded/credited.

3. **Booking Fee Calculation**: The 10% is calculated from the booking price set by the driver, not from the ride price per seat.

4. **Multiple Seats**: Booking fee is multiplied by number of seats booked.

5. **Tax**: Tax may be applied on booking fee. Need to verify tax calculation logic.

---

## Questions to Clarify

1. What is "Secured Cash" payment method? How does it differ from regular Cash?
2. Is booking fee calculated per seat or per booking?
3. How is tax calculated? Is it on booking fee only or total amount?
4. What happens if a student's card expires mid-booking?
5. Is there a minimum booking fee amount?
6. How are refunds handled for students who paid booking fee before approval?
