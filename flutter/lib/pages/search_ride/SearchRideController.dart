import 'dart:async';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/pages/search_ride/SearchRideProvider.dart';
import 'package:proximaride_app/services/connectivity_service.dart';
import 'package:proximaride_app/services/service.dart';

class SearchRideController extends GetxController {
  final serviceController = Get.find<Service>();
  final errorStateManager = ErrorStateManager();
  late final ConnectivityService connectivityService;

  var isLoading = false.obs;
  var isOverlayLoading = false.obs;
  var driverAge = "".obs;
  var driverRating = "".obs;
  var driverPhone = "".obs;
  var passengerRating = "".obs;
  var paymentMethod = "".obs;
  var vehicleType = "".obs;
  var featureList = [].obs;
  var luggage = "".obs;
  var smoking = "".obs;
  var pet = "".obs;
  var errors = [].obs;
  var pinkRideReadOnly = false.obs;
  var pinkRideCheck = false.obs;
  var extraCareCheck = false.obs;

  var vehicleTypeList = [].obs;
  var vehicleTypeLabelList = [].obs;

  ScrollController scrollController = ScrollController();
  var rides = List<dynamic>.empty(growable: true).obs;
  var recentSearchList = List<dynamic>.empty(growable: true).obs;
  var smokingList = [].obs;
  var petList = [].obs;
  var smokingLabelList = [].obs;
  var petLabelList = [].obs;
  var rideFeatureList = [].obs;
  var rideFeatureLabelList = [].obs;
  var paymentOptionList = [].obs;
  var paymentOptionToolTipList = [].obs;
  var paymentOptionLabelList = [].obs;
  var bookingOptionList = [].obs;
  var bookingOptionToolTipList = [].obs;
  var bookingOptionLabelList = [].obs;
  var luggageList = [].obs;
  var luggageListToolTip = [].obs;
  var luggageListLabel = [].obs;
  var passengerRatingList = [].obs;
  var passengerRatingLabelList = [].obs;
  var isScrollLoading = false.obs;
  var noRideFound = false.obs;
  var noMoreData = false.obs;
  var filter = false.obs;
  var actionType = "".obs;
  var fromCityId = 0.obs;
  var toCityId = 0.obs;
  var pageLimit = 10;
  var page = 1;
  var searchTotal = 0.obs;

  var isAlreadyBooked = false.obs;
  var labelTextDetail = {}.obs;
  var popupTextDetail = {}.obs;
  var validationMessageDetail = {}.obs;
  var firmDiscount = "".obs;

  late TextEditingController fromTextEditingController,
      toTextEditingController,
      keywordTextEditingController,
      dateTextEditingController,
      driverNameEditingController;

  @override
  void onInit() async {
    super.onInit();

    // Initialize connectivity service
    try {
      connectivityService = Get.find<ConnectivityService>();
    } catch (e) {
      connectivityService = Get.put(ConnectivityService());
    }

    fromTextEditingController = TextEditingController();
    toTextEditingController = TextEditingController();
    keywordTextEditingController = TextEditingController();
    dateTextEditingController = TextEditingController();
    driverNameEditingController = TextEditingController();
    _listenToFieldChanges();

    await loadInitialData();
  }

  @override
  void onClose() {
    super.onClose();
  }

  Future<void> loadInitialData() async {
    try {
      errorStateManager.setLoading();

      await _loadSearchRideBootstrap();

      errorStateManager.setSuccess();
    } on SocketException {
      errorStateManager.setError(
        "No internet connection. Please check your network and try again.",
        ErrorType.network,
        loadInitialData,
      );
    } on TimeoutException {
      errorStateManager.setError(
        "Request timed out. Please check your connection and try again.",
        ErrorType.network,
        loadInitialData,
      );
    } catch (error) {
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
          "Unable to load search data. Please check your connection and try again.",
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

  Future<void> _loadSearchRideBootstrap() async {
    rides.clear();
    page = 1;
    noRideFound.value = false;
    noMoreData.value = false;
    isScrollLoading(false);

    isLoading(true);

    String features = "";
    if (featureList.isNotEmpty) {
      for (int i = 0; i < featureList.length; i++) {
        features += featureList[i];
        if (i < featureList.length - 1) {
          features += "=";
        }
      }
    }

    await SearchRideProvider()
        .getSearchRideBootstrap(
            toTextEditingController.text,
            fromTextEditingController.text,
            toCityId.value,
            fromCityId.value,
            keywordTextEditingController.text,
            dateTextEditingController.text,
            driverNameEditingController.text,
            driverAge.value,
            driverRating.value,
            driverPhone.value,
            passengerRating.value,
            paymentMethod.value,
            vehicleType.value,
            features,
            luggage.value,
            smoking.value,
            pet.value,
            pinkRideCheck.value,
            extraCareCheck.value,
            pageLimit,
            page,
            serviceController.token,
            serviceController.langId.value)
        .then((resp) async {
      if (resp['status'] != null && resp['status'] == "Success") {
        final data = resp['data'];
        if (data != null) {
          _applyFindRidePageFromBootstrap(data);
          if (data['searchRide'] != null) {
            _applySearchRideResultFromMap(data['searchRide']);
          }
          if (data['searchInit'] != null) {
            _applySearchInitData(data['searchInit']);
          }
        }
      }
      isLoading(false);
    }, onError: (err) {
      isLoading(false);
      throw err;
    });
  }

  void _applyFindRidePageFromBootstrap(dynamic raw) {
    if (raw is! Map) {
      return;
    }
    if (raw['findRidePage'] != null) {
      labelTextDetail.clear();
      labelTextDetail.addAll(
          Map<dynamic, dynamic>.from(raw['findRidePage'] as Map));
      _populateVehicleTypes(
        details: labelTextDetail,
        vehicleTypeOptions: raw['vehicleTypeOptions'],
      );
    }
    if (raw['messages'] != null) {
      popupTextDetail.clear();
      popupTextDetail.addAll(Map<dynamic, dynamic>.from(raw['messages'] as Map));
    }
    if (raw['validationMessages'] != null) {
      validationMessageDetail.clear();
      validationMessageDetail.addAll(
          Map<dynamic, dynamic>.from(raw['validationMessages'] as Map));
    }
  }

  void _applySearchRideResultFromMap(dynamic raw) {
    if (raw is! Map) {
      return;
    }
    final sr = Map<dynamic, dynamic>.from(raw);
    if (sr['rides'] != null &&
        sr['rides'].isNotEmpty &&
        sr['rides']['data'] != null) {
      rides.clear();
      rides.addAll(sr['rides']['data']);
      searchTotal.value = sr['rides']['total'] ?? 0;
      rides.refresh();
      if (sr['firm_cancellation_discount'] != null) {
        firmDiscount.value = sr['firm_cancellation_discount'].toString();
      }
    }
    if (sr['rides'] != null &&
        sr['rides'].isNotEmpty &&
        sr['rides']['data'] != null &&
        sr['rides']['data'].isEmpty) {
      noRideFound.value = true;
    }
    if (sr['recentSearches'] != null) {
      recentSearchList.clear();
      recentSearchList.addAll(sr['recentSearches']);
      recentSearchList.refresh();
    }
  }

  void _populateVehicleTypes({
    required Map<dynamic, dynamic> details,
    dynamic vehicleTypeOptions,
  }) {
    vehicleTypeList.clear();
    vehicleTypeLabelList.clear();

    final normalizedVehicleTypeOptions = vehicleTypeOptions is List
        ? vehicleTypeOptions
        : vehicleTypeOptions is Map
            ? vehicleTypeOptions.values.toList()
            : vehicleTypeOptions is Iterable
                ? vehicleTypeOptions.toList()
                : const [];

    if (normalizedVehicleTypeOptions.isNotEmpty) {
      final seenValues = <String>{};

      for (final option in normalizedVehicleTypeOptions) {
        if (option is! Map) {
          continue;
        }

        final value = option['features_setting_id']?.toString() ??
            option['id']?.toString() ??
            "";
        final label = option['name']?.toString() ??
            option['label']?.toString() ??
            option['slug']?.toString() ??
            "";

        if (value.isEmpty || label.isEmpty || seenValues.contains(value)) {
          continue;
        }

        seenValues.add(value);
        vehicleTypeList.add(value);
        vehicleTypeLabelList.add(label);
      }

      if (vehicleType.value.isNotEmpty &&
          !vehicleTypeList.contains(vehicleType.value)) {
        vehicleType.value = "";
      }
      return;
    }

    final options = [
      {
        'label': details['vehicle_type_convertible_text'] ?? "Convertable",
        'value': details['vehicle_type_convertible_value'],
      },
      {
        'label': details['vehicle_type_coupe_text'] ?? "Coupe",
        'value': details['vehicle_type_coupe_value'],
      },
      {
        'label': details['vehicle_type_hatchback_text'] ?? "Hatchback",
        'value': details['vehicle_type_hatchback_value'],
      },
      {
        'label': details['vehicle_type_minivan_text'] ?? "Minivan",
        'value': details['vehicle_type_minivan_value'],
      },
      {
        'label': details['vehicle_type_sedan_text'] ?? "Sedan",
        'value': details['vehicle_type_sedan_value'],
      },
      {
        'label': details['vehicle_type_station_wagon_text'] ?? "Station wagon",
        'value': details['vehicle_type_station_wagon_value'],
      },
      {
        'label': details['vehicle_type_suv_text'] ?? "SUV",
        'value': details['vehicle_type_suv_value'],
      },
      {
        'label': details['vehicle_type_truck_text'] ?? "Truck",
        'value': details['vehicle_type_truck_value'],
      },
      {
        'label': details['vehicle_type_van_text'] ?? "Van",
        'value': details['vehicle_type_van_value'],
      },
    ];

    final seenValues = <String>{};
    for (final option in options) {
      final value = option['value']?.toString() ?? "";
      if (value.isEmpty || seenValues.contains(value)) {
        continue;
      }

      seenValues.add(value);
      vehicleTypeLabelList.add(option['label']?.toString() ?? "");
      vehicleTypeList.add(value);
    }

    if (vehicleType.value.isNotEmpty &&
        !vehicleTypeList.contains(vehicleType.value)) {
      vehicleType.value = "";
    }
  }

  // Public method for user-triggered searches
  getSearchRide(type) async {
    try {
      if (type == 1 &&
          (fromTextEditingController.text == "" ||
              toTextEditingController.text == "")) {
        if (fromTextEditingController.text == "") {
          _removeFieldError('from');
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
              ":Attribute", labelTextDetail['from_error'] ?? 'Origin');
          var err = {
            'title': "from",
            'eList': [message ?? 'Origin field is required']
          };
          errors.add(err);
        }

        if (toTextEditingController.text == "") {
          _removeFieldError('to');
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
              ":Attribute", labelTextDetail['to_error'] ?? 'Destination');
          var err = {
            'title': "to",
            'eList': [message ?? 'Destination field is required']
          };
          errors.add(err);
        }
        return;
      }

      if (filter.value == true && actionType.value == "clear") {
        bool isConfirmed = await serviceController.showConfirmationDialog(
            "${popupTextDetail['search_result_clear_message'] ?? "Are you sure you want to clear your search result?"}");
        if (isConfirmed == false) {
          actionType.value = "";
          filter.value = false;
          return;
        } else {
          filter.value = false;
        }
      }
      rides.clear();
      page = 1;
      noRideFound.value = false;
      noMoreData.value = false;
      isScrollLoading(false);

      String features = "";
      if (featureList.isNotEmpty) {
        for (int i = 0; i < featureList.length; i++) {
          features += featureList[i];
          if (i < featureList.length - 1) {
            features += "=";
          }
        }
      }
      type == 0 ? isLoading(true) : isOverlayLoading(true);
      SearchRideProvider()
          .getSearchRide(
              toTextEditingController.text,
              fromTextEditingController.text,
              toCityId.value,
              fromCityId.value,
              keywordTextEditingController.text,
              dateTextEditingController.text,
              driverNameEditingController.text,
              driverAge.value,
              driverRating.value,
              driverPhone.value,
              passengerRating.value,
              paymentMethod.value,
              vehicleType.value,
              features,
              luggage.value,
              smoking.value,
              pet.value,
              pinkRideCheck.value,
              extraCareCheck.value,
              pageLimit,
              page,
              serviceController.token)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null) {
            _applySearchRideResultFromMap(resp['data']);
          }
        }
        type == 0 ? isLoading(false) : isOverlayLoading(false);
        if (type != 0) {
          Get.toNamed('/search_ride_result');
        }
      }, onError: (error) {
        type == 0 ? isLoading(false) : isOverlayLoading(false);
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
      type == 0 ? isLoading(false) : isOverlayLoading(false);
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

  Future<void> checkBooking(rideId, fromStopId, toStopId) async {
    try {
      SearchRideProvider().checkBooking(rideId, serviceController.token).then(
          (resp) async {
        if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message'].toString());
        } else if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null) {
            final ride = rides.firstWhere((ride) => ride['id'] == rideId);

            serviceController.loginUserDetail['passenger_average_rating'] =
                (serviceController
                            .loginUserDetail['passenger_average_rating'] ==
                        ""
                    ? '0.0'
                    : serviceController
                        .loginUserDetail['passenger_average_rating'])!;

            if (ride['features']
                    .any((feature) => feature['slug'] == 'pink_rides') &&
                (serviceController.loginUserDetail['gender'] != 'female' &&
                    serviceController.loginUserDetail['gender'] != 'Female')) {
              serviceController.showDialogue(
                  "${popupTextDetail['female_user_message'] ?? 'Only female passengers can select this ride'}");
              return;
            }
            if (ride['features'].any(
                    (feature) => feature['slug'] == 'with_review_passenger') &&
                int.parse(serviceController
                                .loginUserDetail['passenger_total_ratings'] !=
                            null
                        ? serviceController
                            .loginUserDetail['passenger_total_ratings']
                            .toString()
                        : "0") ==
                    0) {
              serviceController.showDialogue(
                  "${popupTextDetail['passenger_with_review_message'] ?? 'Driver only want passengers with reviews'}");
              return;
            }
            if (ride['features']
                    .any((feature) => feature['slug'] == 'star3_passenger') &&
                double.parse(serviceController
                        .loginUserDetail['passenger_average_rating']
                        .toString()) <
                    3) {
              serviceController.showDialogue(
                  "${popupTextDetail['star3_passenger_message'] ?? 'Driver only want passengers with-3 star reviews and above'}");
              return;
            }
            if (ride['features']
                    .any((feature) => feature['slug'] == 'star4_passenger') &&
                double.parse(serviceController
                        .loginUserDetail['passenger_average_rating']
                        .toString()) <
                    4) {
              serviceController.showDialogue(
                  "${popupTextDetail['star4_passenger_message'] ?? 'Driver want only passengers with-4 star reviews and above'}");
              return;
            }

            if (ride['features']
                    .any((feature) => feature['slug'] == 'star5-passenger') &&
                double.parse(serviceController
                                .loginUserDetail['passenger_average_rating'] !=
                            null
                        ? serviceController
                            .loginUserDetail['passenger_average_rating']
                            .toString()
                        : "0.0") <
                    5) {
              serviceController.showDialogue(
                  "${popupTextDetail['star5_passenger_message'] ?? 'Driver want only passengers with-5 star reviews'}");
              return;
            }

            if (resp['data']['hasBooking'] != null) {
              if (resp['data']['hasBooking']) {
                final query = Uri(queryParameters: {
                  'tripId': rideId.toString(),
                  'bookedSeat': resp['data']['seats'].toString(),
                  'fromStopId': fromStopId.toString(),
                  'toStopId': toStopId.toString(),
                }).query;
                Get.toNamed("/book_seat?$query");
                isAlreadyBooked.value = true;
              } else {
                final query = Uri(queryParameters: {
                  'from': fromTextEditingController.text,
                  'to': toTextEditingController.text,
                  'from_stop_id': fromStopId.toString(),
                  'to_stop_id': toStopId.toString(),
                }).query;
                Get.toNamed(
                    '/trip_detail/$rideId/findRide/findRide/0?$query');
              }
            }
          }
        }
      }, onError: (error) {
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

  void _applySearchInitData(dynamic raw) {
    if (raw is! Map) {
      return;
    }
    final data = Map<dynamic, dynamic>.from(raw);

    smokingList.clear();
    smokingLabelList.clear();
    petList.clear();
    petLabelList.clear();
    rideFeatureList.clear();
    passengerRatingList.clear();
    rideFeatureLabelList.clear();
    passengerRatingLabelList.clear();
    bookingOptionList.clear();
    bookingOptionToolTipList.clear();
    bookingOptionLabelList.clear();
    luggageList.clear();
    luggageListToolTip.clear();
    luggageListLabel.clear();
    paymentOptionList.clear();
    paymentOptionToolTipList.clear();
    paymentOptionLabelList.clear();

    if (data['preferencesOptions'] != null) {
      final po = Map<dynamic, dynamic>.from(data['preferencesOptions'] as Map);
      smokingList.add(po['smoking_option1']);
      smokingList.add(po['smoking_option2']);
      smoking.value = "21";
      smokingLabelList.add(po['smoking_option1_label']);
      smokingLabelList.add(po['smoking_option2_label']);
      petList.add(po['animals_option1']);
      petList.add(po['animals_option2']);
      petList.add(po['animals_option3']);
      pet.value = "23";
      petLabelList.add(po['animals_option1_label']);
      petLabelList.add(po['animals_option2_label']);
      petLabelList.add(po['animals_option3_label']);
    }

    if (data['features'] != null) {
      final f = Map<dynamic, dynamic>.from(data['features'] as Map);
      if (f['featuresOptions'] != null) {
        rideFeatureList.addAll(f['featuresOptions']);
      }
      if (f['featuresLabels'] != null) {
        rideFeatureLabelList.addAll(f['featuresLabels']);
      }
    }
    if (data['passengers'] != null) {
      final p = Map<dynamic, dynamic>.from(data['passengers'] as Map);
      if (p['passengerRatingOptions'] != null) {
        passengerRatingList.addAll(p['passengerRatingOptions']);
      }
      if (p['passengerRatingLabels'] != null) {
        passengerRatingLabelList.addAll(p['passengerRatingLabels']);
      }
    }
    if (data['booking'] != null) {
      final b = Map<dynamic, dynamic>.from(data['booking'] as Map);
      if (b['bookingOptions'] != null) {
        bookingOptionList.addAll(b['bookingOptions']);
      }
      if (b['bookingTooltips'] != null) {
        bookingOptionToolTipList.addAll(b['bookingTooltips']);
      }
      if (b['bookingLabels'] != null) {
        bookingOptionLabelList.addAll(b['bookingLabels']);
      }
    }
    if (data['luggage'] != null) {
      final l = Map<dynamic, dynamic>.from(data['luggage'] as Map);
      if (l['luggageOptions'] != null) {
        luggageList.addAll(l['luggageOptions']);
      }
      if (l['luggageTooltips'] != null) {
        luggageListToolTip.addAll(l['luggageTooltips']);
      }
      if (l['luggageLabels'] != null) {
        luggageListLabel.addAll(l['luggageLabels']);
      }
    }
    if (data['payment'] != null) {
      final pay = Map<dynamic, dynamic>.from(data['payment'] as Map);
      if (pay['paymentOptions'] != null) {
        paymentOptionList.addAll(pay['paymentOptions']);
      }
      if (pay['paymentTooltips'] != null) {
        paymentOptionToolTipList.addAll(pay['paymentTooltips']);
      }
      if (pay['paymentLabels'] != null) {
        paymentOptionLabelList.addAll(pay['paymentLabels']);
      }
    }
  }




  Future<void> getMoreRides() async {
    try {
      if (noMoreData.value == true) {
        return;
      }
      isScrollLoading(true);

      String features = "";
      if (featureList.isNotEmpty) {
        for (int i = 0; i < featureList.length; i++) {
          features += featureList[i];
          if (i < featureList.length - 1) {
            features += "=";
          }
        }
      }

      await SearchRideProvider()
          .getSearchRide(
              toTextEditingController.text,
              fromTextEditingController.text,
              toCityId.value,
              fromCityId.value,
              keywordTextEditingController.text,
              dateTextEditingController.text,
              driverNameEditingController.text,
              driverAge.value,
              driverRating.value,
              driverPhone.value,
              passengerRating.value,
              paymentMethod.value,
              vehicleType.value,
              features,
              luggage.value,
              smoking.value,
              pet.value,
              pinkRideCheck.value,
              extraCareCheck.value,
              pageLimit,
              page,
              serviceController.token)
          .then((resp) async {
        if (resp['data'] != null &&
            resp['data']['rides'] != null &&
            resp['data']['rides'].isNotEmpty &&
            resp['data']['rides']['data'] != null &&
            resp['data']['rides']['data'].isEmpty) {
          noMoreData(true);
          return;
        }

        if (resp['data'] != null &&
            resp['data']['rides'] != null &&
            resp['data']['rides'].isNotEmpty &&
            resp['data']['rides']['data'] != null) {
          rides.addAll(resp['data']['rides']['data']);
        }
        isScrollLoading(false);
      }, onError: (error) {
        isScrollLoading(false);
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
      isScrollLoading(false);
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

  clearFilter() {
    driverAge.value = "";
    driverRating.value = "";
    driverPhone.value = "";
    driverNameEditingController.text = "";
    passengerRating.value = "";
    paymentMethod.value = "";
    vehicleType.value = "";
    featureList.clear();
    luggage.value = "";
    smoking.value = "";
    pet.value = "";
  }

  void setOriginLocation({required String label, required int cityId}) {
    fromTextEditingController.text = label;
    fromCityId.value = cityId;
    _removeFieldError('from');
  }

  void setDestinationLocation({required String label, required int cityId}) {
    toTextEditingController.text = label;
    toCityId.value = cityId;
    _removeFieldError('to');
  }

  void swapLocations() {
    final tempLabel = fromTextEditingController.text;
    final tempCityId = fromCityId.value;
    fromTextEditingController.text = toTextEditingController.text;
    fromCityId.value = toCityId.value;
    toTextEditingController.text = tempLabel;
    toCityId.value = tempCityId;
  }

  void applyRecentSearch(Map<String, dynamic> recentSearch) {
    fromTextEditingController.text = recentSearch['from']?.toString() ?? '';
    toTextEditingController.text = recentSearch['to']?.toString() ?? '';
    fromCityId.value = int.tryParse(
            recentSearch['from_city_id']?.toString() ??
                recentSearch['origin_city_id']?.toString() ??
                '') ??
        0;
    toCityId.value = int.tryParse(
            recentSearch['to_city_id']?.toString() ??
                recentSearch['destination_city_id']?.toString() ??
                '') ??
        0;
  }

  void _listenToFieldChanges() {
    fromTextEditingController.addListener(() {
      if (fromTextEditingController.text.trim().isEmpty) {
        fromCityId.value = 0;
      }
      if (fromTextEditingController.text.trim().isNotEmpty) {
        _removeFieldError('from');
      }
    });

    toTextEditingController.addListener(() {
      if (toTextEditingController.text.trim().isEmpty) {
        toCityId.value = 0;
      }
      if (toTextEditingController.text.trim().isNotEmpty) {
        _removeFieldError('to');
      }
    });
  }

  void _removeFieldError(String title) {
    final index = errors
        .indexWhere((element) => element != null && element['title'] == title);
    if (index != -1) {
      errors.removeAt(index);
    }
  }

  void handleBackNavigation() {
    if (Get.key.currentState?.canPop() ?? false) {
      Get.back();
      return;
    }

    Get.offNamed('/navigation');
  }
}
