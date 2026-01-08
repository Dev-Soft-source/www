import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/email_address/EmailAddressController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../widgets/fields_widget.dart';
import '../widgets/progress_circular_widget.dart';


class EmailAddressPage extends GetView<EmailAddressController> {
  const EmailAddressPage({super.key});


  @override
  Widget build(BuildContext context) {
    Get.put(EmailAddressController());
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => secondAppBarWidget(title: "${controller.labelTextDetail['main_heading'] ?? "My e-mail address"}", context: context)),
          leading: const BackButton(
              color: Colors.white
          ),
        ),
        body: Obx(() {
          if(controller.isLoading.value == true){
            return Center(child: progressCircularWidget(context));
          }else{
            return Stack(
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
                        txt22Size(title: "${controller.labelTextDetail['email_description_text'] ?? 'To be eligible to post "Pink rides" and "Extra-care rides", you must enter a valid e-mail address'}",
                            fontFamily: regular, textColor: textColor, context: context),
                        10.heightBox,
                        txt20Size(context: context,title: "${controller.labelTextDetail['email_label'] ?? 'E-mail'}", textColor: Colors.black,fontFamily: regular),
                        10.heightBox,
                        fieldsWidget(fieldType: "email", readonly: true, fontFamily: regular, fontSize: 18.0, placeHolder: "${controller.serviceController.loginUserDetail["email"]}", hintTextColor: Colors.grey),
                      ],
                    ),
                  ),
                ),
                Align(
                  alignment: Alignment.bottomCenter,
                  child: Container(
                    color: Colors.grey.shade100,
                    padding: EdgeInsets.all(getValueForScreenType<double>(
                      context: context,
                      mobile: 15.0,
                      tablet: 15.0,
                    )),
                    width: context.screenWidth,
                    child: elevatedButtonWidget(
                        textWidget: txt28Size(title: "${controller.labelTextDetail['update_button_text'] ?? "Update"}", fontFamily: regular, textColor: Colors.white, context: context),
                        onPressed: () async{
                          Get.toNamed('/update_email_address');
                        },
                        context: context,
                        btnRadius: 5.0
                    ),
                  ),
                ),
                if(controller.isOverlayLoading.value == true)...[
                  overlayWidget(context)
                ]
              ],
            );
          }
        })
    );
  }
}
