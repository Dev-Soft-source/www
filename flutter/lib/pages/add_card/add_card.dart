import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import '../widgets/button_Widget.dart';
import '../widgets/check_box_widget.dart';
import '../widgets/drop_down_date_widget.dart';
import '../widgets/fields_widget.dart';
import '../widgets/tool_tip.dart';
import 'AddCardController.dart';

class AddCard extends GetView<AddCardController> {
  const AddCard({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(AddCardController());
    controller.getType();
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: Obx(() => secondAppBarWidget(title: "${controller.labelTextDetail['main_heading'] ?? "Billing address"}", context: context)),
        leading: const BackButton(color: Colors.white),
      ),
      body: Obx(() {
        if (controller.isLoading.value == true) {
          return Center(child: progressCircularWidget(context));
        } else {
          return Stack(
            children: [
              Container(
                padding: EdgeInsets.all(getValueForScreenType<double>(
                  context: context,
                  mobile: 15.0,
                  tablet: 15.0,
                )),
                child: Obx(() =>
                    SingleChildScrollView(
                        controller: controller.scrollController,
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            5.heightBox,
                            txt20Size(context: context,textColor: Colors.red,title: "${controller.labelTextDetail['mobile_indicate_required_field_label'] ?? '* Indicates required field'}"),
                            5.heightBox,

                            // if(controller.errorList.isNotEmpty)...[
                            //   ListView.builder(
                            //     shrinkWrap: true,
                            //     physics: const NeverScrollableScrollPhysics(),
                            //     itemCount: controller.errorList.length,
                            //     itemBuilder: (context, index){
                            //       return Column(
                            //         crossAxisAlignment: CrossAxisAlignment.start,
                            //         children: [
                            //           Row(
                            //             crossAxisAlignment: CrossAxisAlignment.center,
                            //             mainAxisAlignment: MainAxisAlignment.center,
                            //             children: [
                            //               const Icon(Icons.circle, size: 10, color: Colors.red),
                            //               10.widthBox,
                            //               Expanded(child: txt14Size(title: "${controller.errorList[index]}", fontFamily: regular, textColor: Colors.red, context: context))
                            //             ],
                            //           ),
                            //           5.heightBox,
                            //         ],
                            //       );
                            //     },
                            //   ),
                            //   10.heightBox,
                            // ],
                            Row(
                              children: [
                                txt20Size(
                                  title: "${controller.labelTextDetail['name_on_card_label'] ?? "Name on card"}",
                                  fontFamily: regular,
                                  context: context,
                                ),
                                txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                              ],
                            ),
                            5.heightBox,
                            fieldsWidget(
                              textController: controller.cardNameController,
                              fieldType: "text",
                              readonly: false,
                              fontFamily: regular,
                              fontSize: 18.0,
                              onChanged: (value) {
                                if(controller.errors.firstWhereOrNull((element) => element['title'] == "name_on_card") != null)
                                {
                                  controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "name_on_card"));
                                }
                              },
                              focusNode: controller.focusNodes[1.toString()],
                            ),
                            if (controller.errors.firstWhereOrNull((element) =>
                            element['title'] == "name_on_card") !=
                                null) ...[
                              toolTip(
                                  tip: controller.errors.firstWhereOrNull(
                                          (element) =>
                                      element['title'] == "name_on_card"))
                            ],
                            10.heightBox,
                            Row(
                              children: [
                                txt20Size(
                                    title: "${controller.labelTextDetail['card_number_label'] ?? "Card number"}",
                                    fontFamily: regular,
                                    context: context),
                                txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                              ],
                            ),
                            5.heightBox,
                            fieldsWidget(
                              textController: controller.cardNumberController,
                              fieldType: "number",
                              readonly: false,
                              fontFamily: regular,
                              fontSize: 18.0,
                              onChanged: (value) {
                                if(controller.errors.firstWhereOrNull((element) => element['title'] == "card_number") != null)
                                {
                                  controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "card_number"));
                                }
                              },
                              focusNode: controller.focusNodes[2.toString()],
                            ),
                            if (controller.errors.firstWhereOrNull((element) =>
                            element['title'] == "card_number") !=
                                null) ...[
                              toolTip(
                                  tip: controller.errors.firstWhereOrNull(
                                          (element) =>
                                      element['title'] == "card_number"))
                            ],
                            10.heightBox,
                            Row(
                              children: [
                                txt20Size(
                                    title: "${controller.labelTextDetail['mobile_card_type_label'] ?? "Card type"}",
                                    fontFamily: regular,
                                    context: context),
                                txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                              ],
                            ),
                            5.heightBox,
                            Row(
                              children: [
                                Expanded(
                                  child: Container(
                                      child: dropdownCardTypeWidget(
                                          context: context,
                                          controller: controller,
                                          screenWidth: context.screenWidth,
                                          screenHeight: context.screenHeight
                                      )),
                                ),
                              ],
                            ),
                            if (controller.errors.firstWhereOrNull(
                                    (element) => element['title'] == "card_type") !=
                                null) ...[
                              toolTip(
                                  tip: controller.errors.firstWhereOrNull(
                                          (element) => element['title'] == "card_type"))
                            ],

                            10.heightBox,
                            Row(
                              children: [
                                txt20Size(
                                    title: "${controller.labelTextDetail['mobile_expiry_date_label'] ?? "Expiry date"}",
                                    fontFamily: regular,
                                    context: context),
                                txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                              ],
                            ),
                            5.heightBox,
                            Row(
                              children: [
                                Expanded(
                                    flex: 10,
                                    child: Container(
                                        child: dropdownMonthWidget(
                                          controller: controller,
                                          context: context,
                                          screenHeight: context.screenHeight,
                                          screenWidth: context.screenWidth,
                                          monthPlaceholder: "${controller.labelTextDetail['mobile_month_placeholder'] ?? "Month"}"
                                        ))),
                                const Spacer(flex: 1),
                                Expanded(
                                    flex: 10,
                                    child: Container(
                                        child: dropdownYearWidget(
                                            controller: controller,
                                            context: context,
                                            screenHeight: context.screenHeight,
                                            screenWidth: context.screenWidth,
                                          yearPlaceholder: "${controller.labelTextDetail['mobile_year_placeholder'] ?? "Year"}"
                                        ))),
                              ],
                            ),
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceAround,
                              children: [
                                if (controller.errors.firstWhereOrNull(
                                        (element) => element['title'] == "month") !=
                                    null) ...[
                                  toolTip(
                                      tip: controller.errors.firstWhereOrNull(
                                              (element) => element['title'] == "month"))
                                ],
                                if (controller.errors.firstWhereOrNull(
                                        (element) => element['title'] == "year") !=
                                    null) ...[
                                  toolTip(
                                      tip: controller.errors.firstWhereOrNull(
                                              (element) => element['title'] == "year"))
                                ],
                              ],
                            ),

                            10.heightBox,
                            Row(
                              children: [
                                txt20Size(
                                    title: "${controller.labelTextDetail['security_code_label'] ?? "Security code (CVV)"}",
                                    fontFamily: regular,
                                    context: context),
                                txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                              ],
                            ),
                            5.heightBox,
                            fieldsWidget(
                              maxLength: 4,
                              textController: controller.cvvCodeController,
                              fieldType: "number",
                              readonly: false,
                              fontFamily: regular,
                              fontSize: 18.0,
                              onChanged: (value) {
                                if(controller.errors.firstWhereOrNull((element) => element['title'] == "cvv") != null)
                                {
                                  controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "cvv"));
                                }
                              },
                              focusNode: controller.focusNodes[3.toString()],
                            ),
                            if (controller.errors.firstWhereOrNull(
                                    (element) => element['title'] == "cvv") !=
                                null) ...[
                              toolTip(
                                  tip: controller.errors.firstWhereOrNull(
                                          (element) => element['title'] == "cvv"))
                            ],
                            25.heightBox,
                            txt20Size(
                                title: "${controller.labelTextDetail['mobile_billing_address_label'] ?? "Billing Address"}",
                                fontFamily: regular,
                                context: context),
                            10.heightBox,
                            Row(
                              children: [
                                txt20Size(
                                    title: "${controller.labelTextDetail['mobile_street_name_label'] ?? "Street number/name"}",
                                    fontFamily: regular,
                                    context: context),
                                txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                              ],
                            ),
                            5.heightBox,
                            fieldsWidget(
                              textController: controller.streetController,
                              fieldType: "text",
                              readonly: false,
                              fontFamily: regular,
                              fontSize: 18.0,
                              onChanged: (value) {
                                if(controller.errors.firstWhereOrNull((element) => element['title'] == "street") != null)
                                {
                                  controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "street"));
                                }
                              },
                              focusNode: controller.focusNodes[4.toString()],
                            ),
                            if (controller.errors.firstWhereOrNull(
                                    (element) => element['title'] == "street") !=
                                null) ...[
                              toolTip(
                                  tip: controller.errors.firstWhereOrNull(
                                          (element) => element['title'] == "street"))
                            ],

                            10.heightBox,
                            txt20Size(
                                title: "${controller.labelTextDetail['mobile_house_number_label'] ?? "House/apartment number (optional)"}",
                                fontFamily: regular,
                                context: context),
                            5.heightBox,
                            fieldsWidget(
                                textController: controller.houseApartmentController,
                                fieldType: "text",
                                readonly: false,
                                fontFamily: regular,
                                fontSize: 18.0),

                            10.heightBox,
                            Row(
                              children: [
                                txt20Size(
                                    title: "${controller.labelTextDetail['mobile_city_label'] ?? "City"}",
                                    fontFamily: regular,
                                    context: context),
                                txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                              ],
                            ),
                            5.heightBox,
                            fieldsWidget(
                              textController: controller.cityController,
                              fieldType: "text",
                              readonly: false,
                              fontFamily: regular,
                              fontSize: 18.0,
                              onChanged: (value) {
                                if(controller.errors.firstWhereOrNull((element) => element['title'] == "city") != null)
                                {
                                  controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "city"));
                                }
                              },
                              focusNode: controller.focusNodes[5.toString()],
                            ),
                            if (controller.errors.firstWhereOrNull(
                                    (element) => element['title'] == "city") !=
                                null) ...[
                              toolTip(
                                  tip: controller.errors.firstWhereOrNull(
                                          (element) => element['title'] == "city"))
                            ],

                            10.heightBox,
                            Row(
                              children: [
                                txt20Size(
                                    title: "${controller.labelTextDetail['mobile_province_label'] ?? "Province"}",
                                    fontFamily: regular,
                                    context: context),
                                txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                              ],
                            ),
                            5.heightBox,
                            fieldsWidget(
                              textController: controller.provinceController,
                              fieldType: "text",
                              readonly: false,
                              fontFamily: regular,
                              fontSize: 18.0,
                              onChanged: (value) {
                                if(controller.errors.firstWhereOrNull((element) => element['title'] == "province") != null)
                                {
                                  controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "province"));
                                }
                              },
                              focusNode: controller.focusNodes[6.toString()],
                            ),
                            if (controller.errors.firstWhereOrNull(
                                    (element) => element['title'] == "province") !=
                                null) ...[
                              toolTip(
                                  tip: controller.errors.firstWhereOrNull(
                                          (element) => element['title'] == "province"))
                            ],

                            10.heightBox,
                            Row(
                              children: [
                                txt20Size(
                                    title: "${controller.labelTextDetail['mobile_country_label'] ?? "Country"}",
                                    fontFamily: regular,
                                    context: context),
                                txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                              ],
                            ),
                            5.heightBox,
                            fieldsWidget(
                              textController: controller.countryController,
                              fieldType: "text",
                              readonly: false,
                              fontFamily: regular,
                              fontSize: 18.0,
                              onChanged: (value) {
                                if(controller.errors.firstWhereOrNull((element) => element['title'] == "country") != null)
                                {
                                  controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "country"));
                                }
                              },
                              focusNode: controller.focusNodes[7.toString()],
                            ),
                            if (controller.errors.firstWhereOrNull(
                                    (element) => element['title'] == "country") !=
                                null) ...[
                              toolTip(
                                  tip: controller.errors.firstWhereOrNull(
                                          (element) => element['title'] == "country"))
                            ],

                            10.heightBox,
                            Row(
                              children: [
                                txt20Size(
                                    title: "${controller.labelTextDetail['mobile_postal_code_label'] ?? "Postal code"}",
                                    fontFamily: regular,
                                    context: context),
                                txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                              ],
                            ),
                            5.heightBox,
                            fieldsWidget(
                              textController: controller.postalCodeController,
                              fieldType: "text",
                              maxLength: 7,
                              readonly: false,
                              fontFamily: regular,
                              fontSize: 18.0,
                              onChanged: (value) {
                                if(controller.errors.firstWhereOrNull((element) => element['title'] == "postal_code") != null)
                                {
                                  controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "postal_code"));
                                }
                              },
                              focusNode: controller.focusNodes[8.toString()],
                            ),
                            if (controller.errors.firstWhereOrNull(
                                    (element) => element['title'] == "postal_code") !=
                                null) ...[
                              toolTip(
                                  tip: controller.errors.firstWhereOrNull(
                                          (element) => element['title'] == "postal_code"))
                            ],


                            20.heightBox,
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
                                        controller.makePrimaryCard.value = value!;
                                      }),
                                ),
                                5.widthBox,
                                InkWell(
                                  onTap: (){
                                    controller.makePrimaryCard.value = controller.makePrimaryCard.value == true ? false : true;
                                  },
                                  child: txt16Size(
                                      title: "${controller.labelTextDetail['mobile_primary_card_placeholder'] ?? "Primary card"}",
                                      fontFamily: bold,
                                      context: context),
                                ),
                              ],
                            ),
                            100.heightBox
                          ],
                        )
                    )
                ),
              ),
              Align(
                alignment: Alignment.bottomCenter,
                child: Container(
                  padding: const EdgeInsets.all(15.0),
                  width: context.screenWidth,
                  height:
                  80, //added this height after the persisting argument from the QA that the height of this button is not similar to the rest of the buttons in the app
                  color: Colors.grey.shade100,
                  child: elevatedButtonWidget(
                    textWidget: txt28Size(
                        title: controller.addEditType == 'add'
                            ? "${controller.labelTextDetail['save_button_text'] ?? "Save"}"
                            : "${controller.labelTextDetail['edit_card_button_text'] ?? "Edit card"}",
                        textColor: Colors.white,
                        context: context,
                        fontFamily: regular),
                    onPressed: () async {
                      controller.addCard(context, context.screenHeight);
                    },
                  ),
                ),
              ),
              if (controller.isOverlayLoading.value == true) ...[
                overlayWidget(context)
              ]
            ],
          );
        }
      }),
    );
  }
}
