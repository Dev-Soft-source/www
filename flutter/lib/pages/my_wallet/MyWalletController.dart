import 'dart:async';
import 'dart:io';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter/scheduler.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:flutter_paypal_pay/flutter_paypal_pay.dart';
import 'package:get/get.dart';
import 'package:pay/pay.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/consts/payment_config.dart';
import 'package:proximaride_app/pages/book_seat/BookSeatProvider.dart';
import 'package:proximaride_app/pages/edit_profile/EditProfileProvider.dart';
import 'package:proximaride_app/pages/my_profile/MyProfileController.dart';
import 'package:proximaride_app/pages/my_wallet/MyWalletProvider.dart';
import 'package:proximaride_app/pages/payment_options/PaymentOptionsProvider.dart';
import 'package:proximaride_app/services/connectivity_service.dart';
import 'package:proximaride_app/services/debouncer.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

class MyWalletController extends GetxController
    with GetTickerProviderStateMixin {
  final serviceController = Get.find<Service>();
  late final ConnectivityService connectivityService;
  final errorStateManager = ErrorStateManager();
  var isLoading = false.obs;
  var isOverlayLoading = false.obs;
  var isScrollLoading = false.obs;
  var mainPageIndex = 0.obs;
  var secondTabValue = -1;
  var thirdTabValue = -1;
  var errors = [].obs;
  var cardType = "".obs;
  var month = "".obs;
  var year = "".obs;
  var totalYear = 70;
  var startYear = 2024;
  var makePrimaryCard = false.obs;
  var cards = List<dynamic>.empty(growable: true).obs;
  var selectedCardId = 0.obs;
  var pageLimit = 15;
  var page = 1;
  var userDetail = {}.obs;
  var cityDetail = {}.obs;
  var stateDetail = {}.obs;
  var countryDetail = {}.obs;
  var captureId = "";
  var primaryCardCheck = false.obs;
  var showPayment = false.obs;
  var gPayAmount = 0.0.obs;
  var nativePayAvailable = false.obs;

  late TabController tabController, passengerTabController, driverTabController;
  late PageController pageController,
      passengerPageController,
      driverPageController;

  var passengerType = "ride".obs;
  var driverType = "paidOut".obs;

  var firstTimePage = 0;
  var balance = 0.0.obs;

  late TextEditingController cardNameController,
      cardNumberController,
      cvvCodeController,
      addressController,
      drAmountController;

  //Passenger Ride
  var passengerRideList = List<dynamic>.empty(growable: true).obs;
  ScrollController passengerRideScrollController = ScrollController();
  var passengerRidePage = 1;
  var passengerRideNoMoreData = false.obs;
  var passengerRideLoadMore = false.obs;

  //Passenger Balance
  var passengerBalanceList = List<dynamic>.empty(growable: true).obs;
  ScrollController passengerBalanceScrollController = ScrollController();
  var passengerBalancePage = 1;
  var passengerBalanceNoMoreData = false.obs;
  var passengerBalanceLoadMore = false.obs;

  //Passenger Student Reward
  var passengerRewardList = List<dynamic>.empty(growable: true).obs;
  ScrollController passengerRewardScrollController = ScrollController();
  var passengerRewardPage = 1;
  var passengerRewardNoMoreData = false.obs;
  var passengerRewardLoadMore = false.obs;
  var passengerRewardPoints = 0.obs;

  //Driver PaidOut
  var driverPaidOutList = List<dynamic>.empty(growable: true).obs;
  ScrollController driverPaidOutScrollController = ScrollController();
  var driverPaidOutPage = 1;
  var driverPaidOutNoMoreData = false.obs;
  var driverPaidOutLoadMore = false.obs;

  //Driver Available
  var driverAvailableList = List<dynamic>.empty(growable: true).obs;
  ScrollController driverAvailableScrollController = ScrollController();
  var driverAvailablePage = 1;
  var driverAvailableNoMoreData = false.obs;
  var driverAvailableLoadMore = false.obs;

  //Driver Pending
  var driverPendingList = List<dynamic>.empty(growable: true).obs;
  ScrollController driverPendingScrollController = ScrollController();
  var driverPendingPage = 1;
  var driverPendingNoMoreData = false.obs;
  var driverPendingLoadMore = false.obs;

  //Driver Reward
  var driverRewardList = List<dynamic>.empty(growable: true).obs;
  ScrollController driverRewardScrollController = ScrollController();
  var driverRewardPage = 1;
  var driverRewardNoMoreData = false.obs;
  var driverRewardLoadMore = false.obs;
  var driverRewardPoints = 0.obs;

  //Driver Pending
  var myBalanceList = List<dynamic>.empty(growable: true).obs;
  ScrollController myBalanceScrollController = ScrollController();
  var myBalancePage = 1;
  var myBalanceNoMoreData = false.obs;
  var myBalanceLoadMore = false.obs;
  var labelTextDetail = {}.obs;
  var popupTextDetail = {}.obs;

  final _debouncer = Debouncer(milliseconds: 500);

  @override
  void onInit() async {
    if (Get.isRegistered<MyProfileController>()) {
      final myProfileController = Get.find<MyProfileController>();
      final title = myProfileController.labelTextDetail['my_wallet_label'];
      if (title != null && title.toString().trim().isNotEmpty) {
        labelTextDetail['main_heading'] = title;
      }
    }

    tabController = TabController(length: 2, vsync: this);
    passengerTabController = TabController(length: 3, vsync: this);
    driverTabController = TabController(length: 4, vsync: this);

    pageController = PageController(initialPage: mainPageIndex.value);
    passengerPageController = PageController(initialPage: 0);
    driverPageController = PageController(initialPage: 0);

    cardNameController = TextEditingController();
    cardNumberController = TextEditingController();
    cvvCodeController = TextEditingController();
    addressController = TextEditingController();
    drAmountController = TextEditingController();

    // Initialize connectivity service
    try {
      connectivityService = Get.find<ConnectivityService>();
    } catch (e) {
      connectivityService = Get.put(ConnectivityService());
    }

    // Load initial data
    await checkNativePayAvailability();
    await loadInitialData();

    super.onInit();
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

  Future<void> loadInitialData() async {
    try {
      errorStateManager.setLoading();

      // NO connectivity check - let the API call proceed
      // Only catch exceptions if they actually occur

      // Execute init API calls
      await getPassengerMyRides();
      await getUserDetail();

      errorStateManager.setSuccess();
      isLoading(false);
    } on SocketException {
      logger.error("Network error in loadInitialData: SocketException");
      errorStateManager.setError(
        "No internet connection. Please check your network and try again.",
        ErrorType.network,
        loadInitialData,
      );
    } on TimeoutException {
      logger.error("Timeout error in loadInitialData");
      errorStateManager.setError(
        "Request timed out. Please check your connection and try again.",
        ErrorType.network,
        loadInitialData,
      );
    } catch (error) {
      logger.error("Error in loadInitialData: $error");

      if (error is Map &&
          error.containsKey('type') &&
          error.containsKey('message')) {
        errorStateManager.setError(
          error["message"],
          _parseErrorType(error["type"]),
          loadInitialData,
        );
      } else if (error.toString().contains("SocketException") ||
          error.toString().contains("Network is unreachable") ||
          error.toString().contains("Connection refused")) {
        errorStateManager.setError(
          "No internet connection. Please check your network and try again.",
          ErrorType.network,
          loadInitialData,
        );
      } else {
        errorStateManager.setError(
          "Unable to load wallet data. Please check your connection and try again.",
          ErrorType.unknown,
          loadInitialData,
        );
      }
    }
  }

  ErrorType _parseErrorType(String type) {
    switch (type) {
      case "network":
        return ErrorType.network;
      case "server":
        return ErrorType.server;
      default:
        return ErrorType.unknown;
    }
  }

  @override
  void onClose() {
    super.onClose();
    tabController.dispose();
    passengerTabController.dispose();
    driverTabController.dispose();
    pageController.dispose();
    passengerPageController.dispose();
    driverPageController.dispose();
    passengerRideScrollController.dispose();
    passengerBalanceScrollController.dispose();
    passengerRewardScrollController.dispose();
    driverPaidOutScrollController.dispose();
    driverAvailableScrollController.dispose();
    driverPendingScrollController.dispose();
    driverRewardScrollController.dispose();
    myBalanceScrollController.dispose();
    cardNameController.dispose();
    cardNumberController.dispose();
    cvvCodeController.dispose();
    addressController.dispose();
    drAmountController.dispose();
  }

  getPassengerMyRides() async {
    isLoading(true);
    await MyWalletProvider()
        .getPassengerMyRides(
            serviceController.token, serviceController.langId.value)
        .then((resp) async {
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['myRides'] != null) {
          passengerRideList.addAll(resp['data']['myRides']);
        }
        if (resp['data'] != null && resp['data']['balance'] != null) {
          balance.value = double.parse(resp['data']['balance'].toString());
        }
        if (resp['data'] != null && resp['data']['walletSettingPage'] != null) {
          logger.info(
              "Wallet Setting Page: ${resp['data']['walletSettingPage']}");
          labelTextDetail.addAll(resp['data']['walletSettingPage']);
        }
        if (resp['data'] != null && resp['data']['messages'] != null) {
          popupTextDetail.addAll(resp['data']['messages']);
        }
      }
      isLoading(false);
    }, onError: (err) {
      isLoading(false);
      throw err; // Propagate to loadInitialData
    });
  }

  getStudentRewardPoint() async {
    try {
      isOverlayLoading(true);
      await MyWalletProvider()
          .getStudentRewardPoint(
        serviceController.token,
        serviceController.langId.value,
      )
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null &&
              resp['data']['rewardPointSettings'] != null) {
            passengerRewardList.addAll(resp['data']['rewardPointSettings']);
          }
          if (resp['data'] != null &&
              resp['data']['studentTotalRewardPoint'] != null) {
            passengerRewardPoints.value =
                int.parse(resp['data']['studentTotalRewardPoint'].toString());
          }
        }
        isOverlayLoading(false);
      }, onError: (err) {
        isOverlayLoading(false);
        // Parse structured error from provider
        String errorMessage =
            "Unable to load student rewards. Please try again.";
        if (err is Map && err.containsKey('message')) {
          errorMessage = err['message'];
        }
        serviceController.showDialogue(errorMessage, type: "error");
      });
    } catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(
          "Unable to load student rewards. Please try again.",
          type: "error");
    }
  }

  getPaidOutData() async {
    try {
      isOverlayLoading(true);
      await MyWalletProvider()
          .getPaidOutData(
        serviceController.token,
      )
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null && resp['data']['getPaidout'] != null) {
            driverPaidOutList.addAll(resp['data']['getPaidout']);
          }
        }
        isOverlayLoading(false);
      }, onError: (err) {
        isOverlayLoading(false);
        // Parse structured error from provider
        String errorMessage = "Unable to load paid out data. Please try again.";
        if (err is Map && err.containsKey('message')) {
          errorMessage = err['message'];
        }
        serviceController.showDialogue(errorMessage, type: "error");
      });
    } catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(
          "Unable to load paid out data. Please try again.",
          type: "error");
    }
  }

  getDriverAvailableData() async {
    try {
      isOverlayLoading(true);
      await MyWalletProvider()
          .getDriverAvailableData(
        serviceController.token,
      )
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null &&
              resp['data']['getAvailableBalance'] != null) {
            driverAvailableList.addAll(resp['data']['getAvailableBalance']);
          }
        }
        isOverlayLoading(false);
      }, onError: (err) {
        isOverlayLoading(false);
        // Parse structured error from provider
        String errorMessage =
            "Unable to load available balance. Please try again.";
        if (err is Map && err.containsKey('message')) {
          errorMessage = err['message'];
        }
        serviceController.showDialogue(errorMessage, type: "error");
      });
    } catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(
          "Unable to load available balance. Please try again.",
          type: "error");
    }
  }

  getDriverPendingData() async {
    try {
      isOverlayLoading(true);
      await MyWalletProvider()
          .getDriverPendingData(
        serviceController.token,
      )
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null &&
              resp['data']['getAvailableBalance'] != null) {
            driverPendingList.addAll(resp['data']['getAvailableBalance']);
          }
        }
        isOverlayLoading(false);
      }, onError: (err) {
        isOverlayLoading(false);
        // Parse structured error from provider
        String errorMessage =
            "Unable to load pending balance. Please try again.";
        if (err is Map && err.containsKey('message')) {
          errorMessage = err['message'];
        }
        serviceController.showDialogue(errorMessage, type: "error");
      });
    } catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(
          "Unable to load pending balance. Please try again.",
          type: "error");
    }
  }

  getDriverRewardPoint() async {
    try {
      isOverlayLoading(true);
      await MyWalletProvider()
          .getDriverRewardPoint(
        serviceController.token,
        serviceController.langId.value,
      )
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null &&
              resp['data']['rewardPointSettings'] != null) {
            driverRewardList.addAll(resp['data']['rewardPointSettings']);
          }
          if (resp['data'] != null &&
              resp['data']['driverTotalRewardPoint'] != null) {
            driverRewardPoints.value =
                int.parse(resp['data']['driverTotalRewardPoint'].toString());
          }
        }
        isOverlayLoading(false);
      }, onError: (err) {
        isOverlayLoading(false);
        // Parse structured error from provider
        String errorMessage =
            "Unable to load driver rewards. Please try again.";
        if (err is Map && err.containsKey('message')) {
          errorMessage = err['message'];
        }
        serviceController.showDialogue(errorMessage, type: "error");
      });
    } catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(
          "Unable to load driver rewards. Please try again.",
          type: "error");
    }
  }

  sendPayoutRequest() async {
    bool isConfirmed = await serviceController.showConfirmationDialog(
        "${popupTextDetail['withdraw_message'] ?? "Are you sure you want to request admin for withdraw"}");
    if (isConfirmed) {
      try {
        isOverlayLoading(true);
        MyWalletProvider().sendPayoutRequest(serviceController.token).then(
            (resp) async {
          if (resp['status'] != null && resp['status'] == "Error") {
            serviceController.showDialogue(resp['message'].toString());
          } else if (resp['status'] != null && resp['status'] == "Success") {
            driverAvailableList.clear();
            driverAvailableList.refresh();
            serviceController.showDialogue(resp['message'].toString());
          }
          isOverlayLoading(false);
        }, onError: (err) {
          isOverlayLoading(false);
          // Parse structured error from provider
          String errorMessage =
              "Unable to send payout request. Please try again.";
          if (err is Map && err.containsKey('message')) {
            errorMessage = err['message'];
          }
          serviceController.showDialogue(errorMessage, type: "error");
        });
      } catch (exception) {
        isOverlayLoading(false);
        serviceController.showDialogue(
            "Unable to send payout request. Please try again.",
            type: "error");
      }
    }
  }

  getToUpBalance() async {
    try {
      isOverlayLoading(true);
      await MyWalletProvider()
          .getToUpBalance(
        serviceController.token,
      )
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null && resp['data']['topUpBalances'] != null) {
            myBalanceList.addAll(resp['data']['topUpBalances']);
          }
        }
        isOverlayLoading(false);
      }, onError: (err) {
        isOverlayLoading(false);
        // Parse structured error from provider
        String errorMessage =
            "Unable to load top-up balance. Please try again.";
        if (err is Map && err.containsKey('message')) {
          errorMessage = err['message'];
        }
        serviceController.showDialogue(errorMessage, type: "error");
      });
    } catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(
          "Unable to load top-up balance. Please try again.",
          type: "error");
    }
  }

  getUserDetail() async {
    EditProfileProvider()
        .getUserDetail(serviceController.token, serviceController.langId.value)
        .then((resp) async {
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
      throw err; // Propagate to loadInitialData
    });
  }

  getCardsList() async {
    errors.clear();
    if (drAmountController.text.isEmpty ||
        double.parse(drAmountController.text) <= 0) {
      var err = {
        'title': "amount",
        'eList': [
          "${labelTextDetail['purchase_top_up_error'] ?? 'Must add amount'}"
        ]
      };
      errors.add(err);
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
              primaryCardCheck.value = true;
              selectedCardId.value = getPrimaryCard['id'];
            }
            isOverlayLoading(false);
            isLoading(false);
            Get.toNamed("/balance_book_cards");
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

  paypalMethod() async {
    errors.clear();
    if (drAmountController.text.isEmpty ||
        double.parse(drAmountController.text) <= 0) {
      var err = {
        'title': "amount",
        'eList': [
          "${labelTextDetail['purchase_top_up_error'] ?? 'Must add amount'}"
        ]
      };
      errors.add(err);
      return;
    }
    isOverlayLoading(false);

    if (kIsWeb) {
      serviceController.showDialogue(
        "PayPal is not available in the web app yet. Please use another payment method.",
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
                'value': drAmountController.text,
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
              await buyTopUpBalance();
            }
          },
          onError: (error) {
            serviceController.showDialogue(error.toString(), type: "error");
            isOverlayLoading(false);
          },
          onCancel: (params) {
            isOverlayLoading(false);
          }),
    );
  }

  getGooglePayApplePay(token) async {
    await BookSeatProvider()
        .createPaymentIntent(serviceController.token, gPayAmount.value, token)
        .then((resp) async {
      final paymentIntentId = resp['paymentIntentId'];
      if (paymentIntentId != null) {
        captureId = paymentIntentId;
        await buyTopUpBalance(gPay: true);
      }
    }, onError: (error) {
      isOverlayLoading(false);
      serviceController.showDialogue(error.toString(), type: "error");
    });
  }

  buyTopUpBalance({bool gPay = false}) async {
    try {
      isOverlayLoading(true);
      MyWalletProvider()
          .buyTopUpBalance(serviceController.token, selectedCardId.value,
              drAmountController.text, captureId, gPay)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message'].toString());
        } else if (resp['status'] != null && resp['status'] == "Success") {
          serviceController.thankYouMessage.value = resp['message'] ??
              "You have successfully purchase top up balance";
          Get.offAllNamed("/thank_you/topUp");
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

  claimMyReward(type) async {
    try {
      isOverlayLoading(true);
      MyWalletProvider().claimMyReward(serviceController.token, type).then(
          (resp) async {
        if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message'].toString());
        } else if (resp['status'] != null && resp['status'] == "Success") {
          serviceController.showDialogue(resp['message'].toString());
          if (type == "student") {
            if (resp['data'] != null &&
                resp['data']['studentTotalRewardPoint'] != null) {
              passengerRewardPoints.value =
                  int.parse(resp['data']['studentTotalRewardPoint'].toString());
            }
          } else {
            if (resp['data'] != null &&
                resp['data']['driverTotalRewardPoint'] != null) {
              driverRewardPoints.value =
                  int.parse(resp['data']['driverTotalRewardPoint'].toString());
            }
          }
        }
        isOverlayLoading(false);
      }, onError: (error) {
        logger.error(error.toString());
        isOverlayLoading(false);
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
      });
    } catch (exception) {
      logger.error(exception.toString());
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

  void _safeJumpToPage(PageController controller, int page) {
    if (isClosed) {
      return;
    }

    if (controller.hasClients) {
      controller.jumpToPage(page);
      return;
    }

    SchedulerBinding.instance.addPostFrameCallback((_) {
      if (isClosed || !controller.hasClients) {
        return;
      }
      controller.jumpToPage(page);
    });
  }

  updatePageIndexValue(index) async {
    mainPageIndex.value = index;
    if (index == 0) {
      // Reset passenger tab to first selection (index 0)
      passengerTabController.index = 0;
      _safeJumpToPage(passengerPageController, 0);
    } else if (index == 1) {
      // Reset driver tab to first selection (index 0)
      driverTabController.index = 0;
      _safeJumpToPage(driverPageController, 0);
      if (secondTabValue == -1) {
        await getPaidOutData();
        secondTabValue = 1;
      }
    }
  }

  updatePassengerPageValue(index) async {
    if (index == 0) {
      if (passengerRideList.isEmpty) {
        passengerRidePage = 1;
        passengerRideLoadMore(false);
        passengerRideNoMoreData(false);
        await getPassengerMyRides();
      }
    } else if (index == 1) {
      myBalancePage = 1;
      myBalanceLoadMore(false);
      myBalanceNoMoreData(false);
      // Clear existing items to avoid duplicate render when re-opening tab
      myBalanceList.clear();
      await getToUpBalance();
    } else if (index == 2) {
      if (passengerRewardList.isEmpty) {
        passengerRewardPage = 1;
        passengerRewardLoadMore(false);
        passengerRewardNoMoreData(false);
        await getStudentRewardPoint();
      }
    }
  }

  updateDriverPageValue(index) async {
    if (index == 0) {
      if (driverPaidOutList.isEmpty) {
        driverPaidOutPage = 1;
        driverPaidOutLoadMore(false);
        driverPaidOutNoMoreData(false);
        await getPaidOutData();
      }
    } else if (index == 1) {
      if (driverAvailableList.isEmpty) {
        driverAvailablePage = 1;
        driverAvailableLoadMore(false);
        driverAvailableNoMoreData(false);
        await getDriverAvailableData();
      }
    } else if (index == 2) {
      if (driverPendingList.isEmpty) {
        driverPendingPage = 1;
        driverPendingLoadMore(false);
        driverPendingNoMoreData(false);
        await getDriverPendingData();
      }
    } else if (index == 3) {
      if (driverRewardList.isEmpty) {
        driverRewardPage = 1;
        driverRewardLoadMore(false);
        driverRewardNoMoreData(false);
        await getDriverRewardPoint();
      }
    }
  }

  showPaymentButton(value) async {
    _debouncer.run(() async {
      if (value == "") {
        showPayment.value = false;
      } else {
        showPayment.value = true;
      }
    });
  }
}
