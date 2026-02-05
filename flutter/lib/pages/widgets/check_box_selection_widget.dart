import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/check_box_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
Widget checkBoxSelectionWidget({context, String title = "",  bool value = false, onChanged, onTap, Color textColor = textColor, String infoText = "", bool showToolTipBottom = false, bool isError = false, String extraChargesLabel = "Extra charges may apply; must be agreed in advance", String extraChargesToolTip = "Extra charges may apply; must be agreed in advance", String tooltipMessage= "'Must be agreed upon with the driver BEFORE booking'"}){
  return Padding(
    padding: EdgeInsets.fromLTRB(
        getValueForScreenType<double>(
          context: context,
          mobile: 10.0,
          tablet: 10.0,
        ),
        getValueForScreenType<double>(
          context: context,
          mobile: 0.0,
          tablet: 0.0,
        ),
        getValueForScreenType<double>(
          context: context,
          mobile: 10.0,
          tablet: 10.0,
        ),
        getValueForScreenType<double>(
          context: context,
          mobile: 0.0,
          tablet: 0.0,
        )
    ),
    child: Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        if(infoText != "")...[
          Expanded(
              child: Row(
                mainAxisAlignment: MainAxisAlignment.start,
                children: [
                  InkWell(onTap: onTap, child: txt16Size(title: title, fontFamily: bold, context: context, textColor: textColor)),
                  const SizedBox(width: 10),
                  Tooltip(
                    margin: EdgeInsets.fromLTRB(
                        getValueForScreenType<double>(
                          context: context,
                          mobile: 15.0,
                          tablet: 15.0,
                        ),
                        getValueForScreenType<double>(
                          context: context,
                          mobile: 0.0,
                          tablet: 0.0,
                        ),
                        getValueForScreenType<double>(
                          context: context,
                          mobile: 15.0,
                          tablet: 15.0,
                        ),
                        getValueForScreenType<double>(
                          context: context,
                          mobile: 0.0,
                          tablet: 0.0,
                        )),
                    triggerMode: TooltipTriggerMode.tap,
                    message: infoText,
                    textStyle: const TextStyle(fontSize: 16,color: Colors.white),
                    showDuration: const Duration(days: 100),
                    waitDuration: Duration.zero,
                    child: Image.asset(infoImage,color: Colors.black, width: getValueForScreenType<double>(
                      context: context,
                      mobile: 20.0,
                      tablet: 20.0,
                    ), height: getValueForScreenType<double>(
                      context: context,
                      mobile: 20.0,
                      tablet: 20.0,
                    )),
                  )
                ],
              )
          ),
        ]else if(showToolTipBottom == true)...[
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                InkWell(onTap: onTap, child: txt16Size(title: title, fontFamily: bold, context: context, textColor: textColor)),
                Row(
                  mainAxisAlignment: MainAxisAlignment.start,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    txt16Size(title: "*", context: context, textColor: Colors.red),
                    Expanded(child: InkWell(onTap: onTap, child: txt16Size(title: extraChargesToolTip, context: context, textColor: textColor))),
                    Tooltip(
                      margin: EdgeInsets.fromLTRB(
                          getValueForScreenType<double>(
                            context: context,
                            mobile: 15.0,
                            tablet: 15.0,
                          ),
                          getValueForScreenType<double>(
                            context: context,
                            mobile: 0.0,
                            tablet: 0.0,
                          ),
                          getValueForScreenType<double>(
                            context: context,
                            mobile: 15.0,
                            tablet: 15.0,
                          ),
                          getValueForScreenType<double>(
                            context: context,
                            mobile: 0.0,
                            tablet: 0.0,
                          )),
                      triggerMode: TooltipTriggerMode.tap,
                      message: tooltipMessage,
                      textStyle: const TextStyle(fontSize: 16,color: Colors.white),
                      showDuration: const Duration(days: 100),
                      waitDuration: Duration.zero,
                      child: Image.asset(infoImage,color: Colors.black, width: getValueForScreenType<double>(
                        context: context,
                        mobile: 20.0,
                        tablet: 20.0,
                      ), height: getValueForScreenType<double>(
                        context: context,
                        mobile: 20.0,
                        tablet: 20.0,
                      )),
                    )

                    // Tooltip(
                    //   margin: EdgeInsets.fromLTRB(
                    //       getValueForScreenType<double>(
                    //         context: context,
                    //         mobile: 15.0,
                    //         tablet: 15.0,
                    //       ),
                    //       getValueForScreenType<double>(
                    //         context: context,
                    //         mobile: 0.0,
                    //         tablet: 0.0,
                    //       ),
                    //       getValueForScreenType<double>(
                    //         context: context,
                    //         mobile: 15.0,
                    //         tablet: 15.0,
                    //       ),
                    //       getValueForScreenType<double>(
                    //         context: context,
                    //         mobile: 0.0,
                    //         tablet: 0.0,
                    //       )),
                    //   triggerMode: TooltipTriggerMode.tap,
                    //   message: "Should the ride come and passenger and the driver are unable to agree on the extra charge, cancellation policy will apply, ProximaRide will investigate",
                    //   child: Image.asset(infoImage, width: getValueForScreenType<double>(
                    //     context: context,
                    //     mobile: 16.0,
                    //     tablet: 16.0,
                    //   ), height: getValueForScreenType<double>(
                    //     context: context,
                    //     mobile: 16.0,
                    //     tablet: 16.0,
                    //   )),
                    // ),
                  ],
                )
              ],
            )
          ),
        ]else...[
          Expanded(child: InkWell(onTap: onTap, child: txt16Size(title: title, fontFamily: bold, context: context, textColor: textColor))),
        ],
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
              value: value,
              activeColor: primaryColor,
              onChanged: onChanged,
            isError: isError,
          ),
        ),
      ],
    ),
  );
}

Future<bool> showConfirmationToolTip(message) async {
  return await Get.defaultDialog(
    title: 'Confirm',
    middleText: message,
    middleTextStyle: const TextStyle(fontSize: 18),
    actions: [
      ElevatedButton(
        onPressed: (){
          Get.back(result: true);
        },
        style: ElevatedButton.styleFrom(
            backgroundColor: btnPrimaryColor,
            shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(5)
            )
        ),
        child: txt14SizeWithOutContext(title: "Close", textColor: Colors.white, fontFamily: regular),
      ),
    ],
  );
}
