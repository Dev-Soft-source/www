import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/my_phone_number/MyPhoneNumberController.dart';
import 'package:proximaride_app/pages/my_phone_number/widgets/phone_number_card_widget.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../widgets/tool_tip.dart';

class MyPhoneNumberPage extends GetView<MyPhoneNumberController> {
  const MyPhoneNumberPage({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(MyPhoneNumberController());
    return  Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => secondAppBarWidget(title: "${controller.labelTextDetail['main_heading'] ?? "My phone number"}", context: context)),
          leading: const BackButton(color: Colors.white),
        ),
        body:  Obx(() {
          if(controller.isLoading.value == true){
            return Center(child: progressCircularWidget(context));
          }else{
            return Stack(
              children: [
                Container(
                  padding: EdgeInsets.all(getValueForScreenType<double>(
                    context: context,
                    mobile: 20.0,
                    tablet: 20.0,
                  )),
                  child: SingleChildScrollView(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisAlignment: MainAxisAlignment.start,
                      children: [
                        txt22Size(
                            title:
                            "${controller.labelTextDetail['mobile_phone_no_description_text'] ?? 'To be eligible to post "Pink rides" and "Extra-care rides", you must verify your phone number'}",
                            fontFamily: regular,
                            textColor: textColor,
                            context: context),
                        5.heightBox,
                        txt22Size(
                            title:
                            "${controller.labelTextDetail['mobile_phone_no_description_text1'] ?? 'You can edit your phone number from here as well'}",
                            fontFamily: regular,
                            textColor: textColor,
                            context: context),
                        10.heightBox,
                        ListView.separated(
                            itemCount: controller.numbersList.length,
                            shrinkWrap: true,
                            physics: const NeverScrollableScrollPhysics(),
                            itemBuilder: (context,index){
                              return phoneNumberCardWidget(
                                  cardBgColor: index%2 == 0 ? Colors.white : Colors.grey.shade100,
                                  context: context,
                                  def: controller.numbersList[index]['default'],
                                  number: controller.numbersList[index]['phone'],
                                  verification: controller.numbersList[index]['verified'],
                                  controller: controller,
                                  onVerify: (){
                                    controller.sendVerificationCode(phoneId: controller.numbersList[index]['id']);
                                  },
                                  onDelete: (){
                                    controller.deletePhoneNumber(controller.numbersList[index]['id'], index);
                                  },
                                  onSetDefault: (){
                                    controller.setAsDefaultNumber(controller.numbersList[index]['id'], index);
                                  }
                              );
                            }, separatorBuilder: (context, index){
                          return 20.heightBox;
                        }
                        ),
                        30.heightBox,
                        // if (controller.errorList.isNotEmpty) ...[
                        //   ListView.builder(
                        //     shrinkWrap: true,
                        //     physics: const NeverScrollableScrollPhysics(),
                        //     itemCount: controller.errorList.length,
                        //     itemBuilder: (context, index) {
                        //       return Column(
                        //         crossAxisAlignment: CrossAxisAlignment.start,
                        //         children: [
                        //           Row(
                        //             crossAxisAlignment: CrossAxisAlignment.center,
                        //             mainAxisAlignment: MainAxisAlignment.center,
                        //             children: [
                        //               const Icon(Icons.circle,
                        //                   size: 10, color: Colors.red),
                        //               10.widthBox,
                        //               Expanded(
                        //                   child: txt14Size(
                        //                       title:
                        //                       "${controller.errorList[index]}",
                        //                       fontFamily: regular,
                        //                       textColor: Colors.red,
                        //                       context: context))
                        //             ],
                        //           ),
                        //           5.heightBox,
                        //         ],
                        //       );
                        //     },
                        //   ),
                        // ],
                      txt20Size(fontFamily: regular,context: context,title: controller.labelTextDetail['add_another_phone_number_title'] ??  "Add another phone number"),
                        10.heightBox,
                        Row(
                          children: [
                            Expanded(
                              child: Row(
                                children: [
                                  Expanded(
                                    child: RichText(
                                        text: TextSpan(children: [
                                          textSpan(
                                              context: context,
                                              textColor: textColor,
                                              fontFamily: regular,
                                              title: "${controller.labelTextDetail['mobile_country_code_label'] ?? "Country code"}",
                                              textSize: 20.0),
                                          textSpan(
                                              context: context,
                                              textColor: Colors.red,
                                              fontFamily: regular,
                                              title: "*",
                                              textSize: 20.0),
                                        ])
                                    ),
                                  ),
                                ],
                              ),
                            ),
                            Expanded(
                              child: Row(
                                children: [
                                  Expanded(child: RichText(
                                      text: TextSpan(children: [
                                        textSpan(
                                            context: context,
                                            textColor: textColor,
                                            fontFamily: regular,
                                            title: "${controller.labelTextDetail['mobile_phone_number_label'] ?? "Phone number"}",
                                            textSize: 20.0),
                                        textSpan(
                                            context: context,
                                            textColor: Colors.red,
                                            fontFamily: regular,
                                            title: "*",
                                            textSize: 20.0),
                                      ])
                                  ),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                        Row(
                          children: [
                            Expanded(
                              flex: 5,
                              child: TextFormField(
                                maxLength: 7,
                                decoration: InputDecoration(
                                  counterText: "",
                                  enabledBorder: OutlineInputBorder(
                                      borderRadius: BorderRadius.circular(5.0),
                                      borderSide: BorderSide(
                                          color: Colors.grey.shade400,
                                          style: BorderStyle.solid,
                                          width: 1)),
                                  focusedBorder: OutlineInputBorder(
                                      borderRadius: BorderRadius.circular(5.0),
                                      borderSide:
                                      const BorderSide(color: primaryColor)),
                                  filled: true,
                                  fillColor: inputColor,
                                  contentPadding: const EdgeInsets.symmetric(
                                      vertical: 0.0, horizontal: 8.0),
                                ),
                                controller: controller.countryCodeTextEditingController,
                                style: const TextStyle(
                                    fontSize: 18, fontFamily: regular),
                                keyboardType: TextInputType.visiblePassword,
                                textInputAction: TextInputAction.done,
                                onChanged: (value){
                                  // if (value.isNotEmpty && !RegExp(r'^[0-9]*$').hasMatch(value)) {
                                  //   controller.serviceController.showToast('Please enter a number', ToastGravity.BOTTOM);
                                  //   controller.countryCodeTextEditingController.text = value.substring(0, value.length - 1);
                                  // }
                                  if (value.isNotEmpty &&
                                      (!RegExp(r'^[0-9+]*$').hasMatch(value) ||
                                          (value.indexOf('+') > 0) ||
                                          (value.indexOf('+') != value.lastIndexOf('+')))) {
                                    // controller.serviceController.showToast('Please enter a valid number', ToastGravity.BOTTOM);
                                    controller.countryCodeTextEditingController.text = value.substring(0, value.length - 1);
                                  }
                                },
                              ),
                            ),
                            const Expanded(child: Center()),
                            Expanded(
                              flex: 9,
                              child: TextFormField(
                                maxLength: 14,
                                decoration: InputDecoration(
                                  counterText: "",
                                  enabledBorder: OutlineInputBorder(
                                      borderRadius: BorderRadius.circular(5.0),
                                      borderSide: BorderSide(
                                          color: Colors.grey.shade400,
                                          style: BorderStyle.solid,
                                          width: 1)),
                                  focusedBorder: OutlineInputBorder(
                                      borderRadius: BorderRadius.circular(5.0),
                                      borderSide:
                                      const BorderSide(color: primaryColor)),
                                  filled: true,
                                  fillColor: inputColor,
                                  contentPadding: const EdgeInsets.symmetric(
                                      vertical: 0.0, horizontal: 8.0),
                                ),
                                controller: controller.phoneNumberTextEditingController,
                                style: const TextStyle(
                                    fontSize: 18, fontFamily: regular),
                                keyboardType: TextInputType.visiblePassword,
                                textInputAction: TextInputAction.done,
                                onChanged: (value){
                                  if (value.isNotEmpty && !RegExp(r'^[0-9]*$').hasMatch(value)) {
                                    // controller.serviceController.showToast('Please enter a number', ToastGravity.BOTTOM);
                                    controller.phoneNumberTextEditingController.text = value.substring(0, value.length - 1);
                                  }
                                },
                              ),
                            ),
                            // const Spacer(flex: 2,),
                          ],
                        ),
                        if(controller.errors.firstWhereOrNull((element) => element['title'] == "phoneNumber") != null) ...[
                          toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "phoneNumber"))
                        ],
                        10.heightBox,
                        Row(
                          children: [
                            Expanded(
                              child: elevatedButtonWidget(
                                  textWidget: txt20Size(
                                      title: "${controller.labelTextDetail['save_phoneno_button_text'] ?? "Save phone number"}",
                                      fontFamily: regular,
                                      textColor: Colors.white,
                                      context: context),
                                  onPressed: () {
                                    controller.addNewPhoneNumber();
                                  },
                                  btnColor: primaryColor,
                                  context: context,
                                  btnRadius: 5.0),
                            ),
                          ],
                        ),
                        
                        Row(
                          children: [
                            Expanded(
                              child: elevatedButtonWidget(
                                  textWidget: txt20Size(
                                      title: "${controller.labelTextDetail['send_verification_code_button_text'] ?? "Send verification code"}",
                                      fontFamily: regular,
                                      textColor: Colors.white,
                                      context: context),
                                  onPressed: () {
                                    controller.sendVerificationCode();
                                  },
                                  context: context,
                                  btnRadius: 5.0),
                            ),
                          ],
                        ),
                        50.heightBox,
                      ],
                    ),
                  ),
                ),

                if (controller.isOverlayLoading.value == true) ...[overlayWidget(context)]
              ],
            );
          }
        })
    );
  }
}
