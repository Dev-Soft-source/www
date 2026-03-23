import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/profile_detail/widget/profile_image_widget.dart';
import 'package:proximaride_app/pages/widgets/app_html_text.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

Widget userInfoWidget(
    {required controller,
    context,
    String imagePath = "",
    String userName = "",
    String editProfileLabel = "Edit profile"}) {
  // Get the current hour to determine the greeting
  final hour = DateTime.now().hour;
  String greeting;
  if (hour < 12) {
    greeting = "Morning";
  } else if (hour < 17) {
    greeting = "Afternoon";
  } else {
    greeting = "Evening";
  }

  // Extract first name from userName (which is "FirstName LastName")
  final firstName = userName.split(' ').first;
  final hasHtml = editProfileLabel.contains('<') && editProfileLabel.contains('>');

  return Column(
    crossAxisAlignment: CrossAxisAlignment.start,
    children: [
      // Greeting line
      Padding(
        padding: const EdgeInsets.only(bottom: 4.0),
        child: txt25Size(
          title: "Good $greeting, $firstName!",
          fontFamily: bold,
          textColor: textColor,
          context: context,
        ),
      ),
      // Sub-line
      Padding(
        padding: const EdgeInsets.only(bottom: 16.0),
        child: descriptiveText(
          title: "This is your profile. You can edit it from here.",
          textColor: textColor,
          opacity: 0.7,
          context: context,
        ),
      ),
      // Profile image and name row
      Row(
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          profileImageWidget(
            controller: controller,
            context: context,
            imagePath: imagePath,
            mobileRadius: 48.0,
            tabletRadius: 48.0,
          ),
          10.widthBox,
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Name on one line
                txt25SizeCapitalize(
                  title: userName,
                  fontFamily: bold,
                  textColor: primaryColor,
                  context: context,
                ),
                8.heightBox,
                // Edit Profile button below name
                elevatedButtonWidget(
                  textWidget: hasHtml
                      ? AppHtmlText(
                          data: editProfileLabel,
                          fontSize: buttonFontSize,
                          fontFamily: buttonFontFamily,
                          textColor: Colors.white,
                          linkColor: Colors.white,
                          textAlign: TextAlign.center,
                          openLinksExternally: false,
                        )
                      : buttonLabelText(
                          title: editProfileLabel,
                          textColor: Colors.white,
                          context: context,
                        ),
                  onPressed: () {
                    Get.toNamed('/edit_profile');
                  },
                  context: context,
                ),
              ],
            ),
          ),
        ],
      ),
    ],
  );
}
