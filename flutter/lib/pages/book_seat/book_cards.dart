
import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/book_seat/BookSeatController.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/payment_card_body_Widget.dart';
import 'package:proximaride_app/pages/widgets/payment_card_button_Widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';


class BookCardsPage extends StatelessWidget {
  const BookCardsPage({super.key});

  @override

  Widget build(BuildContext context) {
    var controller =  Get.find<BookSeatController>();
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: secondAppBarWidget(title: "${controller.labelTextDetail['select_card_label'] ?? "Select your card"}", context: context),
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
              Container(
                padding: const EdgeInsets.all(15.0),
                child: SingleChildScrollView(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                        ListView.separated(
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          itemCount: controller.cards.length,
                          itemBuilder: (context, index){
                            return Obx(()=>
                              paymentCardBodyWidget(
                                  selectedCardId: controller.selectedCardId.value,
                                  cardId: controller.cards[index]['id'],
                                  cardNumber: controller.cards[index]['card_number'] ?? "",
                                  cardType: controller.cards[index]['card_type'] ?? "",
                                  context: context,
                                  onChanged: (data){
                                    controller.selectedCardId.value = data!;
                                  }
                              ),
                            );
                          },
                          separatorBuilder: (context, index){
                            return 10.heightBox;
                          },

                        ),

                      120.heightBox
                    ],
                  ),
                ),
              ),
              Align(
                alignment: Alignment.bottomCenter,
                child: paymentCardButtonWidget(
                  btn1Title: "${controller.labelTextDetail['add_card_label'] ?? "Add new card"}",
                  onPressed1: () async{
                    await controller.clearCardFields();
                    // Get.toNamed("/book_add_cards");
                    Get.toNamed('/add_card/add');
                  },
                  btn2Title: "${controller.labelTextDetail['pay_label'] ?? "Pay"}",
                  onPressed2: () async{
                    await controller.bookingRidePaymentType();
                  },
                  context: context,
                  screenWidth: context.screenWidth
                ),
              ),
              if(controller.isOverlayLoading.value == true)...[
                overlayWidget(context)
              ],
            ],
          );
        }
      })
    );
  }
}

