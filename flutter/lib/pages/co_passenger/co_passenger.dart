import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/co_passenger/CoPassengerController.dart';
import 'package:proximaride_app/pages/widgets/circle_image_widget.dart';
import 'package:proximaride_app/pages/widgets/error_state_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

class CoPassengerPage extends StatelessWidget {
  const CoPassengerPage({super.key});

  @override
  Widget build(BuildContext context) {
    final CoPassengerController controller = Get.isRegistered<CoPassengerController>()
        ? Get.find<CoPassengerController>()
        : Get.put(CoPassengerController());
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: secondAppBarWidget(
              title:
                  "${controller.labelTextDetail['co_passenger_main_heading'] ?? "Co-passengers"}",
              context: context),
          leading: safeBackButton(context),
        ),
        body: Obx(() {
          if (controller.errorStateManager.isLoading.value) {
            return Center(child: progressCircularWidget(context));
          }

          if (controller.errorStateManager.hasError.value) {
            return ErrorStateWidget(
              message: controller.errorStateManager.errorMessage.value,
              errorType: controller.errorStateManager.errorType.value,
              onRetry: () {
                if (controller.errorStateManager.onRetry.value != null) {
                  controller.errorStateManager.onRetry.value!();
                }
              },
            );
          }

          if (controller.isLoading.value == true) {
            return Center(child: progressCircularWidget(context));
          } else {
            return ListView.separated(
              padding: const EdgeInsets.all(15.0),
              itemCount: controller.coPassengers.length,
              separatorBuilder: (context, index) => 12.heightBox,
              itemBuilder: (context, index) {
                final coPassenger = controller.coPassengers[index];
                final passenger = coPassenger['passenger'] ?? {};
                final String firstName = passenger['first_name'] ?? "";
                final String lastName = passenger['last_name'] ?? "";
                final String fullName = "$firstName $lastName".trim();
                final String genderLabel = passenger['gender_label'] ?? "";
                final String age = passenger['age']?.toString() ?? "";
                final String profileImage = passenger['profile_image'] ?? "";
                final String seats = coPassenger['seats']?.toString() ?? "";
                final int userId = passenger['id'];
                final bool hasAge = age.isNotEmpty && age != "null";
                final bool hasGender = genderLabel.isNotEmpty;

                return InkWell(
                  onTap: () {
                    Get.toNamed('/profile_detail/passenger/$userId/0');
                  },
                  borderRadius: BorderRadius.circular(16),
                  child: Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: Colors.grey.shade200),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(0.04),
                          blurRadius: 10,
                          offset: const Offset(0, 4),
                        ),
                      ],
                    ),
                    child: Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        circleImageWidget(
                          width: 96.0,
                          height: 96.0,
                          imageType: "network",
                          imagePath: profileImage,
                          context: context,
                        ),
                        12.widthBox,
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              txt25Size(
                                title: fullName.isEmpty ? "Passenger" : fullName,
                                context: context,
                                textColor: primaryColor,
                              ),
                              if (hasAge || hasGender) ...[
                                4.heightBox,
                                txt20Size(
                                  title: [
                                    if (hasGender) genderLabel,
                                    if (hasAge)
                                      "$age ${controller.labelTextDetail['year_old_label'] ?? "years old"}",
                                  ].join(", "),
                                  context: context,
                                ),
                              ],
                              if (seats.isNotEmpty) ...[
                                6.heightBox,
                                txt20Size(
                                  title:
                                      "$seats ${controller.labelTextDetail['trips_card_section_seat_booked'] ?? "seats booked"}",
                                  context: context,
                                  fontFamily: bold,
                                ),
                              ],
                              10.heightBox,
                              textWithUnderLine(
                                title:
                                    controller.labelTextDetail['profile_label'] ??
                                        "Profile",
                                textSize: 20,
                                textColor: primaryColor,
                                context: context,
                                fontFamily: regular,
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                );
              },
            );
          }
        }));
  }
}



