import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

Widget addMoreSpotRideWidget({
  context,
  controller,
  screenWidth,
  bool bookingCheck = false,
  error,
}) {
  return Container(
    decoration: BoxDecoration(
      borderRadius: BorderRadius.circular(8.0),
      border: Border.all(width: 1, color: inputColor),
    ),
    child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        postRideWidget(
          title: "${controller.labelTextDetail['stop_along_the_way_label'] ?? "Add More Stops(Optional)"}",
          screenWidth: screenWidth,
          context: context,
        ),
        if (controller.spotsCount.value > 0)
          Padding(
            padding: EdgeInsets.all(getValueForScreenType<double>(
              context: context,
              mobile: 10.0,
              tablet: 10.0,
            )),
            child: ListView.separated(
              itemCount: controller.spotsCount.value,
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              separatorBuilder: (_, __) => 10.heightBox,
              itemBuilder: (context, index) {
                return InkWell(
                  onTap: bookingCheck == true
                      ? null
                      : () async {
                          await controller.openStopForm(context, index: index);
                        },
                  child: Container(
                    padding: const EdgeInsets.all(12.0),
                    decoration: BoxDecoration(
                      color: Colors.grey.shade50,
                      borderRadius: BorderRadius.circular(10.0),
                      border: Border.all(color: Colors.grey.shade300),
                    ),
                    child: Stack(
                      children: [
                        Padding(
                          padding: const EdgeInsets.only(right: 34),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              txt20Size(
                                title: controller.fromSpotControllers[index].text,
                                context: context,
                                fontFamily: bold,
                              ),
                              4.heightBox,
                              txt18Size(
                                title: controller.pickupSpotControllers[index].text,
                                context: context,
                              ),
                              4.heightBox,
                              Row(
                                children: [
                                  Flexible(
                                    child: txt18Size(
                                      title:
                                          controller.dateSpotControllers[index].text,
                                      context: context,
                                    ),
                                  ),
                                  if (controller
                                      .timeSpotControllers[index].text.isNotEmpty) ...[
                                    10.widthBox,
                                    txt16Size(
                                      title: controller
                                          .timeSpotControllers[index].text,
                                      context: context,
                                      textColor: Colors.grey.shade700,
                                    ),
                                  ],
                                ],
                              ),
                            ],
                          ),
                        ),
                        Positioned(
                          top: 0,
                          right: 0,
                          child: InkWell(
                            onTap: bookingCheck == true
                                ? null
                                : () {
                                    controller.removeNewSpot(index);
                                  },
                            child: Container(
                              padding: const EdgeInsets.all(4),
                              decoration: BoxDecoration(
                                color: Colors.red.shade50,
                                borderRadius: BorderRadius.circular(50),
                              ),
                              child: const Icon(
                                Icons.close,
                                color: Colors.red,
                                size: 18,
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                );
              },
            ),
          )
        else
          Padding(
            padding: EdgeInsets.all(getValueForScreenType<double>(
              context: context,
              mobile: 10.0,
              tablet: 10.0,
            )),
            child: txt16Size(
              title: controller.labelTextDetail['no_stops_added_label'] ??
                  "No extra stops added yet",
              context: context,
              textColor: Colors.grey.shade700,
            ),
          ),
        Obx(
          () {
            controller.stopFormPrerequisiteVersion.value;
            return Padding(
              padding: EdgeInsets.fromLTRB(
                getValueForScreenType<double>(
                  context: context,
                  mobile: 10.0,
                  tablet: 10.0,
                ),
                0,
                getValueForScreenType<double>(
                  context: context,
                  mobile: 10.0,
                  tablet: 10.0,
                ),
                getValueForScreenType<double>(
                  context: context,
                  mobile: 10.0,
                  tablet: 10.0,
                ),
              ),
              child: Align(
                alignment: Alignment.centerRight,
                child: elevatedButtonWidget(
                    enabled:
                        bookingCheck == false && controller.canAddMoreStops,
                    textWidget: txt18Size(
                        title:
                            '${controller.labelTextDetail['add_spot_button_label'] ?? "Add More Stops"}',
                        context: context,
                        textColor: Colors.white),
                    context: context,
                    onPressed: () async {
                      await controller.openStopForm(context);
                    }),
              ),
            );
          },
        ),
      ],
    ),
  );
}
