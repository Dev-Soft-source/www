For the current booking flow, `pay amount` depends mainly on these pieces:

1. `seat fare`
2. `booking fee`
3. `tax` on the booking fee
4. whether `Coffee from the Wall` is used
5. whether the user is a student with booking-fee waiver

**How it works**

`seat fare`
- `rideUnitPrice * selected seats`
- if the ride is `firm`, the fare is reduced by the firm discount first

`booking fee`
- if ride price per seat is `<= 15`, booking fee is `0`
- otherwise booking fee is about `10%` of the seat price per selected seat
- if the user is a verified student with valid status, booking fee can become `0`

`tax`
- tax is calculated on the `booking fee`, not on the full ride fare
- if `deduct_tax == deduct_from_passenger`, then:
  - state-wise tax uses `stateTax`
  - otherwise uses the global tax rate

`Coffee from the Wall`
- if enabled, it subtracts the `booking fee` from what the user pays now
- it does not reduce the seat fare itself
- so:
  - `total due now` drops by the booking fee
  - tax may still remain, depending on the current logic already calculated

**By user status**

`common passenger`
- pays:
  - seat fare
  - booking fee
  - tax
- if Coffee Wall is used:
  - pays seat fare + tax now
  - booking fee is covered by Coffee Wall

`student`
- if booking fee waiver applies:
  - booking fee = `0`
  - tax on booking fee = usually `0`
  - pays mostly just the seat fare
- if waiver does not apply yet:
  - same as common passenger
- if Coffee Wall is used and booking fee is already `0`:
  - effectively nothing extra is reduced, because there is no booking fee to cover

**Important difference by payment method**

`cash ride`
- user usually pays only booking-fee-related amount now
- seat fare is paid to driver later
- so payable-now is smaller

`online ride`
- user pays full ride fare + booking fee + tax now
- minus Coffee Wall effect if used

Main logic is in:
- [BookSeatController.dart](/d:/temp/radu_proxima/www/flutter/lib/pages/book_seat/BookSeatController.dart)
- [pricing_widget.dart](/d:/temp/radu_proxima/www/flutter/lib/pages/book_seat/widget/pricing_widget.dart)

If you want, I can turn this into a simple formula table with 4 cases:
- common passenger, no coffee wall
- common passenger, coffee wall
- student, no coffee wall
- student, coffee wall