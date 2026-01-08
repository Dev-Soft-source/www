import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/payment_options/PaymentOptionsController.dart';
import 'package:proximaride_app/pages/payment_options/widgets/my_card.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

class PaymentOptions extends GetView<PaymentOptionController> {
  const PaymentOptions({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(PaymentOptionController());
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: Obx(() => secondAppBarWidget(title: "${controller.labelTextDetail['main_heading'] ?? "Payment options"}", context: context)),
        leading: const BackButton(color: Colors.white),
      ),
      body: Obx(() {
        if (controller.isLoading.value == true) {
          return Center(child: progressCircularWidget(context));
        } else {
          return Stack(
            children: [
              Container(
                padding: const EdgeInsets.all(15.0),
                child: SingleChildScrollView(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      if (controller.cards.isNotEmpty) ...[
                        for (var i = 0; i < controller.cards.length; i++) ...[
                          myCard(
                              cardBgColor: i % 2 == 0
                                  ? Colors.white
                                  : Colors.grey.shade100,
                              context: context,
                              controller: controller,
                              cardDetail :controller.cards[i],
                              onDelete: () async {
                                await controller
                                    .deleteCard(controller.cards[i]['id']);
                              },
                              onSetPrimary: () {
                                controller.setPrimaryCard(controller.cards[i]['id'].toString(), i);
                                // controller.serviceController.showDialogue('The card has been set as your primary card');
                              }
                          ),
                          20.heightBox,
                        ],
                      ],
                      50.heightBox,
                    ],
                  ),
                ),
              ),
              if (controller.cards.isEmpty) ...[
                Center(
                    child: txt18Size(
                        context: context,
                        fontFamily: regular,
                        title: "${controller.labelTextDetail['no_payment_message'] ?? 'No payment options found yet'}")),
              ],

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
                    textWidget: txt28Size(
                        title: "${controller.labelTextDetail['add_new_card_button_text'] ?? "Add a new card"}",
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
    );
  }
}
