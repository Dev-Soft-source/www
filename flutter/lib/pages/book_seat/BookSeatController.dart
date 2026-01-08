
import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:flutter_paypal_pay/flutter_paypal_pay.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/book_seat/BookSeatProvider.dart';
import 'package:proximaride_app/pages/edit_profile/EditProfileProvider.dart';
import 'package:proximaride_app/pages/my_wallet/MyWalletController.dart';
import 'package:proximaride_app/pages/payment_options/PaymentOptionsProvider.dart';
import 'package:proximaride_app/pages/post_ride/PostRideProvider.dart';
import 'package:proximaride_app/services/service.dart';

class BookSeatController extends GetxController {
  final serviceController = Get.find<Service>();
  var isLoading = false.obs;
  var isOverlayLoading = false.obs;
  var tripId = "";
  var rideDetailId = "";
  var seatAvailable = 0.obs;
  var ride = {}.obs;
  var setting = {}.obs;
  var stateTax = 0.0.obs;
  late TextEditingController messageDriverTextEditingController;
  var cards = List<dynamic>.empty(growable: true).obs;
  var errorList = List.empty(growable: true).obs;
  var errors = [].obs;
  var selectedCardId = 0.obs;
  var pageLimit = 15;
  var page = 1;
  var cardType = "".obs;
  var month = "".obs;
  var year = "".obs;
  var totalYear = 70;
  var startYear = 2024;
  var makePrimaryCard = false.obs;
  var alreadyBookedSeat = 0.obs;
  var currentUserBookedSeat = 0.obs;
  var captureId = "";
  var userDetail = {}.obs;
  var cityDetail = {}.obs;
  var stateDetail = {}.obs;
  var countryDetail = {}.obs;
  var policyType = 'standard'.obs;
  var policyTypeId = ''.obs;
  var cancellationDisable = false.obs;
  var balanceAmt = 0.0;
  var coffeeBalanceAmt = 0.0.obs;
  var bookedByWallet = false.obs;
  var labelTextDetail = {}.obs;
  var popupTextDetail = {}.obs;
  var cancellationOptionList = [].obs;
  var cancellationOptionLabelList = [].obs;
  var cancellationOptionToolTipList = [].obs;
  var bookedSeatIds = [].obs;
  var coffeeFromWall = false.obs;
  var coffeeDisable = false.obs;
  var withOutCoffeeTransaction = 0.0;
  var agreeTerms = false.obs;
  var firmAgreeTerms = false.obs;
  var firmDisclaimer = "".obs;
  var gPayAmount = 0.0.obs;

  var showPinkCheckBox = false.obs;
  var showExtraCareCheckBox = false.obs;
  var pinkAgreeTerms = false.obs;
  var pinkDisclaimer = "".obs;
  var extraCareAgreeTerms = false.obs;
  var extraCareDisclaimer = "".obs;
  var showGPayBtn = true.obs;

  late TextEditingController cardNameController,
      cardNumberController,
      cvvCodeController,
      addressController;

  @override
  void onInit() async {
    // TODO: implement onInit
    super.onInit();

    cardNameController = TextEditingController();
    cardNumberController = TextEditingController();
    cvvCodeController = TextEditingController();
    addressController = TextEditingController();
    messageDriverTextEditingController = TextEditingController();
    tripId = Get.parameters['tripId'] ?? "";
    rideDetailId = Get.parameters['rideDetailId'] ?? "";
    alreadyBookedSeat.value =
        int.parse(Get.parameters['bookedSeat'].toString());
    isLoading(true);
    await getBookSeatDetail();
    await getCancellationOption();
    await getUserDetail();
    isLoading(false);

  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
    cardNameController.dispose();
    cardNumberController.dispose();
    cvvCodeController.dispose();
    addressController.dispose();
    messageDriverTextEditingController.dispose();
  }

  bool isStudent() {
    if (serviceController.loginUserDetail['student'] == "1") {
      DateTime now = DateTime.now();

      String dateString = serviceController.loginUserDetail['student_card_exp_date'].toString();
      List<String> dateParts = dateString.split('-');
      int cardYear = int.parse(dateParts[0]);
      int cardMonth = int.parse(dateParts[1]);
      int cardDay = int.parse(dateParts[2]);

      DateTime cardExpiryDate = DateTime(cardYear, cardMonth, cardDay);


      if (cardExpiryDate.isBefore(now)) {
        return false;
      }
      return true;
    }
    return false;
  }

  double calculateBookingFee(bookingFee, {String method = "",  bool payable = false}) {

    var returnValue  = 0.0;

    if(method == "paypal"){
      returnValue =  bookingFee * (seatAvailable.value);
    }else if(method == "coffee"){
      returnValue =  bookingFee * (seatAvailable.value);
    }else if(payable == true){
      returnValue =  bookingFee * (seatAvailable.value);
    }else{
      returnValue =  bookingFee * (seatAvailable.value + currentUserBookedSeat.value);
    }

    if (isStudent()) {

    }

    var price = double.parse(ride['ride_detail']!= null && ride['ride_detail'][0] != null ? ride['ride_detail'][0]['price'] : '0.0');
    if (price <= 15) {
      returnValue = 0;
    } else if (price <= 30) {
      if(method == "paypal"){
        returnValue =  (price * 0.1) * (seatAvailable.value);
      }else if(method == "coffee"){
        returnValue =  (price * 0.1) * (seatAvailable.value);
      }else if(payable == true){
        returnValue =  (price * 0.1) * (seatAvailable.value);
      }else{
        returnValue =  (price * 0.1) * (seatAvailable.value + currentUserBookedSeat.value);
      }
    } else {
    }

    return returnValue;
  }

  getBookSeatDetail() async {
    try {

      BookSeatProvider()
          .getBookSeatDetail(tripId, rideDetailId, serviceController.token, serviceController.langId.value)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {


          if (resp['data'] != null && resp['data']['bookingPage'] != null) {
            labelTextDetail.addAll(resp['data']['bookingPage']);
          }

          if (resp['data'] != null && resp['data']['ride'] != null) {
            ride.addAll(resp['data']['ride']);

            policyType.value = ride['booking_type_slug'];
            policyTypeId.value = ride['booking_type_slug'] == 'firm' ? "37" : "0";
            if(policyTypeId.value != "37"){
              firmAgreeTerms.value = true;
            }else{
              firmAgreeTerms.value = false;
            }
            var features = [];
            var dataFeature = ride['feature_ids'];
            features.addAll(dataFeature.split('='));
            if(features.contains('1')){
              showPinkCheckBox.value = true;
              pinkAgreeTerms.value = false;
            }else{
              pinkAgreeTerms.value = true;
            }

            if(features.contains('2')){
              showExtraCareCheckBox.value = true;
              extraCareAgreeTerms.value = false;
            }else{
              extraCareAgreeTerms.value = true;
            }


            if(ride['bookings'] != null && ride['bookings'].length > 0){
              var bookings = List<dynamic>.empty(growable: true).obs;
              bookings.addAll(ride['bookings']);
              print(bookings);
              var getDetail = bookings.firstWhereOrNull((element) => element['user_id'] == int.parse(serviceController.loginUserDetail['id'].toString()));
              if(getDetail != null){
                cancellationDisable.value = true;
                currentUserBookedSeat.value = int.parse(getDetail['seats'].toString());
                policyTypeId.value = getDetail['type'].toString();
                if(getDetail['transaction_no_coffee_sum'].length > 0 && getDetail['transaction_no_coffee_sum'][0] != null &&
                    getDetail['transaction_no_coffee_sum'][0]['booking_transaction_sum'] != null){
                  withOutCoffeeTransaction = double.parse(getDetail['transaction_no_coffee_sum'][0]['booking_transaction_sum'].toString());
                }

                if(policyTypeId.value == "37"){
                  policyType.value = "firm";
                }
              }
            }

            for(var i= 0; i < ride['pending_seat_detail'].length; i++){
              if(ride['pending_seat_detail'][i]['user_id'] == serviceController.loginUserDetail['id'] && ride['pending_seat_detail'][i]['status'] == "hold"){
                seatAvailable.value = seatAvailable.value + 1;
                bookedSeatIds.add(ride['pending_seat_detail'][i]['id']);
              }
            }

            print("fsfsdf${currentUserBookedSeat.value}");
          }
          if (resp['data'] != null && resp['data']['setting'] != null) {
            setting.addAll(resp['data']['setting']);
            if(firmDisclaimer.value != ""){
              var data = double.parse(setting['frim_discount'].toString());
              firmDisclaimer.value = firmDisclaimer.value.replaceAll(":Discount", data.toString());
            }
          }

          if(resp['data'] != null && resp['data']['stateTax'] != null){
            stateTax.value = double.parse(resp['data']['stateTax'].toString());
          }

          if (resp['data'] != null && resp['data']['balance'] != null) {
            balanceAmt = double.parse(resp['data']['balance'] != null ? resp['data']['balance'].toString() : '0.0');
          }

          if (resp['data'] != null && resp['data']['coffeeBalance'] != null) {
            coffeeBalanceAmt.value = double.parse(resp['data']['coffeeBalance'] != null ? resp['data']['coffeeBalance'].toString() : '0.0');
          }

          if (resp['data'] != null && resp['data']['messages'] != null) {
            popupTextDetail.addAll(resp['data']['messages']);
          }

          firmDisclaimer.value = labelTextDetail['booking_disclaimer_firm'].toString();
          pinkDisclaimer.value = labelTextDetail['booking_pink_ride_term_agree_text'].toString();
          extraCareDisclaimer.value = labelTextDetail['booking_extra_care_ride_term_agree_text'].toString();


        }
        else if (resp['status'] != null && resp['status'] == "Error") {
          Get.back();
          serviceController.showDialogue(resp['message']);
        }
      }, onError: (err) {
        isLoading(false);
        serviceController.showDialogue(err.toString());

      });
    } catch (exception) {
      isLoading(false);
      serviceController.showDialogue(exception.toString());

    }
  }

  getUserDetail() async {
    try {
      EditProfileProvider().getUserDetail(serviceController.token, serviceController.langId.value).then(
          (resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null && resp['data']['user'] != null) {
            userDetail.addAll(resp['data']['user']);
          }

          if (resp['data'] != null && resp['data']['city'] != null) {
            cityDetail.addAll(resp['data']['city']);
          }

          if (resp['data'] != null && resp['data']['state'] != null) {
            stateDetail.addAll(resp['data']['state']);
          }

          if (resp['data'] != null && resp['data']['country'] != null) {
            countryDetail.addAll(resp['data']['country']);
          }
        }
      }, onError: (err) {
        serviceController.showDialogue(err.toString());

      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString());

    }
  }

  getCancellationOption() async{
    try{
      await PostRideProvider().getCancellationOption(
          serviceController.token,
          serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['cancellationOptions'] != null){
            cancellationOptionList.addAll(resp['data']['cancellationOptions']);
            if(cancellationOptionList.isNotEmpty && policyTypeId.value == ""){
              policyTypeId.value = cancellationOptionList[0].toString();
            }
          }
          if(resp['data'] != null && resp['data']['cancellationTooltips'] != null){
            cancellationOptionToolTipList.addAll(resp['data']['cancellationTooltips']);
          }

          if(resp['data'] != null && resp['data']['cancellationLabels'] != null){
            cancellationOptionLabelList.addAll(resp['data']['cancellationLabels']);
          }


        }
      },onError: (err){
        serviceController.showDialogue(err.toString());
      });

    }catch (exception) {
      serviceController.showDialogue(exception.toString());
    }
  }

  getCardsList() async {
    errors.clear();
    if (seatAvailable.value <= 0 || policyTypeId.value == '') {
      if(seatAvailable.value <=0) {
        var err = {
          'title': "seats",
          'eList': ['Must select at least 1 seat']
        };
        errors.add(err);
      }
      if(policyTypeId.value == '') {
        var err = {
          'title': "policy",
          'eList': ['Must select at least 1 policy']
        };
        errors.add(err);
      }
      return;
    }

    try {
      cards.clear();
      selectedCardId.value = 0;
      isOverlayLoading(true);
      PaymentOptionsProvider()
          .getCards(page, pageLimit, serviceController.token, serviceController.langId.value)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null &&
              resp['data']['cards'] != null &&
              resp['data']['cards']['data'] != null) {
            cards.addAll(resp['data']['cards']['data']);
            var getPrimaryCard = cards
                .firstWhereOrNull((element) => element['primary_card'] == "1");
            if (getPrimaryCard != null) {
              selectedCardId.value = getPrimaryCard['id'];
            }
            isOverlayLoading(false);
            Get.toNamed("/book_cards");
          }
        }
        isOverlayLoading(false);
      }, onError: (err) {
        isOverlayLoading(false);
        serviceController.showDialogue(err.toString());

      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString());

    }
  }

  clearCardFields() {
    cardNameController.text = "";
    cardNumberController.text = "";
    cvvCodeController.text = "";
    addressController.text = "";
    cardType.value = "";
    month.value = "";
    year.value = "";
    makePrimaryCard.value = false;
  }

  bookingRidePaymentType({String paymentType = "stripe", bool gPay = false, String token = ""}) async {
    errors.clear();
    print(messageDriverTextEditingController.text);
    if (seatAvailable.value <= 0 || policyTypeId.value == '') {
      if(seatAvailable.value <=0 || messageDriverTextEditingController.text == "") {
        var err = {
          'title': "seats",
          'eList': ['Must select at least 1 seat']
        };
        errors.add(err);
      }
      if(policyTypeId.value == '') {
        var err = {
          'title': "policy",
          'eList': ['Must select at least 1 policy']
        };
        errors.add(err);
      }

      if(messageDriverTextEditingController.text == '') {
        var err = {
          'title': "message",
          'eList': ['Please enter message']
        };
        errors.add(err);
      }
      return;
    }

    if(paymentType == "stripe" && selectedCardId.value == 0){
      serviceController.showDialogue("${popupTextDetail['need_to_select_card_message'] ?? "Need to select a card"}");
      return;
    }

    isOverlayLoading(true);
    var bookingCredit = "";
    var seatAmount = "";
    var onlinePayment = "";
    var cashPayment = "";
    var total = "";
    var taxAmount = 0.0;

    var bookingId = 0;

    if (currentUserBookedSeat.value != 0) {
      var userId = serviceController.loginUserDetail['id'];
      var bookings = List<dynamic>.empty(growable: true);
      bookings = ride['bookings'];
      var bookingDetail =
          bookings.firstWhereOrNull((element) => element['user_id'] == userId);
      if (bookingDetail != null) {
        bookingId = bookingDetail['id'];
      }
    }
    if (ride['payment_method_slug'] == "cash") {
      if(policyType.value == 'firm'){

        var data = double.parse(setting['frim_discount'].toString());
        data = data / 100;

        bookingCredit =  calculateBookingFee(double.parse(setting['booking_price'].toString()))
            .toStringAsFixed(1);
        //bookingCredit = (double.parse(bookingCredit.toString()) - (double.parse(bookingCredit.toString()) * double.parse(data.toString()))).toStringAsFixed(1);
        seatAmount = (double.parse(ride['ride_detail'][0]['price'].toString()) *
            (int.parse(seatAvailable.value.toString()) +
                currentUserBookedSeat.value))
            .toStringAsFixed(1);
        seatAmount = (double.parse(seatAmount.toString()) - (double.parse(seatAmount.toString()) * double.parse(data.toString()))).toStringAsFixed(1);

        onlinePayment = bookingCredit;
        cashPayment = seatAmount;
        if(setting['deduct_tax'] != null && setting['deduct_tax'] == "deduct_from_passenger"){
          if(setting['tax_type'] == "state_wise_tax"){
            taxAmount = double.parse(((double.parse(bookingCredit.toString()) * stateTax.value) / 100).toString());
          }else{
            taxAmount = double.parse(((double.parse(bookingCredit.toString()) * setting['tax']) / 100).toString());
          }
        }
        total = (double.parse(bookingCredit.toString()) +
            double.parse(seatAmount.toString()) + taxAmount)
            .toStringAsFixed(1);
      }else{
        bookingCredit =  calculateBookingFee(double.parse(setting['booking_price'].toString()))
            .toStringAsFixed(1);
        seatAmount = (double.parse(ride['ride_detail'][0]['price'].toString()) *
            (int.parse(seatAvailable.value.toString()) +
                currentUserBookedSeat.value))
            .toStringAsFixed(1);

        onlinePayment = bookingCredit;
        cashPayment = seatAmount;

        if(setting['deduct_tax'] != null && setting['deduct_tax'] == "deduct_from_passenger"){
          if(setting['tax_type'] == "state_wise_tax"){
            taxAmount = double.parse(((double.parse(bookingCredit.toString()) * stateTax.value) / 100).toString());
          }else{
            taxAmount = double.parse(((double.parse(bookingCredit.toString()) * double.parse(setting['tax'].toString())) / 100).toString());
          }
        }

        total = (double.parse(bookingCredit.toString()) +
            double.parse(seatAmount.toString()) + taxAmount)
            .toStringAsFixed(1);
      }

    }
    else if (ride['payment_method_slug'] == "online") {

      if(policyType.value == 'firm') {
        var data = double.parse(setting['frim_discount'].toString());
        data = data / 100;
        bookingCredit = calculateBookingFee(double.parse(setting['booking_price'].toString()))
            .toStringAsFixed(1);
        //bookingCredit = (double.parse(bookingCredit.toString()) - (double.parse(bookingCredit.toString()) * double.parse(data.toString()))).toStringAsFixed(1);
        seatAmount = (double.parse(ride['ride_detail'][0]['price'].toString()) *
            (int.parse(seatAvailable.value.toString()) +
                currentUserBookedSeat.value))
            .toStringAsFixed(1);
        seatAmount = (double.parse(seatAmount.toString()) - (double.parse(seatAmount.toString()) * double.parse(data.toString()))).toStringAsFixed(1);
        cashPayment = 0.toString();

        if(setting['deduct_tax'] != null && setting['deduct_tax'] == "deduct_from_passenger"){
          if(setting['tax_type'] == "state_wise_tax"){
            taxAmount = double.parse(((double.parse(bookingCredit.toString()) * stateTax.value) / 100).toString());
          }else{
            taxAmount = double.parse(((double.parse(bookingCredit.toString()) * double.parse(setting['tax'].toString())) / 100).toString());
          }
        }

        total = (double.parse(bookingCredit.toString()) +
            double.parse(seatAmount.toString()) + taxAmount)
            .toStringAsFixed(1);
        onlinePayment = (double.parse(total.toString())).toStringAsFixed(1);
      }else{
        bookingCredit = calculateBookingFee(double.parse(setting['booking_price'].toString()))
            .toStringAsFixed(1);
        seatAmount = (double.parse(ride['ride_detail'][0]['price'].toString()) *
            (int.parse(seatAvailable.value.toString()) +
                currentUserBookedSeat.value))
            .toStringAsFixed(1);
        cashPayment = 0.toString();
        if(setting['deduct_tax'] != null && setting['deduct_tax'] == "deduct_from_passenger"){
          if(setting['tax_type'] == "state_wise_tax"){
            taxAmount = double.parse(((double.parse(bookingCredit.toString()) * stateTax.value) / 100).toString());
          }else{
            taxAmount = double.parse(((double.parse(bookingCredit.toString()) * double.parse(setting['tax'].toString())) / 100).toString());
          }
        }
        total = (double.parse(bookingCredit.toString()) +
            double.parse(seatAmount.toString()) + taxAmount)
            .toStringAsFixed(1);
        onlinePayment = (double.parse(total.toString())).toStringAsFixed(1);
      }


    }
    else {
      if(policyType.value == 'firm'){

        var data = double.parse(setting['frim_discount'].toString());
        data = data / 100;

        bookingCredit = calculateBookingFee(double.parse(setting['booking_price'].toString()))
            .toStringAsFixed(1);
        //bookingCredit = (double.parse(bookingCredit.toString()) - (double.parse(bookingCredit.toString()) * double.parse(data.toString()))).toStringAsFixed(1);
        seatAmount = (double.parse(ride['ride_detail'][0]['price'].toString()) *
            (int.parse(seatAvailable.value.toString()) +
                currentUserBookedSeat.value))
            .toStringAsFixed(1);
        seatAmount = (double.parse(seatAmount.toString()) - (double.parse(seatAmount.toString()) * double.parse(data.toString()))).toStringAsFixed(1);
        cashPayment = 0.toString();
        if(setting['deduct_tax'] != null && setting['deduct_tax'] == "deduct_from_passenger"){
          if(setting['tax_type'] == "state_wise_tax"){
            taxAmount = double.parse(((double.parse(bookingCredit.toString()) * stateTax.value) / 100).toString());
          }else{
            taxAmount = double.parse(((double.parse(bookingCredit.toString()) * double.parse(setting['tax'].toString())) / 100).toString());
          }
        }
        total = (double.parse(bookingCredit.toString()) +
            double.parse(seatAmount.toString()) + taxAmount)
            .toStringAsFixed(1);
        onlinePayment = (double.parse(total.toString()))
            .toStringAsFixed(1);

      }else{
        bookingCredit = calculateBookingFee(double.parse(setting['booking_price'].toString()))
            .toStringAsFixed(1);
        seatAmount = (double.parse(ride['ride_detail'][0]['price'].toString()) *
            (int.parse(seatAvailable.value.toString()) +
                currentUserBookedSeat.value))
            .toStringAsFixed(1);
        cashPayment = 0.toString();
        if(setting['deduct_tax'] != null && setting['deduct_tax'] == "deduct_from_passenger"){
          if(setting['tax_type'] == "state_wise_tax"){
            taxAmount = double.parse(((double.parse(bookingCredit.toString()) * stateTax.value) / 100).toString());
          }else{
            taxAmount = double.parse(((double.parse(bookingCredit.toString()) * double.parse(setting['tax'].toString())) / 100).toString());
          }
        }
        total = (double.parse(bookingCredit.toString()) +
            double.parse(seatAmount.toString()) + taxAmount)
            .toStringAsFixed(1);
        onlinePayment = (double.parse(total.toString()))
            .toStringAsFixed(1);
      }

    }

    var paymentMethod = "";
    if (paymentType == "paypal") {
      paymentMethod = "paypal";
      var paypalPayment = 0.0;
      if (currentUserBookedSeat.value != 0) {

        if (ride['payment_method_slug'] == "cash") {
          paypalPayment = calculateBookingFee(double.parse(setting['booking_price'].toString()), method: "paypal");
          if(setting['deduct_tax'] != null && setting['deduct_tax'] == "deduct_from_passenger"){
            if(setting['tax_type'] == "state_wise_tax"){
              taxAmount = double.parse(((paypalPayment * stateTax.value) / 100).toString());
            }else{
              taxAmount = double.parse(((paypalPayment * double.parse(setting['tax'].toString())) / 100).toString());
            }
          }
          paypalPayment = paypalPayment + taxAmount;

        }
        else if (ride['payment_method_slug'] == "online") {

          var bookingCredit1 = "";
          bookingCredit1 =
              calculateBookingFee(double.parse(setting['booking_price'].toString()), method: "paypal")
                  .toStringAsFixed(1);
          var seatAmount1 = (double.parse(ride['ride_detail'][0]['price'].toString()) *
              int.parse(seatAvailable.value.toString()))
              .toStringAsFixed(1);

          if(policyType.value == 'firm'){
            var data = double.parse(setting['frim_discount'].toString());
            data = data / 100;
            //bookingCredit1 = (double.parse(bookingCredit1.toString()) - (double.parse(bookingCredit1.toString()) * double.parse(data.toString()))).toStringAsFixed(1);
            seatAmount1 = (double.parse(seatAmount1.toString()) - (double.parse(seatAmount1.toString()) * double.parse(data.toString()))).toStringAsFixed(1);
          }
          var taxAmount1 = 0.0;
          if(setting['deduct_tax'] != null && setting['deduct_tax'] == "deduct_from_passenger"){
            if(setting['tax_type'] == "state_wise_tax"){
              taxAmount1 = double.parse(((double.parse(bookingCredit1.toString()) * stateTax.value) / 100).toString());
            }else{
              taxAmount1 = double.parse(((double.parse(bookingCredit1.toString()) * double.parse(setting['tax'].toString())) / 100).toString());
            }
          }
          if(coffeeFromWall.value == false){
          }else{
            bookingCredit1 = (double.parse(bookingCredit1.toString()) - double.parse(bookingCredit1.toString())).toStringAsFixed(1);
          }


          var total1 = (double.parse(bookingCredit1.toString()) +
              double.parse(seatAmount1.toString()) + taxAmount1)
              .toStringAsFixed(1);
          paypalPayment = double.parse(total1.toString());
        }
        else {
          var bookingCredit1 =
          calculateBookingFee(double.parse(setting['booking_price'].toString()), method: "paypal")
              .toStringAsFixed(1);
          var seatAmount1 = (double.parse(ride['ride_detail'][0]['price'].toString()) *
                  int.parse(seatAvailable.value.toString()))
              .toStringAsFixed(1);
          if(policyType.value == 'firm'){
            var data = double.parse(setting['frim_discount'].toString());
            data = data / 100;
            //bookingCredit1 = (double.parse(bookingCredit1.toString()) - (double.parse(bookingCredit1.toString()) * double.parse(data.toString()))).toStringAsFixed(1);
            seatAmount1 = (double.parse(seatAmount1.toString()) - (double.parse(seatAmount1.toString()) * double.parse(data.toString()))).toStringAsFixed(1);
          }
          var taxAmount1 = 0.0;
          if(setting['deduct_tax'] != null && setting['deduct_tax'] == "deduct_from_passenger"){
            if(setting['tax_type'] == "state_wise_tax"){
              taxAmount1 = double.parse(((double.parse(bookingCredit1.toString()) * stateTax.value) / 100).toString());
            }else{
              taxAmount1 = double.parse(((double.parse(bookingCredit1.toString()) * double.parse(setting['tax'].toString())) / 100).toString());
            }
          }
          if(coffeeFromWall.value == false){
          }else{
            bookingCredit1 = (double.parse(bookingCredit1.toString()) - double.parse(bookingCredit1.toString())).toStringAsFixed(1);
          }
          var total1 = (double.parse(bookingCredit1.toString()) +
                  double.parse(seatAmount1.toString()) + taxAmount1)
              .toStringAsFixed(1);
          paypalPayment = double.parse(total1.toString());
        }
      }
      else {
        if(coffeeFromWall.value == false){

          if(ride['payment_method_slug'] == "cash"){
            var taxAmount2 = 0.0;
            if(setting['deduct_tax'] != null && setting['deduct_tax'] == "deduct_from_passenger"){
              if(setting['tax_type'] == "state_wise_tax"){
                taxAmount2 = double.parse(((double.parse(bookingCredit.toString()) * stateTax.value) / 100).toString());
              }else{
                taxAmount2 = double.parse(((double.parse(bookingCredit.toString()) * double.parse(setting['tax'].toString())) / 100).toString());
              }
            }
            paypalPayment = double.parse(onlinePayment.toString()) + taxAmount2;
          }else{
            paypalPayment = double.parse(onlinePayment.toString());
          }

        }else{

          paypalPayment = ((double.parse(onlinePayment.toString())) -
              double.parse(bookingCredit.toString()));
        }

      }

      if(gPay == true){
        await getGooglePayApplePay(paypalPayment, bookingCredit, seatAmount, cashPayment,
            total, onlinePayment, paymentMethod, bookingId, taxAmount, token);
      }else{
        await paypalMethod(paypalPayment, bookingCredit, seatAmount, cashPayment,
            total, onlinePayment, paymentMethod, bookingId, taxAmount);
      }

    }
    else if(paymentType == "stripe") {
      paymentMethod = "credit_card";
      await bookingRide(bookingCredit, seatAmount, cashPayment, total,
          onlinePayment, paymentMethod, bookingId, taxAmount);
    }
    else if(paymentType == "cash"){
      await bookingRide(bookingCredit, seatAmount, cashPayment, total, onlinePayment, paymentMethod, bookingId, taxAmount);
    }
  }

  paypalMethod(paypalPayment, bookingCredit, seatAmount, cashPayment, total, onlinePayment, paymentMethod, bookingId, taxAmount) async {
    isOverlayLoading(false);

    Get.to(
      PaypalPay(
          sandboxMode: true,
          clientId: "${dotenv.env['client_id']}",
          secretKey: "${dotenv.env['secret']}",
          returnURL: 'https://test.com/return',
          cancelURL: 'https://test.com/cancel',
          purchaseUnits: [
            {
              'amount': {
                'value': '$paypalPayment',
                'currency_code': 'USD',
              },
              'shipping': {
                'address': {
                  'recipient_name':
                      '${userDetail['first_name']} ${userDetail['last_name']}',
                  'line1': '${userDetail['address']}',
                  'line2': '',
                  'city': '${cityDetail['name']}',
                  'country_code': 'US',
                  'postal_code': '${userDetail['zipcode']}',
                  'phone': '${userDetail['phone']}',
                  'state': '${stateDetail['name']}',
                  'admin_area_2':
                      '${cityDetail['name']}', // Replace 'City Name' with the actual city or locality name
                  'admin_area_1':
                      '${stateDetail['name']}', // Replace 'State/Province' with the actual state or province name
                }
              }
            }
          ],
          note: 'Contact us for any questions on your order.',
          onSuccess: (Map params) async {
            isOverlayLoading(true);
            if (params['data'] != null &&
                params['data']['purchase_units'] != null &&
                params['data']['purchase_units'][0] != null &&
                params['data']['purchase_units'][0]['payments'] != null &&
                params['data']['purchase_units'][0]['payments']['captures'] !=
                    null &&
                params['data']['purchase_units'][0]['payments']['captures']
                        [0] !=
                    null) {
              captureId = params['data']['purchase_units'][0]['payments']
                  ['captures'][0]['id'];
              await bookingRide(bookingCredit, seatAmount, cashPayment, total,
                  onlinePayment, paymentMethod, bookingId, taxAmount);
            }
          },
          onError: (error) {
            serviceController.showDialogue(error.toString());
            isOverlayLoading(false);
          },

          onCancel: (params) {
            serviceController.showDialogue("${popupTextDetail['paypal_not_completed_message'] ?? "Paypal payment is not complete"}");
            isOverlayLoading(false);
          }),
    );
  }

  bookingRide(bookingCredit, seatAmount, cashPayment, total, onlinePayment, paymentMethod, bookingId, taxAmount, {bool gPay = false}) async {


    try {
      if(ride['payment_method_slug'] == "cash"){
        if(coffeeFromWall.value == true){
          paymentMethod = "cash";
          onlinePayment = 0;
        }else{
          if(balanceAmt >= double.parse(bookingCredit.toString()) && balanceAmt != 0.0){
            bookedByWallet.value = true;
            paymentMethod = "credit_card";
          }
        }

      }else{
        if(coffeeFromWall.value == true){
          if(balanceAmt >= double.parse(onlinePayment.toString()) && balanceAmt != 0.0){
            bookedByWallet.value = true;
            paymentMethod = "credit_card";
          }
        }else{
          if(balanceAmt >= double.parse(onlinePayment.toString())  && balanceAmt != 0.0){
            bookedByWallet.value = true;
            paymentMethod = "credit_card";
          }
        }

      }

      if(ride['payment_method_slug'] == "cash"){
        onlinePayment = onlinePayment;
      }else{
        onlinePayment = (double.parse(onlinePayment.toString()) - taxAmount).toStringAsFixed(1);
      }

      var taxPercentage = 0.0;
      var taxType = "";
      var deductType = "";

      if(setting['deduct_tax'] != null && setting['deduct_tax'] == "deduct_from_passenger"){
        deductType = setting['deduct_tax'];
        if(setting['tax_type'] == "state_wise_tax"){
          taxPercentage = stateTax.value;
        }else{
          taxPercentage = double.parse(setting['tax'].toString());
        }
        taxType = setting['tax_type'];
      }

      BookSeatProvider()
          .bookingRide(
              serviceController.token,
              selectedCardId.value,
              bookingCredit,
              int.parse(seatAvailable.value.toString()) +
                  currentUserBookedSeat.value,
              seatAmount,
              onlinePayment,
              cashPayment,
              total,
              ride['id'],
              ride['booking_method_slug'],
              bookingId,
              paymentMethod,
              captureId,
              policyTypeId.value,
              bookedByWallet.value,
              coffeeFromWall.value,
              bookedSeatIds,
              taxPercentage,
              deductType,
              taxType,
              taxAmount,
              messageDriverTextEditingController.text,
              gPay
          )
          .then((resp) async {
        errorList.clear();
        if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message'].toString());


        } else if (resp['status'] != null && resp['status'] == "Success") {

          if(bookedByWallet.value == true){
            bool isMyWalletControllerRegistered = Get.isRegistered<MyWalletController>();
            if(isMyWalletControllerRegistered){
              var tempController = Get.find<MyWalletController>();
               tempController.balance.value = balanceAmt - double.parse(onlinePayment.toString());
              tempController.balance.refresh();
            }
          }

          if (ride['booking_method_slug'] == "manual") {
            serviceController.thankYouMessage.value = resp['message'] ?? "Your request has been successfully sent to the driver";
            Get.offAllNamed("/thank_you/manualBooking");
          } else {
            serviceController.thankYouMessage.value = resp['message'] ?? "You have successfully booked seat(s) in the ride";
            Get.offAllNamed("/thank_you/instantBooking");
          }
        }
        isOverlayLoading(false);
      }, onError: (error) {
        isOverlayLoading(false);
        serviceController.showDialogue(error.toString());
      });
    } catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString());

    }
  }

  seatOnHold(seatId, index) async{


    if(errors.firstWhereOrNull((element) => element['title'] == "seats") != null)
    {
      errors.remove(errors.firstWhereOrNull((element) => element['title'] == "seats"));
    }

    try {
      var type = "add";
      if(bookedSeatIds.contains(seatId)){
        type = "remove";
      }
      await BookSeatProvider().seatOnHold(
          serviceController.token,
          seatId, type)
          .then((resp) async {
        errorList.clear();
        if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message'].toString());
        } else if (resp['status'] != null && resp['status'] == "Success") {
          if(type == "add"){
            bookedSeatIds.add(seatId);
            ride['pending_seat_detail'][index-1]['status'] = "hold";
            seatAvailable.value = bookedSeatIds.length;
          }else{
            bookedSeatIds.remove(seatId);
            ride['pending_seat_detail'][index-1]['status'] = "pending";
            seatAvailable.value = bookedSeatIds.length;
          }

        }
        isOverlayLoading(false);
      }, onError: (error) {
        isOverlayLoading(false);
        serviceController.showDialogue(error.toString());
      });
    } catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString());

    }
  }

  getGooglePayApplePay(paypalPayment, bookingCredit, seatAmount, cashPayment,
      total, onlinePayment, paymentMethod, bookingId, taxAmount, token) async{

    await BookSeatProvider().createPaymentIntent(serviceController.token, paypalPayment, token)
        .then((resp) async {
      final paymentIntentId = resp['paymentIntentId'];
      if(paymentIntentId != null){
        captureId = paymentIntentId;
        await bookingRide(bookingCredit, seatAmount, cashPayment, total,
            onlinePayment, paymentMethod, bookingId, taxAmount, gPay: true);
      }
    }, onError: (error) {
      isOverlayLoading(false);
      serviceController.showDialogue(error.toString());
    });


  }


}
