import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/card_shadow_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';
import 'package:url_launcher/url_launcher.dart';
Widget cancellationPolicyWidget({context, String policyType = "", double screenWidth = 0.0, int policyRate = 0,
  String heading = "Cancellation policy", String bookingTypeSlug = "", String bookingTypeToolTip = "",
  String discountLabel = "discount", String cancellationPolicyUrl = ""}){
  return cardShadowWidget(
      context: context,
      widgetChild: Column(
        mainAxisAlignment: MainAxisAlignment.start,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          postRideWidget(title: heading, screenWidth: screenWidth, context: context, bgColor: bookingTypeSlug == "firm" ? Colors.red : primaryColor),
          Container(
              padding: EdgeInsets.fromLTRB(getValueForScreenType<double>(
                context: context,
                mobile: 15.0,
                tablet: 15.0,
              ),getValueForScreenType<double>(
                context: context,
                mobile: 10.0,
                tablet: 10.0,
              ),getValueForScreenType<double>(
                context: context,
                mobile: 15.0,
                tablet: 15.0,
              ),getValueForScreenType<double>(
                context: context,
                mobile: 10.0,
                tablet: 10.0,
              )),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  // txt18Size(title: policyType, context: context),
                  Row(
                    children: [
                      txt18SizeCapitalized(context: context,title: policyType),
                      if(bookingTypeSlug == "firm")...[
                        txt18Size(context: context, title: ' ($discountLabel $policyRate%)'),
                      ]
                    ],
                  ),
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
                    message: bookingTypeToolTip,
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
          if(bookingTypeSlug == "firm" && cancellationPolicyUrl != "")...[
            Container(
                padding: EdgeInsets.fromLTRB(getValueForScreenType<double>(
                  context: context,
                  mobile: 15.0,
                  tablet: 15.0,
                ),getValueForScreenType<double>(
                  context: context,
                  mobile: 0.0,
                  tablet: 0.0,
                ),getValueForScreenType<double>(
                  context: context,
                  mobile: 15.0,
                  tablet: 15.0,
                ),getValueForScreenType<double>(
                  context: context,
                  mobile: 10.0,
                  tablet: 10.0,
                )),
                child: InkWell(
                  onTap: () async{
                    final Uri _url = Uri.parse(cancellationPolicyUrl);
                    if (!await launchUrl(_url)) {
                    throw Exception('Could not launch $_url');
                    }
                  },
                  child: textWithUnderLine(title: cancellationPolicyUrl, context: context, textSize: 16.0, textColor: primaryColor),
                )
            ),
          ]
        ],
      )
  );
}