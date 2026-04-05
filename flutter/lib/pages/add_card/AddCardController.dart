import 'package:flutter/material.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:flutter_stripe/flutter_stripe.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/pages/book_seat/BookSeatController.dart';
import 'package:proximaride_app/pages/my_wallet/MyWalletController.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/service.dart';

import '../payment_options/PaymentOptionsController.dart';
import 'AddCardProvider.dart';

class AddCardController extends GetxController {
  final serviceController = Get.find<Service>();
  late PaymentOptionController paymentOptionController;
  BookSeatController? bookSeatController;
  MyWalletController? myWalletController;
  var isLoading = false.obs;
  var isOverlayLoading = false.obs;
  final errors = [].obs;

  ScrollController scrollController = ScrollController();
  /// Field initializers so the add-card UI never reads these before [onInit] runs.
  final TextEditingController cardNameController = TextEditingController();

  final Map<String, FocusNode> focusNodes = {};

  var makePrimaryCard = false.obs;

  var addEditType = "";

  var labelTextDetail = {}.obs;
  var validationMessageDetail = {}.obs;

  String pageTypeFrom = "";

  /// Stripe [CardField]: card number, expiry, CVC (no postal in-widget).
  final CardEditController cardFieldController = CardEditController();

  CardFieldInputDetails cardFieldDetails =
      const CardFieldInputDetails(complete: false);

  void onCardFieldChanged(CardFieldInputDetails? details) {
    cardFieldDetails = details ?? const CardFieldInputDetails(complete: false);
  }

  /// Optional ISO-2 hint for the card widget (CVC length, etc.); from payment options when available.
  String get defaultCardCountryCode {
    final c = paymentOptionController.stripeConfig['country']
        ?.toString()
        .trim()
        .toUpperCase();
    if (c != null && c.length == 2) {
      return c;
    }
    return 'CA';
  }

  @override
  void onInit() async {
    super.onInit();

    if (!Get.isRegistered<PaymentOptionController>()) {
      Get.put(PaymentOptionController());
    }
    paymentOptionController = Get.find<PaymentOptionController>();

    focusNodes['1'] = FocusNode();
    focusNodes['1']?.addListener(() {
      if (focusNodes['1']!.hasFocus == false) {
        validateField(
          'Name on card',
          'name_on_card',
          cardNameController.text,
        );
      }
    });

    isLoading(true);
    await getLabelTextDetail();
    isLoading(false);

    getType();
    pageTypeFrom = '';
    if (Get.isRegistered<PaymentOptionController>()) {
      paymentOptionController = Get.find<PaymentOptionController>();
      pageTypeFrom = 'paymentOptions';
    }
    if (Get.isRegistered<BookSeatController>()) {
      bookSeatController = Get.find<BookSeatController>();
      pageTypeFrom = 'bookSeat';
    }
    if (Get.isRegistered<MyWalletController>()) {
      myWalletController = Get.find<MyWalletController>();
      pageTypeFrom = 'myWallet';
    }
  }

  @override
  void onClose() {
    cardFieldController.dispose();
    scrollController.dispose();
    cardNameController.dispose();
    for (final n in focusNodes.values) {
      n.dispose();
    }
    super.onClose();
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
      }
      errorList.add(message ?? '$fieldData field is required');
      errors.add({'title': fieldName, 'eList': errorList});
      return;
    }

    if (errorList.isNotEmpty) {
      errors.add({'title': fieldName, 'eList': errorList});
    }
    update();
  }

  void getType() {
    final p = Get.parameters['type']?.trim() ?? '';
    if (p.isNotEmpty) {
      addEditType = p;
      return;
    }
    // Parameters can be empty on some navigations; infer from the current path.
    final path = Get.currentRoute.split('?').first;
    if (path.endsWith('/add')) {
      addEditType = 'add';
    } else if (path.endsWith('/edit')) {
      addEditType = 'edit';
    } else {
      addEditType = 'add';
    }
  }

  void _mergeNewCardIntoList(RxList<dynamic> cards, Map<String, dynamic> newCard) {
    if (cards.isEmpty) {
      cards.add(newCard);
    } else if (newCard['primary_card'] == "1") {
      cards[0]['primary_card'] = "0";
      final previousPrimary = cards[0];
      cards[0] = newCard;
      cards.add(previousPrimary);
    } else {
      cards.add(newCard);
    }
    cards.refresh();
  }

  Future<void> addCard(BuildContext context, double screenHeight) async {
    errors.clear();
    if (cardNameController.text.trim().isEmpty) {
      var message = validationMessageDetail['required'];
      message = message.replaceAll(
        ":Attribute",
        labelTextDetail['card_name_error'] ?? 'Card name',
      );
      errors.add({
        'title': "name_on_card",
        'eList': [message ?? 'Card name field is required'],
      });
      scrollError(MediaQuery.of(context).viewInsets.bottom, 1, screenHeight);
      return;
    }
    if (!cardFieldDetails.complete) {
      serviceController.showDialogue(
        'Please enter a valid card number, expiration date, and security code.',
        type: "error",
      );
      return;
    }
    errors.clear();

    final keyboardBottom = MediaQuery.of(context).viewInsets.bottom;
    isOverlayLoading(true);
    try {
      final intentResp = await AddCardProvider()
          .createSetupIntent(serviceController.token);
      if (intentResp is Map &&
          intentResp['status'] != null &&
          intentResp['status'] == 'Error') {
        isOverlayLoading(false);
        serviceController.showDialogue(
          intentResp['message']?.toString() ??
              'Could not start card setup. Please try again.',
          type: "error",
        );
        return;
      }
      if (intentResp is! Map || intentResp['data'] == null) {
        isOverlayLoading(false);
        serviceController.showDialogue(
            'Could not start card setup. Please try again.',
            type: "error");
        return;
      }
      final data = intentResp['data'] as Map;
      final clientSecret = data['clientSecret']?.toString();
      final setupIntentId = data['setupIntentId']?.toString();
      if (clientSecret == null ||
          clientSecret.isEmpty ||
          setupIntentId == null ||
          setupIntentId.isEmpty) {
        isOverlayLoading(false);
        serviceController.showDialogue(
            'Could not start card setup. Please try again.',
            type: "error");
        return;
      }

      var publishableKey = data['publishableKey']?.toString().trim() ?? '';
      if (publishableKey.isEmpty) {
        publishableKey = dotenv.env['STRIPE_KEY']?.trim() ?? '';
      }
      if (publishableKey.isEmpty || !publishableKey.startsWith('pk_')) {
        isOverlayLoading(false);
        serviceController.showDialogue(
          'Payment could not start: Stripe publishable key is missing. '
          'Set STRIPE_KEY in the server .env (and rebuild), or STRIPE_KEY in assets/.env for the app.',
          type: "error",
        );
        return;
      }
      Stripe.publishableKey = publishableKey;
      await Stripe.instance.applySettings();

      SetupIntent si;
      try {
        si = await Stripe.instance.confirmSetupIntent(
          paymentIntentClientSecret: clientSecret,
          params: PaymentMethodParams.card(
            paymentMethodData: PaymentMethodData(
              billingDetails: BillingDetails(
                name: cardNameController.text.trim(),
              ),
            ),
          ),
        );
      } on StripeException catch (e) {
        isOverlayLoading(false);
        if (e.error.code == FailureCode.Canceled) {
          return;
        }
        final msg = _stripeUserMessage(e);
        serviceController.showDialogue(msg, type: "error");
        return;
      }

      var status = si.status.toLowerCase();
      for (var i = 0; i < 5 && status == 'requires_action'; i++) {
        await Stripe.instance.handleNextActionForSetupIntent(clientSecret);
        si = await Stripe.instance.retrieveSetupIntent(clientSecret);
        status = si.status.toLowerCase();
      }

      if (status != 'succeeded') {
        isOverlayLoading(false);
        serviceController.showDialogue(
          si.lastSetupError?.message ??
              'Card setup did not complete. Please try again.',
          type: "error",
        );
        return;
      }

      final primaryStr = paymentOptionController.cards.isNotEmpty
          ? (makePrimaryCard.value ? '1' : '0')
          : '1';

      AddCardProvider()
          .addCardWithSetupIntent(
        serviceController.token,
        cardNameController.text.trim(),
        setupIntentId,
        primaryStr,
      )
          .then(
        (resp) async {
          if (resp is! Map) {
            isOverlayLoading(false);
            return;
          }
          if (resp['status'] != null && resp['status'] == "Error") {
            serviceController.showDialogue(resp['message'], type: "error");
          } else if (resp['errors'] != null) {
            if (resp['errors']['name_on_card'] != null) {
              errors.add({
                'title': "name_on_card",
                'eList': resp['errors']['name_on_card'],
              });
              scrollError(keyboardBottom, 1, screenHeight);
            }
          } else if (resp['status'] != null && resp['status'] == "Success") {
            final rawCard = resp['data'] != null ? resp['data']['card'] : null;
            if (rawCard is Map) {
              final newCard = Map<String, dynamic>.from(rawCard);
              if (pageTypeFrom == 'paymentOptions') {
                _mergeNewCardIntoList(paymentOptionController.cards, newCard);
              }
              if (pageTypeFrom == 'bookSeat') {
                _mergeNewCardIntoList(bookSeatController!.cards, newCard);
              }
              if (pageTypeFrom == 'myWallet') {
                _mergeNewCardIntoList(myWalletController!.cards, newCard);
              }
            }
            Get.back();
            serviceController.showDialogue(resp['message'], type: "success");
            cardNameController.clear();
            makePrimaryCard.value = false;
          }
          isOverlayLoading(false);
        },
        onError: (err) {
          isOverlayLoading(false);
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
    } on StripeConfigException catch (e) {
      isOverlayLoading(false);
      serviceController.showDialogue(
        e.message.isNotEmpty
            ? e.message
            : 'Stripe is not configured. Add STRIPE_KEY to assets/.env or update the app.',
        type: "error",
      );
    } catch (exception) {
      isOverlayLoading(false);
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

  String _stripeUserMessage(StripeException e) {
    final err = e.error;
    final localized = err.localizedMessage?.trim();
    if (localized != null && localized.isNotEmpty) {
      return localized;
    }
    final message = err.message?.trim();
    if (message != null && message.isNotEmpty) {
      return message;
    }
    return 'Could not complete card setup.';
  }

  void scrollError(double keyboardBottomInset, int position, double screenHeight) {
    var pos = position * 100.0;
    if (keyboardBottomInset > 0) {
      pos -= 100.0;
    }
    scrollController.animateTo(
      pos - screenHeight / 4,
      duration: const Duration(milliseconds: 300),
      curve: Curves.easeInOut,
    );
  }
}
