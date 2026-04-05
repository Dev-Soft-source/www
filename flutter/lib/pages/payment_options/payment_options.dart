import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/payment_options/PaymentOptionsController.dart';
import 'package:proximaride_app/pages/payment_options/widgets/my_card.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/error_state_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

String _stripeRegionLine(Map<String, dynamic> cfg) {
  final country = cfg['country']?.toString().toUpperCase() ?? 'CA';
  final currency = cfg['currency']?.toString().toUpperCase() ?? 'CAD';
  return 'Secured with Stripe · $country · $currency';
}

class PaymentOptions extends StatelessWidget {
  const PaymentOptions({super.key});

  @override
  Widget build(BuildContext context) {
    final PaymentOptionController controller = Get.isRegistered<PaymentOptionController>()
        ? Get.find<PaymentOptionController>()
        : Get.put(PaymentOptionController());
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: Obx(() => secondAppBarWidget(
            title:
                "${controller.labelTextDetail['main_heading'] ?? "Payment options"}",
            context: context)),
        leading: safeBackButton(context),
      ),
      body: SafeArea(
        child: Obx(() {
          if (controller.errorStateManager.isLoading.value) {
            return Center(child: progressCircularWidget(context));
          } else if (controller.errorStateManager.hasError.value) {
            return ErrorStateWidget(
              message: controller.errorStateManager.errorMessage.value,
              errorType: controller.errorStateManager.errorType.value,
              onRetry: () {
                if (controller.errorStateManager.onRetry.value != null) {
                  controller.errorStateManager.onRetry.value!();
                }
              },
            );
          } else if (controller.isLoading.value == true) {
            return Center(child: progressCircularWidget(context));
          } else {
            final minBodyHeight = MediaQuery.of(context).size.height -
                MediaQuery.of(context).padding.vertical -
                kToolbarHeight -
                80;
            return Stack(
              children: [
                Container(
                  padding: const EdgeInsets.all(15.0),
                  child: SingleChildScrollView(
                    child: ConstrainedBox(
                      constraints: BoxConstraints(minHeight: minBodyHeight),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        mainAxisAlignment: controller.cards.isEmpty
                            ? MainAxisAlignment.center
                            : MainAxisAlignment.start,
                        children: [
                          if (controller.stripeConfig.isNotEmpty) ...[
                            Center(
                              child: Text(
                                _stripeRegionLine(controller.stripeConfig),
                                textAlign: TextAlign.center,
                                style: TextStyle(
                                  color: Colors.grey.shade700,
                                  fontFamily: regular,
                                  fontSize: getValueForScreenType<double>(
                                    context: context,
                                    mobile: 13.0,
                                    tablet: 13.0,
                                  ),
                                ),
                              ),
                            ),
                            20.heightBox,
                          ],
                          if (controller.cards.isNotEmpty) ...[
                            for (var i = 0; i < controller.cards.length; i++) ...[
                              myCard(
                                  cardBgColor: i % 2 == 0
                                      ? Colors.white
                                      : Colors.grey.shade100,
                                  context: context,
                                  controller: controller,
                                  cardDetail: controller.cards[i],
                                  onDelete: () async {
                                    await controller
                                        .deleteCard(controller.cards[i]['id']);
                                  },
                                  onSetPrimary: () {
                                    controller.setPrimaryCard(
                                        controller.cards[i]['id'].toString(), i);
                                  }),
                              20.heightBox,
                            ],
                            24.heightBox,
                          ],
                          if (controller.cards.isEmpty) ...[
                            Icon(
                              Icons.credit_card_outlined,
                              size: 56,
                              color: Colors.grey.shade400,
                            ),
                            20.heightBox,
                            Padding(
                              padding:
                                  const EdgeInsets.symmetric(horizontal: 12.0),
                              child: Text(
                                "${controller.labelTextDetail['no_payment_message'] ?? 'No payment options found yet'}",
                                textAlign: TextAlign.center,
                                style: TextStyle(
                                  color: textColor,
                                  fontFamily: regular,
                                  fontSize: getValueForScreenType<double>(
                                    context: context,
                                    mobile: 18.0,
                                    tablet: 18.0,
                                  ),
                                  height: 1.35,
                                ),
                              ),
                            ),
                            12.heightBox,
                            Text(
                              'Tap the button below to add a card securely with Stripe.',
                              textAlign: TextAlign.center,
                              style: TextStyle(
                                color: Colors.grey.shade700,
                                fontFamily: regular,
                                fontSize: getValueForScreenType<double>(
                                  context: context,
                                  mobile: 14.0,
                                  tablet: 14.0,
                                ),
                                height: 1.35,
                              ),
                            ),
                          ],
                        ],
                      ),
                    ),
                  ),
                ),

                // Align(
                //   alignment: Alignment.bottomCenter,
                //   child: Container(
                //     padding:
                //         const EdgeInsets.all(15.0),
                //     width: context.screenWidth,
                //     color: Colors.grey.shade100,
                //     child: elevatedButtonWidget(
                //       textWidget: txt28Size(
                //           title: "Add a new card",
                //           textColor: Colors.white,
                //           context: context,
                //           fontFamily: regular),
                //       onPressed: () async {
                //         Get.toNamed('/add_card/add');
                //       },
                //       btnColor: primaryColor,
                //     ),
                //   ),
                // ),
                Align(
                  alignment: Alignment.bottomCenter,
                  child: Container(
                    width: context.screenWidth,
                    height:
                        80, //added this height after the persisting argument from the QA that the height of this button is not similar to the rest of the buttons in the app
                    padding: const EdgeInsets.all(15.0),
                    color: Colors.grey.shade100,
                    child: elevatedButtonWidget(
                      textWidget: txt22Size(
                          title: controller.cards.isNotEmpty
                              ? "${controller.labelTextDetail['add_new_card_button_text'] ?? "Add a New Card"}"
                              : "Add a Card",
                          textColor: Colors.white,
                          context: context,
                          fontFamily: regular),
                      onPressed: () async {
                        Get.toNamed('/add_card/add');
                      },
                    ),
                  ),
                ),

                if (controller.isOverlayLoading.value == true) ...[
                  overlayWidget(context),
                ]
              ],
            );
          }
        }),
      ),
    );
  }
}



