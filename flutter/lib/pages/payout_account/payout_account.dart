import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/payout_account/PayoutAccountController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/check_box_widget.dart';
import 'package:proximaride_app/pages/widgets/error_state_widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../widgets/tool_tip.dart';

class PayoutAccountPage extends StatelessWidget {
  const PayoutAccountPage({super.key});

  @override
  Widget build(BuildContext context) {
    final PayoutAccountController controller = Get.isRegistered<PayoutAccountController>()
        ? Get.find<PayoutAccountController>()
        : Get.put(PayoutAccountController());
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => secondAppBarWidget(
              title:
                  "${controller.labelTextDetail['main_heading'] ?? "Payout Options"}",
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
              return Stack(
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
                                    borderRadius: BorderRadius.circular(5.0)),
                                child: TabBar(
                                  controller: controller.tabController,
                                  onTap: (index) {
                                    controller.pageController.animateToPage(
                                        index,
                                        duration:
                                            const Duration(milliseconds: 1),
                                        curve: Curves.linear);
                                  },
                                  indicatorColor: primaryColor,
                                  indicatorSize: TabBarIndicatorSize.tab,
                                  dividerColor: Colors.transparent,
                                  labelColor: Colors.white,
                                  unselectedLabelColor: textColor,
                                  // Top-level payout tabs: 20px titles
                                  labelStyle: const TextStyle(
                                      fontFamily: regular, fontSize: 20),
                                  unselectedLabelStyle: const TextStyle(
                                      fontFamily: regular, fontSize: 20),
                                  indicator: BoxDecoration(
                                      borderRadius: BorderRadius.circular(
                                          5), // Creates border
                                      color: btnPrimaryColor),
                                  labelPadding: const EdgeInsets.all(5.0),
                                  tabs: [
                                    Text(
                                        "${controller.labelTextDetail['web_interac_transfer_description'] ?? "Interac e-Transfer"}",
                                        textAlign: TextAlign.center),
                                    Text(
                                        "${controller.labelTextDetail['bank_detail_heading'] ?? "Bank detail"}",
                                        textAlign: TextAlign.center),
                                    Text(
                                        "${controller.labelTextDetail['paypal_detail_heading'] ?? "Paypal detail"}",
                                        textAlign: TextAlign.center),
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
                                      children: [
                                        txt20Size(
                                          title: "${controller.labelTextDetail['interac_detail_heading'] ?? 'Interac e-Transfer Details:'}",
                                          fontFamily: regular,
                                          textColor: textColor,
                                          context: context,
                                        ),
                                        10.heightBox,
                                        txt18Size(
                                          title: "${controller.labelTextDetail['interac_autodeposit_info_paragraph'] ?? 'Please ensure the email matches the one registered for Autodeposit at your bank.'}",
                                          fontFamily: regular,
                                          context: context,
                                          textColor: textColor,
                                        ),
                                        10.heightBox,
                                        txt18Size(
                                          title: "${controller.labelTextDetail['mobile_indicate_required_field_label'] ?? '* Indicates required fields'}",
                                          fontFamily: regular,
                                          context: context,
                                          textColor: Colors.red,
                                        ),
                                        10.heightBox,
                                        formLabelRequired(
                                          title:
                                              "${controller.labelTextDetail['interac_email_label'] ?? 'Email Address'}",
                                          context: context,
                                          fontFamily: regular,
                                          labelColor: textColor,
                                        ),
                                        5.heightBox,
                                        Obx(() => fieldsWidget(
                                              textController: controller.interacEmailTextEditingController,
                                              fieldType: "email",
                                              readonly: controller.interacEmailReadOnly.value,
                                              fontFamily: regular,
                                              fontSize: 18.0,
                                              placeHolder: "${controller.labelTextDetail['interac_email_placeholder'] ?? 'e.g. name@email.com'}",
                                              focusNode: controller.focusNodes['interac_email'],
                                              onChanged: (_) {
                                                controller.errors.removeWhere((e) => e['title'] == 'interac_email');
                                                controller.validateInteracFormFields();
                                              },
                                            )),
                                        if (controller.errors.firstWhereOrNull((e) => e['title'] == 'interac_email') != null)
                                          toolTip(
                                            tip: controller.errors.firstWhereOrNull((e) => e['title'] == 'interac_email'),
                                          ),
                                        10.heightBox,
                                        formLabelRequired(
                                          title:
                                              "${controller.labelTextDetail['interac_email_confirm_label'] ?? 'Confirm Email Address'}",
                                          context: context,
                                          fontFamily: regular,
                                          labelColor: textColor,
                                        ),
                                        5.heightBox,
                                        fieldsWidget(
                                          textController: controller.interacEmailConfirmTextEditingController,
                                          fieldType: "email",
                                          readonly: false,
                                          fontFamily: regular,
                                          fontSize: 18.0,
                                          placeHolder: "${controller.labelTextDetail['interac_email_confirm_placeholder'] ?? 'Re-enter your email address'}",
                                          focusNode: controller.focusNodes['interac_email_confirm'],
                                          onChanged: (_) {
                                            controller.errors.removeWhere((e) => e['title'] == 'interac_email_confirm');
                                            controller.validateInteracFormFields();
                                          },
                                        ),
                                        if (controller.errors.firstWhereOrNull((e) => e['title'] == 'interac_email_confirm') != null)
                                          toolTip(
                                            tip: controller.errors.firstWhereOrNull((e) => e['title'] == 'interac_email_confirm'),
                                          ),
                                        10.heightBox,
                                        Obx(
                                          () => Row(
                                            crossAxisAlignment: CrossAxisAlignment.center,
                                            children: [
                                              checkBoxWidget(
                                                compact: true,
                                                value: controller.interacAutodepositChecked.value,
                                                onChanged: (v) {
                                                  controller.interacAutodepositChecked.value = v == true;
                                                  controller.errors.removeWhere((e) => e['title'] == 'interac_autodeposit');
                                                  controller.validateInteracFormFields();
                                                },
                                              ),
                                              8.widthBox,
                                              Expanded(
                                                child: txt18Size(
                                                  title:
                                                      "${controller.labelTextDetail['interac_autodeposit_text_before'] ?? 'I have enabled Interac'} ${controller.labelTextDetail['interac_autodeposit_highlight'] ?? 'Autodeposit'} ${controller.labelTextDetail['interac_autodeposit_text_after'] ?? 'for this email address.'}",
                                                  fontFamily: regular,
                                                  context: context,
                                                  textColor: textColor,
                                                ),
                                              ),
                                            ],
                                          ),
                                        ),
                                        if (controller.errors.firstWhereOrNull((e) => e['title'] == 'interac_autodeposit') != null)
                                          toolTip(
                                            tip: controller.errors.firstWhereOrNull((e) => e['title'] == 'interac_autodeposit'),
                                          ),
                                        10.heightBox,
                                        txt18Size(
                                          title: "${controller.labelTextDetail['processing_fee_text'] ?? 'Processing Fee: \$2.00 CAD per withdrawal.'}",
                                          fontFamily: regular,
                                          context: context,
                                          textColor: textColor,
                                        ),
                                        10.heightBox,
                                        Row(
                                          crossAxisAlignment: CrossAxisAlignment.center,
                                          children: [
                                            checkBoxWidget(
                                              compact: true,
                                              value: controller.setDefault.value == 'interac',
                                              onChanged: (value) async {
                                                controller.setDefault.value = value == false ? '' : 'interac';
                                                controller.validateInteracFormFields();
                                              },
                                            ),
                                            8.widthBox,
                                            Expanded(
                                              child: InkWell(
                                                onTap: () {
                                                  controller.setDefault.value =
                                                      controller.setDefault.value == 'interac' ? '' : 'interac';
                                                  controller.validateInteracFormFields();
                                                },
                                                child: txt18Size(
                                                  title: "${controller.labelTextDetail['set_default_checkbox_label'] ?? 'Set as default'}",
                                                  fontFamily: regular,
                                                  context: context,
                                                  textColor: textColor,
                                                ),
                                              ),
                                            ),
                                          ],
                                        ),
                                        10.heightBox,
                                        Obx(
                                          () => SizedBox(
                                            width: context.screenWidth,
                                            child: elevatedButtonWidget(
                                              enabled: controller.isInteracFormValid.value,
                                              textWidget: txt22Size(
                                                title: controller.interacBtnText.value == 1
                                                    ? "${controller.labelTextDetail['update_btn_label'] ?? 'Update'}"
                                                    : "${controller.labelTextDetail['save_payout_method_btn'] ?? controller.labelTextDetail['save_btn_label'] ?? 'Save'}",
                                                fontFamily: regular,
                                                textColor: Colors.white,
                                                context: context,
                                              ),
                                              onPressed: () async {
                                                await controller.updateInteracDetail();
                                              },
                                              context: context,
                                              btnRadius: 5.0,
                                            ),
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
                                        txt20Size(
                                          title: "${controller.labelTextDetail['bank_detail_heading'] ?? 'Bank details'}:",
                                          fontFamily: regular,
                                          textColor: textColor,
                                          context: context,
                                        ),
                                        10.heightBox,
                                        txt18Size(
                                          title: "${controller.labelTextDetail['bank_detail_info_paragraph'] ?? 'Enter your bank details to receive funds via Direct Deposit (EFT)'}",
                                          fontFamily: regular,
                                          context: context,
                                          textColor: textColor,
                                        ),
                                        10.heightBox,
                                        txt18Size(
                                          title: "${controller.labelTextDetail['mobile_indicate_required_field_label'] ?? '* Indicates required fields'}",
                                          fontFamily: regular,
                                          context: context,
                                          textColor: Colors.red,
                                        ),
                                        10.heightBox,
                                        formLabelRequired(
                                          title:
                                              "${controller.labelTextDetail['bank_title_label'] ?? 'Account Holder Name'}",
                                          context: context,
                                          fontFamily: regular,
                                          labelColor: textColor,
                                        ),
                                        5.heightBox,
                                        fieldsWidget(
                                          textController: controller.bankTitleTextEditingController,
                                          fieldType: "text",
                                          readonly: false,
                                          fontFamily: regular,
                                          fontSize: 18.0,
                                          placeHolder: "${controller.labelTextDetail['bank_title_placeholder'] ?? 'As it appears on your bank statement'}",
                                          focusNode: controller.focusNodes['account_holder_name'],
                                          onChanged: (_) {
                                            controller.errors.removeWhere((e) => e['title'] == 'account_holder_name');
                                            controller.validateBankFormFields();
                                          },
                                        ),
                                        if (controller.errors.firstWhereOrNull((e) => e['title'] == 'account_holder_name') != null)
                                          toolTip(
                                            tip: controller.errors.firstWhereOrNull((e) => e['title'] == 'account_holder_name'),
                                          ),
                                        10.heightBox,
                                        formLabelRequired(
                                          title:
                                              "${controller.labelTextDetail['branch_number_label'] ?? 'Transit Number (5 digits)'}",
                                          context: context,
                                          fontFamily: regular,
                                          labelColor: textColor,
                                        ),
                                        5.heightBox,
                                        fieldsWidget(
                                          textController: controller.branchNumberTextEditingController,
                                          fieldType: "number",
                                          maxLength: 5,
                                          readonly: false,
                                          fontFamily: regular,
                                          fontSize: 18.0,
                                          placeHolder: "${controller.labelTextDetail['branch_number_placeholder'] ?? 'The branch code'}",
                                          focusNode: controller.focusNodes['branch_number'],
                                          inputFormatters: [FilteringTextInputFormatter.digitsOnly],
                                          onChanged: (_) {
                                            controller.errors.removeWhere((e) => e['title'] == 'branch_number');
                                            controller.validateBankFormFields();
                                          },
                                        ),
                                        if (controller.errors.firstWhereOrNull((e) => e['title'] == 'branch_number') != null)
                                          toolTip(
                                            tip: controller.errors.firstWhereOrNull((e) => e['title'] == 'branch_number'),
                                          ),
                                        10.heightBox,
                                        formLabelRequired(
                                          title:
                                              "${controller.labelTextDetail['institution_number_label'] ?? 'Institution Number (3 digits)'}",
                                          context: context,
                                          fontFamily: regular,
                                          labelColor: textColor,
                                        ),
                                        5.heightBox,
                                        fieldsWidget(
                                          textController: controller.institutionNumberTextEditingController,
                                          fieldType: "number",
                                          maxLength: 3,
                                          readonly: false,
                                          fontFamily: regular,
                                          fontSize: 18.0,
                                          placeHolder: "${controller.labelTextDetail['institution_number_placeholder'] ?? 'e.g. 004 for TD'}",
                                          focusNode: controller.focusNodes['institution_number'],
                                          inputFormatters: [FilteringTextInputFormatter.digitsOnly],
                                          onChanged: (_) {
                                            controller.errors.removeWhere((e) => e['title'] == 'institution_number');
                                            controller.validateBankFormFields();
                                          },
                                        ),
                                        if (controller.errors.firstWhereOrNull((e) => e['title'] == 'institution_number') != null)
                                          toolTip(
                                            tip: controller.errors.firstWhereOrNull((e) => e['title'] == 'institution_number'),
                                          ),
                                        10.heightBox,
                                        formLabelRequired(
                                          title:
                                              "${controller.labelTextDetail['account_number_label'] ?? 'Account Number'}",
                                          context: context,
                                          fontFamily: regular,
                                          labelColor: textColor,
                                        ),
                                        5.heightBox,
                                        fieldsWidget(
                                          textController: controller.accountNumberTextEditingController,
                                          fieldType: "number",
                                          maxLength: 12,
                                          readonly: false,
                                          fontFamily: regular,
                                          fontSize: 18.0,
                                          placeHolder: "${controller.labelTextDetail['account_number_placeholder'] ?? '7–12 digits'}",
                                          focusNode: controller.focusNodes['account_holder_number'],
                                          inputFormatters: [FilteringTextInputFormatter.digitsOnly],
                                          onChanged: (_) {
                                            controller.errors.removeWhere((e) => e['title'] == 'account_holder_number');
                                            controller.validateBankFormFields();
                                          },
                                        ),
                                        if (controller.errors.firstWhereOrNull((e) => e['title'] == 'account_holder_number') != null)
                                          toolTip(
                                            tip: controller.errors.firstWhereOrNull((e) => e['title'] == 'account_holder_number'),
                                          ),
                                        10.heightBox,
                                        txt18Size(
                                          title: "${controller.labelTextDetail['processing_fee_text'] ?? 'Processing Fee: \$2.00 CAD per withdrawal.'}",
                                          fontFamily: regular,
                                          context: context,
                                          textColor: textColor,
                                        ),
                                        10.heightBox,
                                        txt18Size(
                                          title: "${controller.labelTextDetail['bank_funds_note'] ?? 'Note: Funds typically arrive in 1–3 business days.'}",
                                          fontFamily: regular,
                                          context: context,
                                          textColor: textColor,
                                        ),
                                        10.heightBox,
                                        Row(
                                          crossAxisAlignment: CrossAxisAlignment.center,
                                          children: [
                                            checkBoxWidget(
                                              compact: true,
                                              value: controller.setDefault.value == 'bank',
                                              onChanged: (value) async {
                                                controller.setDefault.value = value == false ? '' : 'bank';
                                                controller.validateBankFormFields();
                                              },
                                            ),
                                            8.widthBox,
                                            Expanded(
                                              child: InkWell(
                                                onTap: () {
                                                  controller.setDefault.value =
                                                      controller.setDefault.value == 'bank' ? '' : 'bank';
                                                  controller.validateBankFormFields();
                                                },
                                                child: txt18Size(
                                                  title: "${controller.labelTextDetail['set_default_checkbox_label'] ?? 'Set as default'}",
                                                  fontFamily: regular,
                                                  context: context,
                                                  textColor: textColor,
                                                ),
                                              ),
                                            ),
                                          ],
                                        ),
                                        10.heightBox,
                                        if (controller.bankStatus.value == 'admin_verify') ...[
                                          formLabelRequired(
                                            title:
                                                "${controller.labelTextDetail['admin_sent_amount_placeholder'] ?? 'Admin sent amount'}",
                                            context: context,
                                            fontFamily: regular,
                                            labelColor: textColor,
                                          ),
                                          fieldsWidget(
                                            textController: controller.userVerifyAmountTextEditingController,
                                            fieldType: "number",
                                            readonly: false,
                                            fontFamily: regular,
                                            fontSize: 18.0,
                                            focusNode: controller.focusNodes['user_verify_amount'],
                                            onChanged: (_) {
                                              controller.errors.removeWhere((e) => e['title'] == 'user_verify_amount');
                                              controller.validateBankVerifyField();
                                            },
                                          ),
                                          if (controller.errors.firstWhereOrNull((e) => e['title'] == 'user_verify_amount') != null)
                                            toolTip(
                                              tip: controller.errors.firstWhereOrNull((e) => e['title'] == 'user_verify_amount'),
                                            ),
                                          10.heightBox,
                                        ],
                                        Obx(
                                          () => SizedBox(
                                            width: context.screenWidth,
                                            child: controller.bankStatus.value == 'admin_verify'
                                                ? elevatedButtonWidget(
                                                    enabled: controller.isBankVerifyValid.value,
                                                    textWidget: txt22Size(
                                                      title: "${controller.labelTextDetail['verify_button_text'] ?? 'Verify bank'}",
                                                      fontFamily: regular,
                                                      textColor: Colors.white,
                                                      context: context,
                                                    ),
                                                    onPressed: () async {
                                                      await controller.verifyBank();
                                                    },
                                                    context: context,
                                                    btnRadius: 5.0,
                                                  )
                                                : elevatedButtonWidget(
                                                    enabled: controller.isBankFormValid.value,
                                                    textWidget: txt22Size(
                                                      title: controller.bankBtnText.value == 1
                                                          ? "${controller.labelTextDetail['update_btn_label'] ?? 'Update'}"
                                                          : "${controller.labelTextDetail['save_payout_method_btn'] ?? controller.labelTextDetail['save_btn_label'] ?? 'Save'}",
                                                      fontFamily: regular,
                                                      textColor: Colors.white,
                                                      context: context,
                                                    ),
                                                    onPressed: () async {
                                                      await controller.updateBankDetail();
                                                    },
                                                    context: context,
                                                    btnRadius: 5.0,
                                                  ),
                                          ),
                                        ),
                                        20.heightBox,
                                      ],
                                    ),
                                  ),
                                  SingleChildScrollView(
                                    child: Column(
                                      crossAxisAlignment:
                                          CrossAxisAlignment.start,
                                      mainAxisAlignment:
                                          MainAxisAlignment.start,
                                      children: [
                                        // Paypal section heading: 20px
                                        txt20Size(
                                            title:
                                                "${controller.labelTextDetail['paypal_account_heading'] ?? "Paypal account"}",
                                            fontFamily: regular,
                                            textColor: textColor,
                                            context: context),
                                        // 10.heightBox,
                                        Divider(),
                                        // Sub-heading/body text: 20px
                                        txt20Size(
                                            title:
                                                "${controller.labelTextDetail['paypal_account_sub_heading'] ?? "Enter the PayPal email address where you would like to receive your payouts."}",
                                            fontFamily: regular,
                                            textColor: textColor,
                                            context: context),
                                        10.heightBox,
                                        // Red required-fields note: 18px
                                        txt18Size(
                                            title:
                                                "${controller.labelTextDetail['mobile_paypal_indicate_required_label'] ?? "* Indicates required fields"}",
                                            fontFamily: regular,
                                            context: context,
                                            textColor: Colors.red),
                                        10.heightBox,
                                        formLabelRequired(
                                          title:
                                              "${controller.labelTextDetail['paypal_email_label'] ?? "Paypal email"}",
                                          context: context,
                                          fontFamily: regular,
                                          labelColor: textColor,
                                        ),
                                        5.heightBox,
                                        Obx(() => controller
                                                .isPaypalEditMode.value
                                            ? // Edit mode: Show editable field
                                            Column(
                                                crossAxisAlignment:
                                                    CrossAxisAlignment.start,
                                                children: [
                                                  fieldsWidget(
                                                    textController: controller
                                                        .paypalEmailTextEditingController,
                                                    fieldType: "email",
                                                    readonly: false,
                                                    fontFamily: regular,
                                                    fontSize: 18.0,
                                                    focusNode: controller
                                                        .focusNodes['paypal_email'],
                                                    onChanged: (value) {
                                                      if (controller.errors
                                                              .firstWhereOrNull(
                                                                  (element) =>
                                                                      element[
                                                                          'title'] ==
                                                                      "paypal_email") !=
                                                          null) {
                                                        controller.errors
                                                            .remove(controller
                                                                .errors
                                                                .firstWhereOrNull(
                                                                    (element) =>
                                                                        element[
                                                                            'title'] ==
                                                                        "paypal_email"));
                                                      }
                                                      controller
                                                          .validatePaypalFormFields();
                                                    },
                                                  ),
                                                  if (controller.errors
                                                          .firstWhereOrNull(
                                                              (element) =>
                                                                  element[
                                                                      'title'] ==
                                                                  "paypal_email") !=
                                                      null) ...[
                                                    toolTip(
                                                        tip: controller.errors
                                                            .firstWhereOrNull(
                                                                (element) =>
                                                                    element[
                                                                        'title'] ==
                                                                    "paypal_email"))
                                                  ],
                                                ],
                                              )
                                            : // View mode: Show non-editable text with Edit button
                                            Container(
                                                padding:
                                                    const EdgeInsets.symmetric(
                                                        horizontal: 12.0,
                                                        vertical: 15.0),
                                                decoration: BoxDecoration(
                                                  color: Colors.grey.shade100,
                                                  borderRadius:
                                                      BorderRadius.circular(
                                                          5.0),
                                                  border: Border.all(
                                                    color: Colors.grey.shade400,
                                                    width: 1,
                                                  ),
                                                ),
                                                child: Row(
                                                  mainAxisAlignment:
                                                      MainAxisAlignment
                                                          .spaceBetween,
                                                  children: [
                                                    Expanded(
                                                      child: txt18Size(
                                                          title: controller
                                                              .paypalEmailTextEditingController
                                                              .text,
                                                          fontFamily: regular,
                                                          textColor: textColor,
                                                          context: context),
                                                    ),
                                                    InkWell(
                                                      onTap: () {
                                                        controller
                                                            .togglePaypalEditMode();
                                                      },
                                                      child: Container(
                                                        padding:
                                                            const EdgeInsets
                                                                .symmetric(
                                                                horizontal:
                                                                    12.0,
                                                                vertical: 6.0),
                                                        decoration:
                                                            BoxDecoration(
                                                          color:
                                                              btnPrimaryColor,
                                                          borderRadius:
                                                              BorderRadius
                                                                  .circular(
                                                                      5.0),
                                                        ),
                                                        child: txt18Size(
                                                            title: "Edit",
                                                            fontFamily: bold,
                                                            textColor:
                                                                Colors.white,
                                                            context: context),
                                                      ),
                                                    ),
                                                  ],
                                                ),
                                              )),
                                        10.heightBox,
                                        Row(
                                          crossAxisAlignment:
                                              CrossAxisAlignment.center,
                                          children: [
                                            checkBoxWidget(
                                              compact: true,
                                              value: controller.setDefault.value ==
                                                  "paypal",
                                              onChanged: (value) async {
                                                controller.setDefault.value =
                                                    value == false
                                                        ? ""
                                                        : "paypal";
                                                controller
                                                    .validatePaypalFormFields();
                                              },
                                            ),
                                            8.widthBox,
                                            Expanded(
                                              child: InkWell(
                                                onTap: () {
                                                  controller.setDefault.value =
                                                      controller.setDefault
                                                                  .value ==
                                                              "paypal"
                                                          ? ""
                                                          : "paypal";
                                                  controller
                                                      .validatePaypalFormFields();
                                                },
                                                child: txt18Size(
                                                    title:
                                                        "${controller.labelTextDetail['paypal_set_default_checkbox_label'] ?? "Set default"}",
                                                    fontFamily: regular,
                                                    context: context,
                                                    textColor: textColor),
                                              ),
                                            ),
                                          ],
                                        ),
                                        10.heightBox,
                                        SizedBox(
                                          width: context.screenWidth,
                                          child: elevatedButtonWidget(
                                              enabled: controller
                                                  .isPaypalFormValid.value,
                                              textWidget: txt22Size(
                                                  title: controller
                                                              .paypalBtnText
                                                              .value ==
                                                          1
                                                      ? "${controller.labelTextDetail['update_btn_label'] ?? "Update"}"
                                                      : "${controller.labelTextDetail['save_btn_label'] ?? "Save"}",
                                                  fontFamily: regular,
                                                  textColor: Colors.white,
                                                  context: context),
                                              onPressed: () async {
                                                await controller
                                                    .updatePaypalDetail();
                                              },
                                              context: context,
                                              btnRadius: 5.0),
                                        ),
                                        20.heightBox,
                                      ],
                                    ),
                                  ),
                                ],
                                onPageChanged: (index) async {
                                  controller.tabController.index = index;
                                  await controller.updatePageIndexValue(index);
                                },
                              ))
                            ],
                          )),
                        ],
                      )),
                  if (controller.isOverlayLoading.value == true) ...[
                    overlayWidget(context)
                  ]
                ],
              );
            }
          }),
        ));
  }
}



