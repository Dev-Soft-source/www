import 'package:dropdown_button2/dropdown_button2.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/payout_account/PayoutAccountController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/check_box_widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../widgets/tool_tip.dart';


class PayoutAccountPage extends GetView<PayoutAccountController> {
  const PayoutAccountPage({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(PayoutAccountController());
    return Obx( () =>
        Scaffold(
            appBar: AppBar(
              backgroundColor: primaryColor,
              title: secondAppBarWidget(title: "${controller.labelTextDetail['main_heading'] ??"Payout options"}", context: context),
              leading: const BackButton(
                  color: Colors.white
              ),
            ),
            body: controller.isLoading.value == true ?
                Center(child: progressCircularWidget(context)) :
                Stack(
                children: [
                 Container(
                  padding: EdgeInsets.all(getValueForScreenType<double>(
                    context: context,
                    mobile: 15.0,
                    tablet: 15.0,
                  )),
                  child: Column(
                    children: [
                      Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Container(
                                padding: const EdgeInsets.all(10.0),
                                decoration: BoxDecoration(
                                    color: Colors.grey.shade200,
                                    borderRadius: BorderRadius.circular(5.0)
                                ),
                                child: TabBar(
                                  controller: controller.tabController,
                                  onTap: (index){
                                    controller.pageController.animateToPage(index, duration: const Duration(milliseconds: 1), curve: Curves.linear);
                                  },
                                  indicatorColor: primaryColor,
                                  indicatorSize: TabBarIndicatorSize.tab,
                                  dividerColor: Colors.transparent,
                                  labelColor: Colors.white,
                                  unselectedLabelColor: textColor,
                                  labelStyle: const TextStyle(
                                      fontFamily: regular,
                                      fontSize: 22
                                  ),
                                  unselectedLabelStyle: const TextStyle(
                                      fontFamily: regular,
                                      fontSize: 22
                                  ),
                                  indicator: BoxDecoration(
                                      borderRadius: BorderRadius.circular(5), // Creates border
                                      color: btnPrimaryColor
                                  ),
                                  labelPadding: const EdgeInsets.all(5.0),
                                  tabs:[
                                    Text("${controller.labelTextDetail['bank_detail_heading'] ?? "Bank detail"}", textAlign: TextAlign.center),
                                    Text("${controller.labelTextDetail['paypal_detail_heading'] ?? "Paypal detail"}", textAlign: TextAlign.center),
                                  ],
                                ),
                              ),
                              10.heightBox,
                              Expanded(
                                  child: PageView(
                                    controller: controller.pageController,
                                    children: [
                                      SingleChildScrollView(
                                        child: Column(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          mainAxisAlignment: MainAxisAlignment.start,
                                          children: [
                                            txt22Size(title: "${controller.labelTextDetail['bank_account_heading'] ?? "Bank account"}",
                                                fontFamily: regular, textColor: textColor, context: context),
                                            10.heightBox,
                                            txt20Size(title: "${controller.labelTextDetail['mobile_indicate_required_field_label'] ?? "* Indicates required field"}", fontFamily: regular, context: context,textColor: Colors.red),
                                            10.heightBox,
                                            Row(
                                              children: [
                                                txt22Size(
                                                    title: "${controller.labelTextDetail['bank_name_label'] ?? "Bank name"}",
                                                    fontFamily: regular,
                                                    context: context),
                                                txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                                              ],
                                            ),
                                            5.heightBox,
                                        
                                            DropdownButtonFormField2(
                                              isExpanded: true,
                                              decoration: InputDecoration(
                                                enabledBorder: OutlineInputBorder(
                                                  borderRadius: BorderRadius.circular(5.0),
                                                  borderSide: BorderSide(
                                                    color: controller.errors.firstWhereOrNull((element) => element['title'] == "type") != null ? primaryColor : Colors.grey.shade400,
                                                    style: BorderStyle.solid,
                                                    width: 1,
                                                  ),
                                                ),
                                                focusedBorder: OutlineInputBorder(
                                                  borderRadius: BorderRadius.circular(5.0),
                                                  borderSide: const BorderSide(color: primaryColor),
                                                ),
                                                contentPadding: const EdgeInsets.symmetric(vertical: 0.0, horizontal: 8.0),
                                                fillColor: inputColor,
                                              ),
                                              value: controller.bankId.value,
                                              items: [
                                                DropdownMenuItem(
                                                  value: "",
                                                  child: controller.bankId.value == "" ?
                                                  Row(
                                                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                                    children: [
                                                      txt18Size(title: "${controller.labelTextDetail['select_bank_label'] ?? "Select bank"}", context: context, fontFamily: bold),
                                                      Icon(Icons.check, color: btnPrimaryColor, size: 20)
                                                    ],
                                                  ) : txt18Size(title: "${controller.labelTextDetail['select_bank_label'] ?? "Select bank"}", context: context, fontFamily: bold),
                                                ),
                                                for(var i =0; i < controller.banks.length; i++)...[
                                                  DropdownMenuItem(
                                                    value: "${controller.banks[i]['id']}",
                                                    child: controller.bankId.value == "${controller.banks[i]['id']}" ?
                                                    Row(
                                                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                                      children: [
                                                        txt18Size(title: "${controller.banks[i]['name']}", context: context, fontFamily: bold),
                                                        Icon(Icons.check, color: btnPrimaryColor, size: 20)
                                                      ],
                                                    ) : txt18Size(title: "${controller.banks[i]['name']}", context: context, fontFamily: bold),
                                                  ),
                                                ]
                                              ],
                                              onChanged: (data) {
                                                controller.bankId.value = data!;
                                                if (controller.errors.firstWhereOrNull((element) => element['title'] == "bank_name") != null) {
                                                  controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "bank_name"));
                                                }
                                              },
                                              alignment: AlignmentDirectional.topCenter,
                                              dropdownStyleData: DropdownStyleData(
                                                maxHeight: context.screenHeight * 0.45,
                                                width: context.screenWidth - 30,
                                                // padding: EdgeInsets.only(bottom: 100),
                                                decoration: BoxDecoration(
                                                  border: Border.all(width: 2,color: primaryColor),
                                                  borderRadius: const BorderRadius.only(bottomLeft: Radius.circular(10.0), bottomRight: Radius.circular(10.0)),
                                        
                                                ),
                                              ),
                                            ),
                                            if(controller.errors.firstWhereOrNull((element) => element['title'] == "bank_name") != null) ...[
                                              toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "bank_name"))
                                            ],
                                            10.heightBox,
                                            Row(
                                              children: [
                                                txt20Size(title: "${controller.labelTextDetail['bank_branch_label'] ?? "Branch name"}", fontFamily: regular, textColor: textColor, context: context),
                                                txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                                              ],
                                            ),
                                            5.heightBox,
                                            fieldsWidget(
                                              textController:
                                              controller.branchTextEditingController,
                                              fieldType: "text",
                                              readonly: false,
                                              fontFamily: regular,
                                              fontSize: 18.0,
                                              onChanged: (value) {
                                                if(controller.errors.firstWhereOrNull((element) => element['title'] == "branch") != null)
                                                {
                                                  controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "branch"));
                                                }
                                              },
                                            ),
                                            if (controller.errors.firstWhereOrNull((element) => element['title'] == "branch") != null) ...[
                                              toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "branch"))
                                            ],
                                            10.heightBox,
                                            Row(
                                              children: [
                                                txt20Size(title: "${controller.labelTextDetail['institution_number_label'] ?? "Institution number"}", fontFamily: regular, textColor: textColor, context: context),
                                                txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                                              ],
                                            ),
                                            5.heightBox,
                                            fieldsWidget(
                                              textController:
                                              controller.institutionNumberTextEditingController,
                                              fieldType: "text",
                                              readonly: false,
                                              fontFamily: regular,
                                              fontSize: 18.0,
                                              onChanged: (value) {
                                                if(controller.errors.firstWhereOrNull((element) => element['title'] == "institution_number") != null)
                                                {
                                                  controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "institution_number"));
                                                }
                                              },
                                            ),
                                            if (controller.errors.firstWhereOrNull((element) => element['title'] == "institution_number") != null) ...[
                                              toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "institution_number"))
                                            ],
                                            10.heightBox,
                                            Row(
                                              children: [
                                                txt20Size(title: "${controller.labelTextDetail['branch_address_label'] ?? "Branch address"}", fontFamily: regular, textColor: textColor, context: context),
                                                txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                                              ],
                                            ),
                                            5.heightBox,
                                            fieldsWidget(
                                              textController:
                                              controller.branchAddressTextEditingController,
                                              placeHolder: "${controller.labelTextDetail['branch_address_placeholder'] ?? "Branch address"}",
                                              fieldType: "text",
                                              readonly: false,
                                              fontFamily: regular,
                                              fontSize: 18.0,
                                              onChanged: (value) {
                                                if(controller.errors.firstWhereOrNull((element) => element['title'] == "branch_address") != null)
                                                {
                                                  controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "branch_address"));
                                                }
                                              },
                                            ),
                                            if (controller.errors.firstWhereOrNull((element) => element['title'] == "branch_address") != null) ...[
                                              toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "branch_address"))
                                            ],
                                            10.heightBox,
                                            Row(
                                              children: [
                                                txt20Size(title: "${controller.labelTextDetail['branch_number_label'] ?? "Branch number"}", fontFamily: regular, textColor: textColor, context: context),
                                                txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                                              ],
                                            ),
                                            5.heightBox,
                                            fieldsWidget(
                                              textController:
                                              controller.branchNumberTextEditingController,
                                              fieldType: "text",
                                              readonly: false,
                                              fontFamily: regular,
                                              fontSize: 18.0,
                                              onChanged: (value) {
                                                if(controller.errors.firstWhereOrNull((element) => element['title'] == "branch_number") != null)
                                                {
                                                  controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "branch_number"));
                                                }
                                              },
                                            ),
                                            if (controller.errors.firstWhereOrNull((element) => element['title'] == "branch_number") != null) ...[
                                              toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "branch_number"))
                                            ],
                                            10.heightBox,

                                            Row(
                                              children: [
                                                txt20Size(title: "${controller.labelTextDetail['bank_title_label'] ?? "Bank title"}", fontFamily: regular, textColor: textColor, context: context),
                                                txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                                              ],
                                            ),
                                            5.heightBox,
                                            fieldsWidget(
                                              textController:
                                              controller.bankTitleTextEditingController,
                                              fieldType: "text",
                                              readonly: false,
                                              fontFamily: regular,
                                              fontSize: 18.0,
                                              onChanged: (value) {
                                                if(controller.errors.firstWhereOrNull((element) => element['title'] == "account_holder_name") != null)
                                                {
                                                  controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "account_holder_name"));
                                                }
                                              },
                                            ),
                                            if (controller.errors.firstWhereOrNull((element) => element['title'] == "account_holder_name") != null) ...[
                                              toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "account_holder_name"))
                                            ],
                                            10.heightBox,
                                            Row(
                                              children: [
                                                txt20Size(title: "${controller.labelTextDetail['account_number_label'] ?? "Account number"}", fontFamily: regular, textColor: textColor, context: context),
                                                txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                                              ],
                                            ),
                                            5.heightBox,
                                            fieldsWidget(
                                              textController:
                                              controller.accountNumberTextEditingController,
                                              fieldType: "number",
                                              readonly: false,
                                              fontFamily: regular,
                                              fontSize: 18.0,
                                              onChanged: (value) {
                                                if(controller.errors.firstWhereOrNull((element) => element['title'] == "account_holder_number") != null)
                                                {
                                                  controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "account_holder_number"));
                                                }
                                              },
                                            ),
                                            if (controller.errors.firstWhereOrNull((element) => element['title'] == "account_holder_number") != null) ...[
                                              toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "account_holder_number"))
                                            ],
                                            10.heightBox,
                                            Row(
                                              children: [
                                                txt20Size(title: "${controller.labelTextDetail['address_label'] ?? "Address"}", fontFamily: regular, textColor: textColor, context: context),
                                                txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                                              ],
                                            ),
                                            5.heightBox,
                                            fieldsWidget(
                                              textController:
                                              controller.addressTextEditingController,
                                              placeHolder: "${controller.labelTextDetail['account_address_placeholder'] ?? "Address"}",
                                              fieldType: "text",
                                              readonly: false,
                                              fontFamily: regular,
                                              fontSize: 18.0,
                                              onChanged: (value) {
                                                if(controller.errors.firstWhereOrNull((element) => element['title'] == "account_holder_address") != null)
                                                {
                                                  controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "account_holder_address"));
                                                }
                                              },
                                            ),
                                            if (controller.errors.firstWhereOrNull((element) => element['title'] == "account_holder_address") != null) ...[
                                              toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "account_holder_address"))
                                            ],
                                            10.heightBox,
                                            if(controller.bankStatus.value == "admin_verify")...[
                                              Row(
                                                children: [
                                                  txt20Size(title: "${controller.labelTextDetail['admin_sent_amount_placeholder'] ?? "Admin sent amount"}", fontFamily: regular, textColor: textColor, context: context),
                                                  txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                                                ],
                                              ),
                                              5.heightBox,
                                              fieldsWidget(
                                                textController:
                                                controller.userVerifyAmountTextEditingController,
                                                fieldType: "number",
                                                readonly: false,
                                                fontFamily: regular,
                                                fontSize: 18.0,
                                                onChanged: (value) {
                                                  if(controller.errors.firstWhereOrNull((element) => element['title'] == "user_verify_amount") != null)
                                                  {
                                                    controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "user_verify_amount"));
                                                  }
                                                },
                                              ),
                                              if (controller.errors.firstWhereOrNull((element) => element['title'] == "user_verify_amount") != null) ...[
                                                toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "user_verify_amount"))
                                              ],
                                              10.heightBox,
                                            ],
                                            Row(
                                              children: [
                                                InkWell(
                                                  onTap: (){
                                                    controller.setDefault.value = controller.setDefault.value == "bank" ? "" : "bank";
                                                  },
                                                  child: txt20Size(
                                                      title: "${controller.labelTextDetail['set_default_checkbox_label'] ?? "Set default"}",
                                                      fontFamily: regular,
                                                      context: context),
                                                ),
                                                txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                                                5.widthBox,
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
                                                    value: controller.setDefault.value ==
                                                        "bank"
                                                        ? true
                                                        : false,
                                                    onChanged: (value) async {
                                                      controller.setDefault.value = value == false ? "" : "bank";
                                                    },
                                                  ),
                                                ),
                                              ],
                                            ),
                                            10.heightBox,
                                            SizedBox(
                                              width: context.screenWidth,
                                              child: controller.bankStatus.value == "admin_verify" ?
                                                  elevatedButtonWidget(
                                                  textWidget: txt28Size(title: "${controller.labelTextDetail['verify_button_text'] ?? "Verify bank"}", fontFamily: regular, textColor: Colors.white, context: context),
                                                  onPressed: () async{
                                                  await controller.verifyBank();
                                                  },
                                                  context: context,
                                                  btnRadius: 5.0
                                                  ) : elevatedButtonWidget(
                                                  textWidget: txt28Size(title: controller.bankBtnText.value == 1 ? "${controller.labelTextDetail['update_btn_label'] ?? "Update"}" : "${controller.labelTextDetail['save_btn_label'] ?? "Save"}", fontFamily: regular, textColor: Colors.white, context: context),
                                                  onPressed: () async{
                                                    await controller.updateBankDetail();
                                                  },
                                                  context: context,
                                                  btnRadius: 5.0
                                              ),
                                            ),
                                            20.heightBox,
                                          ],
                                        ),
                                      ),
                                      SingleChildScrollView(
                                        child: Column(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          mainAxisAlignment: MainAxisAlignment.start,
                                          children: [
                                            txt22Size(title: "${controller.labelTextDetail['paypal_account_heading'] ?? "Paypal account"}",
                                                fontFamily: regular, textColor: textColor, context: context),
                                            10.heightBox,
                                            txt20Size(title: "${controller.labelTextDetail['mobile_paypal_indicate_required_label'] ?? "* Indicates required field"}", fontFamily: regular, context: context,textColor: Colors.red),
                                            10.heightBox,
                                            Row(
                                              children: [
                                                txt20Size(title: "${controller.labelTextDetail['paypal_email_label'] ?? "Paypal email"}", fontFamily: regular, textColor: textColor, context: context),
                                                txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                                              ],
                                            ),
                                            5.heightBox,
                                            fieldsWidget(
                                              textController:
                                              controller.paypalEmailTextEditingController,
                                              fieldType: "email",
                                              readonly: false,
                                              fontFamily: regular,
                                              fontSize: 18.0,
                                              onChanged: (value) {
                                                if(controller.errors.firstWhereOrNull((element) => element['title'] == "paypal_email") != null)
                                                {
                                                  controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "paypal_email"));
                                                }
                                              },
                                            ),
                                            if (controller.errors.firstWhereOrNull((element) => element['title'] == "paypal_email") != null) ...[
                                              toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "paypal_email"))
                                            ],
                                            10.heightBox,
                                            Row(
                                              children: [
                                                InkWell(
                                                  onTap: (){
                                                    controller.setDefault.value = controller.setDefault.value == "paypal" ? "" : "paypal";
                                                  },
                                                  child: txt20Size(
                                                      title: "${controller.labelTextDetail['paypal_set_default_checkbox_label'] ?? "Set default"}",
                                                      fontFamily: regular,
                                                      context: context),
                                                ),
                                                txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                                                5.widthBox,
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
                                                    value: controller.setDefault.value ==
                                                        "paypal"
                                                        ? true
                                                        : false,
                                                    onChanged: (value) async {
                                                      controller.setDefault.value = value == false ? "" : "paypal";
                                                    },
                                                  ),
                                                ),
                                              ],
                                            ),
                                            10.heightBox,
                                            SizedBox(
                                              width: context.screenWidth,
                                              child: elevatedButtonWidget(
                                                  textWidget: txt28Size(title: controller.paypalBtnText.value == 1 ? "${controller.labelTextDetail['update_btn_label'] ?? "Update"}" : "${controller.labelTextDetail['save_btn_label'] ?? "Save"}", fontFamily: regular, textColor: Colors.white, context: context),
                                                  onPressed: () async{
                                                    await controller.updatePaypalDetail();
                                                  },
                                                  context: context,
                                                  btnRadius: 5.0
                                              ),
                                            ),
                                            20.heightBox,
                                          ],
                                        ),
                                      ),
                                    ],
                                    onPageChanged: (index) async{
                                      controller.tabController.index = index;
                                      await controller.updatePageIndexValue(index);
                                    },
                                  )
                              )
                            ],
                          )
                      ),
                    ],
                  )
                ),
                if(controller.isOverlayLoading.value == true)...[
                  overlayWidget(context)
                ]
              ],
            )
        )
    );
  }
}
