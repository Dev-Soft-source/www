import 'dart:async';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/pages/edit_profile/EditProfileController.dart';
import 'package:proximaride_app/pages/edit_profile/EditProfileProvider.dart';
import 'package:proximaride_app/pages/location/LocationProvider.dart';
import 'package:proximaride_app/pages/post_ride/PostRideController.dart';
import 'package:proximaride_app/pages/search_ride/SearchRideController.dart';
import 'package:proximaride_app/pages/stages/StageController.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/debouncer.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

class LocationController extends GetxController {
  final serviceController = Get.find<Service>();
  final errorStateManager = ErrorStateManager();
  dynamic tempController;

  var isCountry = "";
  var isState = "";
  var isCity = "".obs;

  var isLoading = false.obs;
  var isOverlayLoading = false.obs;
  var countryId = 0.obs;
  var countryName = "".obs;
  var stateId = 0.obs;
  var stateName = "".obs;
  var cityId = 0.obs;
  var cityName = "".obs;
  var spotIndex = 0.obs;
  var spot = "no".obs;

  var countries = List<dynamic>.empty(growable: true).obs;
  var searchCountries = List<dynamic>.empty(growable: true).obs;
  var states = List<dynamic>.empty(growable: true).obs;
  var searchStates = List<dynamic>.empty(growable: true).obs;
  var cities = List<dynamic>.empty(growable: true).obs;
  var searchCities = List<dynamic>.empty(growable: true).obs;
  var errorList = List<dynamic>.empty(growable: true).obs;

  late TextEditingController searchTextEditingController;
  var labelTextDetail = {}.obs;

  final _debouncer = Debouncer(milliseconds: 500);

  bool isEditProfileRegistered = false;
  bool isStageControllerRegistered = false;
  bool isSearchControllerRegistered = false;
  bool isPostRideControllerRegistered = false;

  @override
  void onInit() async {
    // TODO: implement onInit
    super.onInit();
    isEditProfileRegistered = Get.isRegistered<EditProfileController>();
    isStageControllerRegistered = Get.isRegistered<StageController>();
    isSearchControllerRegistered = Get.isRegistered<SearchRideController>();
    isPostRideControllerRegistered = Get.isRegistered<PostRideController>();

    if (isEditProfileRegistered) {
      tempController = Get.find<EditProfileController>();
    } else if (isStageControllerRegistered) {
      tempController = Get.find<StageController>();
    } else if (isSearchControllerRegistered) {
      tempController = Get.find<SearchRideController>();
    } else if (isPostRideControllerRegistered) {
      tempController = Get.find<PostRideController>();
    }

    isCountry = Get.parameters['country'] ?? "";
    isState = Get.parameters['state'] ?? "";
    countryId.value = int.parse(Get.parameters['countryId'] ?? "0");
    isCity.value = Get.parameters['city'] ?? "";
    stateId.value = int.parse(Get.parameters['stateId'] ?? "0");
    spotIndex.value = int.parse(Get.parameters['index'] ?? "0");
    spot.value = Get.parameters['spot'] ?? "no";

    searchTextEditingController = TextEditingController();

    isLoading(true);
    await loadInitialData();
  }

  Future<void> loadInitialData() async {
    try {
      errorStateManager.setLoading();
      isLoading(true);

      await _getLabelTextDetail();

      if (isCountry != "") {
        await _getCountries();
      }
      if (isState != "") {
        await _getStates();
      }
      if (isCity.value == "city") {
        await _getCities();
      }

      errorStateManager.setSuccess();
      isLoading(false);
    } on SocketException {
      logger.error("Network error in loadInitialData: SocketException");
      isLoading(false);
      errorStateManager.setError(
        "No internet connection. Please check your network and try again.",
        ErrorType.network,
        loadInitialData,
      );
    } on TimeoutException {
      logger.error("Timeout error in loadInitialData");
      isLoading(false);
      errorStateManager.setError(
        "Request timed out. Please check your connection and try again.",
        ErrorType.network,
        loadInitialData,
      );
    } catch (error) {
      logger.error("Error in loadInitialData: $error");
      isLoading(false);

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
          "Unable to load location data. Please check your connection and try again.",
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
    // TODO: implement onClose
    super.onClose();
    searchTextEditingController.dispose();
  }

  Future<void> _getLabelTextDetail() async {
    try {
      final resp = await StageProvider().getLabelTextDetail(
          serviceController.langId.value,
          locationPageSetting,
          serviceController.token);

      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null &&
            resp['data']['selectLocationSetting'] != null) {
          labelTextDetail.addAll(resp['data']['selectLocationSetting']);
        }
      } else {
        // Keep silent or throw if critical. Usually label text is critical enough.
        // throw {"type": "server", "message": resp['message'] ?? "Failed to load labels"};
      }
    } on SocketException {
      throw {
        "type": "network",
        "message":
            "No internet connection. Please check your network and try again."
      };
    } on TimeoutException {
      throw {
        "type": "network",
        "message":
            "Request timed out. Please check your connection and try again."
      };
    } catch (error) {
      if (error is Map && error.containsKey('type')) {
        rethrow;
      }
      throw {
        "type": "unknown",
        "message": "Unable to load page details. Please try again."
      };
    }
  }

  Future<void> _getCountries() async {
    try {
      isOverlayLoading(true);
      final resp =
          await LocationProvider().getCountries(serviceController.token);

      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['countries'] != null) {
          countries.addAll(resp['data']['countries']);
          searchCountries.addAll(countries);
        }
      } else {
        throw {
          "type": "server",
          "message": resp['message'] ?? "Failed to load countries."
        };
      }
      isOverlayLoading(false);
    } on SocketException {
      throw {
        "type": "network",
        "message":
            "No internet connection. Please check your network and try again."
      };
    } on TimeoutException {
      throw {
        "type": "network",
        "message":
            "Request timed out. Please check your connection and try again."
      };
    } catch (error) {
      isOverlayLoading(false);
      if (error is Map && error.containsKey('type')) {
        rethrow;
      }
      throw {
        "type": "unknown",
        "message": "Unable to load countries. Please try again."
      };
    }
  }

  Future<void> _getStates() async {
    try {
      isOverlayLoading(true);
      final resp = await EditProfileProvider()
          .getStates(countryId.value, serviceController.token);

      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['states'] != null) {
          states.addAll(resp['data']['states']);
          searchStates.addAll(states);
        }
      } else {
        throw {
          "type": "server",
          "message": resp['message'] ?? "Failed to load states."
        };
      }
      isOverlayLoading(false);
    } on SocketException {
      throw {
        "type": "network",
        "message":
            "No internet connection. Please check your network and try again."
      };
    } on TimeoutException {
      throw {
        "type": "network",
        "message":
            "Request timed out. Please check your connection and try again."
      };
    } catch (error) {
      isOverlayLoading(false);
      if (error is Map && error.containsKey('type')) {
        rethrow;
      }
      throw {
        "type": "unknown",
        "message": "Unable to load states. Please try again."
      };
    }
  }

  Future<void> _getCities({String searchHistory = ""}) async {
    try {
      if (isCity.value != "city") {
        cities.clear();
        searchCities.clear();
      }
      isOverlayLoading(true);
      final resp = await EditProfileProvider()
          .getCities(stateId.value, searchHistory, serviceController.token);

      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['cities'] != null) {
          cities.addAll(resp['data']['cities']);
          searchCities.addAll(cities);
        }
      } else {
        throw {
          "type": "server",
          "message": resp['message'] ?? "Failed to load cities."
        };
      }
      isOverlayLoading(false);
    } on SocketException {
      throw {
        "type": "network",
        "message":
            "No internet connection. Please check your network and try again."
      };
    } on TimeoutException {
      throw {
        "type": "network",
        "message":
            "Request timed out. Please check your connection and try again."
      };
    } catch (error) {
      isOverlayLoading(false);
      if (error is Map && error.containsKey('type')) {
        rethrow;
      }
      throw {
        "type": "unknown",
        "message": "Unable to load cities. Please try again."
      };
    }
  }

  filterCountries(value) {
    searchCountries.clear();
    if (value == "" || value == null) {
      searchCountries.addAll(countries);
    } else {
      searchCountries.addAll(countries
          .where((item) =>
              item['name'].toLowerCase().contains(value.toLowerCase()))
          .toList());
    }
  }

  filterStates(value) {
    searchStates.clear();
    if (value == "" || value == null) {
      searchStates.addAll(states);
    } else {
      searchStates.addAll(states
          .where((item) =>
              item['name'].toLowerCase().contains(value.toLowerCase()))
          .toList());
    }
  }

  filterCities(value) {
    searchCities.clear();
    if (value == "" || value == null) {
      searchCities.addAll(cities);
    } else {
      searchCities.addAll(cities
          .where((item) =>
              item['name'].toLowerCase().contains(value.toLowerCase()))
          .toList());
    }
  }

  searchCitiesData(data) async {
    _debouncer.run(() async {
      if (data != "" && data.toString().length >= 2) {
        await _getCities(searchHistory: data);
      } else {
        if (isCity.value != "city") {
          cities.clear();
          searchCities.clear();
        }
      }
    });
  }
}
