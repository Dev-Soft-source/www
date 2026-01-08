import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/check_box_widget.dart';
import 'package:proximaride_app/pages/widgets/drop_down_date_widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/widgets/tool_tip.dart';


Widget addCardWidget({context, controller}){
  return Container(
    padding: EdgeInsets.all(getValueForScreenType<double>(
      context: context,
      mobile: 15.0,
      tablet: 15.0,
    )),
    child: SingleChildScrollView(
      child: Obx(() {
        return Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            10.heightBox,
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
            txt20Size(
              title: "Name on card",
              fontFamily: regular,
              context: context,),
            5.heightBox,
            fieldsWidget(
              textController: controller.cardNameController,
              fieldType: "text",
              readonly: false,
              fontFamily: regular,
              fontSize: 18.0,
            ),
            // if(controller.errors.firstWhereOrNull((element) => element['title'] == "name_on_card") != null) ...[
            //   toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "name_on_card"))
            // ],
            10.heightBox,
            txt20Size(
                title: "Card number",
                fontFamily: regular,
                context: context),
            5.heightBox,
            fieldsWidget(
                textController: controller.cardNumberController,
                fieldType: "number",
                readonly: false,
                fontFamily: regular,
                fontSize: 18.0),
            // if(controller.errors.firstWhereOrNull((element) => element['title'] == "card_number") != null) ...[
            //   toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "card_number"))
            // ],
            10.heightBox,
            txt20Size(
                title: "Card type",
                fontFamily: regular,
                context: context),
            5.heightBox,
            Row(
              children: [
                Expanded(

                  child: Container(child: dropdownCardTypeWidget(context: context,controller: controller)),
                ),
              ],
            ),
            // if(controller.errors.firstWhereOrNull((element) => element['title'] == "card_type") != null) ...[
            //   toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "card_type"))
            // ],


            10.heightBox,
            txt20Size(
                title: "Expiry date",
                fontFamily: regular,
                context: context),
            5.heightBox,
            Row(
              children: [
                Expanded(
                    flex: 10,
                    child: Container(
                        child: dropdownMonthWidget(
                            controller: controller,
                            context: context))),
                // if(controller.errors.firstWhereOrNull((element) => element['title'] == "month") != null) ...[
                //   toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "month"))
                // ],
                const Spacer(flex: 1),
                Expanded(
                    flex: 10,
                    child: Container(
                        child: dropdownYearWidget(
                            controller: controller,
                            context: context))),
                // if(controller.errors.firstWhereOrNull((element) => element['title'] == "year") != null) ...[
                //   toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "year"))
                // ],
              ],
            ),
            10.heightBox,
            txt20Size(
                title: "Security (CVV) code",
                fontFamily: regular,
                context: context),
            5.heightBox,
            fieldsWidget(
                textController: controller.cvvCodeController,
                fieldType: "number",
                readonly: false,
                fontFamily: regular,
                fontSize: 18.0),
            // if(controller.errors.firstWhereOrNull((element) => element['title'] == "cvv") != null) ...[
            //   toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "cvv"))
            // ],
            10.heightBox,
            txt20Size(
                title: "Address",
                fontFamily: regular,
                context: context),
            5.heightBox,
            fieldsWidget(
                textController: controller.addressController,
                fieldType: "text",
                readonly: false,
                fontFamily: regular,
                fontSize: 18.0),
            // if(controller.errors.firstWhereOrNull((element) => element['title'] == "address") != null) ...[
            //   toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "address"))
            // ],
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
                      title: "Primary card",
                      fontFamily: bold,
                      context: context),
                ),
              ],
            ),
            100.heightBox
          ],
        );
      }),
    ),
  );
}