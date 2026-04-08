import 'dart:convert';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:pay/pay.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';
import 'package:proximaride_app/pages/widgets/card_shadow_widget.dart';
import 'package:proximaride_app/pages/widgets/payment_option_card.dart';
import 'package:proximaride_app/consts/constFileLink.dart';

import '../../../consts/payment_config.dart';

Widget onlinePaymentWidget({context, controller, screenWidth}) {
  return Obx(
    () => cardShadowWidget(
      context: context,
      widgetChild: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          postRideWidget(
            title:
                "${controller.labelTextDetail['like_to_pay_label'] ?? "How would you like to pay?"}",
            screenWidth: screenWidth,
            context: context,
          ),
          Container(
            padding: EdgeInsets.fromLTRB(
              getValueForScreenType<double>(
                context: context,
                mobile: 10.0,
                tablet: 10.0,
              ),
              getValueForScreenType<double>(
                context: context,
                mobile: 10.0,
                tablet: 10.0,
              ),
              getValueForScreenType<double>(
                context: context,
                mobile: 10.0,
                tablet: 10.0,
              ),
              getValueForScreenType<double>(
                context: context,
                mobile: 10.0,
                tablet: 10.0,
              ),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                paymentOptionCard(
                  context: context,
                  img: paypal,
                  iconHeight: 28.0,
                  iconWidth: 84.0,
                  onTap: () async {
                    await controller.bookingRidePaymentType(
                        paymentType: "paypal");
                  },
                ),
                10.heightBox,
                paymentOptionCard(
                  context: context,
                  img: visa,
                  title:
                      "${controller.labelTextDetail['credit_card_label'] ?? 'Pay with credit card'}",
                  iconHeight: 18.0,
                  iconWidth: 86.0,
                  onTap: () async {
                    await controller.getCardsList();
                  },
                ),
                10.heightBox,
                if (controller.agreeTerms.value == true &&
                    controller.firmAgreeTerms.value == true &&
                    controller.firmCancellationUnderstandChecked.value ==
                        true &&
                    controller.pinkAgreeTerms.value == true &&
                    controller.extraCareAgreeTerms.value == true &&
                    controller.messageDriverTextEditingController.text != "" &&
                    controller.showGPayBtn.value == true &&
                    controller.nativePayAvailable.value == true) ...[
                  Platform.isIOS
                      ? SizedBox(
                          width: screenWidth,
                          child: ApplePayButton(
                            paymentConfiguration:
                                PaymentConfiguration.fromJsonString(
                                    defaultApplePay),
                            paymentItems: [
                              PaymentItem(
                                label: 'Total',
                                amount: controller.gPayAmount.value
                                    .toStringAsFixed(2),
                                status: PaymentItemStatus.final_price,
                              )
                            ],
                            style: ApplePayButtonStyle.black,
                            width: double.infinity,
                            height: 50,
                            type: ApplePayButtonType.buy,
                            margin: const EdgeInsets.only(top: 15.0),
                            onPaymentResult: (result) async {
                              final rawTokenJson = result['paymentMethodData']
                                  ['tokenizationData']['token'];
                              final decodedToken = jsonDecode(rawTokenJson);
                              final token = decodedToken['id'];
                              await controller.bookingRidePaymentType(
                                  paymentType: "paypal",
                                  gPay: true,
                                  token: token);
                            },
                            loadingIndicator: const Center(
                              child: CircularProgressIndicator(),
                            ),
                          ),
                        )
                      : SizedBox(
                          width: screenWidth,
                          child: GooglePayButton(
                            paymentConfiguration:
                                PaymentConfiguration.fromJsonString(
                                    defaultGooglePay),
                            paymentItems: [
                              PaymentItem(
                                label: 'Total',
                                amount: controller.gPayAmount.value
                                    .toStringAsFixed(2),
                                status: PaymentItemStatus.final_price,
                              )
                            ],
                            type: GooglePayButtonType.pay,
                            onPaymentResult: (result) async {
                              final rawTokenJson = result['paymentMethodData']
                                  ['tokenizationData']['token'];
                              final decodedToken = jsonDecode(rawTokenJson);
                              final token = decodedToken['id'];
                              await controller.bookingRidePaymentType(
                                  paymentType: "paypal",
                                  gPay: true,
                                  token: token);
                            },
                            loadingIndicator: const Center(
                              child: CircularProgressIndicator(),
                            ),
                          ),
                        ),
                ],
              ],
            ),
          ),
          10.heightBox,
        ],
      ),
    ),
  );
}
