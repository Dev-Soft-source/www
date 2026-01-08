import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/chat/ChatController.dart';
import 'package:proximaride_app/pages/my_trips/MyTripProvider.dart';
import 'package:proximaride_app/services/service.dart';

class MyTripController extends GetxController with GetTickerProviderStateMixin{


  var tripSettings = {}.obs;
  var rideSettings = {}.obs;
  final serviceController = Get.find<Service>();
  late TabController tabController, tripTabController, rideTabController;
  late PageController pageController, tripPageController, ridePageController;
  var isLoading = false.obs;
  var isOverlayLoading = false.obs;
  var tripType = "upcoming".obs;
  var rideType = "upcoming".obs;
  var pageLimit = 5;
  var firstTimePage = 0;
  var mainTabValue = -1;
  var ride = {}.obs;
  var cancelRideInfo = {}.obs;
  late TextEditingController reviewTextEditingController, tripCancelTextEditingController, blockDaysTextEditingController;
  var vehicleCondition = 0.0.obs;
  var conscious = 0.0.obs;
  var comfort = 0.0.obs;
  var communication = 0.0.obs;
  var attitude = 0.0.obs;
  var hygiene = 0.0.obs;
  var respect = 0.0.obs;
  var safety = 0.0.obs;
  var timeliness = 0.0.obs;
  var errorList = List.empty(growable: true).obs;
  var errors = [].obs;
  var removePassengerType = "0".obs;
  var reviewPassengerImage = "".obs;
  var reviewPassengerName = "".obs;
  var passengerBookingId = "".obs;
  var removePassenger = "".obs;

  var labelTextDetail = {}.obs;
  var labelTextTripDetail = {}.obs;

  var reviewType = "";
  var rideId = "";
  var pageType = "";

  //UpcomingTrip
  var upComingTripList = List<dynamic>.empty(growable: true).obs;
  ScrollController upComingTripScrollController = ScrollController();
  var upComingTripPage = 1;
  var upComingTripNoMoreData = false.obs;
  var upComingTripLoadMore = false.obs;
  var upComingTripTotal = 0.obs;

  //CompletedTrip
  var completedTripList = List<dynamic>.empty(growable: true).obs;
  ScrollController completedTripScrollController = ScrollController();
  var completedTripPage = 1;
  var completedTripNoMoreData = false.obs;
  var completedTripLoadMore = false.obs;
  var completedTripTotal = 0.obs;

  //CancelledTrip
  var cancelledTripList = List<dynamic>.empty(growable: true).obs;
  ScrollController cancelledTripScrollController = ScrollController();
  var cancelledTripPage = 1;
  var cancelledTripNoMoreData = false.obs;
  var cancelledTripLoadMore = false.obs;
  var cancelledTripTotal = 0.obs;

  //UpcomingRide
  var upComingRideList = List<dynamic>.empty(growable: true).obs;
  ScrollController upComingRideScrollController = ScrollController();
  var upComingRidePage = 1;
  var upComingRideNoMoreData = false.obs;
  var upComingRideLoadMore = false.obs;
  var upComingRideTotal = 0.obs;

  //CompletedTrip
  var completedRideList = List<dynamic>.empty(growable: true).obs;
  ScrollController completedRideScrollController = ScrollController();
  var completedRidePage = 1;
  var completedRideNoMoreData = false.obs;
  var completedRideLoadMore = false.obs;
  var completedRideTotal = 0.obs;

  //CancelledRide
  var cancelledRideList = List<dynamic>.empty(growable: true).obs;
  ScrollController cancelledRideScrollController = ScrollController();
  var cancelledRidePage = 1;
  var cancelledRideNoMoreData = false.obs;
  var cancelledRideLoadMore = false.obs;
  var mainPageIndex = 0.obs;
  var cancelledRideTotal = 0.obs;
  var confirmRideCheckBox = false.obs;


@override
  void onInit() async{
    // TODO: implement onInit
    super.onInit();

    tabController = TabController(length: 2, vsync: this);
    tripTabController = TabController(length: 3, vsync: this);
    rideTabController = TabController(length: 3, vsync: this);

    pageController = PageController(initialPage: mainPageIndex.value);
    tripPageController = PageController(initialPage: 0);
    ridePageController = PageController(initialPage: 0);

    reviewTextEditingController = TextEditingController();
    tripCancelTextEditingController = TextEditingController();
    blockDaysTextEditingController = TextEditingController();

    if(serviceController.loginUserDetail['driver_liscense'] != "null"){
      tabController.index = 1;
      await getAllRides();
    }else{
      tabController.index = 0;
      await getAllTrips();
    }


    //paginateGetAllTrips();
    //paginateGetAllRides();
  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
    // upComingTripScrollController.dispose();
    // completedTripScrollController.dispose();
    // cancelledTripScrollController();
    //
    // upComingRideScrollController.dispose();
    // completedRideScrollController.dispose();
    // cancelledRideScrollController.dispose();
    //
    // tabController.dispose();
    // tripTabController.dispose();
    // rideTabController.dispose();
    //
    // pageController.dispose();
    // tripPageController.dispose();
    // ridePageController.dispose();
  }

  getAllTrips() async{
    try{
      firstTimePage == 0 ? isLoading(true) : isOverlayLoading(true);
      await MyTripProvider().getAllTrips(
        tripType.value == "upcoming" ? upComingTripPage : tripType.value == "completed" ? completedTripPage : cancelledTripPage,
        serviceController.token,
        tripType.value,
        pageLimit,
        serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){

          if(resp['data'] != null && resp['data']['bookings'] != null && resp['data']['bookings']['data'] != null){
            if(tripType.value == "upcoming"){
              upComingTripList.addAll(resp['data']['bookings']['data']);
              upComingTripTotal.value = resp['data']['bookings']['total'] ?? 0;
              if(resp['data'] != null && resp['data']['tripsPage'] != null && labelTextTripDetail.isEmpty){
                labelTextTripDetail.addAll(resp['data']['tripsPage']);
              }
              if(resp['data'] != null && resp['data']['rideDetailPage'] != null && labelTextDetail.isEmpty){
                labelTextDetail.addAll(resp['data']['rideDetailPage']);
              }


            }else if(tripType.value == "completed"){
              completedTripList.addAll(resp['data']['bookings']['data']);
              completedTripTotal.value = resp['data']['bookings']['total'] ?? 0;
              tripSettings.addAll(resp['data']['setting']);
            }else{

              cancelledTripList.addAll(resp['data']['bookings']['data']);
              cancelledTripTotal.value = resp['data']['bookings']['total'] ?? 0;

            }
          }
        }
        firstTimePage == 0 ? isLoading(false) : isOverlayLoading(false);
        firstTimePage = 1;
      },onError: (err){
        firstTimePage == 0 ? isLoading(false) : isOverlayLoading(false);
        serviceController.showDialogue(err.toString());

      });

    }catch (exception) {
      firstTimePage == 0 ? isLoading(false) : isOverlayLoading(false);
            serviceController.showDialogue(exception.toString());

    }
  }

  // void paginateGetAllTrips(){
  //   upComingTripScrollController.addListener(() async {
  //     if(upComingTripScrollController.position.pixels == upComingTripScrollController.position.maxScrollExtent){
  //       upComingTripPage++;
  //       await getMoreTripsList();
  //     }
  //   });
  //
  //   completedTripScrollController.addListener(() async {
  //     if(completedTripScrollController.position.pixels == completedTripScrollController.position.maxScrollExtent){
  //       completedTripPage++;
  //       await getMoreTripsList();
  //     }
  //   });
  //
  //   cancelledTripScrollController.addListener(() async {
  //     if(cancelledTripScrollController.position.pixels == cancelledTripScrollController.position.maxScrollExtent){
  //       cancelledTripPage++;
  //       await getMoreTripsList();
  //     }
  //   });
  // }

  getMoreTripsList() async{
    if(tripType.value == "upcoming" && upComingTripNoMoreData.value == true){
      return;
    }else if(tripType.value == "completed" && completedTripNoMoreData.value == true){
      return;
    }else if(tripType.value == "cancelled" && cancelledTripNoMoreData.value == true){
      return;
    }
    try {
      tripType.value == "upcoming" ? upComingTripLoadMore(true) : tripType.value == "completed" ? completedTripLoadMore(true) : cancelledTripLoadMore(true);
      await MyTripProvider().getAllTrips(
          tripType.value == "upcoming" ? upComingTripPage : tripType.value == "completed" ? completedTripPage : cancelledTripPage,
          serviceController.token,
          tripType.value,
          pageLimit,
        serviceController.langId.value
      ).then((resp) async{
        if(resp['data'] != null && resp['data']['bookings'] != null && resp['data']['bookings']['data'] != null && resp['data']['bookings']['data'].isNotEmpty){

        }else{
          tripType.value == "upcoming" ? upComingTripLoadMore(false) : tripType.value == "completed" ? completedTripLoadMore(false) : cancelledTripLoadMore(false);
          tripType.value == "upcoming" ? upComingTripNoMoreData(true) : tripType.value == "completed" ? completedTripNoMoreData(true) : cancelledTripNoMoreData(true);
        }
        if(resp['data'] != null && resp['data']['bookings'] != null && resp['data']['bookings']['data'] != null){
          if(tripType.value == "upcoming"){
            upComingTripList.addAll(resp['data']['bookings']['data']);
          }else if(tripType.value == "completed"){
            completedTripList.addAll(resp['data']['bookings']['data']);
          }else{
            cancelledTripList.addAll(resp['data']['bookings']['data']);
          }
        }
        tripType.value == "upcoming" ? upComingTripLoadMore(false) : tripType.value == "completed" ? completedTripLoadMore(false) : cancelledTripLoadMore(false);
      } ,onError: (err){
        tripType.value == "upcoming" ? upComingTripLoadMore(false) : tripType.value == "completed" ? completedTripLoadMore(false) : cancelledTripLoadMore(false);
        
        serviceController.showDialogue(err.toString());

      });
    } catch (exception) {
      tripType.value == "upcoming" ? upComingTripLoadMore(false) : tripType.value == "completed" ? completedTripLoadMore(false) : cancelledTripLoadMore(false);
            serviceController.showDialogue(exception.toString());

    }
  }

  loadMoreTrips(type) async{
    if(type == "upcoming"){
      tripType.value = "upcoming";
      upComingTripPage++;
      await getMoreTripsList();
    }else if(type == "completed"){
      tripType.value = "completed";
      completedTripPage++;
      await getMoreTripsList();
    }else if(type == "cancelled"){
      tripType.value = "cancelled";
      cancelledTripPage++;
      await getMoreTripsList();
    }
  }

  updateTripPageValue(index) async{
    if(index == 0){
      tripType.value = "upcoming";
      if(upComingTripList.isEmpty){
        upComingTripPage = 1;
        upComingTripLoadMore(false);
        upComingTripNoMoreData(false);
        await getAllTrips();
      }
    }else if(index == 1){
      tripType.value = "completed";
      if(completedTripList.isEmpty){
        completedTripPage = 1;
        completedTripLoadMore(false);
        completedTripNoMoreData(false);
        await getAllTrips();
      }
    }else if(index == 2){
      tripType.value = "cancelled";
      if(cancelledTripList.isEmpty) {
        cancelledTripPage = 1;
        cancelledTripLoadMore(false);
        cancelledTripNoMoreData(false);
        await getAllTrips();
      }
    }
  }

  updatePageIndexValue(index) async{
    mainPageIndex.value = index;
    if(index == 0){
      if(upComingTripList.isEmpty){
        await getAllTrips();
        mainTabValue = 1;
      }
    }else if(index == 1){
      if(mainTabValue == -1 && upComingRideList.isEmpty){
        await getAllRides();
        mainTabValue = 1;
      }
    }
  }

  getAllRides() async{
    try{
      isOverlayLoading(true);
      await MyTripProvider().getAllRides(
          rideType.value == "upcoming" ? upComingRidePage : rideType.value == "completed" ? completedRidePage : cancelledRidePage,
          serviceController.token,
          rideType.value,
          pageLimit,
        serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['rides'] != null && resp['data']['rides']['data'] != null){
            if(rideType.value == "upcoming"){
              upComingRideList.addAll(resp['data']['rides']['data']);
              upComingRideTotal.value = resp['data']['rides']['total'] ?? 0;

              if(resp['data'] != null && resp['data']['tripsPage'] != null && labelTextTripDetail.isEmpty){
                labelTextTripDetail.addAll(resp['data']['tripsPage']);
              }

              if(resp['data'] != null && resp['data']['rideDetailPage'] != null && labelTextDetail.isEmpty){
                labelTextDetail.addAll(resp['data']['rideDetailPage']);
              }

            }else if(rideType.value == "completed"){
              completedRideList.addAll(resp['data']['rides']['data']);

              completedRideTotal.value = resp['data']['rides']['total'] ?? 0;
            }else{
              cancelledRideList.addAll(resp['data']['rides']['data']);
              cancelledRideTotal.value = resp['data']['rides']['total'] ?? 0;
            }
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

  // void paginateGetAllRides(){
  //   upComingRideScrollController.addListener(() async {
  //     if(upComingRideScrollController.position.pixels == upComingRideScrollController.position.maxScrollExtent){
  //       upComingRidePage++;
  //       await getMoreRidesList();
  //     }
  //   });
  //
  //   completedRideScrollController.addListener(() async {
  //     if(completedRideScrollController.position.pixels == completedRideScrollController.position.maxScrollExtent){
  //       completedRidePage++;
  //       await getMoreRidesList();
  //     }
  //   });
  //
  //   cancelledRideScrollController.addListener(() async {
  //     if(cancelledRideScrollController.position.pixels == cancelledRideScrollController.position.maxScrollExtent){
  //       cancelledRidePage++;
  //       await getMoreRidesList();
  //     }
  //   });
  // }

  getMoreRidesList() async{
    if(rideType.value == "upcoming" && upComingRideNoMoreData.value == true){
      return;
    }else if(rideType.value == "completed" && completedRideNoMoreData.value == true){
      return;
    }else if(rideType.value == "cancelled" && cancelledRideNoMoreData.value == true){
      return;
    }
    try {
      rideType.value == "upcoming" ? upComingRideLoadMore(true) : rideType.value == "completed" ? completedRideLoadMore(true) : cancelledRideLoadMore(true);
      await MyTripProvider().getAllRides(
          rideType.value == "upcoming" ? upComingRidePage : rideType.value == "completed" ? completedRidePage : cancelledRidePage,
          serviceController.token,
          rideType.value,
          pageLimit,
          serviceController.langId.value
      ).then((resp) async{
        if(resp['data'] != null && resp['data']['rides'] != null && resp['data']['rides']['data'] != null && resp['data']['rides']['data'].isNotEmpty){

        }else{
          rideType.value == "upcoming" ? upComingRideLoadMore(false) : rideType.value == "completed" ? completedRideLoadMore(false) : cancelledRideLoadMore(false);
          rideType.value == "upcoming" ? upComingRideNoMoreData(true) : rideType.value == "completed" ? completedRideNoMoreData(true) : cancelledRideNoMoreData(true);
        }
        if(resp['data'] != null && resp['data']['rides'] != null && resp['data']['rides']['data'] != null){
          if(rideType.value == "upcoming"){
            upComingRideList.addAll(resp['data']['rides']['data']);
          }else if(rideType.value == "completed"){
            completedRideList.addAll(resp['data']['rides']['data']);
          }else{
            cancelledRideList.addAll(resp['data']['rides']['data']);
          }
        }
        rideType.value == "upcoming" ? upComingRideLoadMore(false) : rideType.value == "completed" ? completedRideLoadMore(false) : cancelledRideLoadMore(false);
      } ,onError: (err){
        rideType.value == "upcoming" ? upComingRideLoadMore(false) : rideType.value == "completed" ? completedRideLoadMore(false) : cancelledRideLoadMore(false);
        
        serviceController.showDialogue(err.toString());

      });
    } catch (exception) {
      rideType.value == "upcoming" ? upComingRideLoadMore(false) : rideType.value == "completed" ? completedRideLoadMore(false) : cancelledRideLoadMore(false);
            serviceController.showDialogue(exception.toString());

    }
  }

  loadMoreRides(type) async{
    if(type == "upcoming"){
      rideType.value = "upcoming";
      upComingRidePage++;
      await getMoreRidesList();
    }else if(type == "completed"){
      rideType.value = "completed";
      completedRidePage++;
      await getMoreRidesList();
    }else if(type == "cancelled"){
      rideType.value = "cancelled";
      cancelledRidePage++;
      await getMoreRidesList();
    }
  }

  updateRidePageValue(index) async{
    if(index == 0){
      rideType.value = "upcoming";
      if(upComingRideList.isEmpty){
        upComingRidePage = 1;
        upComingRideLoadMore(false);
        upComingRideNoMoreData(false);
        await getAllRides();
      }
    }else if(index == 1){
      rideType.value = "completed";
      if(completedRideList.isEmpty){
        completedRidePage = 1;
        completedRideLoadMore(false);
        completedRideNoMoreData(false);
        await getAllRides();
      }
    }else if(index == 2){
      rideType.value = "cancelled";
      if(cancelledRideList.isEmpty) {
        cancelledRidePage = 1;
        cancelledRideLoadMore(false);
        cancelledRideNoMoreData(false);
        await getAllRides();
      }
    }
  }

  addDriverReview(rideIds, reviewTypes) async{
    isOverlayLoading(true);
    ride.clear();
    vehicleCondition.value = 0.0;
    conscious.value = 0.0;
    comfort.value = 0.0;
    communication.value = 0.0;
    attitude.value = 0.0;
    hygiene.value = 0.0;
    respect.value = 0.0;
    safety.value = 0.0;
    timeliness.value = 0.0;
    errorList.clear();
    reviewTextEditingController.text = "";
    rideId = "";
    pageType = "";
    reviewType = "";
    var getRideDetail = completedTripList.firstWhereOrNull((element) => element['ride_id'] == rideIds);
    if(getRideDetail != null){
      ride.addAll(getRideDetail);

      rideId = rideIds.toString();
      reviewType = reviewTypes;
      Get.toNamed("/add_review");
    }
    isOverlayLoading(false);
  }

  getTripDetail(bookingId) async{
    errorList.clear();
    cancelRideInfo.clear();
    reviewTextEditingController.text = "";
    pageType = "";
    rideId = "";
    reviewType == "";
    var getRideDetail = upComingTripList.firstWhereOrNull((element) => element['id'] == bookingId);
    if(getRideDetail != null){
      cancelRideInfo.addAll(getRideDetail);
      isOverlayLoading(false);
      pageType = "trip";
      rideId = getRideDetail['ride_id'].toString();
      Get.toNamed('/trip_detail/${getRideDetail['ride_id']}/trip/upcoming/${getRideDetail['ride_detail_id']}');
    }
  }

  postDriverReview(rideId) async{
  errors.clear();

    if(reviewTextEditingController.text == ""){
      var err = {
        'title': "review",
        'eList' : ['Please enter review']
      };
      errors.add(err);
      return;
    }

    try{
      isOverlayLoading(true);
      await MyTripProvider().addReview(
        rideId,
        reviewTextEditingController.text.trim(),
        vehicleCondition.value,
        conscious.value,
        comfort.value,
        communication.value,
        attitude.value,
        hygiene.value,
        respect.value,
        safety.value,
        timeliness.value,
        serviceController.token
      ).then((resp) async {
        errorList.clear();
        if(resp['status'] != null && resp['status'] == "Error"){
          serviceController.showDialogue(resp['message'].toString());
        }else if(resp['errors'] != null){
          if(resp['errors']['review'] != null){
            errorList.addAll(resp['errors']['review']);
          }
        }else if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data']['rating'] != null){
            for(var completeTrip in completedTripList){
              if(completeTrip['ride_id'] == rideId){
                completeTrip['rating'] = resp['data']['rating'];
              }
            }
          }
          completedTripList.refresh();
          Get.back();
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

  bool checkCanceledRideSeats(String seats){
    for(int i=0;i<seats.length; i++)
      {
        if(seats[i] == "" || seats[i] == "" || seats[i] == "" || seats[i] == "" || seats[i] == "" || seats[i] == "" || seats[i] == "" || seats[i] == "" || seats[i] == "")
        {

        }
      }
    return false;
  }

  cancelMyBooking(bookingId) async{
  errors.clear();
    if(Get.parameters['pageType'] == "trip"){
      if(reviewTextEditingController.text == "" || tripCancelTextEditingController.text == ""){

        if(tripCancelTextEditingController.text == ""){
          var err = {
            'title': "seats",
            'eList' : ['Cancel seats field is required']
          };
          errors.add(err);
        }

        if(reviewTextEditingController.text == ""){
          var err = {
            'title': "message",
            'eList' : ['Message field is required']
          };
          errors.add(err);
        }
        return;
      }
    }

    if(pageType == "trip"){

      var sureMessage = labelTextTripDetail['cancel_booking_confirm_message'] ?? "Are you sure you want to cancel this booking?";
      var getBooking = upComingTripList.firstWhereOrNull((element) => element['id'].toString() ==  bookingId);
      if(getBooking != null){
        String rideDate = getBooking['ride']['date'];
        String rideTime = getBooking['ride']['time'];
        String bookedOn = getBooking['booked_on'];
        DateTime rideDateTime = DateTime.parse('$rideDate $rideTime');
        DateTime bookingDateTime = DateTime.parse(bookedOn);
        int hoursDifference = rideDateTime.difference(bookingDateTime).inHours;

        if (getBooking['type'] == "37"){
          sureMessage = labelTextTripDetail['cancel_booking_confirm_firm_message'] ?? "Are you sure you want to cancel booking?";
        }else{
          if(hoursDifference > 48){
            sureMessage = labelTextTripDetail['cancel_booking_confirm_48_hour_message'] ?? "Are you sure you want to cancel booking?";
          }else if(hoursDifference >= 12 && hoursDifference <= 48){
            sureMessage = labelTextTripDetail['cancel_booking_confirm_12_to_48_hour_message'] ?? "Are you sure you want to cancel booking?";
          }else if(hoursDifference < 12){
            sureMessage = labelTextTripDetail['cancel_booking_confirm_less_12_hour_message'] ?? "Are you sure you want to cancel booking?";
          }
        }
      }



      bool isConfirmed = await serviceController.showConfirmationDialog(sureMessage, cancelYesBtn: labelTextTripDetail['booking_cancel_btn_yes_label'] ?? "Yes, cancel it", cancelNoBtn: labelTextTripDetail['booking_cancel_btn_no_label'] ?? "No, take me back");

      if(isConfirmed == false){
        return;
      }
    }

    if(pageType == "ride"){
      bool isConfirmed = await serviceController.showConfirmationDialog(labelTextDetail['cancel_ride_confirmation'] ?? "Are you sure you want to cancel this ride", cancelYesBtn: labelTextDetail['cancel_ride_yes_btn'] ?? "Yes, cancel it", cancelNoBtn: labelTextDetail['cancel_ride_no_btn'] ?? "No, take me back" );
      if(isConfirmed == false){
        return;
      }
    }

    try{

      isOverlayLoading(true);
      await MyTripProvider().cancelMyBooking(
          bookingId,
          reviewTextEditingController.text.trim(),
          tripCancelTextEditingController.text.trim(),
          serviceController.token,
          pageType
      ).then((resp) async {
        errorList.clear();
        if(resp['status'] != null && resp['status'] == "Error"){
          serviceController.showDialogue(resp['message'].toString());
        }else if(resp['errors'] != null){
          if(resp['errors']['message'] != null){
            errorList.addAll(resp['errors']['message']);
          }
          if(resp['errors']['reason'] != null){
            errorList.addAll(resp['errors']['reason']);
          }
        }else if(resp['status'] != null && resp['status'] == "Success"){

          if(pageType == "trip"){
            isOverlayLoading(false);
            serviceController.navigationIndex.value = 0;
            serviceController.showDialogue(resp['message'].toString(), path: '/navigation', off: 1);

            // var getCancelTrip = upComingTripList.firstWhereOrNull((element) => element['id'] == bookingId);
            // if(getCancelTrip != null){
            //   print(resp['data']['booking']['status'] );
            //   if(tripCancelTextEditingController.text == getCancelTrip['seats'] && resp['data']['booking']['status'] == "4"){
            //     upComingTripList.removeWhere((element) => element['id'] == bookingId);
            //     print(upComingTripList);
            //     if(cancelledTripList.isNotEmpty){
            //       cancelledTripList.add(getCancelTrip);
            //     }
            //   }else{
            //     var index = int.parse(upComingTripList.indexWhere((element) => element['id'] == bookingId).toString());
            //     upComingTripList[index] = resp['data']['booking'];
            //   }
            // }
            // cancelledTripList.refresh();
            // upComingTripList.refresh();
            //
            // Get.back();
            // Get.back();
          }

          if(pageType == "ride"){
            serviceController.navigationIndex.value = 1;
            serviceController.showDialogue(resp['message'].toString(), path: '/navigation', off: 1);
            serviceController.showDialogue(resp['message'].toString());
            // var getCancelTrip = upComingRideList.firstWhereOrNull((element) => element['id'] == bookingId);
            // if(getCancelTrip != null){
            //   upComingRideList.removeWhere((element) => element['id'] == bookingId);
            //   if(cancelledRideList.isNotEmpty){
            //     cancelledRideList.add(getCancelTrip);
            //   }
            // }
            // cancelledRideList.refresh();
            // upComingRideList.refresh();
            //
            // Get.back();
            // Get.back();
          }



          tripCancelTextEditingController.clear();
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

  getRideDetail(bookingId) async{
    errorList.clear();
    cancelRideInfo.clear();
    reviewTextEditingController.text = "";
    tripCancelTextEditingController.text = "";
    pageType = "";
    rideId = "";
    reviewType == "";
    var getRideDetail = upComingRideList.firstWhereOrNull((element) => element['id'] == bookingId);
    if(getRideDetail != null){
      cancelRideInfo.addAll(getRideDetail);
      isOverlayLoading(false);
      pageType = "ride";
      rideId = getRideDetail['id'].toString();
      Get.toNamed('/trip_detail/${getRideDetail['id']}/ride/upcoming/0');
    }
  }

  getCompletedRideData(rideId) async{
    errorList.clear();
    cancelRideInfo.clear();
    reviewTextEditingController.text = "";
    tripCancelTextEditingController.text = "";
    var getRideDetail = completedRideList.firstWhereOrNull((element) => element['id'] == rideId);
    if(getRideDetail != null){
      cancelRideInfo.addAll(getRideDetail);
      isOverlayLoading(false);
      Get.toNamed('/review_passenger/$rideId');
    }
  }

  addPassengerReview(rideIds, reviewStatus, image, name, bookingId) async{
    isOverlayLoading(true);
    ride.clear();
    vehicleCondition.value = 0.0;
    conscious.value = 0.0;
    comfort.value = 0.0;
    communication.value = 0.0;
    attitude.value = 0.0;
    hygiene.value = 0.0;
    respect.value = 0.0;
    safety.value = 0.0;
    timeliness.value = 0.0;
    errorList.clear();
    reviewTextEditingController.text = "";
    tripCancelTextEditingController.text = "";
    rideId = "";
    pageType = "";
    reviewType = "";
    reviewPassengerImage.value = image ?? "";
    reviewPassengerName.value = name ?? "";
    passengerBookingId.value = bookingId;
    var getRideDetail = completedRideList.firstWhereOrNull((element) => element['id'] == rideIds);
    if(getRideDetail != null){
      ride.addAll(getRideDetail);
      reviewType = reviewStatus;
      rideId = rideIds.toString();
      Get.toNamed("/add_review");
    }

    isOverlayLoading(false);
  }

  storePassengerReview(bookingId) async{
  errors.clear();
  if(reviewTextEditingController.text == ""){
      var err = {
        'title': "review",
        'eList' : ['Please enter review']
      };
      errors.add(err);
      return;
    }
    try{
      isOverlayLoading(true);

      await MyTripProvider().storePassengerReview(
          bookingId,
          reviewTextEditingController.text.trim(),
          conscious.value,
          comfort.value,
          communication.value,
          attitude.value,
          hygiene.value,
          respect.value,
          safety.value,
          timeliness.value,
          serviceController.token
      ).then((resp) async {
        errorList.clear();
        if(resp['status'] != null && resp['status'] == "Error"){
          serviceController.showDialogue(resp['message'].toString());
        }else if(resp['errors'] != null){
          if(resp['errors']['review'] != null){
            errorList.addAll(resp['errors']['review']);
          }
        }else if(resp['status'] != null && resp['status'] == "Success"){

          if(resp['data']['rating'] != null){
            for(var rideInfo in cancelRideInfo['bookings']){
              if(rideInfo['id'] == int.parse(bookingId.toString())){
                rideInfo['rating'] = resp['data']['rating'];
              }
            }
            cancelRideInfo.refresh();
            var rideData = completedRideList.firstWhereOrNull((element) => element['id'] == rideId);
            if(rideData != null){
              for(var ride in rideData['bookings']){
                if(ride['id'] == bookingId){
                  ride['rating'] = resp['data']['rating'];
                }
              }
            }
          }
          completedRideList.refresh();
          Get.back();
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

  removePassengerFromRide(bookingId) async{

    var sureMessage = labelTextTripDetail['cancel_booking_confirm_message'] ?? "Are you sure you want to cancel this booking?";
    var getBooking = upComingTripList.firstWhereOrNull((element) => element['id'].toString() ==  bookingId);
    if(getBooking != null){
      String rideDate = getBooking['ride']['date'];
      String rideTime = getBooking['ride']['time'];
      String bookedOn = getBooking['booked_on'];
      DateTime rideDateTime = DateTime.parse('$rideDate $rideTime');
      DateTime bookingDateTime = DateTime.parse(bookedOn);
      int hoursDifference = rideDateTime.difference(bookingDateTime).inHours;

      if (getBooking['type'] == "37"){
        sureMessage = labelTextTripDetail['cancel_booking_confirm_firm_message'] ?? "Are you sure you want to cancel booking?";
      }else{
        if(hoursDifference > 48){
          sureMessage = labelTextTripDetail['cancel_booking_confirm_48_hour_message'] ?? "Are you sure you want to cancel booking?";
        }else if(hoursDifference >= 12 && hoursDifference <= 48){
          sureMessage = labelTextTripDetail['cancel_booking_confirm_12_to_48_hour_message'] ?? "Are you sure you want to cancel booking?";
        }else if(hoursDifference < 12){
          sureMessage = labelTextTripDetail['cancel_booking_confirm_less_12_hour_message'] ?? "Are you sure you want to cancel booking?";
        }
      }
    }

  bool isConfirmed = await serviceController.showConfirmationDialog(sureMessage, cancelYesBtn: labelTextTripDetail['booking_cancel_btn_yes_label'] ?? "Yes, cancel it", cancelNoBtn: labelTextTripDetail['booking_cancel_btn_no_label'] ?? "No, take me back");
  if(isConfirmed == false){
    return;
  }
    try{
      isOverlayLoading(true);
      await MyTripProvider().removePassengerFromRide(
          bookingId,
          removePassengerType.value,
          reviewTextEditingController.text.trim(),
          tripCancelTextEditingController.text.trim(),
          blockDaysTextEditingController.text.trim(),
          removePassenger.value,
          serviceController.token
      ).then((resp) async {
        errorList.clear();
        if(resp['status'] != null && resp['status'] == "Error"){
          serviceController.showDialogue(resp['message'].toString());
        }else if(resp['errors'] != null){
          if(resp['errors']['admin_message'] != null){
            errorList.addAll(resp['errors']['admin_message']);
          }if(resp['errors']['passenger_message'] != null){
            errorList.addAll(resp['errors']['passenger_message']);
          }
        }else if(resp['status'] != null && resp['status'] == "Success"){

          bool isRegistered = Get.isRegistered<ChatController>();
          if(isRegistered == true){
            var chatController = Get.find<ChatController>();
            await chatController.getChats();
          }

          serviceController.navigationIndex.value = 0;
          serviceController.showDialogue(resp['message'].toString(), path: '/navigation', off: 1);
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

  updateMyTrips() async{
    upComingTripList.clear();
    upComingTripPage = 1;
    upComingTripNoMoreData.value = false;
    upComingTripLoadMore.value = false;

    //CompletedTrip
    completedTripList.clear();
    completedTripPage = 1;
    completedTripNoMoreData.value = false;
    completedTripLoadMore.value = false;

    //CancelledTrip
    cancelledTripList.clear();
    cancelledTripPage = 1;
    cancelledTripNoMoreData.value = false;
    cancelledTripLoadMore.value = false;

    //UpcomingRide
    upComingRideList.clear();
    upComingRidePage = 1;
    upComingRideNoMoreData.value = false;
    upComingRideLoadMore.value = false;

    //CompletedTrip
    completedRideList.clear();
    completedRidePage = 1;
    completedRideNoMoreData.value = false;
    completedRideLoadMore.value = false;

    //CancelledRide
    cancelledRideList.clear();
    cancelledRidePage = 1;
    cancelledRideNoMoreData.value = false;
    cancelledRideLoadMore.value = false;

    tripType.value = "upcoming";
    rideType.value = "upcoming";
    firstTimePage = 0;
    getAllTrips();
  }

}