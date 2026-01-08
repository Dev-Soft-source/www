import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/DeepLinkController.dart';
import 'package:proximaride_app/pages/messaging_page/widgets/message_container.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import '../widgets/network_cache_image_widget.dart';
import '../widgets/progress_circular_widget.dart';
import 'MessagingController.dart';

class MessagingPage extends GetView<MessagingController> {
  const MessagingPage({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put((MessagingController()));
    return Scaffold(
        appBar: AppBar(
          title: Container(

            padding: const EdgeInsets.only(left: 0.0, top: 10.0, bottom: 10.0, right: 0.0),
            child: Obx(() {
              return Row(
                mainAxisAlignment: MainAxisAlignment.start,
                children: [
                  Container(
                    decoration: BoxDecoration(
                      color: Colors.blueAccent.shade100.withOpacity(0.3),
                      borderRadius: BorderRadius.circular(50.0),
                    ),
                    child: ClipRRect(
                      child: ClipRRect(
                        borderRadius: BorderRadius.circular(50.0),
                        child: networkCacheImageWidget(
                          controller.chatUserInfo['profile_image'] ?? "",
                          BoxFit.contain,
                          50.0,
                          50.0,
                        ),
                      ),
                    ),
                  ),
                  10.widthBox,
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        txt22SizeEllipsis(
                            title: "${controller.chatUserInfo['first_name'] ?? ""} ${controller.chatUserInfo['last_name'] ?? ""}",
                            fontFamily: regular,
                            textColor: Colors.black,
                            context: context),
                        // if(controller.chatUserInfo['online'].toString() == "1")...[
                        //   txt14Size(
                        //       title: 'Online',
                        //       textColor: primaryColor,
                        //       fontFamily: bold,
                        //       context: context)
                        // ],
                      ],
                    ),
                  ),
                ],
              );
            })
          ),
          leading: const BackButton(color: Colors.black),
          bottom: PreferredSize(
            preferredSize: const Size.fromHeight(10.0),
            child: Container(
              decoration: const BoxDecoration(
                border: Border(
                  bottom: BorderSide(
                    color: Colors.grey,
                    width: 1.0,
                  ),
                ),
              ),
            ),
          ),
        ),
        body: Obx(() {
          if (controller.isLoading.value == true) {
            return Center(child: progressCircularWidget(context));
          } else {
            return Stack(
              children: [
                Container(
                  padding: const EdgeInsets.all(10),
                  child: Stack(
                    children: [
                      SingleChildScrollView(
                        reverse: true,
                        child: Container(
                          padding: EdgeInsets.all(getValueForScreenType<double>(
                              context: context, mobile: 20, tablet: 30)),
                          color: Colors.white,
                          child: Column(
                            children: [
                              for (var index = 0;
                              index < controller.messagesList.length;
                              index++) ...[
                                Align(
                                  alignment: controller.messagesList[index]
                                  ['sender']['id'] ==
                                      controller.userId
                                      ? Alignment.centerRight
                                      : Alignment.centerLeft,
                                  child: messageContainer(
                                    context: context,
                                    message: controller.messagesList[index]
                                    ['message'],
                                    from: controller.messagesList[index]
                                    ['redirect'].toString() == "1" ? controller.messagesList[index]['ride_detail']['departure'].toString() : "",
                                    to: controller.messagesList[index]
                                    ['redirect'].toString() == "1" ? controller.messagesList[index]['ride_detail']['destination'].toString() : "",
                                    date: controller.messagesList[index]
                                  ['redirect'].toString() == "1" ? controller.messagesList[index]['ride_detail']['date'].toString() : "",
                                    rideTime: controller.messagesList[index]
                                    ['redirect'].toString() == "1" ? controller.messagesList[index]['ride_detail']['time'].toString() : "",
                                    onTap: controller.messagesList[index]
                                    ['redirect'].toString() == "1" ? (){
                                      Get.toNamed('/trip_detail/${controller.messagesList[index]
                                      ['ride_id']}/findRide/findRide/${controller.messagesList[index]
                                      ['ride_detail_id']}');
                                    } : null,
                                    time: controller.messagesList[index]
                                    ['created_at']
                                        .substring(11, 16),
                                    msgType: controller.messagesList[index]
                                    ['sender']['id'] ==
                                        controller.userId
                                        ? 1
                                        : 2,
                                  ),
                                ),
                                10.heightBox,
                              ],
                              150.heightBox,
                            ],
                          ),
                        ),
                      ),
                      if(controller.type != "old")
                        Align(
                          alignment: Alignment.bottomCenter,
                          child: Container(
                            decoration: BoxDecoration(
                              color: Colors.grey.shade100,
                              borderRadius: BorderRadius.circular(30),
                            ),
                            child: Row(
                              children: [
                                Expanded(
                                  flex: 6,
                                  child: Padding(
                                    padding: const EdgeInsets.symmetric(horizontal: 10.0), // Add padding
                                    child: TextFormField(
                                      maxLines: 4,
                                      controller: controller.typedMessageController,
                                      decoration: InputDecoration(
                                        hintText: "${controller.labelTextDetail['type_message_placeholder'] ?? "Type your message"}", // Add hint text
                                        enabledBorder: InputBorder.none, // Remove all borders
                                        focusedBorder: InputBorder.none, // Remove focus border too
                                      ),
                                      style: const TextStyle(
                                        fontSize: 16,
                                        fontFamily: regular,
                                      ),
                                      keyboardType: TextInputType.visiblePassword,
                                      textInputAction: TextInputAction.done,
                                    ),
                                  ),
                                ),
                                Expanded(
                                  flex: 1,
                                  child: InkWell(
                                    onTap: () {controller.sendMessage();},
                                    child: Image.asset(sendMessageIcon),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                    ],
                  ),
                ),
                if(controller.isOverlayLoading.value == true)...[
                  overlayWidget(context)
                ]
              ],
            );

          }
        }
        ));
  }
}
