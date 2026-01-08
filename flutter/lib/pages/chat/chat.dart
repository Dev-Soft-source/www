import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/DeepLinkController.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import '../widgets/main_appbar_widget.dart';
import '../widgets/progress_circular_widget.dart';
import 'ChatController.dart';
import 'Widget/chat_card.dart';
// import 'package:flutter_localizations/flutter_localizations.dart';


class ChatPage extends GetView<ChatController> {
  const ChatPage({super.key});


  @override
  Widget build(BuildContext context) {

    Get.put(ChatController());
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: Obx(() => mainAppBarWidget(context, controller.serviceController.langId.value, controller.serviceController.langIcon.value, context.screenWidth, controller.serviceController)),
      ),
      body: Obx(() {
        if (controller.isLoading.value == true) {
          return Center(child: progressCircularWidget(context));
        } else {
          return Stack(
            children: [
              Container(
                padding: EdgeInsets.all(getValueForScreenType<double>(
                    context: context, mobile: 20, tablet: 20)),
                color: Colors.white,
                child: Column(
                  children: [

                    Row(
                      mainAxisSize: MainAxisSize.min,
                      mainAxisAlignment: MainAxisAlignment.end,
                      children: [
                        Expanded(child: txt25Size(
                          title: "${controller.labelTextDetail['main_heading'] ?? 'Chats'}",
                          context: context,
                          fontFamily: regular,
                          textColor: primaryColor,
                        )),
                        TextButton.icon(
                          onPressed: () {
                            Get.toNamed('/old_messages');
                          },
                          icon: Image.asset(
                            oldMessagesIcon,
                            height: 20,
                            width: 20,
                            color: Colors.white,
                          ),
                          label: txt22Size(
                            title: "${controller.labelTextDetail['old_messages_heading'] ?? 'Old messages'}",
                            textColor: Colors.white,
                            fontFamily: regular,
                            context: context,
                          ),
                          style: TextButton.styleFrom(
                            backgroundColor: primaryColor,
                            foregroundColor: Colors.white,// or use a light shade for subtle look
                            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(8),
                              side: BorderSide(color: primaryColor),
                            ),
                          ),
                        ),

                      ],
                    ),
                    10.heightBox,
                    controller.myChats.isEmpty ? Center(
                        child:
                        Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Image.asset(noChats),
                            txt16Size(title: "${controller.labelTextDetail['no_messages_label'] ??"You have no messages"}", context: context),
                          ],
                        )) : Expanded(
                      child: ListView.separated(
                        itemCount: controller.myChats.length,
                        itemBuilder: (context, index) {
                          String otherUser = controller.userId.toString() == controller.myChats[index]['sender']['id'].toString() ? "receiver" : "sender";
                          return chatCard(
                              context: context,
                              image: controller.myChats[index][otherUser]['profile_image'] ?? "",
                              name: "${controller.myChats[index][otherUser]['first_name'] ?? ""} ${controller.myChats[index][otherUser]['last_name'] ?? ""}",
                              controller: controller,
                              time: controller.myChats[index]['created_at'].substring(11, 16),
                              message: controller.myChats[index]['message'],
                              numberOfMessages: controller.myChats[index]['unread_count'],
                              chatObj: controller.myChats[index],
                              onTap: () {
                                Get.toNamed(
                                    '/messaging_page/${controller.myChats[index][otherUser]['id']}/0/new');
                              });
                        },
                        separatorBuilder: (context, index) {
                          return const SizedBox();
                        },
                      ),
                    ),

                  ],
                ),
              ),
              if(controller.isOverlayLoading.value == true)...[
                overlayWidget(context),
              ]
            ],
          );
        }
      }),
    );
  }
}