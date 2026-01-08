 import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/profile_detail/ProfileDetailController.dart';
import 'package:proximaride_app/pages/profile_detail/widget/driver_widget/driver_info_widget.dart';
import 'package:proximaride_app/pages/profile_detail/widget/driver_widget/vehicle_info_widget.dart';
import 'package:proximaride_app/pages/profile_detail/widget/mini_bio_widget.dart';
import 'package:proximaride_app/pages/profile_detail/widget/passenger_widget/passenger_info_widget.dart';
import 'package:proximaride_app/pages/profile_detail/widget/user_widget/user_info_widget.dart';
import 'package:proximaride_app/pages/profile_detail/widget/user_widget/user_passenger_driven_widget.dart';
import 'package:proximaride_app/pages/widgets/review_card.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

class ProfileDetailPage extends GetView<ProfileDetailController> {
  const ProfileDetailPage({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(ProfileDetailController());
    return Obx(
      () => Scaffold(
          appBar: AppBar(
            backgroundColor: primaryColor,
            title: secondAppBarWidget(
                title: controller.profileType == "driver"
                ? controller.driverTitle.value
                : controller.profileType == "passenger"
                ? "${controller.userProfile['first_name'] ?? ""}'s ${controller.labelTextDetail['profile_label'] ?? "profile"}"
                : "${controller.userProfile['first_name'] ?? ""}'s ${controller.labelTextDetail['profile_label'] ?? "profile"}",
                context: context),
            leading: const BackButton(color: Colors.white),
          ),
          body: Obx(() {
            if (controller.isLoading.value == true) {
              return Center(child: progressCircularWidget(context));
            } else {
              return SingleChildScrollView(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(
                      padding: EdgeInsets.all(getValueForScreenType<double>(
                        context: context,
                        mobile: 15.0,
                        tablet: 15.0,
                      )),
                      child: controller.profileType == "driver"
                          ? driverInfoWidget(
                              imagePath:
                                  "${controller.ride['driver'] != null ? controller.ride['driver']['profile_image'] : ""}",
                              context: context,
                              passengerDrivenLabel: "${controller.labelTextDetail['passenger_driven_label'] ?? "Passenger driven"}",
                              joinedLabel: "${controller.labelTextDetail['joined_label'] ?? "Joined"}",
                              driverName:
                                  "${controller.ride['driver'] != null ? controller.ride['driver']['first_name'] : ""} ${controller.ride['driver'] != null ? controller.ride['driver']['last_name'] : ""}",
                          driven: controller.ride['driver'] != null ? '${controller.ride['driver']['passenger_driven']} ${controller.labelTextDetail['passenger_label'] ?? "passengers"}' : '',

                          gender:
                                  "${controller.ride['driver'] != null ? controller.ride['driver']['gender_label'] : ""}",
                              date:
                                  "${controller.ride['driver'] != null ? controller.ride['driver']['created_at'] : ""}")
                          : controller.profileType == "passenger"
                            ? passengerInfoWidget(
                                imagePath:
                                    "${controller.userProfile['profile_image'] ?? ""}",
                                context: context,
                                yearOldLabel: "${controller.labelTextDetail['year_old_label'] ?? "years old"}",
                                joinedLabel: "${controller.labelTextDetail['joined_label'] ?? "Joined"}",
                                passengerName:
                                    "${controller.userProfile['first_name'] ?? ""} ${controller.userProfile['last_name'] ?? ""}",
                                gender:
                                    "${controller.userProfile['gender_label'] ?? ""}",
                                date:
                                    "${controller.userProfile['created_at'] ?? ""}",
                                age:
                                    "${controller.userProfile['age'] ?? ""}")
                            : userInfoWidget(
                                context: context,
                                editProfileLabel: "${controller.labelTextDetail['edit_profile_text'] ?? "Edit profile"}",
                                imagePath:
                                    "${controller.serviceController.loginUserDetail['profile_image']}",
                                userName:
                                    "${controller.userProfile['first_name']} ${controller.userProfile['last_name']}"),
                    ),
                    10.heightBox,
                    Container(
                        padding: EdgeInsets.only(
                            left: getValueForScreenType<double>(
                          context: context,
                          mobile: 15.0,
                          tablet: 15.0,
                        )),
                        child: txt25Size(
                            title: "${controller.labelTextDetail['min_bio_label'] ?? "Mini bio"}",
                            textColor: textColor,
                            fontFamily: regular,
                            context: context)),
                    miniBioWidget(
                        context: context,
                        miniBio:
                            "${controller.profileType == "user" || controller.profileType == "passenger" ? controller.userProfile['about'] : controller.ride['driver'] != null ? controller.ride['driver']['about'] : ""}"),
                    10.heightBox,
                    if (controller.profileType == "driver" &&
                        controller.pageType == "0") ...[
                      Container(
                          padding: EdgeInsets.only(
                              left: getValueForScreenType<double>(
                            context: context,
                            mobile: 15.0,
                            tablet: 15.0,
                          )),
                          child: txt25Size(
                              title: "${controller.labelTextDetail['vehicle_info_label'] ?? "Vehicle info"}",
                              textColor: textColor,
                              fontFamily: regular,
                              context: context)),
                      vehicleInfoWidget(
                          context: context,
                          vehicleDetail:
                              "${controller.ride['year']} ${controller.ride['make']} ${controller.ride['model']}",
                          licenseNumber: "${controller.ride['license_no']}",
                          vehicleImage: "${controller.ride['car_image']}",
                          type: "${controller.ride['car_type']}"),
                    ],
                    if (controller.profileType == "driver" &&
                        controller.pageType == "1") ...[
                      userPassengerDrivenWidget(
                          passengerDriven:
                              controller.passengerDriven.value.toString(),
                          rideTaken: controller.rideTaken.value.toString(),
                          kmShared: controller.kmShared.value.toString(),
                          kmSharedLabel: "${controller.labelTextDetail['km_shared_label'] ?? "KM shared"}",
                          passengerDrivenLabel: "${controller.labelTextDetail['passenger_driven_label'] ?? "Passenger driven"}",
                          ridesTakenLabel: "${controller.labelTextDetail['rides_taken_label'] ?? "Rides taken"}",
                          context: context),
                      10.heightBox,
                    ],
                    if (controller.profileType == "user" ||
                        controller.profileType == "passenger") ...[
                      userPassengerDrivenWidget(
                          passengerDriven:
                              controller.passengerDriven.value.toString(),
                          rideTaken: controller.rideTaken.value.toString(),
                          kmShared: controller.kmShared.value.toString(),
                          kmSharedLabel: "${controller.labelTextDetail['km_shared_label'] ?? "KM shared"}",
                          passengerDrivenLabel: "${controller.labelTextDetail['passenger_driven_label'] ?? "Passenger driven"}",
                          ridesTakenLabel: "${controller.labelTextDetail['rides_taken_label'] ?? "Rides taken"}",
                          context: context),
                      10.heightBox,
                    ],
                    Container(
                      padding: EdgeInsets.only(
                          left: getValueForScreenType<double>(
                        context: context,
                        mobile: 15.0,
                        tablet: 15.0,
                      )),
                      child: txt25Size(
                          title: "${controller.totalReviews} ${controller.labelTextDetail['review_label'] ?? "Reviews"}",
                          textColor: textColor,
                          fontFamily: regular,
                          context: context),
                    ),
                    10.heightBox,
                    Container(
                      padding: EdgeInsets.only(
                        left: getValueForScreenType<double>(
                          context: context,
                          mobile: 15.0,
                          tablet: 15.0,
                        ),
                        right: getValueForScreenType<double>(
                          context: context,
                          mobile: 15.0,
                          tablet: 15.0,
                        ),
                      ),
                      child: ListView.separated(
                          physics: const NeverScrollableScrollPhysics(),
                          shrinkWrap: true,
                          itemCount: controller.reviews.length,
                          itemBuilder: (context, index) {
                            return InkWell(
                              onTap: () {
                                Get.toNamed(
                                    '/review_detail/${controller.reviews[index]['id']}/from/user');
                              },
                              child: reviewCard(
                                reviewCardColor: index % 2 == 0
                                    ? Colors.white
                                    : Colors.grey.shade100,
                                context: context,
                                controller: controller,
                                bottomSheetHeight:
                                    (context.screenHeight / 2) + 50,
                                name: controller.reviews[index]['from']
                                    ['first_name'],
                                review: controller.reviews[index]['review'],
                                image: controller.reviews[index]['from']
                                        ['profile_image'] ??
                                    (controller.reviews[index]['from']
                                                ['gender'] ==
                                            'female'
                                        ? defaultFemaleImage
                                        : defaultMaleImage),
                                addedOn: controller.reviews[index]['added_on'],
                                rating: controller.reviews[index]['average_rating'] != null ? double.parse(controller.reviews[index]['average_rating']) : 0.0,
                                replied: (controller.reviews[index]
                                            ['replies'] !=
                                        null)
                                    ? controller.reviews[index]['replies']
                                        ['reply']
                                    : '',
                                showReply: (controller.reviews[index]
                                            ['replies'] !=
                                        null)
                                    ? false
                                    : true,
                                profileType: controller.profileType,
                                repliedLabel: "${controller.labelTextDetail['replied_label'] ?? "replied"}",
                                responseLabel: "${controller.labelTextDetail['response_label'] ?? "response"}",
                                replyLabel: "${controller.labelTextDetail['reply_label'] ?? "Reply"}",
                                replyHeadingLabel:"${controller.labelTextDetail['reply_heading_label'] ?? "Reply"}",
                                replyPlaceholder: "${controller.labelTextDetail['reply_placeholder'] ?? "Enter your reply"}",
                                replySubmitButtonLabel:"${controller.labelTextDetail['reply_submit_button_label'] ?? "Submit"}",
                                onSubmit: () {
                                  controller.addReply(
                                      controller.reviews[index]['id'],
                                      controller.replyTextController.text);
                                  controller.replyTextController.clear();
                                  Get.back();
                                },
                                screenWidth: context.screenWidth,
                                screenHeight: context.screenHeight,
                                index: index,
                              ),
                            );
                          },
                          separatorBuilder: (context, index) {
                            return const SizedBox();
                          }),
                    ),
                    if(controller.reviews.length >= 3)...[
                      Padding(
                        padding: const EdgeInsets.all(15.0),
                        child: Align(
                          alignment: Alignment.centerRight,
                          child: InkWell(
                              onTap: () {
                                if (controller.profileType == "driver") {
                                  Get.toNamed(
                                      '/review/${controller.profileType}/${controller.ride['driver'] != null ? controller.ride['driver']['id'] : "0"}');
                                } else {
                                  Get.toNamed(
                                      '/review/${controller.profileType}/${controller.profileId}');
                                }
                              },
                              child: textWithUnderLine(
                                  title: "${controller.labelTextDetail['link_review_label'] ?? "View all reviews"}",
                                  textSize: 22,
                                  textColor: primaryColor,
                                  context: context,
                                  fontFamily: regular)),
                        ),
                      )
                    ]
                  ],
                ),
              );
            }
          })),
    );
  }
}
