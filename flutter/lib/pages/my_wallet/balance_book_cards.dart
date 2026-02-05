
import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/my_wallet/MyWalletController.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/payment_card_body_Widget.dart';
import 'package:proximaride_app/pages/widgets/payment_card_button_Widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';


class BalanceBookCardsPage extends StatelessWidget {
  const BalanceBookCardsPage({super.key});

  @override

  Widget build(BuildContext context) {
    var controller =  Get.find<MyWalletController>();
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
                  btn2Title: controller.primaryCardCheck.value == true ? "${controller.labelTextDetail['1_tap_btn_label'] ?? "1 tap buy"}" : "${controller.labelTextDetail['pay_label'] ?? "Pay"}",
                  onPressed2: () async{
                    await controller.buyTopUpBalance();
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

