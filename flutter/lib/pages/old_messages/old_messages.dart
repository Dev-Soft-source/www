import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import '../chat/Widget/chat_card.dart';
import '../widgets/progress_circular_widget.dart';
import 'OldMessagesController.dart';

class OldMessages extends GetView<OldMessagesController> {
  const OldMessages({super.key});


  @override
  Widget build(BuildContext context) {
    Get.put(OldMessagesController());
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: Obx(() => secondAppBarWidget(context: context, title: "${controller.labelTextDetail['old_chat_page_main_heading'] ?? "Old messages"}")),
        leading: const BackButton(color: Colors.white),
      ),
      body: Obx(() {
        if (controller.isLoading.value == true) {
          return Center(child: progressCircularWidget(context));
        } else {
          return Container(
              padding: EdgeInsets.all(getValueForScreenType<double>(
                  context: context, mobile: 20, tablet: 30)),
              color: Colors.white,
              child: Column(
                children: [
                  SingleChildScrollView(
                    child: ListView.separated(
                        physics: const NeverScrollableScrollPhysics(),
                        shrinkWrap: true,
                        itemCount: controller.oldMessagesList.length,
                        itemBuilder: (context, index) {
                          String otherUser = controller.userId.toString() == controller.oldMessagesList[index]['sender']['id'].toString() ? "receiver" : "sender";
                          return chatCard(
                              context: context,
                              image:  controller.oldMessagesList[index][otherUser]['profile_image'] ?? "",
                              name: "${controller.oldMessagesList[index][otherUser]['first_name'] ?? ""} ${controller.oldMessagesList[index][otherUser]['last_name'] ?? ""}",
                              controller: controller,
                              time: controller.oldMessagesList[index]['created_at'].substring(11, 16),
                              message: controller.oldMessagesList[index]['message'],
                              numberOfMessages: 0,
                              onTap: () {
                                Get.toNamed(
                                    '/messaging_page/${controller.oldMessagesList[index][otherUser]['id']}/${controller.oldMessagesList[index]['ride_id']}/old');
                              });
                        },
                        separatorBuilder: (context, index) {
                          return const SizedBox();
                        }),
                  ),
                  if(controller.oldMessagesList.isEmpty)...[
                    Center(
                        child:
                        Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Image.asset(noChats),
                            txt16Size(title: "${controller.labelTextDetail['old_chat_page_no_messages_label'] ?? "You have no old messages"}", context: context),
                          ],
                        )),
                  ]
                ],
              ));
        }
      }),
    );
  }
}
