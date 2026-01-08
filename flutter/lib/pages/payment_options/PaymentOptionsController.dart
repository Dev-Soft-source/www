import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/payment_options/PaymentOptionsProvider.dart';
import 'package:proximaride_app/services/service.dart';

class PaymentOptionController extends GetxController {
  final serviceController = Get.find<Service>();
  var errorList = List.empty(growable: true).obs;
  var isLoading = false.obs;
  var isOverlayLoading = false.obs;
  var isScrollLoading = false.obs;
  var page = 1;
  var pageLimit = 6;
  var noMoreData = false.obs;
  ScrollController scrollController = ScrollController();
  late TextEditingController cardNameController,
      cardNumberController,
      cvvCodeController,
      addressController;

  var addEditType = "";

  var totalYear = 70;
  var startYear = DateTime.now().year;
  var makePrimaryCard = false.obs;
  var editCardId = 0;
  var primaryCardActive = 0.obs;

  var labelTextDetail = {}.obs;
  var popupTextDetail = {}.obs;

  var cards = List<dynamic>.empty(growable: true).obs;

  @override
  void onInit() async {
    // TODO: implement onInit
    super.onInit();

    getCards();
    cardNameController = TextEditingController();
    cardNumberController = TextEditingController();
    cvvCodeController = TextEditingController();
    addressController = TextEditingController();
  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
    scrollController.dispose();
    cardNameController.dispose();
    cardNumberController.dispose();
    cvvCodeController.dispose();
    addressController.dispose();
  }

  void getType() {
    addEditType = Get.parameters['type'] ?? "";
  }

  getCards() async {
    try {
      isLoading(true);
      PaymentOptionsProvider().getCards(1, 10, serviceController.token, serviceController.langId.value).then(
          (resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null &&
              resp['data']['cards'] != null &&
              resp['data']['cards']['data'] != null) {
            cards.addAll(resp['data']['cards']['data']);
          }
          if(resp['data'] != null && resp['data']['paymentOptionPage'] != null){
            labelTextDetail.addAll(resp['data']['paymentOptionPage']);
          }
          if(resp['data'] != null && resp['data']['messages'] != null){
            popupTextDetail.addAll(resp['data']['messages']);
          }
        }
        isLoading(false);
      }, onError: (err) {
        isLoading(false);

serviceController.showDialogue(err.toString());


      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString());
    }
  }

  deleteCard(id) async {
    bool isConfirmed = await serviceController.showConfirmationDialog("${popupTextDetail['delete_card_message'] ?? "Are you sure you want to delete this card"}");

    if (isConfirmed) {
      try {
        isOverlayLoading(true);
        PaymentOptionsProvider().deleteCard(serviceController.token, id).then(
            (resp) async {
          errorList.clear();
          if (resp['status'] != null && resp['status'] == "Error") {
            serviceController.showDialogue(resp['message'].toString());
          } else if (resp['status'] != null && resp['status'] == "Success") {
            cards.removeWhere((element) => element['id'] == id);
            cards.refresh();
            serviceController.showDialogue(resp['message'].toString());
          }
          isOverlayLoading(false);
        }, onError: (err) {
          isOverlayLoading(false);

serviceController.showDialogue(err.toString());


        });
      } catch (exception) {
        isOverlayLoading(false);

      serviceController.showDialogue(exception.toString());
      }
    }
  }

  setPrimaryCard(cardId, index) async{

    try{
      PaymentOptionsProvider().setPrimaryCard(serviceController.token, cardId).then(
              (resp) async {
            if (resp['status'] != null && resp['status'] == "Success") {
              cards[index]['primary_card'] = "1";
              if(cards.length > 1){
                var obj = cards[index];
                cards[0]['primary_card'] = "0";
                cards[index] = cards[0];
                cards[0] = obj;
              }
              cards.refresh();
              serviceController.showDialogue(resp['message'].toString());
            }
            isLoading(false);
          }, onError: (err) {
        isLoading(false);

        serviceController.showDialogue(err.toString());
      });

    }catch(exception){
      serviceController.showDialogue(exception.toString());
    }

  }




}
