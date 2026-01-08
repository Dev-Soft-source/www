
import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/book_seat/BookSeatController.dart';
import 'package:proximaride_app/pages/widgets/add_card_widget.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';


class BookAddCardPage extends StatelessWidget {
  const BookAddCardPage({super.key});

  @override

  Widget build(BuildContext context) {
    var controller =  Get.find<BookSeatController>();
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: secondAppBarWidget(title: "Add new card", context: context),
        leading: const BackButton(
            color: Colors.white
        ),
      ),

      body: Obx(() {
        if(controller.isLoading.value == true){
          return Center(child: progressCircularWidget(context));
        }else{
          return Stack(
            children: [
              addCardWidget(context: context, controller: controller),
              Align(
                alignment: Alignment.bottomCenter,
                child: Container(
                  padding: const EdgeInsets.all(10.0),
                  width: context.screenWidth,
                  height: 70,
                  color: Colors.grey.shade100,
                  child: SizedBox(
                    width: context.screenWidth,
                    height: 50,
                    child: elevatedButtonWidget(
                      textWidget: txt22Size(
                          title: "Save",
                          textColor: Colors.white,
                          context: context,
                          fontFamily: regular),
                      onPressed: () async {
                        // controller.addCard();
                      },
                    ),
                  ),
                ),
              ),
              if (controller.isOverlayLoading.value == true) ...[
                overlayWidget(context)
              ]
            ],
          );
        }
      })
    );
  }
}

