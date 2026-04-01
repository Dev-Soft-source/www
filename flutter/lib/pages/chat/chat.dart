import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import '../widgets/main_appbar_widget.dart';
import '../widgets/progress_circular_widget.dart';
import 'ChatController.dart';
import 'Widget/chat_card.dart';
import 'package:proximaride_app/pages/widgets/error_state_widget.dart';
// import 'package:flutter_localizations/flutter_localizations.dart';

class ChatPage extends StatelessWidget {
  const ChatPage({super.key});

  @override
  Widget build(BuildContext context) {
    final ChatController controller = Get.isRegistered<ChatController>()
        ? Get.find<ChatController>()
        : Get.put(ChatController());
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: Obx(() => mainAppBarWidget(
            context,
            controller.serviceController.langId.value,
            controller.serviceController.langIcon.value,
            context.screenWidth,
            controller.serviceController)),
      ),
      body: SafeArea(
        child: Obx(() {
          if (controller.errorStateManager.isLoading.value) {
            return Center(child: progressCircularWidget(context));
          } else if (controller.errorStateManager.hasError.value) {
            return ErrorStateWidget(
              message: controller.errorStateManager.errorMessage.value,
              errorType: controller.errorStateManager.errorType.value,
              onRetry: () {
                if (controller.errorStateManager.onRetry.value != null) {
                  controller.errorStateManager.onRetry.value!();
                }
              },
            );
          } else if (controller.isLoading.value == true) {
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
                          Expanded(
                              child: txt25Size(
                            title:
                                "${controller.labelTextDetail['main_heading'] ?? 'Chats'}",
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
                              title:
                                  "${controller.labelTextDetail['old_messages_heading'] ?? 'Old messages'}",
                              textColor: Colors.white,
                              fontFamily: regular,
                              context: context,
                            ),
                            style: TextButton.styleFrom(
                              backgroundColor: primaryColor,
                              foregroundColor: Colors
                                  .white, // or use a light shade for subtle look
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 12, vertical: 12),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(4),
                                side: BorderSide(color: primaryColor),
                              ),
                            ),
                          ),
                        ],
                      ),
                      10.heightBox,
                      controller.myChats.isEmpty
                          ? Center(
                              child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Image.asset(noChats),
                                txt20Size(
                                    title:
                                        "${controller.labelTextDetail['no_messages_label'] ?? "You have no messages"}",
                                    context: context),
                              ],
                            ))
                          : Expanded(
                              child: ListView.separated(
                                itemCount: controller.myChats.length,
                                itemBuilder: (context, index) {
                                  final chat = controller.myChats[index];
                                  final sender =
                                      chat['sender'] is Map ? chat['sender'] : {};
                                  final receiver = chat['receiver'] is Map
                                      ? chat['receiver']
                                      : {};
                                  final senderId =
                                      sender['id']?.toString() ?? "";
                                  final createdAt =
                                      chat['created_at']?.toString() ?? "";
                                  final otherUser =
                                      controller.userId.toString() == senderId
                                          ? receiver
                                          : sender;
                                  return chatCard(
                                      context: context,
                                      image:
                                          otherUser['profile_image'] ?? "",
                                      name:
                                          "${otherUser['first_name'] ?? ""} ${otherUser['last_name'] ?? ""}",
                                      controller: controller,
                                      time: createdAt.length >= 16
                                          ? createdAt.substring(11, 16)
                                          : "",
                                      message: chat['message'],
                                      numberOfMessages: chat['unread_count'],
                                      chatObj: chat,
                                      onDelete: () async {
                                        await controller.deleteChat(chat);
                                      },
                                      onTap: () {
                                        // Use ride_id from chat object if available, otherwise use 0
                                        var chatRideId =
                                            chat['ride_id']?.toString() ??
                                            chat['rideId']?.toString() ??
                                            '0';
                                        final otherUserId =
                                            otherUser['id']?.toString() ?? '0';
                                        Get.toNamed(
                                            '/messaging_page/$otherUserId/$chatRideId/new');
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
                if (controller.isOverlayLoading.value == true) ...[
                  overlayWidget(context),
                ]
              ],
            );
          }
        }),
      ),
    );
  }
}


