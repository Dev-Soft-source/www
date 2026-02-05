import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:fluttertoast/fluttertoast.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/chat/chat.dart';
import 'package:proximaride_app/pages/my_profile/my_profile.dart';
import 'package:proximaride_app/pages/my_trips/MyTripController.dart';
import 'package:proximaride_app/pages/my_trips/my_trip.dart';
import 'package:proximaride_app/pages/navigation/NavigationController.dart';

class NavigationPage extends GetView<NavigationController> {
  const NavigationPage({super.key});

  @override
  Widget build(BuildContext context) {
    var pages = [const ChatPage(), const MyTripsPage(), const MyProfilePage()];
    Get.put(NavigationController());
    return PopScope(
      canPop: true, //When false, blocks the current route from being popped.
      onPopInvoked: (didPop) {
        // ignore: unused_local_variable
        var closed = true;
        if (controller.currentNavIndex.value == 1) {
          controller.currentNavIndex.value = 0;
          controller.closedApp.value = 0;
          closed = false;
        } else if (controller.currentNavIndex.value == 2) {
          controller.currentNavIndex.value = 0;
          controller.closedApp.value = 0;
          closed = false;
        } else if (controller.currentNavIndex.value == 3) {
          controller.currentNavIndex.value = 0;
          controller.closedApp.value = 0;
          closed = false;
        } else if (controller.currentNavIndex.value == 4) {
          controller.currentNavIndex.value = 0;
          controller.closedApp.value = 0;
          closed = false;
        } else {
          if (controller.closedApp.value == 2) {
            closed = true;
          } else {
            controller.closedApp.value = controller.closedApp.value + 1;
            controller.serviceController
                .showToast("Double Tap To Exit App", ToastGravity.CENTER);
            closed = false;
          }
        }
      },
      child: Scaffold(
        body: Column(
          children: [
            Obx(() => Expanded(
                child: pages.elementAt(controller.currentNavIndex.value)))
          ],
        ),
        bottomNavigationBar: Obx(
          () => BottomNavigationBar(
            currentIndex: controller.currentNavIndex.value,
            type: BottomNavigationBarType.fixed,
            backgroundColor: Colors.grey.shade100,
            selectedItemColor: primaryColor,
            selectedLabelStyle: const TextStyle(fontFamily: bold),
            selectedFontSize: 20,
            unselectedLabelStyle: const TextStyle(fontFamily: bold),
            unselectedFontSize: 20,
            items: [
              BottomNavigationBarItem(
                  icon: Image.asset(chatOutLineImage,
                      width: 40, color: Colors.grey.shade800),
                  label: controller.serviceController.navigationChatLabel.value,
                  activeIcon: Image.asset(chatFillImage, width: 40)),
              BottomNavigationBarItem(
                  icon: Image.asset(myTripsOutlineImage,
                      width: 40, color: Colors.grey.shade800),
                  label:
                      controller.serviceController.navigationMyTripLabel.value,
                  activeIcon: Image.asset(myTripsFillImage, width: 40)),
              BottomNavigationBarItem(
                  icon: controller.serviceController
                              .loginUserDetail['profile_image'] !=
                          null
                      ? CircleAvatar(
                          child: ClipRRect(
                            borderRadius: BorderRadius.circular(50.0),
                            child: CachedNetworkImage(
                              imageUrl:
                                  "${controller.serviceController.loginUserDetail['profile_image']}",
                              width: double.infinity,
                              height: double.infinity,
                              fit: BoxFit.cover,
                              errorWidget: (context, url, error) => controller
                                          .serviceController
                                          .loginUserDetail['gender'] ==
                                      'female'
                                  ? Image.asset(defaultFemaleImage,
                                      width: double.infinity,
                                      height: double.infinity,
                                      fit: BoxFit.cover)
                                  : Image.asset(defaultMaleImage,
                                      width: double.infinity,
                                      height: double.infinity,
                                      fit: BoxFit.cover),
                            ),
                          ),
                        )
                      : Image.asset(profileOutLineImage,
                          width: 40, color: Colors.grey.shade800),
                  label: controller
                      .serviceController.navigationMyProfileLabel.value,
                  activeIcon: controller.serviceController
                              .loginUserDetail['profile_image'] !=
                          null
                      ? CircleAvatar(
                          child: ClipRRect(
                            borderRadius: BorderRadius.circular(50.0),
                            child: CachedNetworkImage(
                                imageUrl:
                                    "${controller.serviceController.loginUserDetail['profile_image']}",
                                width: double.infinity,
                                height: double.infinity,
                                fit: BoxFit.cover,
                                errorWidget: (context, url, error) => controller
                                            .serviceController
                                            .loginUserDetail['gender'] ==
                                        'female'
                                    ? Image.asset(defaultFemaleImage,
                                        width: double.infinity,
                                        height: double.infinity,
                                        fit: BoxFit.cover)
                                    : Image.asset(defaultMaleImage,
                                        width: double.infinity,
                                        height: double.infinity,
                                        fit: BoxFit.cover)),
                          ),
                        )
                      : Image.asset(profileFillImage,
                          width: double.infinity,
                          height: double.infinity,
                          fit: BoxFit.cover)),
            ],
            onTap: (value) {
              bool isRegistered = Get.isRegistered<MyTripController>();
              if (isRegistered == true) {
                var tripController = Get.find<MyTripController>();
                tripController.tripTabController.index = 0;
                tripController.rideTabController.index = 0;
              }
              controller.currentNavIndex.value = value;
            },
          ),
        ),
      ),
    );
  }
}
