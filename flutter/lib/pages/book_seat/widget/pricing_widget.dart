import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/book_seat/BookSeatController.dart';
import 'package:proximaride_app/pages/widgets/card_shadow_widget.dart';
import 'package:proximaride_app/pages/widgets/app_html_text.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';
Widget pricingWidget({context, controller, screenWidth}){
  final int selectedSeatCount =
      (controller.seatAvailable.value as num).toInt() +
          (controller.currentUserBookedSeat.value as num).toInt();
  final int payableSeatCount =
      (controller.seatAvailable.value as num).toInt();

  void showHtmlTooltip(String html) {
    showDialog(
      context: context,
      builder: (dialogContext) => AlertDialog(
        backgroundColor: tooltipBackgroundColor,
        contentPadding: const EdgeInsets.all(16),
        content: SingleChildScrollView(
          child: AppHtmlText(
            data: html,
            fontSize: 18,
            fontFamily: carlito,
            textColor: Colors.white,
            linkColor: Colors.white,
          ),
        ),
      ),
    );
  }

  Widget infoTooltipButton(String html) {
    return InkWell(
      onTap: () => showHtmlTooltip(html),
      child: Image.asset(
        infoImage,
        color: Colors.black,
        width: getValueForScreenType<double>(
          context: context,
          mobile: 20.0,
          tablet: 20.0,
        ),
        height: getValueForScreenType<double>(
          context: context,
          mobile: 20.0,
          tablet: 20.0,
        ),
      ),
    );
  }
  var amount = controller.rideUnitPrice() * selectedSeatCount;
  var bookingFee =
      controller.bookingFeeAmountMinorForSeatCount(selectedSeatCount) / 100;
  var coffeeBookingFee =
      controller.bookingFeeAmountMinorForSeatCount(payableSeatCount) / 100;

  var seatAmount = amount;
  var discountFirm = 0.0;
  if(controller.policyType == 'firm') {
    amount = amount - amount * (double.parse(controller.setting['frim_discount'].toString()) / 100);
    discountFirm = seatAmount * (double.parse(controller.setting['frim_discount'].toString()) / 100);
    //bookingFee = bookingFee - bookingFee * (double.parse(controller.setting['frim_discount'].toString()) / 100);
  }

  

  var taxAmt = 0.0;
  if(controller.setting['deduct_tax'] != null && controller.setting['deduct_tax'] == "deduct_from_passenger"){
    if(controller.setting['tax_type'] == "state_wise_tax"){
      taxAmt = double.parse(((bookingFee * controller.stateTax.value) / 100).toString());
    }else{
      taxAmt = double.parse(((bookingFee * double.parse(controller.setting['tax'].toString())) / 100).toString());
    }
  }

  final bool isCashPayment = controller.ride['payment_method_slug'] == "cash";
  var total = isCashPayment ? bookingFee + taxAmt : amount + bookingFee + taxAmt;



  var payableAmount = controller.rideUnitPrice() * payableSeatCount;
  var payableBookingFee =
      controller.bookingFeeAmountMinorForSeatCount(payableSeatCount) / 100;

  if(controller.policyType == 'firm') {
    payableAmount = payableAmount - payableAmount * (double.parse(controller.setting['frim_discount'].toString()) / 100);
    //payableBookingFee = payableBookingFee - payableBookingFee * (double.parse(controller.setting['frim_discount'].toString()) / 100);
  }

  var payableTaxAmt =0.0;
  if(controller.setting['deduct_tax'] != null && controller.setting['deduct_tax'] == "deduct_from_passenger"){
    if(controller.setting['tax_type'] == "state_wise_tax"){
      payableTaxAmt = double.parse(((payableBookingFee * controller.stateTax.value) / 100).toString());
    }else{
      payableTaxAmt = double.parse(((payableBookingFee * double.parse(controller.setting['tax'].toString())) / 100).toString());
    }
  }

  var payableTotal = payableAmount + payableBookingFee + payableTaxAmt;

  if(isCashPayment){
    controller.gPayAmount.value = payableBookingFee + payableTaxAmt;
  }else{
    controller.gPayAmount.value = payableTotal;
  }





  if(coffeeBookingFee > controller.coffeeBalanceAmt.value){
    controller.coffeeFromWall.value = false;
    controller.coffeeDisable.value = false;
  }

  if(controller.coffeeFromWall.value == true){
    total = total - bookingFee;
    payableTotal = payableTotal - payableBookingFee;

    if(isCashPayment){
      controller.gPayAmount.value = controller.gPayAmount.value  - payableBookingFee;
    }else{
      controller.gPayAmount.value = payableTotal;
    }


    Future.delayed(const Duration(milliseconds: 500), () {
      controller.showGPayBtn.value = true;
      controller.showGPayBtn.refresh();
    });

  }



  if(isCashPayment){
    if(total <= controller.balanceAmt && controller.balanceAmt != 0.0){
      controller.bookedByWallet.value = true;
    }else{
      controller.bookedByWallet.value = false;
    }
  }else{
    if(total <= controller.balanceAmt && controller.balanceAmt != 0.0){
      controller.bookedByWallet.value = true;
    }else{
      controller.bookedByWallet.value = false;
    }
  }



  return cardShadowWidget(
      context: context,
    widgetChild: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        postRideWidget(title: "${controller.labelTextDetail['pricing_label'] ?? "Pricing"}", screenWidth: screenWidth, context: context),
        Container(
          padding: EdgeInsets.fromLTRB(getValueForScreenType<double>(
            context: context,
            mobile: 10.0,
            tablet: 10.0,
          ),getValueForScreenType<double>(
            context: context,
            mobile: 10.0,
            tablet: 10.0,
          ),getValueForScreenType<double>(
            context: context,
            mobile: 10.0,
            tablet: 10.0,
          ),getValueForScreenType<double>(
            context: context,
            mobile: 10.0,
            tablet: 10.0,
          )),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                  Expanded(
                    child: txt20Size(
                      context: context,
                      title: "$selectedSeatCount ${controller.labelTextDetail['seat_label'] ?? "Seat"}",
                    ),
                  ),
                  8.widthBox,
                  txt20Size(context: context, title: "\$${seatAmount.toStringAsFixed(2)}")
                ],
              ),
              10.heightBox,
              if(controller.policyType == 'firm')...[
                txt20Size(context: context, title: "${controller.labelTextDetail['firm_cancellation_label_price_section'] ?? "Firm cancellation"} ${controller.setting['frim_discount']}%"),
                10.heightBox,
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: txt20Size(
                        context: context,
                        title: "${controller.labelTextDetail['firm_discount_label_price_section'] ?? "Discount"}",
                      ),
                    ),
                    8.widthBox,
                    txt20Size(context: context, title: "\$${discountFirm.toStringAsFixed(2)}")
                  ],
                ),
                10.heightBox,
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: txt20Size(
                        context: context,
                        title: "${controller.labelTextDetail['firm_your_price_label_price_section'] ?? "Your price"}",
                      ),
                    ),
                    8.widthBox,
                    txt20Size(context: context, title: "\$${amount.toStringAsFixed(2)}")
                  ],
                ),
                10.heightBox,
              ],
              if(controller.coffeeFromWall.value == true)...[
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: txt20Size(
                        context: context,
                        title: "${controller.labelTextDetail['booking_fee_label'] ?? "Booking fee"}",
                      ),
                    ),
                    8.widthBox,
                    Row(
                      mainAxisAlignment: MainAxisAlignment.start,
                      children: [
                        5.widthBox,
                        infoTooltipButton("${controller.labelTextDetail['coffee_from_wall_tooltip'] ?? "Coffee from the wall"}"),
                        5.widthBox,
                        txt20Size(context: context, title: "\$${bookingFee.toStringAsFixed(2)}"),
                      ],
                    )
                  ],
                ),
              ]else...[
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: txt20Size(
                        context: context,
                        title: "${controller.labelTextDetail['booking_fee_label'] ?? "Booking fee"}",
                      ),
                    ),
                    8.widthBox,
                    txt20Size(context: context, title: "\$${bookingFee.toStringAsFixed(2)}")
                  ],
                ),
              ],

              if(controller.setting['deduct_tax'] != null && controller.setting['deduct_tax'] == "deduct_from_passenger")...[
                10.heightBox,
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: txt20Size(
                        context: context,
                        title: "${controller.labelTextDetail['tax_label'] ?? "Tax"}",
                      ),
                    ),
                    8.widthBox,
                    txt20Size(context: context, title: "\$${taxAmt.toStringAsFixed(2)}")
                  ],
                ),
              ],

              if(controller.coffeeBalanceAmt.value != 0.0)...[
                10.heightBox,
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  crossAxisAlignment: CrossAxisAlignment.center,
                  children: [
                    Flexible(
                      child: _coffeeWallToggle(
                        context: context,
                        controller: controller,
                      ),
                    ),
                    8.widthBox,
                    Row(
                      mainAxisSize: MainAxisSize.min,
                      crossAxisAlignment: CrossAxisAlignment.center,
                      children: [
                        infoTooltipButton("${controller.labelTextDetail['coffee_from_wall_tooltip'] ?? "Coffee from the wall"}"),
                        if(controller.coffeeFromWall.value == true)...[
                          4.widthBox,
                          txt20Size(context: context, title: "-\$${bookingFee.toStringAsFixed(2)}", textColor: Colors.red)
                        ]
                      ],
                    )
                  ],
                )
              ],
              10.heightBox,
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  txt20Size(context: context,title: '${controller.labelTextDetail['total_label'] ?? 'Total'}'),
                  txt20Size(context: context, title: "\$${total.toStringAsFixed(2)}", textColor: primaryColor)
                ],
              ),
              if(payableSeatCount > 0)...[
                10.heightBox,
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    txt20Size(context: context, title: "${controller.labelTextDetail['payable_amount_heading'] ?? "Total payable amount"}"),
                    txt20Size(context: context, title: "\$${payableTotal.toStringAsFixed(2)}", textColor: primaryColor)
                  ],
                )
              ],
            ],
          )
        ),
        10.heightBox,
      ],
    )
  );
}

/// Single toggle-style button: tap switches coffee-from-wall on/off.
Widget _coffeeWallToggle({
  required BuildContext context,
  required BookSeatController controller,
}) {
  final disabled = controller.coffeeDisable.value;
  final wallOn = controller.coffeeFromWall.value;
  final fontSize = getValueForScreenType<double>(
    context: context,
    mobile: 18.0,
    tablet: 20.0,
  );

  final label = (controller.labelTextDetail['coffee_from_wall_label'] ??
          'Coffee from the wall');
      // .replaceFirst(' from ', '\nfrom ');

  return Material(
    color: Colors.transparent,
    child: InkWell(
      onTap: disabled
          ? null
          : () {
              controller.showGPayBtn.value = false;
              controller.showGPayBtn.refresh();
              controller.coffeeFromWall.value = !controller.coffeeFromWall.value;
              Future.delayed(const Duration(milliseconds: 500), () {
                if (controller.coffeeFromWall.value == false) {
                  controller.showGPayBtn.value = true;
                  controller.showGPayBtn.refresh();
                }
              });
            },
      borderRadius: BorderRadius.circular(8),
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        curve: Curves.easeInOut,
        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 10),
        decoration: BoxDecoration(
          color: wallOn ? primaryColor : Colors.white,
          borderRadius: BorderRadius.circular(8),
          border: Border.all(
            color: wallOn ? primaryColor : Colors.grey.shade500,
            width: wallOn ? 2 : 1,
          ),
        ),
        alignment: Alignment.center,
        child: Text(
          label,
          textAlign: TextAlign.center,
          maxLines: 3,
          overflow: TextOverflow.ellipsis,
          softWrap: true,
          style: TextStyle(
            fontSize: fontSize,
            fontFamily: regular,
            fontWeight: wallOn ? FontWeight.w500 : FontWeight.w500,
            color: wallOn ? Colors.white : textColor,
            height: 1.2,
          ),
        ),
      ),
    ),
  );
}
