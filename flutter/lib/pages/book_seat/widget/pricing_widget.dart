import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/card_shadow_widget.dart';
import 'package:proximaride_app/pages/widgets/check_box_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';
Widget pricingWidget({context, controller, screenWidth}){


  var amount = double.parse(controller.ride['ride_detail']!= null && controller.ride['ride_detail'][0] != null ? controller.ride['ride_detail'][0]['price'] : '0.0') * (int.parse(controller.seatAvailable.value.toString()) + controller.currentUserBookedSeat.value);
  var bookingFee = controller.calculateBookingFee(double.parse(controller.setting['booking_price'] != null ? controller.setting['booking_price'].toString() : "0"));
  var coffeeBookingFee = controller.calculateBookingFee(double.parse(controller.setting['booking_price'] != null ? controller.setting['booking_price'].toString() : "0"), method: "coffee");

  var seatAmount = amount;
  var discountFirm = 0.0;
  if(controller.policyType == 'firm') {
    amount = amount - amount * (double.parse(controller.setting['frim_discount'].toString()) / 100);
    discountFirm = seatAmount * (double.parse(controller.setting['frim_discount'].toString()) / 100);
    //bookingFee = bookingFee - bookingFee * (double.parse(controller.setting['frim_discount'].toString()) / 100);
  }

  var taxAmt =0.0;
  if(controller.setting['deduct_tax'] != null && controller.setting['deduct_tax'] == "deduct_from_passenger"){
    if(controller.setting['tax_type'] == "state_wise_tax"){
      taxAmt = double.parse(((bookingFee * controller.stateTax.value) / 100).toString());
    }else{
      taxAmt = double.parse(((bookingFee * double.parse(controller.setting['tax'])) / 100).toString());
    }
  }

  var total = amount + bookingFee + taxAmt;



  var payableAmount = double.parse(controller.ride['ride_detail']!= null && controller.ride['ride_detail'][0] != null ? controller.ride['ride_detail'][0]['price'] : '0.0') * (int.parse(controller.seatAvailable.value.toString()));
  var payableBookingFee = controller.calculateBookingFee(double.parse(controller.setting['booking_price'] != null ? controller.setting['booking_price'].toString() : "0"), payable: true);

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

  if(controller.ride['payment_method_slug'] == "cash"){
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

    if(controller.ride['payment_method_slug'] == "cash"){
      controller.gPayAmount.value = controller.gPayAmount.value  - payableBookingFee;
    }else{
      controller.gPayAmount.value = payableTotal;
    }


    Future.delayed(const Duration(milliseconds: 500), () {
      controller.showGPayBtn.value = true;
      controller.showGPayBtn.refresh();
    });

  }



  if(controller.ride['payment_method_slug'] == "cash"){
    if(bookingFee <= controller.balanceAmt && controller.balanceAmt != 0.0){
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
                  txt20Size(context: context, title: "${int.parse(controller.seatAvailable.value.toString()) + controller.currentUserBookedSeat.value} ${controller.labelTextDetail['seat_label'] ?? "Seat"}"),
                  txt16Size(context: context, title: "\$${seatAmount.toStringAsFixed(1)}")
                ],
              ),
              10.heightBox,
              if(controller.policyType == 'firm')...[
                txt20Size(context: context, title: "${controller.labelTextDetail['firm_cancellation_label_price_section'] ?? "Firm cancellation"} ${controller.setting['frim_discount']}%"),
                10.heightBox,
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    txt20Size(context: context, title: "${controller.labelTextDetail['firm_discount_label_price_section'] ?? "Discount"}"),
                    txt16Size(context: context, title: "\$${discountFirm.toStringAsFixed(1)}")
                  ],
                ),
                10.heightBox,
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    txt20Size(context: context, title: "${controller.labelTextDetail['firm_your_price_label_price_section'] ?? "Your price"}"),
                    txt16Size(context: context, title: "\$${amount.toStringAsFixed(1)}")
                  ],
                ),
                10.heightBox,
              ],
              if(controller.coffeeFromWall.value == true)...[
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    txt20Size(context: context, title: "${controller.labelTextDetail['booking_fee_label'] ?? "Booking fee"}"),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.start,
                      children: [
                        txt16Size(context: context, title: "\$${bookingFee.toStringAsFixed(1)}"),
                        5.widthBox,
                        Tooltip(
                          margin: EdgeInsets.fromLTRB(
                              getValueForScreenType<double>(
                                context: context,
                                mobile: 15.0,
                                tablet: 15.0,
                              ),
                              getValueForScreenType<double>(
                                context: context,
                                mobile: 0.0,
                                tablet: 0.0,
                              ),
                              getValueForScreenType<double>(
                                context: context,
                                mobile: 15.0,
                                tablet: 15.0,
                              ),
                              getValueForScreenType<double>(
                                context: context,
                                mobile: 0.0,
                                tablet: 0.0,
                              )),
                          triggerMode: TooltipTriggerMode.tap,
                          message: "${controller.labelTextDetail['coffee_from_wall_tooltip'] ?? "Coffee from the wall"}",
                          textStyle: const TextStyle(fontSize: 16,color: Colors.white),
                          showDuration: const Duration(days: 100),
                          waitDuration: Duration.zero,
                          child: Image.asset(infoImage,color: Colors.black, width: getValueForScreenType<double>(
                            context: context,
                            mobile: 20.0,
                            tablet: 20.0,
                          ), height: getValueForScreenType<double>(
                            context: context,
                            mobile: 20.0,
                            tablet: 20.0,
                          )),
                        ),
                      ],
                    )
                  ],
                ),
              ]else...[
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    txt20Size(context: context, title: "${controller.labelTextDetail['booking_fee_label'] ?? "Booking fee"}"),
                    txt16Size(context: context, title: "\$${bookingFee.toStringAsFixed(1)}")
                  ],
                ),
              ],

              if(controller.setting['deduct_tax'] != null && controller.setting['deduct_tax'] == "deduct_from_passenger")...[
                10.heightBox,
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    txt20Size(context: context, title: "${controller.labelTextDetail['tax_label'] ?? "Tax"}"),
                    txt16Size(context: context, title: "\$${taxAmt.toStringAsFixed(2)}")
                  ],
                ),
              ],

              if(controller.coffeeBalanceAmt.value != 0.0)...[
                10.heightBox,
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Row(
                      children: [
                        InkWell(
                          onTap: controller.coffeeDisable.value == false ? () {
                            controller.showGPayBtn.value = false;
                            controller.showGPayBtn.refresh();
                            controller.coffeeFromWall.value = controller.coffeeFromWall.value == true ? false : true;
                            Future.delayed(const Duration(milliseconds: 500), () {
                              if(controller.coffeeFromWall.value == false){
                                controller.showGPayBtn.value = true;
                                controller.showGPayBtn.refresh();
                              }
                            });

                            } : null,
                          child: Container(
                            padding: EdgeInsets.all(5.0),
                            decoration: BoxDecoration(
                              color: controller.coffeeFromWall.value == true ? primaryColor.withOpacity(0.1) : Colors.white,
                              borderRadius: BorderRadius.circular(5.0),
                              border: Border.all(color: controller.coffeeFromWall.value == true ? primaryColor.withOpacity(0.1) : Colors.grey.shade500, style: BorderStyle.solid, width: 1),
                            ),
                              child: txt20Size(
                                  context: context,
                                  title: "${controller.labelTextDetail['coffee_from_wall_label'] ?? "Coffee from the wall"}",
                                  fontFamily: regular,
                                textColor: controller.coffeeFromWall.value == true ? primaryColor : textColor
                              )
                          )
                        ),
                        5.widthBox,
                        Tooltip(
                          margin: EdgeInsets.fromLTRB(
                              getValueForScreenType<double>(
                                context: context,
                                mobile: 15.0,
                                tablet: 15.0,
                              ),
                              getValueForScreenType<double>(
                                context: context,
                                mobile: 0.0,
                                tablet: 0.0,
                              ),
                              getValueForScreenType<double>(
                                context: context,
                                mobile: 15.0,
                                tablet: 15.0,
                              ),
                              getValueForScreenType<double>(
                                context: context,
                                mobile: 0.0,
                                tablet: 0.0,
                              )),
                          triggerMode: TooltipTriggerMode.tap,
                          message: "${controller.labelTextDetail['coffee_from_wall_tooltip'] ?? "Coffee from the wall"}",
                          textStyle: const TextStyle(fontSize: 16,color: Colors.white),
                          showDuration: const Duration(days: 100),
                          waitDuration: Duration.zero,
                          child: Image.asset(infoImage,color: Colors.black, width: getValueForScreenType<double>(
                            context: context,
                            mobile: 20.0,
                            tablet: 20.0,
                          ), height: getValueForScreenType<double>(
                            context: context,
                            mobile: 20.0,
                            tablet: 20.0,
                          )),
                        ),

                      ],
                    ),
                    if(controller.coffeeFromWall.value == true)...[
                      txt16Size(context: context, title: "-\$${bookingFee.toStringAsFixed(1)}", textColor: Colors.red)
                    ]
                  ],
                )
              ],
              10.heightBox,
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  txt20Size(context: context,title: '${controller.labelTextDetail['total_label'] ?? 'Total'}'),
                  txt16Size(context: context, title: "\$${total.toStringAsFixed(1)}", textColor: primaryColor)
                ],
              ),
              if(controller.seatAvailable.value.toString() != "0" && controller.currentUserBookedSeat.value != 0)...[
                10.heightBox,
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    txt20Size(context: context, title: "${controller.labelTextDetail['payable_amount_heading'] ?? "Total payable amount"}"),
                    txt16Size(context: context, title: "\$${payableTotal.toStringAsFixed(1)}", textColor: primaryColor)
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