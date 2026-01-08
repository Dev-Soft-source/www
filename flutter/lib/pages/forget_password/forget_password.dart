import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/forget_password/ForgetPasswordController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../widgets/tool_tip.dart';

class ForgetPasswordPage extends GetView<ForgetPasswordController> {
  const ForgetPasswordPage({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(ForgetPasswordController());
    return Obx(() => Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title:
              secondAppBarWidget(title: "${controller.labelTextDetail['main_heading'] ?? "Forgot password?"}", context: context),
          leading: const BackButton(color: Colors.white),
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
              child: SingleChildScrollView(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisAlignment: MainAxisAlignment.start,
                  children: [
                    txt18Size(
                        title: "${controller.labelTextDetail['main_label'] ?? "Please enter the e-mail you used to sign up"}",
                        fontFamily: regular,
                        textColor: textColor,
                        context: context),
                    5.heightBox,
                    fieldsWidget(
                        textController: controller.emailTextEditingController,
                        fieldType: "email",
                        readonly: false,
                        fontFamily: regular,
                        fontSize: 18.0,
                        placeHolder: "",
                        onChanged: (value){
                          if(controller.errors.firstWhereOrNull((element) => element['title'] == "email") != null)
                          {
                            controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "email"));
                          }
                        }
                    ),
                    if(controller.errors.firstWhereOrNull((element) => element['title'] == "email") != null) ...[
                      toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "email"))
                    ],
                  ],
                ),
              ),
            ),
            Align(
              alignment: Alignment.bottomCenter,
              child: Container(
                padding: EdgeInsets.all(getValueForScreenType<double>(
                  context: context,
                  mobile: 15.0,
                  tablet: 15.0,
                )),
                width: context.screenWidth,
                height: 75,
                child: elevatedButtonWidget(
                    textWidget: txt22Size(
                        title: "${controller.labelTextDetail['button_label'] ?? "Reset password"}",
                        fontFamily: regular,
                        textColor: Colors.white,
                        context: context),
                    onPressed: () async {
                      await controller.forgetPassword();
                    },
                    context: context,
                    btnRadius: 5.0),
              ),
            ),
            if (controller.isOverlayLoading.value == true) ...[
              overlayWidget(context)
            ]
          ],
        )
    ));
  }
}
