import 'dart:convert';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:pay/pay.dart';
import 'package:proximaride_app/consts/payment_config.dart';
import 'package:proximaride_app/pages/widgets/payment_option_card.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/widgets/tool_tip.dart';
import '../../consts/constFileLink.dart';
import '../widgets/fields_widget.dart';
import '../widgets/second_appbar_widget.dart';
import 'MyWalletController.dart';

import 'package:get/get.dart';
import 'package:pay/pay.dart';

import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:pay/pay.dart';

class TopUpMyBalance extends StatelessWidget {
  const TopUpMyBalance({super.key});

  @override
  Widget build(BuildContext context) {
    var controller =  Get.find<MyWalletController>();
    return Scaffold(
        appBar: AppBar(
          leading: const BackButton(color: Colors.white),
          title: Obx(() => secondAppBarWidget(context: context, title: "${controller.labelTextDetail['top_up_main_heading'] ?? "Top up my balance"}")),
          backgroundColor: primaryColor,
        ),
        body: Obx(() =>
        controller.isLoading.value == true ?
        Center(child: progressCircularWidget(context)) :
        SingleChildScrollView(
          child: Container(
            padding: const EdgeInsets.only(top: 20, left: 20, right: 20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    txt20Size(
                        title: "${controller.labelTextDetail['purchase_top_up_label'] ?? "Purchase top up balance"}",
                        fontFamily: regular,
                        context: context),
                    txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                  ],
                ),
                5.heightBox,
                fieldsWidget(
                  maxLength: 30,
                  textController:
                  controller.drAmountController,
                  onChanged: (value) async{
                    controller.gPayAmount.value = double.parse(value.toString());
                    controller.gPayAmount.refresh();
                    controller.showPayment.value = false;
                    await controller.showPaymentButton(value);
                    if(controller.errors.firstWhereOrNull((element) => element['title'] == "amount") != null)
                    {
                      controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "amount"));
                    }
                  },
                  fieldType: "number",
                  readonly: false,
                  fontFamily: regular,
                  fontSize: 18.0,
                  placeHolder: "${controller.labelTextDetail['purchase_top_up_placeholder'] ?? "Enter the amount you want to add"}",
                  isError: controller.errors.firstWhereOrNull((element) => element['title'] == "amount") != null,
                ),
                if(controller.errors.firstWhereOrNull((element) => element['title'] == "amount") != null) ...[
                  toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "amount"))
                ],

                30.heightBox,
                txt18Size(
                    title: "${controller.labelTextDetail['pay_with_label'] ?? 'Pay with'}",
                    fontFamily: regular,
                    context: context,
                    textColor: Colors.black),
                5.heightBox,
                paymentOptionCard(context: context, img: paypal, iconWidth: 84.0, iconHeight: 28.0,onTap: () async{
                  await controller.paypalMethod();
                }),
                10.heightBox,
                paymentOptionCard(
                    context: context,
                    img: visa,
                    title: "${controller.labelTextDetail['credit_card_label'] ?? 'Credit Card'}",
                    iconHeight: 18.0,
                    iconWidth: 86.0,
                    onTap: () async{
                      await controller.getCardsList();
                    }
                ),
                10.heightBox,
                if(controller.showPayment.value  == true)...[
                  Platform.isIOS ?  SizedBox(
                    width: context.screenWidth,
                    child: ApplePayButton(
                      paymentConfiguration: PaymentConfiguration.fromJsonString(defaultApplePay),
                      paymentItems: [
                        PaymentItem(
                          label: 'Total',
                          amount: controller.gPayAmount.value.toStringAsFixed(2),
                          status: PaymentItemStatus.final_price,
                        )
                      ],
                      style: ApplePayButtonStyle.black,
                      width: double.infinity,
                      height: 50,
                      type: ApplePayButtonType.buy,
                      margin: const EdgeInsets.only(top: 15.0),
                      onPaymentResult: (result) async{
                        final rawTokenJson = result['paymentMethodData']['tokenizationData']['token'];
                        final decodedToken = jsonDecode(rawTokenJson);
                        final token = decodedToken['id'];
                        await controller.getGooglePayApplePay(token);
                      },
                      loadingIndicator: const Center(
                        child: CircularProgressIndicator(),
                      ),
                    ),
                  ) : SizedBox(
                    width: context.screenWidth,
                    child: GooglePayButton(
                      paymentConfiguration: PaymentConfiguration.fromJsonString(defaultGooglePay),
                      paymentItems: [
                        PaymentItem(
                          label: 'Total',
                          amount: controller.gPayAmount.value.toStringAsFixed(2),
                          status: PaymentItemStatus.final_price,
                        )
                      ],
                      type: GooglePayButtonType.pay,
                      onPaymentResult: (result) async{
                        final rawTokenJson = result['paymentMethodData']['tokenizationData']['token'];
                        final decodedToken = jsonDecode(rawTokenJson);
                        final token = decodedToken['id'];
                        await controller.getGooglePayApplePay(token);
                      },
                      onError: (data){
                        print("dfddfd");
                        print(data);
                      },
                      loadingIndicator: const Center(
                        child: CircularProgressIndicator(),
                      ),
                    ),
                  ),
                ]
              ],
            ),
          ),
        ),
        )
    );
  }
}
