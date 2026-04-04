import 'package:flutter/material.dart';
import 'package:flutter_stripe/flutter_stripe.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/pages/book_seat/BookSeatController.dart';
import 'package:proximaride_app/pages/my_wallet/MyWalletController.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

import '../payment_options/PaymentOptionsController.dart';
import 'AddCardProvider.dart';
import 'card_number_input_formatter.dart';

class AddCardController extends GetxController {
  final serviceController = Get.find<Service>();
  late PaymentOptionController paymentOptionController;
  BookSeatController? bookSeatController;
  MyWalletController? myWalletController;
  var errorList = List.empty(growable: true).obs;
  var isLoading = false.obs;
  var isOverlayLoading = false.obs;
  var isScrollLoading = false.obs;
  final errors = [].obs;
  var scrollField = false;

  ScrollController scrollController = ScrollController();
  late TextEditingController cardNameController,
      cardNumberController,
      cvvCodeController,
      streetController,
      houseApartmentController,
      cityController,
      provinceController,
      countryController,
      postalCodeController;

  final Map<String, FocusNode> focusNodes = {};

  var cardType = "".obs;
  var month = "".obs;
  var year = "".obs;
  var makePrimaryCard = false.obs;

  var addEditType = "";
  var totalYear = 10;
  var startYear = 0;
  var editCardId = 0;

  String pageTypeFrom = "";

  var labelTextDetail = {}.obs;
  var validationMessageDetail = {}.obs;

  @override
  void onInit() async {
    // TODO: implement onInit
    super.onInit();

    if (!Get.isRegistered<PaymentOptionController>()) {
      Get.put(PaymentOptionController());
    }
    paymentOptionController = Get.find<PaymentOptionController>();

    startYear = DateTime.now().year;

    isLoading(true);
    await getLabelTextDetail();
    isLoading(false);

    cardNameController = TextEditingController();
    cardNumberController = TextEditingController();
    cvvCodeController = TextEditingController();
    streetController = TextEditingController();
    houseApartmentController = TextEditingController();
    cityController = TextEditingController();
    provinceController = TextEditingController();
    countryController = TextEditingController();
    postalCodeController = TextEditingController();

    countryController.text = "Canada";

    for (int i = 1; i <= 9; i++) {
      focusNodes[i.toString()] = FocusNode();
      // Attach the onFocusChange listener
      focusNodes[i.toString()]?.addListener(() {
        if (!focusNodes[i.toString()]!.hasFocus) {
          // Field has lost focus, trigger validation
          if (i == 1) {
            validateField(
              'Name on card',
              'name_on_card',
              cardNameController.text,
            );
          } else if (i == 2) {
            validateField(
              'Card number',
              'card_number',
              cardNumberDigits,
              type: 'numeric',
            );
          } else if (i == 3) {
            validateField(
              'CVV',
              'cvv',
              cvvCodeController.text,
              type: 'numeric',
            );
          } else if (i == 4) {
            validateField('Street', 'street', streetController.text);
          } else if (i == 5) {
            validateField('City', 'city', cityController.text);
          } else if (i == 6) {
            validateField('Province', 'province', provinceController.text);
          } else if (i == 7) {
            validateField('Country', 'country', countryController.text);
          } else if (i == 8) {
            validateField(
              'Postal code',
              'postal_code',
              postalCodeController.text,
            );
          }
        }
      });
    }

    getType();
    bool isRegistered = Get.isRegistered<PaymentOptionController>();
    if (isRegistered) {
      paymentOptionController = Get.find<PaymentOptionController>();
      pageTypeFrom = 'paymentOptions';
    }

    isRegistered = Get.isRegistered<BookSeatController>();
    if (isRegistered) {
      bookSeatController = Get.find<BookSeatController>();
      pageTypeFrom = 'bookSeat';
    }

    isRegistered = Get.isRegistered<MyWalletController>();
    if (isRegistered) {
      myWalletController = Get.find<MyWalletController>();
      pageTypeFrom = 'myWallet';
    }
  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
    scrollController.dispose();
    cardNameController.dispose();
    cardNumberController.dispose();
    cvvCodeController.dispose();
    streetController.dispose();
    houseApartmentController.dispose();
    cityController.dispose();
    provinceController.dispose();
    countryController.dispose();
    postalCodeController.dispose();
  }

  /// PAN digits only (spaces / non-digits removed) for Stripe and validation.
  String get cardNumberDigits =>
      cardNumberController.text.replaceAll(RegExp(r'\D'), '');

  /// Max digits allowed in the number field for the selected card type.
  int get cardNumberMaxDigits => maxPanDigitsForCardType(cardType.value);

  /// Trims the field if the user changes card type to one with a shorter max length.
  void clampCardNumberToMaxLength() {
    clampCardNumberField(cardNumberController, cardType.value);
  }

  Future<void> getLabelTextDetail() async {
    try {
      await StageProvider()
          .getLabelTextDetail(
        serviceController.langId.value,
        billingAddressSetting,
        serviceController.token,
      )
          .then(
        (resp) async {
          if (resp['status'] != null && resp['status'] == "Success") {
            if (resp['data'] != null &&
                resp['data']['billingAddressSettingPage'] != null) {
              labelTextDetail.addAll(
                resp['data']['billingAddressSettingPage'],
              );
            }

            if (resp['data'] != null &&
                resp['data']['validationMessages'] != null) {
              validationMessageDetail.addAll(
                resp['data']['validationMessages'],
              );
            }
          }
        },
        onError: (error) {
          if (error is Map &&
              error.containsKey('type') &&
              error.containsKey('message')) {
            serviceController.showDialogue(error['message'], type: "error");
          } else if (error is Map &&
              error.containsKey('type') &&
              error['type'] == 'network') {
            serviceController.showDialogue(
                "No internet connection. Please check your network and try again.",
                type: "error");
          } else {
            serviceController.showDialogue(error.toString(), type: "error");
          }
          isLoading(false);
        },
      );
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
      isLoading(false);
    }
  }

  void validateField(
    String fieldData,
    String fieldName,
    String fieldValue, {
    String type = 'string',
    bool isRequired = true,
    int wordsLimit = 50,
  }) {
    errors.removeWhere((element) => element['title'] == fieldName);
    List<String> errorList = [];

    if (isRequired && fieldValue.isEmpty) {
      var message = validationMessageDetail['required'];
      if (fieldName == "name_on_card") {
        message = message.replaceAll(
          ":Attribute",
          labelTextDetail['card_name_error'] ?? fieldData,
        );
      } else if (fieldName == "card_number") {
        message = message.replaceAll(
          ":Attribute",
          labelTextDetail['card_number_error'] ?? fieldData,
        );
      } else if (fieldName == "cvv") {
        message = message.replaceAll(
          ":Attribute",
          labelTextDetail['cvv_error'] ?? fieldData,
        );
      } else if (fieldName == "street") {
        message = message.replaceAll(
          ":Attribute",
          labelTextDetail['address_error'] ?? fieldData,
        );
      } else if (fieldName == "city") {
        message = message.replaceAll(
          ":Attribute",
          labelTextDetail['city_error'] ?? fieldData,
        );
      } else if (fieldName == "province") {
        message = message.replaceAll(
          ":Attribute",
          labelTextDetail['province_error'] ?? fieldData,
        );
      } else if (fieldName == "country") {
        message = message.replaceAll(
          ":Attribute",
          labelTextDetail['country_error'] ?? fieldData,
        );
      } else if (fieldName == "postal_code") {
        message = message.replaceAll(
          ":Attribute",
          labelTextDetail['postal_code_error'] ?? fieldData,
        );
      }
      errorList.add(message ?? '$fieldData field is required');
      errors.add({'title': fieldName, 'eList': errorList});
      return;
    }

    switch (type) {
      case 'numeric':
        if (fieldValue.isNotEmpty && double.tryParse(fieldValue) == null) {
          var message = validationMessageDetail['numeric'];
          if (fieldName == "card_number") {
            message = message.replaceAll(
              ":attribute",
              labelTextDetail['card_number_error'] ?? fieldData,
            );
          } else if (fieldName == "cvv") {
            message = message.replaceAll(
              ":attribute",
              labelTextDetail['cvv_error'] ?? fieldData,
            );
          }
          errorList.add(message ?? '$fieldName must be a number');
        }
        break;
      case 'date':
        if (fieldValue.isNotEmpty && DateTime.tryParse(fieldValue) == null) {
          errorList.add('$fieldName must be a valid date');
        }
        break;
      case 'time':
        if (fieldValue.isNotEmpty &&
            !RegExp(r'^\d{2}:\d{2}$').hasMatch(fieldValue)) {
          errorList.add('$fieldName must be in the format HH:MM');
        }
        break;
      case 'max_words':
        if (fieldValue.isNotEmpty &&
            fieldValue.split(' ').length > wordsLimit) {
          errorList.add('$fieldName must have at most $wordsLimit words');
        }
        break;
      default:
        break;
    }

    if (errorList.isNotEmpty) {
      errors.add({'title': fieldName, 'eList': errorList});
    }
    update();
  }

  void getType() {
    addEditType = Get.parameters['type'] ?? "";
  }

  addCard(context, screenHeight) async {
    errors.clear();
    scrollField = false;
    try {
      var tokenId = "";

      if (cardNameController.text.isEmpty ||
          cardNumberDigits.isEmpty ||
          cardType.value == "" ||
          month.value == "" ||
          year.value == "" ||
          cvvCodeController.text.isEmpty ||
          streetController.text.isEmpty ||
          cityController.text.isEmpty ||
          provinceController.text.isEmpty ||
          countryController.text.isEmpty ||
          postalCodeController.text.isEmpty) {
        if (cardNameController.text == "") {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
            ":Attribute",
            labelTextDetail['card_name_error'] ?? 'Card name',
          );
          var err = {
            'title': "name_on_card",
            'eList': [message ?? 'Card name field is required'],
          };
          errors.add(err);
          if (scrollField == false) {
            scrollError(context, 1, screenHeight);
            scrollField = true;
          }
        }
        if (cardNumberDigits.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
            ":Attribute",
            labelTextDetail['card_number_error'] ?? 'Card number',
          );
          var err = {
            'title': "card_number",
            'eList': [message ?? 'Card number field is required'],
          };
          errors.add(err);
          if (scrollField == false) {
            scrollError(context, 2, screenHeight);
            scrollField = true;
          }
        }
        if (cardType.value == "") {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
            ":Attribute",
            labelTextDetail['card_type_error'] ?? 'Card type',
          );
          var err = {
            'title': "card_type",
            'eList': [message ?? 'Card type field is required'],
          };
          errors.add(err);
          if (scrollField == false) {
            scrollError(context, 3, screenHeight);
            scrollField = true;
          }
        }
        if (month.value == "") {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
            ":Attribute",
            labelTextDetail['month_error'] ?? 'Month',
          );
          var err = {
            'title': "month",
            'eList': [message ?? 'Month is required'],
          };
          errors.add(err);
          if (scrollField == false) {
            scrollError(context, 4, screenHeight);
            scrollField = true;
          }
        }
        if (year.value == "") {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
            ":Attribute",
            labelTextDetail['year_error'] ?? 'Year',
          );
          var err = {
            'title': "year",
            'eList': [message ?? 'Year is required'],
          };
          errors.add(err);
          if (scrollField == false) {
            scrollError(context, 4, screenHeight);
            scrollField = true;
          }
        }
        if (cvvCodeController.text == "") {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
            ":Attribute",
            labelTextDetail['cvv_error'] ?? 'CVV code',
          );
          var err = {
            'title': "cvv",
            'eList': [message ?? 'CVV code field is required'],
          };
          errors.add(err);
          if (scrollField == false) {
            scrollError(context, 5, screenHeight);
            scrollField = true;
          }
        }
        if (streetController.text == "") {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
            ":Attribute",
            labelTextDetail['address_error'] ?? 'Street',
          );
          var err = {
            'title': "street",
            'eList': [message ?? 'Street field is required'],
          };
          errors.add(err);
          if (scrollField == false) {
            scrollError(context, 6, screenHeight);
            scrollField = true;
          }
        }
        if (cityController.text == "") {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
            ":Attribute",
            labelTextDetail['city_error'] ?? 'City',
          );
          var err = {
            'title': "city",
            'eList': [message ?? 'City field is required'],
          };
          errors.add(err);
          if (scrollField == false) {
            scrollError(context, 7, screenHeight);
            scrollField = true;
          }
        }
        if (provinceController.text == "") {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
            ":Attribute",
            labelTextDetail['province_error'] ?? 'Province',
          );
          var err = {
            'title': "province",
            'eList': [message ?? 'Province field is required'],
          };
          errors.add(err);
        }
        if (countryController.text == "") {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
            ":Attribute",
            labelTextDetail['country_error'] ?? 'Country',
          );
          var err = {
            'title': "country",
            'eList': [message ?? 'Country field is required'],
          };
          errors.add(err);
        }
        if (postalCodeController.text == "") {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
            ":Attribute",
            labelTextDetail['postal_code_error'] ?? 'Postal code',
          );
          var err = {
            'title': "postal_code",
            'eList': [message ?? 'Postal code field is required'],
          };
          errors.add(err);
        }
        return;
      }
      errors.clear();

      if (cardType.value == "Visa" && cardNumberDigits.length != 16) {
        var err = {
          'title': "card_number",
          'eList': ['Please enter a valid card number'],
        };
        errors.add(err);
        if (scrollField == false) {
          scrollError(context, 2, screenHeight);
          scrollField = true;
        }
        return;
      }
      if (cardType.value == "MasterCard" &&
          cardNumberDigits.length != 16) {
        var err = {
          'title': "card_number",
          'eList': ['Please enter a valid card number'],
        };
        errors.add(err);
        if (scrollField == false) {
          scrollError(context, 2, screenHeight);
          scrollField = true;
        }
        return;
      }
      if (cardType.value == "AmEx" && cardNumberDigits.length != 15) {
        var err = {
          'title': "card_number",
          'eList': ['Please enter a valid card number'],
        };
        errors.add(err);
        if (scrollField == false) {
          scrollError(context, 2, screenHeight);
          scrollField = true;
        }
        return;
      }
      if (cardType.value == "Dis" && cardNumberDigits.length != 16) {
        var err = {
          'title': "card_number",
          'eList': ['Please enter a valid card number'],
        };
        errors.add(err);
        if (scrollField == false) {
          scrollError(context, 2, screenHeight);
          scrollField = true;
        }
        return;
      }
      if (cardType.value == "CUP" &&
          !(cardNumberDigits.length == 16 ||
              cardNumberDigits.length == 19)) {
        var err = {
          'title': "card_number",
          'eList': ['Please enter a valid card number'],
        };
        errors.add(err);
        if (scrollField == false) {
          scrollError(context, 2, screenHeight);
          scrollField = true;
        }
        return;
      }
      if (cardType.value == "JC" &&
          !(cardNumberDigits.length == 16 ||
              cardNumberDigits.length == 19)) {
        var err = {
          'title': "card_number",
          'eList': ['Please enter a valid card number'],
        };
        errors.add(err);
        if (scrollField == false) {
          scrollError(context, 2, screenHeight);
          scrollField = true;
        }
        return;
      }
      if (cardType.value == "DiC" &&
          !(cardNumberDigits.length == 14 ||
              cardNumberDigits.length == 16)) {
        var err = {
          'title': "card_number",
          'eList': ['Please enter a valid card number'],
        };
        errors.add(err);
        if (scrollField == false) {
          scrollError(context, 2, screenHeight);
          scrollField = true;
        }
        return;
      }
      isOverlayLoading(true);

      CardTokenParams cardParams = CardTokenParams(
        type: TokenType.Card,
        name: cardNameController.text,
      );
      await Stripe.instance.dangerouslyUpdateCardDetails(
        CardDetails(
          number: cardNumberDigits,
          cvc: cvvCodeController.text.trim(),
          expirationMonth: int.parse(month.value.toString()),
          expirationYear: int.parse(year.value.toString()),
        ),
      );

      try {
        TokenData token = await Stripe.instance.createToken(
          CreateTokenParams.card(params: cardParams),
        );
        tokenId = token.id.toString();
      } on StripeException catch (e) {
        isOverlayLoading(false);
        serviceController.showDialogue(e.error.message?.replaceAll('.', ' '),
            type: "error");
      } catch (e) {
        isOverlayLoading(false);
        serviceController.showDialogue(
            e.toString().replaceAll(RegExp(r'^Exception:\s*'), ''),
            type: "error");
      }

      if (tokenId.isEmpty) {
        isOverlayLoading(false);
        return;
      }

      var address =
          "${streetController.text},${houseApartmentController.text},${cityController.text},${provinceController.text},${countryController.text},${postalCodeController.text}";

      logger.info(
        "Primary card: ${paymentOptionController.cards.isNotEmpty ? makePrimaryCard.value ? 1.toString() : 0.toString() : 1.toString()}",
      );

      AddCardProvider()
          .addCard(
        serviceController.token,
        cardNameController.text,
        cardNumberDigits,
        cardType.value.toString(),
        month.value.toString(),
        year.value.toString(),
        cvvCodeController.text,
        address,
        paymentOptionController.cards.isNotEmpty
            ? makePrimaryCard.value
                ? 1.toString()
                : 0.toString()
            : 1.toString(),
        tokenId,
      )
          .then(
        (resp) async {
          errorList.clear();
          if (resp['status'] != null && resp['status'] == "Error") {
            serviceController.showDialogue(resp['message'], type: "error");
          } else if (resp['errors'] != null) {
            if (resp['errors']['name_on_card'] != null) {
              var err = {
                'title': "name_on_card",
                'eList': resp['errors']['name_on_card'],
              };
              errors.add(err);
              if (scrollField == false) {
                scrollError(context, 1, screenHeight);
                scrollField = true;
              }
            }
            if (resp['errors']['address'] != null) {
              var err = {
                'title': "address",
                'eList': resp['errors']['address'],
              };
              errors.add(err);
            }
          } else if (resp['status'] != null && resp['status'] == "Success") {
            if (resp['data']['card'] != null) {
              if (pageTypeFrom == 'paymentOptions') {
                var cards = paymentOptionController.cards;
                var newCard = resp['data']['card'];
                if (cards.isEmpty) {
                  cards.add(newCard);
                } else if (newCard['primary_card'] == "1") {
                  cards[0]['primary_card'] = "0";
                  var previousPrimary = cards[0];
                  cards[0] = newCard;
                  cards.add(previousPrimary);
                } else {
                  cards.add(newCard);
                }
                cards.refresh();
              }
              if (pageTypeFrom == 'bookSeat') {
                var cards = bookSeatController!.cards;
                var newCard = resp['data']['card'];
                if (cards.isEmpty) {
                  cards.add(newCard);
                } else if (newCard['primary_card'] == "1") {
                  cards[0]['primary_card'] = "0";
                  var previousPrimary = cards[0];
                  cards[0] = newCard;
                  cards.add(previousPrimary);
                } else {
                  cards.add(newCard);
                }
                cards.refresh();
              }

              if (pageTypeFrom == 'myWallet') {
                var cards = myWalletController!.cards;
                var newCard = resp['data']['card'];
                if (cards.isEmpty) {
                  cards.add(newCard);
                } else if (newCard['primary_card'] == "1") {
                  cards[0]['primary_card'] = "0";
                  var previousPrimary = cards[0];
                  cards[0] = newCard;
                  cards.add(previousPrimary);
                } else {
                  cards.add(newCard);
                }
                cards.refresh();
              }
            }
            Get.back();
            serviceController.showDialogue(resp['message'], type: "success");

            cardNameController.clear();
            cardNumberController.clear();
            cardType.value = "";
            month.value = "";
            year.value = "";
            cvvCodeController.clear();

            makePrimaryCard.value = false;
          }
          isOverlayLoading(false);
        },
        onError: (err) {
          isOverlayLoading(false);
          isLoading(false);
          isOverlayLoading.refresh();
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
        },
      );
    } catch (exception) {
      isOverlayLoading(false);
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

  scrollError(context, position, screenHeight) {
    position = position * 100.0;
    if (MediaQuery.of(context).viewInsets.bottom > 0) {
      // Keyboard is visible, adjust the scroll to avoid the keyboard
      position -= 100.0; // Adjust as per your requirement
    }

    // Scroll to the calculated position with some margin
    scrollController.animateTo(
      position - screenHeight / 4, // This adjusts the position dynamically
      duration: Duration(milliseconds: 300),
      curve: Curves.easeInOut,
    );
  }
}
