import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';

Widget disclaimerWidget({context, controller, screenWidth}) {
  final List<String> disclaimerItems = [
    _stripLeadingDisclaimerNumber(
      "${controller.labelTextDetail['app_disclaimers_description1'] ?? "I will drive safely and respect the driving rules and regulations such as speed limits"}",
    ),
    _stripLeadingDisclaimerNumber(
      "${controller.labelTextDetail['app_disclaimers_description2'] ?? "I will show up at least five minutes before the time of the ride, and will depart on time. If a passenger is late, I will wait for them for a minimum of five minutes"}",
    ),
    _stripLeadingDisclaimerNumber(
      "${controller.labelTextDetail['app_disclaimers_description3'] ?? "Any cancellation will demand, approve from me and that if I exceed a quote of cancellations (more than two rides for every three months) without a good reason,I will incur a penalty"}",
    ),
    _stripLeadingDisclaimerNumber(
      "${controller.labelTextDetail['app_disclaimers_description4'] ?? "If a passenger does not show up on time, and I want to leave without them, I will call the passenger to ask where they are. I will also gather evidence to prove that I was present at the meeting place on time"}",
    ),
  ];

  final bool pinkRideSelected = controller.rideFeatureList.isNotEmpty &&
      controller.featureList
          .contains(controller.rideFeatureList.first.toString());
  final bool extraCareRideSelected = controller.rideFeatureList.length > 1 &&
      controller.featureList
          .contains(controller.rideFeatureList[1].toString());

  if (pinkRideSelected) {
    disclaimerItems.add(
      _stripLeadingDisclaimerNumber(
        controller.labelTextDetail['pink_ride_disclaimer_text'] ??
            'I understand that this is a Pink Ride, exclusive to female members. I will not send a male driver in my place and will not accept any male passengers over 12 years old, even if the booking is made by a female.',
      ),
    );
  }

  if (extraCareRideSelected) {
    disclaimerItems.add(
      _stripLeadingDisclaimerNumber(
        controller.labelTextDetail['extra_care_ride_disclaimer_text'] ??
            'I understand that this is an Extra+ Ride, exclusive to members with highest review score. I will adhere to its standards',
      ),
    );
  }

  return Container(
      decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(8.0),
          border: Border.all(width: 1, color: inputColor)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          postRideWidget(
              title:
                  "${controller.labelTextDetail['disclaimers_label'] ?? "Disclaimer"}",
              screenWidth: screenWidth,
              context: context,
              isRequired: true),
          Container(
            padding: EdgeInsets.all(getValueForScreenType<double>(
              context: context,
              mobile: 10.0,
              tablet: 10.0,
            )),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                for (int i = 0; i < disclaimerItems.length; i++) ...[
                  txt20Size(
                      title: '${i + 1}. ${disclaimerItems[i]}',
                      fontFamily: bold,
                      context: context),
                  if (i != disclaimerItems.length - 1)
                    const Divider(color: inputColor),
                ],
              ],
            ),
          ),
        ],
      ));
}

String _stripLeadingDisclaimerNumber(String text) {
  return text.replaceFirst(RegExp(r'^\s*\d+\.\s*'), '').trim();
}
