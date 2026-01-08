import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/pages/edit_profile/EditProfileController.dart';
import 'package:proximaride_app/pages/edit_profile/EditProfileProvider.dart';
import 'package:proximaride_app/pages/location/LocationProvider.dart';
import 'package:proximaride_app/pages/post_ride/PostRideController.dart';
import 'package:proximaride_app/pages/search_ride/SearchRideController.dart';
import 'package:proximaride_app/pages/stages/StageController.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/debouncer.dart';
import 'package:proximaride_app/services/service.dart';

class LocationController extends GetxController {
  final serviceController = Get.find<Service>();
  var tempController;

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

    if(isEditProfileRegistered)
    {
      tempController = Get.find<EditProfileController>();
    } else if(isStageControllerRegistered){
      tempController = Get.find<StageController>();
    }else if(isSearchControllerRegistered){
      tempController = Get.find<SearchRideController>();
    }else if(isPostRideControllerRegistered){
      tempController = Get.find<PostRideController>();
    }




    isCountry = Get.parameters['country'] ?? "";
    isState = Get.parameters['state'] ?? "";
    countryId.value = int.parse(Get.parameters['countryId'] ?? "0");
    isCity.value = Get.parameters['city'] ?? "";
    stateId.value = int.parse(Get.parameters['stateId'] ?? "0");
    spotIndex.value = int.parse(Get.parameters['index'] ?? "0");
    spot.value = Get.parameters['spot'] ?? "no";

    isLoading(true);
    await getLabelTextDetail();
    isLoading(false);

    if(isCountry != "") {
      getCountries();
      }
    if(isState != "") {
      getStates();
      }
    if(isCity.value == "city"){
      getCities();
    }


    searchTextEditingController = TextEditingController();

  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
    searchTextEditingController.dispose();

  }

  Future<void> getLabelTextDetail() async {
    try {
      await StageProvider().getLabelTextDetail(serviceController.langId.value, locationPageSetting, serviceController.token).then((resp) async {
        if (resp['status']!= null && resp['status'] == "Success") {
          if(resp['data'] != null && resp['data']['selectLocationSetting'] != null){
            labelTextDetail.addAll(resp['data']['selectLocationSetting']);
          }
        }
      }, onError: (error) {
        serviceController.showDialogue(error.toString());
        isLoading(false);
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString());
      isLoading(false);
    }
  }

  getCountries() async {
    try {
      // if (countries.isNotEmpty) {
      //   Get.toNamed("/country");
      //   return;
      // } else {
        isOverlayLoading(true);
        LocationProvider().getCountries(serviceController.token).then(
                (resp) async {
              if (resp['status'] != null && resp['status'] == "Success") {
                if (resp['data'] != null && resp['data']['countries'] != null) {
                  countries.addAll(resp['data']['countries']);
                  searchCountries.addAll(countries);
                  isOverlayLoading(false);
                  // Get.toNamed("/country");
                }
              }
              isOverlayLoading(false);
            }, onError: (err) {
          isOverlayLoading(false);

serviceController.showDialogue(err.toString());


        });
      //}
    } catch (exception) {

        serviceController.showDialogue(exception.toString());

    }
  }

  getStates() async {
    try {
      // if (states.isNotEmpty) {
      //   Get.toNamed("/state/${countryId.value}");
      //   return;
      // } else {
        isOverlayLoading(true);
        EditProfileProvider()
            .getStates(countryId.value, serviceController.token)
            .then((resp) async {
          if (resp['status'] != null && resp['status'] == "Success") {
            if (resp['data'] != null && resp['data']['states'] != null) {
              states.addAll(resp['data']['states']);
              searchStates.addAll(states);
              isOverlayLoading(false);
              // Get.toNamed("/state/${countryId.value}");
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

  getCities({String searchHistory = ""}) async {
    try {
      if(isCity.value != "city"){
        cities.clear();
        searchCities.clear();
      }
      // if (cities.isNotEmpty) {
      //   Get.toNamed("/city/${stateId.value}");
      //   return;
      // } else {
        isOverlayLoading(true);
        EditProfileProvider()
            .getCities(stateId.value, searchHistory, serviceController.token)
            .then((resp) async {
          if (resp['status'] != null && resp['status'] == "Success") {
            if (resp['data'] != null && resp['data']['cities'] != null) {
              cities.addAll(resp['data']['cities']);
              searchCities.addAll(cities);
              isOverlayLoading(false);
              // Get.toNamed("/city/${stateId.value}");
            }
          }
          isOverlayLoading(false);
        }, onError: (err) {
          isOverlayLoading(false);

serviceController.showDialogue(err.toString());


        });
      //}
    } catch (exception) {

        serviceController.showDialogue(exception.toString());

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

  searchCitiesData(data) async{
    _debouncer.run(() async {
      if(data != "" && data.toString().length >= 2){
        await getCities(searchHistory: data);
      }else{
        if(isCity.value != "city"){
          cities.clear();
          searchCities.clear();
        }
      }
    });
  }

}