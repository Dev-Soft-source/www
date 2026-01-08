import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';

Widget disclaimerWidget({context, controller, screenWidth}) {
  return Container(
      decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(8.0),
          border: Border.all(width: 1, color: inputColor)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          postRideWidget(
              title: "${controller.labelTextDetail['disclaimers_label'] ?? "Disclaimer"}",
              screenWidth: screenWidth,
              context: context,
              isRequired: true
          ),
          Container(
            padding: EdgeInsets.all(getValueForScreenType<double>(
              context: context,
              mobile: 10.0,
              tablet: 10.0,
            )),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                txt16Size(
                    title:
                    "${controller.labelTextDetail['app_disclaimers_description1'] ?? "1. I will drive safely and respect the driving rules and regulations such as speed limits"}",
                    fontFamily: bold,
                    context: context),
                const Divider(color: inputColor),
                txt16Size(
                    title:
                    "${controller.labelTextDetail['app_disclaimers_description2'] ?? "2. I will show up at least five minutes before the time of the ride, and will depart on time. If a passenger is late, I will wait for them for a minimum of five minutes"}",
                    fontFamily: bold,
                    context: context),
                const Divider(color: inputColor),
                txt16Size(
                    title:
                    "${controller.labelTextDetail['disclaimers_description3'] ?? "3. Any cancellation will demand, approve from me and that if I exceed a quote of cancellations (more than two rides for every three months) without a good reason,I will incur a penalty"}",
                    fontFamily: bold,
                    context: context),
                const Divider(color: inputColor),
                txt16Size(
                    title:
                    "${controller.labelTextDetail['disclaimers_description4'] ?? "4. If a passenger does not show up on time, and I want to leave without them, I will call the passenger to ask where they are. I will also gather evidence to prove that I was present at the meeting place on time"}",
                    fontFamily: bold,
                    context: context),
              ],
            ),
          ),
        ],
      ));
}
