import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart';
import 'dart:convert';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:flutter_paypal_pay/flutter_paypal_pay.dart';
import 'package:get/get.dart';
import 'package:pay/pay.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/helpers/ride_feature_ids.dart';
import 'package:proximaride_app/consts/payment_config.dart';
import 'package:proximaride_app/pages/book_seat/BookSeatProvider.dart';
import 'package:proximaride_app/pages/edit_profile/EditProfileProvider.dart';
import 'package:proximaride_app/pages/my_trips/MyTripController.dart';
import 'package:proximaride_app/pages/my_wallet/MyWalletController.dart';
import 'package:proximaride_app/pages/navigation/NavigationController.dart';
import 'package:proximaride_app/pages/payment_options/PaymentOptionsProvider.dart';
import 'package:proximaride_app/pages/post_ride/PostRideProvider.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

class BookSeatController extends GetxController {
  final serviceController = Get.find<Service>();
  final seatSectionKey = GlobalKey();
  final messageSectionKey = GlobalKey();
  final termsSectionKey = GlobalKey();
  var isLoading = false.obs;
  var isOverlayLoading = false.obs;
  var tripId = "";
  var fromStopId = "";
  var toStopId = "";
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
  var paypalEmail = "";
  var paypalPayerId = "";
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
  var firmCancellationUnderstand = "".obs;
  var firmCancellationUnderstandChecked = false.obs;
  var gPayAmount = 0.0.obs;

  var showPinkCheckBox = false.obs;
  var showExtraCareCheckBox = false.obs;
  var pinkAgreeTerms = false.obs;
  var pinkDisclaimer = "".obs;
  var extraCareAgreeTerms = false.obs;
  var extraCareDisclaimer = "".obs;
  var showGPayBtn = true.obs;
  var nativePayAvailable = false.obs;
  var hasShownSeatHoldWarning = false.obs;

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
    _resetSeatSelectionState();
    tripId = Get.parameters['tripId'] ?? "";
    fromStopId = Get.parameters['fromStopId'] ?? "0";
    toStopId = Get.parameters['toStopId'] ?? "0";
    alreadyBookedSeat.value =
        int.parse(Get.parameters['bookedSeat'].toString());
    isLoading(true);
    await checkNativePayAvailability();
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

  void _resetSeatSelectionState() {
    seatAvailable.value = 0;
    alreadyBookedSeat.value = 0;
    currentUserBookedSeat.value = 0;
    cancellationDisable.value = false;
    bookedSeatIds.clear();
    hasShownSeatHoldWarning.value = false;
  }

  Future<void> checkNativePayAvailability() async {
    if (kIsWeb) {
      nativePayAvailable.value = false;
      return;
    }

    try {
      final payClient = Pay({
        PayProvider.google_pay:
            PaymentConfiguration.fromJsonString(defaultGooglePay),
        PayProvider.apple_pay:
            PaymentConfiguration.fromJsonString(defaultApplePay),
      });

      final provider =
          defaultTargetPlatform == TargetPlatform.iOS
              ? PayProvider.apple_pay
              : PayProvider.google_pay;

      nativePayAvailable.value = await payClient.userCanPay(provider);
    } catch (_) {
      nativePayAvailable.value = false;
    }
  }

  bool isStudent() {
    final studentValue =
        (userDetail['student'] ?? serviceController.loginUserDetail['student'])
            ?.toString();

            logger.info("isStudent: ${studentValue.toString()}");
    if (studentValue == "1") {
      DateTime now = DateTime.now();

      final dateString =
          (userDetail['student_card_exp_date'] ??
                  serviceController.loginUserDetail['student_card_exp_date'])
              .toString();
      if (dateString.isEmpty || dateString == "null" || !dateString.contains('-')) {
        return false;
      }
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

  double calculateBookingFee(bookingFee,
      {String method = "", bool payable = false}) {
    var returnValue = 0.0;

    if (method == "paypal") {
      returnValue = bookingFee * (seatAvailable.value);
    } else if (method == "coffee") {
      returnValue = bookingFee * (seatAvailable.value);
    } else if (payable == true) {
      returnValue = bookingFee * (seatAvailable.value);
    } else {
      returnValue =
          bookingFee * (seatAvailable.value + currentUserBookedSeat.value);
    }

    if (isStudent()) {
      return 0.0;
    }

    var price = rideUnitPrice();
    if (price <= 15) {
      returnValue = 0;
    } else if (price <= 30) {
      if (method == "paypal") {
        returnValue = (price * 0.1) * (seatAvailable.value);
      } else if (method == "coffee") {
        returnValue = (price * 0.1) * (seatAvailable.value);
      } else if (payable == true) {
        returnValue = (price * 0.1) * (seatAvailable.value);
      } else {
        returnValue =
            (price * 0.1) * (seatAvailable.value + currentUserBookedSeat.value);
      }
    } else {}

    return returnValue;
  }

  double rideUnitPrice() {
    return _minorToMajor(rideUnitPriceMinor());
  }

  int _parseMinor(dynamic value) {
    if (value == null) {
      return 0;
    }

    if (value is int) {
      return value;
    }

    if (value is num) {
      return value.round();
    }

    final parsed = double.tryParse(value.toString());
    return parsed?.round() ?? 0;
  }

  int _majorToMinor(dynamic value) {
    if (value == null) {
      return 0;
    }

    final parsed = double.tryParse(value.toString());
    if (parsed == null) {
      return 0;
    }

    return (parsed * 100).round();
  }

  double _minorToMajor(int value) {
    return value / 100;
  }

  String _minorToMajorString(int value) {
    return _minorToMajor(value).toStringAsFixed(2);
  }

  int _selectedSeatCount({bool payable = false}) {
    if (payable) {
      return seatAvailable.value;
    }

    return seatAvailable.value + currentUserBookedSeat.value;
  }

  int rideUnitPriceMinor() {
    if (ride['price_minor'] != null) {
      return _parseMinor(ride['price_minor']);
    }

    if (ride['priceMinor'] != null) {
      return _parseMinor(ride['priceMinor']);
    }

    if (ride['matched_segment_price_minor'] != null) {
      return _parseMinor(ride['matched_segment_price_minor']);
    }

    if (ride['matched_segment_priceMinor'] != null) {
      return _parseMinor(ride['matched_segment_priceMinor']);
    }

    if (ride['matched_segment_price_major'] != null) {
      return _majorToMinor(ride['matched_segment_price_major']);
    }

    if (ride['matched_segment_priceMajor'] != null) {
      return _majorToMinor(ride['matched_segment_priceMajor']);
    }

    if (ride['price_major'] != null) {
      return _majorToMinor(ride['price_major']);
    }

    if (ride['priceMajor'] != null) {
      return _majorToMinor(ride['priceMajor']);
    }

    if (ride['ride_detail'] != null && ride['ride_detail'].isNotEmpty) {
      if (ride['ride_detail']['price_minor'] != null) {
        return _parseMinor(ride['ride_detail']['price_minor']);
      }

      if (ride['ride_detail']['priceMinor'] != null) {
        return _parseMinor(ride['ride_detail']['priceMinor']);
      }

      if (ride['ride_detail']['price_major'] != null) {
        return _majorToMinor(ride['ride_detail']['price_major']);
      }

      if (ride['ride_detail']['priceMajor'] != null) {
        return _majorToMinor(ride['ride_detail']['priceMajor']);
      }

      if (ride['ride_detail']['price'] != null) {
        return _parseMinor(ride['ride_detail']['price']);
      }
    }

    if (ride['price'] != null) {
      return _parseMinor(ride['price']);
    }

    return 0;
  }

  double bookingFeeRatePercent() {
    if (isStudent()) {
      return 0;
    }

    final priceMinor = rideUnitPriceMinor();
    if (priceMinor <= 1500) {
      return 0;
    }

    if (priceMinor <= 3000) {
      return 10;
    }

    return double.tryParse(setting['booking_price']?.toString() ?? '0') ?? 0;
  }

  int bookingFeeAmountMinorForSeatCount(int seatCount) {
    if (seatCount <= 0) {
      return 0;
    }

    final amountMinor = rideUnitPriceMinor() * seatCount;
    return ((amountMinor * bookingFeeRatePercent()) / 100).round();
  }

  int _firmDiscountedMinor(int amountMinor) {
    if (policyType.value != 'firm') {
      return amountMinor;
    }

    final discountRate =
        double.tryParse(setting['frim_discount']?.toString() ?? '0') ?? 0;

    return (amountMinor - ((amountMinor * discountRate) / 100)).round();
  }

  int _passengerTaxMinor(int bookingCreditMinor) {
    if (setting['deduct_tax'] == null ||
        setting['deduct_tax'] != "deduct_from_passenger") {
      return 0;
    }

    final taxRate = setting['tax_type'] == "state_wise_tax"
        ? stateTax.value
        : (double.tryParse(setting['tax']?.toString() ?? '0') ?? 0);

    return ((bookingCreditMinor * taxRate) / 100).round();
  }

  getBookSeatDetail() async {
    try {
      _resetSeatSelectionState();
      ride.clear();
      BookSeatProvider()
          .getBookSeatDetail(tripId, fromStopId, toStopId,
              serviceController.token, serviceController.langId.value)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null && resp['data']['bookingPage'] != null) {
            labelTextDetail.addAll(resp['data']['bookingPage']);
          }

          if (resp['data'] != null && resp['data']['ride'] != null) {
            ride.addAll(resp['data']['ride']);

            policyType.value = ride['booking_type_slug']?.toString() ?? "";
            policyTypeId.value =
                ride['booking_type_slug'] == 'firm' ? "37" : "0";
            if (policyTypeId.value != "37") {
              firmAgreeTerms.value = true;
              firmCancellationUnderstandChecked.value = true;
            } else {
              firmAgreeTerms.value = false;
              firmCancellationUnderstandChecked.value = false;
            }
            final features = parseRideFeatureIdsFromRide(
                Map<String, dynamic>.from(ride));
            if (features.contains('1')) {
              showPinkCheckBox.value = true;
              pinkAgreeTerms.value = false;
            } else {
              pinkAgreeTerms.value = true;
            }

            if (features.contains('2')) {
              showExtraCareCheckBox.value = true;
              extraCareAgreeTerms.value = false;
            } else {
              extraCareAgreeTerms.value = true;
            }

            if (ride['bookings'] != null && ride['bookings'].length > 0) {
              var bookings = List<dynamic>.empty(growable: true).obs;
              bookings.addAll(ride['bookings']);
              logger.info("Bookings: ${bookings.toString()}");
              var getDetail = bookings.firstWhereOrNull((element) =>
                  element['user_id'] ==
                  int.parse(
                      serviceController.loginUserDetail['id'].toString()));
              if (getDetail != null) {
                if (alreadyBookedSeat.value != 0) {
                  cancellationDisable.value = true;
                  currentUserBookedSeat.value =
                      int.parse(getDetail['seats'].toString());
                  policyTypeId.value = getDetail['type'].toString();
                  if (getDetail['transaction_no_coffee_sum'].length > 0 &&
                      getDetail['transaction_no_coffee_sum'][0] != null &&
                      getDetail['transaction_no_coffee_sum'][0]
                              ['booking_transaction_sum'] !=
                          null) {
                    withOutCoffeeTransaction = double.parse(
                        getDetail['transaction_no_coffee_sum'][0]
                                ['booking_transaction_sum']
                            .toString());
                  }

                  if (policyTypeId.value == "37") {
                    policyType.value = "firm";
                    firmCancellationUnderstandChecked.value = false;
                  }
                }
              }
            }

            for (var i = 0; i < ride['pending_seat_detail'].length; i++) {
              if (ride['pending_seat_detail'][i]['user_id'] ==
                      serviceController.loginUserDetail['id'] &&
                  ride['pending_seat_detail'][i]['status'] == "hold") {
                seatAvailable.value = seatAvailable.value + 1;
                bookedSeatIds.add(ride['pending_seat_detail'][i]['id']);
              }
            }

            logger.info(
                "Current User Booked Seat: ${currentUserBookedSeat.value}");
          }
          if (resp['data'] != null && resp['data']['setting'] != null) {
            setting.addAll(resp['data']['setting']);
            if (firmDisclaimer.value != "") {
              var data = double.parse(setting['frim_discount'].toString());
              firmDisclaimer.value =
                  firmDisclaimer.value.replaceAll(":Discount", data.toString());
            }
          }

          if (resp['data'] != null && resp['data']['stateTax'] != null) {
            stateTax.value = double.parse(resp['data']['stateTax'].toString());
          }

          if (resp['data'] != null && resp['data']['balance'] != null) {
            balanceAmt = double.parse(resp['data']['balance'] != null
                ? resp['data']['balance'].toString()
                : '0.0');
          }

          if (resp['data'] != null && resp['data']['coffeeBalance'] != null) {
            coffeeBalanceAmt.value = double.parse(
                resp['data']['coffeeBalance'] != null
                    ? resp['data']['coffeeBalance'].toString()
                    : '0.0');
          }

          if (resp['data'] != null && resp['data']['messages'] != null) {
            popupTextDetail.addAll(resp['data']['messages']);
          }

          firmDisclaimer.value =
              labelTextDetail['booking_disclaimer_firm'].toString();
          firmCancellationUnderstand.value =
              labelTextDetail['firm_cancellation_understand_text'].toString();
          pinkDisclaimer.value =
              labelTextDetail['booking_pink_ride_term_agree_text'].toString();
          extraCareDisclaimer.value =
              labelTextDetail['booking_extra_care_ride_term_agree_text']
                  .toString();
        } else if (resp['status'] != null && resp['status'] == "Error") {
          isLoading(false);
          serviceController.showDialogue(resp['message'], type: "error");
        }
      }, onError: (err) {
        isLoading(false);
        if (err is Map &&
            err.containsKey('type') &&
            err.containsKey('message')) {
          serviceController.showDialogue(err['message'], type: "error");
        } else if (err is Map &&
            err.containsKey('type') &&
            err['type'] == 'network') {
          serviceController.showDialogue(
              "No internet connection. Please check your network and try again.",
              type: "error");
        } else {
          serviceController.showDialogue(err.toString(), type: "error");
        }
      });
    } catch (exception) {
      isLoading(false);
      if (exception is Map &&
          exception.containsKey('type') &&
          exception.containsKey('message')) {
        serviceController.showDialogue(exception['message'], type: "error");
      } else if (exception is Map &&
          exception.containsKey('type') &&
          exception['type'] == 'network') {
        serviceController.showDialogue(
            "No internet connection. Please check your network and try again.",
            type: "error");
      } else {
        serviceController.showDialogue(exception.toString(), type: "error");
      }
    }
  }

  getUserDetail() async {
    try {
      EditProfileProvider()
          .getUserDetail(
              serviceController.token, serviceController.langId.value)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null && resp['data']['user'] != null) {
            userDetail.addAll(resp['data']['user']);
            serviceController.loginUserDetail.addAll({
              ...serviceController.loginUserDetail,
              ...Map<String, Object>.fromEntries(
                (resp['data']['user'] as Map)
                    .entries
                    .where((entry) => entry.value != null)
                    .map((entry) => MapEntry(entry.key.toString(), entry.value as Object)),
              ),
            });
            serviceController.loginUserDetail.refresh();
            await serviceController.secureStorage.write(
              key: "userInfo",
              value: jsonEncode(serviceController.loginUserDetail),
            );
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
        if (err is Map &&
            err.containsKey('type') &&
            err.containsKey('message')) {
          serviceController.showDialogue(err['message'], type: "error");
        } else if (err is Map &&
            err.containsKey('type') &&
            err['type'] == 'network') {
          serviceController.showDialogue(
              "No internet connection. Please check your network and try again.",
              type: "error");
        } else {
          serviceController.showDialogue(err.toString(), type: "error");
        }
      });
    } catch (exception) {
      if (exception is Map &&
          exception.containsKey('type') &&
          exception.containsKey('message')) {
        serviceController.showDialogue(exception['message'], type: "error");
      } else if (exception is Map &&
          exception.containsKey('type') &&
          exception['type'] == 'network') {
        serviceController.showDialogue(
            "No internet connection. Please check your network and try again.",
            type: "error");
      } else {
        serviceController.showDialogue(exception.toString(), type: "error");
      }
    }
  }

  getCancellationOption() async {
    try {
      await PostRideProvider()
          .getCancellationOption(
              serviceController.token, serviceController.langId.value)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null &&
              resp['data']['cancellationOptions'] != null) {
            cancellationOptionList.addAll(resp['data']['cancellationOptions']);
            if (cancellationOptionList.isNotEmpty && policyTypeId.value == "") {
              policyTypeId.value = cancellationOptionList[0].toString();
            }
          }
          if (resp['data'] != null &&
              resp['data']['cancellationTooltips'] != null) {
            cancellationOptionToolTipList
                .addAll(resp['data']['cancellationTooltips']);
          }

          if (resp['data'] != null &&
              resp['data']['cancellationLabels'] != null) {
            cancellationOptionLabelList
                .addAll(resp['data']['cancellationLabels']);
          }
        }
      }, onError: (err) {
        if (err is Map &&
            err.containsKey('type') &&
            err.containsKey('message')) {
          serviceController.showDialogue(err['message'], type: "error");
        } else if (err is Map &&
            err.containsKey('type') &&
            err['type'] == 'network') {
          serviceController.showDialogue(
              "No internet connection. Please check your network and try again.",
              type: "error");
        } else {
          serviceController.showDialogue(err.toString(), type: "error");
        }
      });
    } catch (exception) {
      if (exception is Map &&
          exception.containsKey('type') &&
          exception.containsKey('message')) {
        serviceController.showDialogue(exception['message'], type: "error");
      } else if (exception is Map &&
          exception.containsKey('type') &&
          exception['type'] == 'network') {
        serviceController.showDialogue(
            "No internet connection. Please check your network and try again.",
            type: "error");
      } else {
        serviceController.showDialogue(exception.toString(), type: "error");
      }
    }
  }

  bool validateBookingPrerequisites({bool requireMessage = true}) {
    errors.clear();

    if (seatAvailable.value <= 0) {
      errors.add({
        'title': "seats",
        'eList': ['Must select at least 1 seat']
      });
    }

    if (policyTypeId.value == '') {
      errors.add({
        'title': "policy",
        'eList': ['Must select at least 1 policy']
      });
    }

    if (requireMessage && messageDriverTextEditingController.text == '') {
      errors.add({
        'title': "message",
        'eList': ['Please enter message']
      });
    }

    if (agreeTerms.value != true) {
      errors.add({
        'title': "agree_terms",
        'eList': ['Please select agree terms']
      });
    }

    if (policyTypeId.value == "37" && firmAgreeTerms.value != true) {
      errors.add({
        'title': "firm_agree_terms",
        'eList': ['Please select agree terms']
      });
    }

    if (policyTypeId.value == "37" &&
        firmCancellationUnderstandChecked.value != true) {
      errors.add({
        'title': "firm_cancellation_understand",
        'eList': ['Please select agree terms']
      });
    }

    if (showPinkCheckBox.value == true && pinkAgreeTerms.value != true) {
      errors.add({
        'title': "pink_agree_terms",
        'eList': ['Please select agree terms']
      });
    }

    if (showExtraCareCheckBox.value == true &&
        extraCareAgreeTerms.value != true) {
      errors.add({
        'title': "extra_agree_terms",
        'eList': ['Please select agree terms']
      });
    }

    final isValid = errors.isEmpty;
    if (!isValid) {
      scrollToFirstError();
    }

    return isValid;
  }

  void scrollToFirstError() {
    if (errors.isEmpty) {
      return;
    }

    final firstErrorTitle = errors.first['title']?.toString();
    GlobalKey targetKey = termsSectionKey;

    if (firstErrorTitle == 'seats' || firstErrorTitle == 'policy') {
      targetKey = seatSectionKey;
    } else if (firstErrorTitle == 'message') {
      targetKey = messageSectionKey;
    }

    WidgetsBinding.instance.addPostFrameCallback((_) {
      final targetContext = targetKey.currentContext;
      if (targetContext != null) {
        Scrollable.ensureVisible(
          targetContext,
          duration: const Duration(milliseconds: 350),
          curve: Curves.easeInOut,
          alignment: 0.12,
        );
      }
    });
  }

  getCardsList() async {
    if (!validateBookingPrerequisites()) {
      return;
    }

    try {
      cards.clear();
      selectedCardId.value = 0;
      isOverlayLoading(true);
      PaymentOptionsProvider()
          .getCards(page, pageLimit, serviceController.token,
              serviceController.langId.value)
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
        serviceController.showDialogue(err.toString(), type: "error");
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString(), type: "error");
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

  bookingRidePaymentType(
      {String paymentType = "stripe",
      bool gPay = false,
      String token = ""}) async {
    logger.info(messageDriverTextEditingController.text);
    if (!validateBookingPrerequisites()) {
      return;
    }

    if (paymentType == "stripe" && selectedCardId.value == 0) {
      serviceController.showDialogue(
          "${popupTextDetail['need_to_select_card_message'] ?? "Need to select a card"}",
          type: "warning");
      return;
    }

    isOverlayLoading(true);
    var bookingCredit = "";
    var seatAmount = "";
    var onlinePayment = "";
    var cashPayment = "";
    var total = "";
    var taxAmount = 0.0;
    var bookingCreditMinor = 0;
    var seatAmountMinor = 0;
    var onlinePaymentMinor = 0;
    var cashPaymentMinor = 0;
    var totalMinor = 0;
    var taxAmountMinor = 0;

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
    final selectedSeatCount = _selectedSeatCount();
    bookingCreditMinor = bookingFeeAmountMinorForSeatCount(selectedSeatCount);
    seatAmountMinor =
        _firmDiscountedMinor(rideUnitPriceMinor() * selectedSeatCount);
    taxAmountMinor = _passengerTaxMinor(bookingCreditMinor);
    totalMinor = bookingCreditMinor + seatAmountMinor + taxAmountMinor;

    if (ride['payment_method_slug'] == "cash") {
      onlinePaymentMinor = bookingCreditMinor;
      cashPaymentMinor = seatAmountMinor;
    } else {
      onlinePaymentMinor = totalMinor;
      cashPaymentMinor = 0;
    }

    bookingCredit = _minorToMajorString(bookingCreditMinor);
    seatAmount = _minorToMajorString(seatAmountMinor);
    onlinePayment = _minorToMajorString(onlinePaymentMinor);
    cashPayment = _minorToMajorString(cashPaymentMinor);
    total = _minorToMajorString(totalMinor);
    taxAmount = _minorToMajor(taxAmountMinor);

    var paymentMethod = "";
    if (paymentType == "paypal") {
      paymentMethod = gPay ? "credit_card" : "paypal";
      var paypalPayment = 0.0;
      var paypalPaymentMinor = 0;
      if (currentUserBookedSeat.value != 0) {
        final payableSeatCount = _selectedSeatCount(payable: true);
        var payableBookingCreditMinor =
            bookingFeeAmountMinorForSeatCount(payableSeatCount);
        var payableSeatAmountMinor =
            _firmDiscountedMinor(rideUnitPriceMinor() * payableSeatCount);
        final payableTaxMinor = _passengerTaxMinor(payableBookingCreditMinor);

        if (ride['payment_method_slug'] == "cash") {
          paypalPaymentMinor = payableBookingCreditMinor + payableTaxMinor;
        } else {
          if (coffeeFromWall.value == true) {
            payableBookingCreditMinor = 0;
          }

          paypalPaymentMinor = payableBookingCreditMinor +
              payableSeatAmountMinor +
              payableTaxMinor;
        }
      } else {
        if (coffeeFromWall.value == false) {
          if (ride['payment_method_slug'] == "cash") {
            paypalPaymentMinor = onlinePaymentMinor + taxAmountMinor;
          } else {
            paypalPaymentMinor = onlinePaymentMinor;
          }
        } else {
          paypalPaymentMinor = onlinePaymentMinor - bookingCreditMinor;
        }
      }

      paypalPayment = _minorToMajor(paypalPaymentMinor);

      if (gPay == true) {
        await getGooglePayApplePay(
            paypalPayment,
            bookingCredit,
            seatAmount,
            cashPayment,
            total,
            onlinePayment,
            paymentMethod,
            bookingId,
            taxAmount,
            token);
      } else {
        await paypalMethod(
            paypalPayment,
            bookingCredit,
            seatAmount,
            cashPayment,
            total,
            onlinePayment,
            paymentMethod,
            bookingId,
            taxAmount);
      }
    } else if (paymentType == "stripe") {
      paymentMethod = "credit_card";
      await bookingRide(bookingCredit, seatAmount, cashPayment, total,
          onlinePayment, paymentMethod, bookingId, taxAmount);
    } else if (paymentType == "cash") {
      await bookingRide(bookingCredit, seatAmount, cashPayment, total,
          onlinePayment, paymentMethod, bookingId, taxAmount);
    }
  }

  paypalMethod(paypalPayment, bookingCredit, seatAmount, cashPayment, total,
      onlinePayment, paymentMethod, bookingId, taxAmount) async {
    isOverlayLoading(false);
    paypalEmail = "";
    paypalPayerId = "";

    if (kIsWeb) {
      serviceController.showDialogue(
        "PayPal is not available in the web app yet. Please use credit card or Google/Apple Pay.",
        type: "warning",
      );
      return;
    }

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
              paypalEmail =
                  (params['data']['payer']?['email_address'] ?? '').toString();
              paypalPayerId =
                  (params['data']['payer']?['payer_id'] ?? '').toString();
              captureId = params['data']['purchase_units'][0]['payments']
                  ['captures'][0]['id'];
              await bookingRide(bookingCredit, seatAmount, cashPayment, total,
                  onlinePayment, paymentMethod, bookingId, taxAmount);
            }
          },
          onError: (error) {
            serviceController.showDialogue(error.toString(), type: "error");
            isOverlayLoading(false);
          },
          onCancel: (params) {
            serviceController.showDialogue(
                "${popupTextDetail['paypal_not_completed_message'] ?? "Paypal payment is not complete"}");
            isOverlayLoading(false);
          }),
    );
  }

  bookingRide(bookingCredit, seatAmount, cashPayment, total, onlinePayment,
      paymentMethod, bookingId, taxAmount,
      {bool gPay = false}) async {
    try {
      if (!gPay && paymentMethod != "paypal") {
        paypalEmail = "";
        paypalPayerId = "";
      }

      if (ride['payment_method_slug'] == "cash") {
        if (coffeeFromWall.value == true) {
          paymentMethod = "cash";
          onlinePayment = 0;
        } else {
          if (balanceAmt >= double.parse(bookingCredit.toString()) &&
              balanceAmt != 0.0) {
            bookedByWallet.value = true;
            paymentMethod = "credit_card";
          }
        }
      } else {
        if (coffeeFromWall.value == true) {
          if (balanceAmt >= double.parse(onlinePayment.toString()) &&
              balanceAmt != 0.0) {
            bookedByWallet.value = true;
            paymentMethod = "credit_card";
          }
        } else {
          if (balanceAmt >= double.parse(onlinePayment.toString()) &&
              balanceAmt != 0.0) {
            bookedByWallet.value = true;
            paymentMethod = "credit_card";
          }
        }
      }

      if (ride['payment_method_slug'] == "cash") {
        onlinePayment = onlinePayment;
      } else {
        onlinePayment = _minorToMajorString(
            _majorToMinor(onlinePayment) - _majorToMinor(taxAmount));
      }

      var taxPercentage = 0.0;
      var taxType = "";
      var deductType = "";

      if (setting['deduct_tax'] != null &&
          setting['deduct_tax'] == "deduct_from_passenger") {
        deductType = setting['deduct_tax'];
        if (setting['tax_type'] == "state_wise_tax") {
          taxPercentage = stateTax.value;
        } else {
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
              fromStopId,
              toStopId,
              gPay,
              agreeTerms.value,
              firmAgreeTerms.value,
              pinkAgreeTerms.value,
              extraCareAgreeTerms.value,
              paypalEmail,
              paypalPayerId,
              firmCancellationUnderstandChecked.value)
          .then((resp) async {
        errorList.clear();
        if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message'].toString(),
              type: "error");
        } else if (resp['status'] != null && resp['status'] == "Success") {
          if (bookedByWallet.value == true) {
            bool isMyWalletControllerRegistered =
                Get.isRegistered<MyWalletController>();
            if (isMyWalletControllerRegistered) {
              var tempController = Get.find<MyWalletController>();
              tempController.balance.value =
                  balanceAmt - double.parse(onlinePayment.toString());
              tempController.balance.refresh();
            }
          }

          serviceController.navigationIndex.value = 1;
          if (Get.isRegistered<NavigationController>()) {
            Get.find<NavigationController>().currentNavIndex.value = 1;
          }
          if (Get.isRegistered<MyTripController>()) {
            Get.find<MyTripController>().openDefaultTabForCurrentUser();
          }
          Get.offAllNamed('/navigation');
        }
        isOverlayLoading(false);
      }, onError: (error) {
        isOverlayLoading(false);
        serviceController.showDialogue(error.toString(), type: "error");
      });
    } catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString(), type: "error");
    }
  }

  seatOnHold(seatId, index) async {
    if (errors.firstWhereOrNull((element) => element['title'] == "seats") !=
        null) {
      errors.remove(
          errors.firstWhereOrNull((element) => element['title'] == "seats"));
    }

    try {
      var type = "add";
      if (bookedSeatIds.contains(seatId)) {
        type = "remove";
      }
      await BookSeatProvider()
          .seatOnHold(serviceController.token, seatId, type)
          .then((resp) async {
        errorList.clear();
        if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message'].toString(),
              type: "error");
        } else if (resp['status'] != null && resp['status'] == "Success") {
          if (type == "add") {
            final shouldShowSeatHoldWarning =
                bookedSeatIds.isEmpty && hasShownSeatHoldWarning.value == false;
            bookedSeatIds.add(seatId);
            ride['pending_seat_detail'][index - 1]['status'] = "hold";
            seatAvailable.value = bookedSeatIds.length;
            if (shouldShowSeatHoldWarning) {
              final seatHoldMessage =
                  labelTextDetail['seat_hold_message'] ??
                      "Your selected seat(s) will be held for 10 minutes. If the booking isn't completed within that time, the seat(s) will be released and made available to others.";
              hasShownSeatHoldWarning.value = true;
              serviceController.showDialogue(
                seatHoldMessage.toString(),
                type: "info",
              );
            }
          } else {
            bookedSeatIds.remove(seatId);
            ride['pending_seat_detail'][index - 1]['status'] = "pending";
            seatAvailable.value = bookedSeatIds.length;
            if (bookedSeatIds.isEmpty) {
              hasShownSeatHoldWarning.value = false;
            }
          }
        }
        isOverlayLoading(false);
      }, onError: (error) {
        isOverlayLoading(false);
        serviceController.showDialogue(error.toString(), type: "error");
      });
    } catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString(), type: "error");
    }
  }

  getGooglePayApplePay(paypalPayment, bookingCredit, seatAmount, cashPayment,
      total, onlinePayment, paymentMethod, bookingId, taxAmount, token) async {
    await BookSeatProvider()
        .createPaymentIntent(serviceController.token, paypalPayment, token)
        .then((resp) async {
      final paymentIntentId = resp['paymentIntentId'];
      if (paymentIntentId != null) {
        captureId = paymentIntentId;
        await bookingRide(bookingCredit, seatAmount, cashPayment, total,
            onlinePayment, paymentMethod, bookingId, taxAmount,
            gPay: true);
      }
    }, onError: (error) {
      isOverlayLoading(false);
      serviceController.showDialogue(error.toString(), type: "error");
    });
  }
}
