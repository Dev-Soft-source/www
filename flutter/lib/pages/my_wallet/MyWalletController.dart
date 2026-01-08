import 'package:flutter/material.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:flutter_paypal_pay/flutter_paypal_pay.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/pages/book_seat/BookSeatProvider.dart';
import 'package:proximaride_app/pages/edit_profile/EditProfileProvider.dart';
import 'package:proximaride_app/pages/my_wallet/MyWalletProvider.dart';
import 'package:proximaride_app/pages/payment_options/PaymentOptionsProvider.dart';
import 'package:proximaride_app/services/debouncer.dart';
import 'package:proximaride_app/services/service.dart';

class MyWalletController extends GetxController with GetTickerProviderStateMixin{
  final serviceController = Get.find<Service>();
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

  late TabController tabController, passengerTabController, driverTabController;
  late PageController pageController, passengerPageController, driverPageController;

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

    await getPassengerMyRides();
    await getUserDetail();


    // TODO: implement onInit
    super.onInit();
  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
    cardNameController.dispose();
    cardNumberController.dispose();
    cvvCodeController.dispose();
    addressController.dispose();
    drAmountController.dispose();

  }


  getPassengerMyRides() async{
    try{
      isLoading(true);
      await MyWalletProvider().getPassengerMyRides(
          serviceController.token,
        serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['myRides'] != null){
            passengerRideList.addAll(resp['data']['myRides']);
          }
          if(resp['data'] != null && resp['data']['balance'] != null){
            balance.value = double.parse(resp['data']['balance'].toString());
          }
          if(resp['data'] != null && resp['data']['walletSettingPage'] != null){
            labelTextDetail.addAll(resp['data']['walletSettingPage']);
          }
          if(resp['data'] != null && resp['data']['messages'] != null){
            popupTextDetail.addAll(resp['data']['messages']);
          }
        }
        isLoading(false);
      },onError: (err){
        isLoading(false);
        serviceController.showDialogue(err.toString());
      });

    }catch (exception) {
      isLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }

  getStudentRewardPoint() async{
    try{
      isOverlayLoading(true);
      await MyWalletProvider().getStudentRewardPoint(
        serviceController.token,
        serviceController.langId.value,
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['rewardPointSettings'] != null){
            passengerRewardList.addAll(resp['data']['rewardPointSettings']);
          }
          if(resp['data'] != null && resp['data']['studentTotalRewardPoint'] != null){
            passengerRewardPoints.value = int.parse(resp['data']['studentTotalRewardPoint'].toString());
          }
        }
        isOverlayLoading(false);
      },onError: (err){
        isOverlayLoading(false);
        serviceController.showDialogue(err.toString());
      });

    }catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }

  getPaidOutData() async{
    try{
      isOverlayLoading(true);
      await MyWalletProvider().getPaidOutData(
        serviceController.token,
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['getPaidout'] != null){
            driverPaidOutList.addAll(resp['data']['getPaidout']);
          }
        }
        isOverlayLoading(false);
      },onError: (err){
        isOverlayLoading(false);
        serviceController.showDialogue(err.toString());
      });

    }catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }

  getDriverAvailableData() async{
    try{
      isOverlayLoading(true);
      await MyWalletProvider().getDriverAvailableData(
        serviceController.token,
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['getAvailableBalance'] != null){
            driverAvailableList.addAll(resp['data']['getAvailableBalance']);
          }
        }
        isOverlayLoading(false);
      },onError: (err){
        isOverlayLoading(false);
        serviceController.showDialogue(err.toString());
      });

    }catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }

  getDriverPendingData() async{
    try{
      isOverlayLoading(true);
      await MyWalletProvider().getDriverPendingData(
        serviceController.token,
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['getAvailableBalance'] != null){
            driverPendingList.addAll(resp['data']['getAvailableBalance']);
          }
        }
        isOverlayLoading(false);
      },onError: (err){
        isOverlayLoading(false);
        serviceController.showDialogue(err.toString());
      });

    }catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }

  getDriverRewardPoint() async{
    try{
      isOverlayLoading(true);
      await MyWalletProvider().getDriverRewardPoint(
        serviceController.token,
        serviceController.langId.value,
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['rewardPointSettings'] != null){
            driverRewardList.addAll(resp['data']['rewardPointSettings']);
          }
          if(resp['data'] != null && resp['data']['driverTotalRewardPoint'] != null){
            driverRewardPoints.value = int.parse(resp['data']['driverTotalRewardPoint'].toString());
          }
        }
        isOverlayLoading(false);
      },onError: (err){
        isOverlayLoading(false);
        serviceController.showDialogue(err.toString());
      });

    }catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }

  sendPayoutRequest() async{
    bool isConfirmed = await serviceController.showConfirmationDialog("${popupTextDetail['withdraw_message'] ?? "Are you sure you want to request admin for withdraw"}");
    if(isConfirmed){
      try{
        isOverlayLoading(true);
        MyWalletProvider().sendPayoutRequest(
            serviceController.token
        ).then((resp) async {
          if(resp['status'] != null && resp['status'] == "Error"){
            serviceController.showDialogue(resp['message'].toString());
          }else if(resp['status'] != null && resp['status'] == "Success"){
            driverAvailableList.clear();
            driverAvailableList.refresh();
            serviceController.showDialogue(resp['message'].toString());
          }
          isOverlayLoading(false);
        },onError: (err){
          isOverlayLoading(false);
          serviceController.showDialogue(err.toString());
        });

      }catch (exception) {
        isOverlayLoading(false);
        serviceController.showDialogue(exception.toString());
      }
    }
  }

  getToUpBalance() async{
    try{
      isOverlayLoading(true);
      await MyWalletProvider().getToUpBalance(
        serviceController.token,
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['topUpBalances'] != null){
            myBalanceList.addAll(resp['data']['topUpBalances']);
          }
        }
        isOverlayLoading(false);
      },onError: (err){
        isOverlayLoading(false);
        serviceController.showDialogue(err.toString());
      });

    }catch (exception) {
      isOverlayLoading(false);
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

  getCardsList() async {
    errors.clear();
    if (drAmountController.text.isEmpty || double.parse(drAmountController.text) <= 0) {
        var err = {
          'title': "amount",
          'eList': ["${labelTextDetail['purchase_top_up_error'] ?? 'Must add amount'}"]
        };
        errors.add(err);
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
        serviceController.showDialogue(err.toString());
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString());

    }
  }

  paypalMethod() async {

    errors.clear();
    if (drAmountController.text.isEmpty || double.parse(drAmountController.text) <= 0) {
      var err = {
        'title': "amount",
        'eList': ["${labelTextDetail['purchase_top_up_error'] ?? 'Must add amount'}"]
      };
      errors.add(err);
      return;
    }
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
            serviceController.showDialogue(error.toString());
            isOverlayLoading(false);
          },

          onCancel: (params) {
            isOverlayLoading(false);
          }),
    );
  }


  getGooglePayApplePay(token) async{
    await BookSeatProvider().createPaymentIntent(serviceController.token, gPayAmount.value, token)
        .then((resp) async {
      final paymentIntentId = resp['paymentIntentId'];
      if(paymentIntentId != null){
        captureId = paymentIntentId;
        await buyTopUpBalance(gPay: true);
      }
    }, onError: (error) {
      isOverlayLoading(false);
      serviceController.showDialogue(error.toString());
    });


  }


  buyTopUpBalance({bool gPay = false}) async {
    try {
      isOverlayLoading(true);
      MyWalletProvider()
          .buyTopUpBalance(
          serviceController.token,
          selectedCardId.value,
          drAmountController.text,
          captureId,
          gPay
          )
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message'].toString());
        } else if (resp['status'] != null && resp['status'] == "Success") {
          serviceController.thankYouMessage.value = resp['message'] ?? "You have successfully purchase top up balance";
          Get.offAllNamed("/thank_you/topUp");
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

  claimMyReward(type) async{
    try {
      isOverlayLoading(true);
      MyWalletProvider().claimMyReward(
        serviceController.token,
        type
      ).then((resp) async {
        if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message'].toString());
        } else if (resp['status'] != null && resp['status'] == "Success") {
          serviceController.showDialogue(resp['message'].toString());
          if(type == "student"){
            if(resp['data'] != null && resp['data']['studentTotalRewardPoint'] != null){
              passengerRewardPoints.value = int.parse(resp['data']['studentTotalRewardPoint'].toString());
            }
          }else{
            if(resp['data'] != null && resp['data']['driverTotalRewardPoint'] != null){
              driverRewardPoints.value = int.parse(resp['data']['driverTotalRewardPoint'].toString());
            }
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

  updatePageIndexValue(index) async{
    mainPageIndex.value = index;
    if(index == 0){
    }else if(index == 1){
      if(secondTabValue == -1){
        await getPaidOutData();
        secondTabValue = 1;
      }
    }
  }

  updatePassengerPageValue(index) async{
    if(index == 0){
      if(passengerRideList.isEmpty){
        passengerRidePage = 1;
        passengerRideLoadMore(false);
        passengerRideNoMoreData(false);
        await getPassengerMyRides();
      }
    }else if(index == 1){
      myBalancePage = 1;
      myBalanceLoadMore(false);
      myBalanceNoMoreData(false);
      await getToUpBalance();
    }else if(index == 2){
      if(passengerRewardList.isEmpty) {
        passengerRewardPage = 1;
        passengerRewardLoadMore(false);
        passengerRewardNoMoreData(false);
        await getStudentRewardPoint();
      }
    }
  }

  updateDriverPageValue(index) async{
    if(index == 0){
      if(driverPaidOutList.isEmpty){
        driverPaidOutPage = 1;
        driverPaidOutLoadMore(false);
        driverPaidOutNoMoreData(false);
        await getPaidOutData();
      }
    }else if(index == 1){
      if(driverAvailableList.isEmpty){
        driverAvailablePage = 1;
        driverAvailableLoadMore(false);
        driverAvailableNoMoreData(false);
        await getDriverAvailableData();
      }
    }else if(index == 2){
      if(driverPendingList.isEmpty) {
        driverPendingPage = 1;
        driverPendingLoadMore(false);
        driverPendingNoMoreData(false);
        await getDriverPendingData();
      }
    }else if(index == 3){
      if(driverRewardList.isEmpty) {
        driverRewardPage = 1;
        driverRewardLoadMore(false);
        driverRewardNoMoreData(false);
        await getDriverRewardPoint();
      }
    }
  }

  showPaymentButton(value) async{
    _debouncer.run(() async {
     if(value == ""){
       showPayment.value = false;
     }else{
       showPayment.value = true;
     }
    });
  }
}
