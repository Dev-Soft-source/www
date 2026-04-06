import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_stripe/flutter_stripe.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import '../widgets/button_Widget.dart';
import '../widgets/check_box_widget.dart';
import '../widgets/fields_widget.dart';
import '../widgets/tool_tip.dart';
import 'AddCardController.dart';

class AddCard extends StatelessWidget {
  const AddCard({super.key});

  @override
  Widget build(BuildContext context) {
    final AddCardController controller = Get.isRegistered<AddCardController>()
        ? Get.find<AddCardController>()
        : Get.put(AddCardController());
    return Scaffold(
      resizeToAvoidBottomInset: true,
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: Obx(() => secondAppBarWidget(
            title:
                "${controller.labelTextDetail['main_heading'] ?? "Billing address"}",
            context: context)),
        leading: safeBackButton(context),
      ),
      body: Obx(() {
        if (controller.isLoading.value == true) {
          return Center(child: progressCircularWidget(context));
        }
        return Stack(
          children: [
            Container(
              padding: EdgeInsets.all(getValueForScreenType<double>(
                context: context,
                mobile: 15.0,
                tablet: 15.0,
              )),
              child: Obx(() => SingleChildScrollView(
                    controller: controller.scrollController,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        5.heightBox,
                        txt18Size(
                            context: context,
                            textColor: Colors.red,
                            fontFamily: carlito,
                            title:
                                "${controller.labelTextDetail['mobile_indicate_required_field_label'] ?? '* Indicates required fields'}"),
                        5.heightBox,
                        Text(
                          'Enter your card details below. Card number, expiry, and CVC are handled securely by Stripe; '
                          'add the cardholder name on this page.',
                          style: TextStyle(
                            color: Colors.grey.shade700,
                            fontFamily: regular,
                            fontSize: getValueForScreenType<double>(
                              context: context,
                              mobile: 15.0,
                              tablet: 15.0,
                            ),
                            height: 1.35,
                          ),
                        ),
                        16.heightBox,
                        CardField(
                          controller: controller.cardFieldController,
                          enablePostalCode: false,
                          countryCode: controller.defaultCardCountryCode,
                          onCardChanged: controller.onCardFieldChanged,
                          decoration: InputDecoration(
                            filled: true,
                            fillColor: inputColor,
                            contentPadding: const EdgeInsets.symmetric(
                              vertical: 12,
                              horizontal: 8,
                            ),
                            enabledBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(5),
                              borderSide: BorderSide(
                                color: Colors.grey.shade400,
                              ),
                            ),
                            focusedBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(5),
                              borderSide: const BorderSide(
                                color: primaryColor,
                              ),
                            ),
                          ),
                        ),
                        20.heightBox,
                        Row(
                          children: [
                            txt20Size(
                              title:
                                  "${controller.labelTextDetail['name_on_card_label'] ?? "Name on card"}",
                              fontFamily: regular,
                              context: context,
                            ),
                            txt18Size(
                                title: "*",
                                fontFamily: regular,
                                context: context,
                                textColor: Colors.red),
                          ],
                        ),
                        5.heightBox,
                        fieldsWidget(
                          textController: controller.cardNameController,
                          fieldType: "text",
                          readonly: false,
                          fontFamily: regular,
                          fontSize: 18.0,
                          inputFormatters: [
                            FilteringTextInputFormatter.allow(
                                RegExp(r'[a-zA-Z\s\-]')),
                          ],
                          onChanged: (value) {
                            if (controller.errors.firstWhereOrNull((element) =>
                                    element['title'] == "name_on_card") !=
                                null) {
                              controller.errors.remove(controller.errors
                                  .firstWhereOrNull((element) =>
                                      element['title'] == "name_on_card"));
                            }
                          },
                          focusNode: controller.focusNodes['1'],
                        ),
                        if (controller.errors.firstWhereOrNull((element) =>
                                element['title'] == "name_on_card") !=
                            null) ...[
                          toolTip(
                              tip: controller.errors.firstWhereOrNull(
                                  (element) =>
                                      element['title'] == "name_on_card"))
                        ],
                        if (controller
                            .paymentOptionController.cards.isNotEmpty) ...[
                          24.heightBox,
                          Row(
                            mainAxisAlignment: MainAxisAlignment.start,
                            children: [
                              SizedBox(
                                width: getValueForScreenType<double>(
                                  context: context,
                                  mobile: 25.0,
                                  tablet: 25.0,
                                ),
                                height: getValueForScreenType<double>(
                                  context: context,
                                  mobile: 25.0,
                                  tablet: 25.0,
                                ),
                                child: checkBoxWidget(
                                    value: controller.makePrimaryCard.value,
                                    onChanged: (value) async {
                                      controller.makePrimaryCard.value =
                                          value!;
                                    }),
                              ),
                              5.widthBox,
                              InkWell(
                                onTap: () {
                                  controller.makePrimaryCard.value =
                                      controller.makePrimaryCard.value == true
                                          ? false
                                          : true;
                                },
                                child: txt20Size(
                                    title:
                                        "${controller.labelTextDetail['mobile_primary_card_placeholder'] ?? "Primary card"}",
                                    fontFamily: bold,
                                    context: context),
                              ),
                            ],
                          ),
                        ],
                        100.heightBox
                      ],
                    ),
                  )),
            ),
            if (controller.isOverlayLoading.value == true) ...[
              overlayWidget(context)
            ]
          ],
        );
      }),
      bottomNavigationBar: Container(
        padding: const EdgeInsets.all(15.0),
        width: context.screenWidth,
        height: 80,
        color: Colors.grey.shade100,
        child: elevatedButtonWidget(
          enabled: true,
          textWidget: txt22Size(
              title: "${controller.labelTextDetail['save_button_text'] ?? "Save"}",
                  
              textColor: Colors.white,
              context: context,
              fontFamily: regular),
          onPressed: () async {
            await controller.addCard(context, context.screenHeight);
          },
        ),
      ),
    );
  }
}
