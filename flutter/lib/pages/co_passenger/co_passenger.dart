import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/co_passenger/CoPassengerController.dart';
import 'package:proximaride_app/pages/widgets/error_state_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

class CoPassengerPage extends GetView<CoPassengerController> {
  const CoPassengerPage({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(CoPassengerController());
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
            return Container(
              padding: const EdgeInsets.all(15.0),
              child: Wrap(
                spacing: 4.0,
                runSpacing: 4.0, // Adjust run spacing as needed
                children: controller.coPassengers.map((coPassenger) {
                  // final String userImage = coPassenger['passenger']['profile_image'];
                  final String userName =
                      coPassenger['passenger']['first_name'];
                  final int userId = coPassenger['passenger']['id'];

                  return InkWell(
                    onTap: () {
                      Get.toNamed('/profile_detail/passenger/$userId/0');
                    },
                    child:
                        txt22SizeCapitalized(context: context, title: userName),
                  );
                }).toList(), // Convert the map result to a List<Widget>
              ),
            );
          }
        }));
  }
}

