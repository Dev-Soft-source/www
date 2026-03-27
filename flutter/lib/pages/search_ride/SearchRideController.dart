import 'dart:async';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/pages/post_ride/PostRideProvider.dart';
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

    // Load secondary data after a delay with overlay loading
    Future.delayed(const Duration(seconds: 1), () async {
      isOverlayLoading(true);
      try {
        await _getRideFeatureOptions();
      } catch (error) {
        // Show dialog for secondary data load errors
        _handleSecondaryLoadError(error);
      }
      isOverlayLoading(false);
    });
  }

  @override
  void onClose() {
    super.onClose();
  }

  Future<void> loadInitialData() async {
    try {
      errorStateManager.setLoading();

      await _getLabelTextDetail();
      await _getSearchRide(0);

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

  void _handleSecondaryLoadError(dynamic error) {
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
  }

  // Private method for initial load
  Future<void> _getLabelTextDetail() async {
    isLoading(true);
    await SearchRideProvider()
        .getLabelTextDetail(serviceController.langId)
        .then((resp) async {
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['findRidePage'] != null) {
          labelTextDetail.addAll(resp['data']['findRidePage']);
          _populateVehicleTypes(
            details: labelTextDetail,
            vehicleTypeOptions: resp['data']['vehicleTypeOptions'],
          );
        }

        if (resp['data'] != null && resp['data']['messages'] != null) {
          popupTextDetail.addAll(resp['data']['messages']);
        }

        if (resp['data'] != null &&
            resp['data']['validationMessages'] != null) {
          validationMessageDetail.addAll(resp['data']['validationMessages']);
        }
      }
      isLoading(false);
    }, onError: (err) {
      isLoading(false);
      throw err; // Propagate to loadInitialData
    });
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

  // Private method for initial search
  Future<void> _getSearchRide(type) async {
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
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null &&
            resp['data']['rides'] != null &&
            resp['data']['rides'].isNotEmpty &&
            resp['data']['rides']['data'] != null) {
          rides.clear();
          rides.addAll(resp['data']['rides']['data']);
          searchTotal.value = resp['data']['rides']['total'] ?? 0;
          rides.refresh();
          firmDiscount.value =
              resp['data']['firm_cancellation_discount'].toString();
        }

        if (resp['data'] != null &&
            resp['data']['rides'] != null &&
            resp['data']['rides'].isNotEmpty &&
            resp['data']['rides']['data'] != null &&
            resp['data']['rides']['data'].isEmpty) {
          noRideFound.value = true;
        }
        if (resp['data'] != null && resp['data']['recentSearches'] != null) {
          recentSearchList.clear();
          recentSearchList.addAll(resp['data']['recentSearches']);
          recentSearchList.refresh();
        }
      }
      type == 0 ? isLoading(false) : isOverlayLoading(false);
    }, onError: (error) {
      type == 0 ? isLoading(false) : isOverlayLoading(false);
      throw error; // Propagate to loadInitialData
    });
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
          if (resp['data'] != null &&
              resp['data']['rides'] != null &&
              resp['data']['rides'].isNotEmpty &&
              resp['data']['rides']['data'] != null) {
            rides.clear();
            rides.addAll(resp['data']['rides']['data']);
            searchTotal.value = resp['data']['rides']['total'] ?? 0;
            rides.refresh();
            firmDiscount.value =
                resp['data']['firm_cancellation_discount'].toString();
          }

          if (resp['data'] != null &&
              resp['data']['rides'] != null &&
              resp['data']['rides'].isNotEmpty &&
              resp['data']['rides']['data'] != null &&
              resp['data']['rides']['data'].isEmpty) {
            noRideFound.value = true;
          }
          if (resp['data'] != null && resp['data']['recentSearches'] != null) {
            recentSearchList.clear();
            recentSearchList.addAll(resp['data']['recentSearches']);
            recentSearchList.refresh();
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

  Future<void> checkBooking(rideId, tripDetailId) async {
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
                Get.toNamed(
                    "/book_seat/$rideId/${resp['data']['seats']}/$tripDetailId");
                isAlreadyBooked.value = true;
              } else {
                final query = Uri(queryParameters: {
                  'from': fromTextEditingController.text,
                  'to': toTextEditingController.text,
                  'from_city_id': fromCityId.value.toString(),
                  'to_city_id': toCityId.value.toString(),
                }).query;
                Get.toNamed(
                    '/trip_detail/$rideId/findRide/findRide/$tripDetailId?$query');
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

  // 
  Future<void> _getRideFeatureOptions() async {
    await PostRideProvider()
        .getSearchRideInitData(
            serviceController.token, serviceController.langId.value)
        .then((resp) async {
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null) {

          final data = resp['data'];
          if (data['preferences'] != null &&
              data['preferences']['preferencesOptions'] != null) {
            smokingList.add(
                data['preferences']['preferencesOptions']['smoking_option1']);
            smokingList.add(
                data['preferences']['preferencesOptions']['smoking_option2']);

            smoking.value = "21";

            smokingLabelList.add(data['preferences']['preferencesOptions']
                ['smoking_option1_label']);
            smokingLabelList.add(data['preferences']['preferencesOptions']
                ['smoking_option2_label']);

            petList.add(
                data['preferences']['preferencesOptions']['animals_option1']);
            petList.add(
                data['preferences']['preferencesOptions']['animals_option2']);
            petList.add(
                data['preferences']['preferencesOptions']['animals_option3']);

            pet.value = "23";

            petLabelList.add(data['preferences']['preferencesOptions']
                ['animals_option1_label']);
            petLabelList.add(data['preferences']['preferencesOptions']
                ['animals_option2_label']);
            petLabelList.add(data['preferences']['preferencesOptions']
                ['animals_option3_label']);
          }

          if (data['features']['featuresOptions'] != null) {
            rideFeatureList.addAll(data['features']['featuresOptions']);
          }
          if (data['passengers']['passengerRatingOptions'] != null) {
            passengerRatingList.addAll(data['passengers']['passengerRatingOptions']);
          }

          if (data['features']['featuresLabels'] != null) {
            rideFeatureLabelList.addAll(data['features']['featuresLabels']);
          }

          if (data['passengers']['passengerRatingLabels'] != null) {
            passengerRatingLabelList.addAll(data['passengers']['passengerRatingLabels']);
          }

          if (data['booking']['bookingOptions'] != null) {
            bookingOptionList.addAll(data['booking']['bookingOptions']);
          }
          if (data['booking']['bookingTooltips'] != null) {
            bookingOptionToolTipList.addAll(data['booking']['bookingTooltips']);
          }

          if (data['booking']['bookingLabels'] != null) {
            bookingOptionLabelList.addAll(data['booking']['bookingLabels']);
          }

          if (data['luggage']['luggageOptions'] != null) {
            luggageList.addAll(data['luggage']['luggageOptions']);
          }
          if (data['luggage']['luggageTooltips'] != null) {
            luggageListToolTip.addAll(data['luggage']['luggageTooltips']);
          }

          if (data['luggage']['luggageLabels'] != null) {
            luggageListLabel.addAll(data['luggage']['luggageLabels']);
          }

          if (data['payment']['paymentOptions'] != null) {
            paymentOptionList.addAll(data['payment']['paymentOptions']);
          }

          if (data['payment']['paymentTooltips'] != null) {
            paymentOptionToolTipList.addAll(data['payment']['paymentTooltips']);
          }
          if (data['payment']['paymentLabels'] != null) {
            paymentOptionLabelList.addAll(data['payment']['paymentLabels']);
          }

        }
      }
    }, onError: (error) {
      throw error;
    });
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
