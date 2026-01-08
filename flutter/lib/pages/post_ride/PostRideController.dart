import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/post_ride/PostRideProvider.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/services/service.dart';


class PostRideController extends GetxController{

  final serviceController = Get.find<Service>();
  ScrollController scrollController = ScrollController();

  var bookingType = "".obs;
  var bookingTypeList = ["standard","firm"].obs;

  var isLoading = false.obs;
  var isOverlayLoading = false.obs;
  var seatAvailable = 0.obs;
  var seatMiddle = 0.obs;
  var seatBack = 0.obs;
  var scrollField = false;

  var skipNow = false.obs;
  var addNewVehicle = false.obs;
  var alreadyAdded = false.obs;
  var firmCancellationPrice = 0.obs;

  var fuel = "".obs;
  var vehicleType = "".obs;
  var carImageName = "".obs;
  var carImagePath = "".obs;
  var carImageNameOriginal = "".obs;
  var carImagePathOriginal = "".obs;
  var smoking = "No".obs;
  var pet = "No".obs;
  var featureList = [].obs;
  var bookingOption = "".obs;
  var luggage = "".obs;
  var paymentOption = "".obs;
  var disclaimer = false.obs;
  var recurring = false.obs;
  var recurringType = "".obs;
  var acceptMoreLuggage = "".obs;
  var openCustomized = "".obs;
  var vehicleId = "".obs;
  var carOldImagePath = "".obs;
  var errorList = List<dynamic>.empty(growable: true).obs;
  final errors = [].obs;
  var bookings = false.obs;

  var smokingList = [].obs;
  var petList = [].obs;
  var smokingLabelList = [].obs;
  var petLabelList = [].obs;
  var rideFeatureList = [].obs;
  var rideFeatureLabelList = [].obs;
  var paymentOptionList = [].obs;
  var bookingOptionList = [].obs;
  var bookingOptionLabelList = [].obs;
  var bookingOptionToolTipList = [].obs;
  var paymentOptionToolTipList = [].obs;
  var paymentOptionLabelList = [].obs;
  var luggageList = [].obs;
  var luggageListLabel = [].obs;
  var luggageListToolTip = [].obs;
  var cancellationOptionList = [].obs;
  var cancellationOptionLabelList = [].obs;
  var cancellationOptionToolTipList = [].obs;
  var vehicleTypeList = [].obs;
  var vehicleTypeLabelList = [].obs;

  var vehicleList = List<dynamic>.empty(growable: true).obs;
  var pinkRideToolTipText = "".obs;
  var extraCareRideToolTipText = "".obs;
  var overallRating = 0.0.obs;
  var rideId = 0.obs;
  var rideType = "".obs;
  var pinkRideReadOnly = false.obs;
  var extraCareRideReadOnly = false.obs;
  var labelTextDetail = {}.obs;
  var popupTextDetail = {}.obs;
  var validationMessageDetail = {}.obs;


  late TextEditingController fromTextEditingController, toTextEditingController, pickUpLocationTextEditingController, dropOffLocationTextEditingController,
      dateTextEditingController, timeTextEditingController, dropOffDescriptionTextEditingController, makeTextEditingController, modelTextEditingController,
      licenseNumberTextEditingController, yearTextEditingController, colorTextEditingController, pricePerSeatTextEditingController, anythingTextEditingController,
      recurringTripsTextEditingController;

  final Map<String, FocusNode> focusNodes = {};

  List<TextEditingController> fromSpotControllers = [];
  List<TextEditingController> toSpotControllers = [];
  List<TextEditingController> priceSpotControllers = [];
  var rideDetailIds = [];

  var spotsCount = 1.obs;
  var showErrorSpot = false.obs;

  var reviews = List<dynamic>.empty(growable: true).obs;
@override

void onInit() async {
  super.onInit();
    fromTextEditingController = TextEditingController();
    toTextEditingController= TextEditingController();
    pickUpLocationTextEditingController = TextEditingController();
    dropOffLocationTextEditingController = TextEditingController();
    dateTextEditingController = TextEditingController();
    timeTextEditingController = TextEditingController();
    dropOffDescriptionTextEditingController = TextEditingController();
    makeTextEditingController = TextEditingController();
    modelTextEditingController = TextEditingController();
    licenseNumberTextEditingController = TextEditingController();
    colorTextEditingController = TextEditingController();
    yearTextEditingController = TextEditingController();
    pricePerSeatTextEditingController = TextEditingController();
    anythingTextEditingController = TextEditingController();
    recurringTripsTextEditingController = TextEditingController();


  fromSpotControllers.add(TextEditingController());
  toSpotControllers.add(TextEditingController());
  priceSpotControllers.add(TextEditingController());
  rideDetailIds.add("0");

    rideId.value = int.parse(Get.parameters['id'].toString());
    rideType.value = Get.parameters['type'].toString();

  for (int i = 1; i <= 12; i++) {
    focusNodes[i.toString()] = FocusNode();
    // Attach the onFocusChange listener
    focusNodes[i.toString()]?.addListener(() {
      if (!focusNodes[i.toString()]!.hasFocus) {
        // Field has lost focus, trigger validation
        if (i == 1) {
          validateField('from', fromTextEditingController.text,);
        } else if (i == 2) {
          validateField('to', toTextEditingController.text,);
        } else if (i == 3) {
          validateField('pickup', pickUpLocationTextEditingController.text);
        } else if (i == 4) {
          validateField('dropoff', dropOffLocationTextEditingController.text,);
        } else if (i == 5) {
          validateField('details', dropOffDescriptionTextEditingController.text,);
        } else if (i == 6) {
          validateField('make', makeTextEditingController.text,);
        } else if (i == 7) {
          validateField('model', modelTextEditingController.text,);
        } else if (i == 8) {
          validateField('license_no', licenseNumberTextEditingController.text,);
        } else if (i == 9) {
          validateField('color', colorTextEditingController.text,);
        } else if (i == 10) {
          validateField('year', yearTextEditingController.text,type: 'numeric');
        } else if (i == 11) {
          validateField('price', pricePerSeatTextEditingController.text,type: 'numeric');
        }
      }
    });
  }

  // Use consolidated API call for better performance (11 APIs -> 1 API)
  isOverlayLoading(true);
  await getPostRideInitData();
  isOverlayLoading(false);

  // Handle edit/duplicate ride if needed
  if (Get.parameters['id'] != "0" && Get.parameters['type'] == "new") {
    isOverlayLoading(true);
    await getPostRideAgainData(Get.parameters['id'], 'postRideAgain');
  } else if(Get.parameters['id'] != "0" && Get.parameters['type'] == "update"){
    isOverlayLoading(true);
    await editPostRideData(Get.parameters['id']);
  }
}

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
    // fromTextEditingController.dispose();
    // toTextEditingController.dispose();
    // pickUpLocationTextEditingController.dispose();
    // dropOffLocationTextEditingController.dispose();
    // dateTextEditingController.dispose();
    // timeTextEditingController.dispose();
    // dropOffDescriptionTextEditingController.dispose();
    // makeTextEditingController.dispose();
    // modelTextEditingController.dispose();
    // licenseNumberTextEditingController.dispose();
    // yearTextEditingController.dispose();
    // colorTextEditingController.dispose();
    // pricePerSeatTextEditingController.dispose();
    // anythingTextEditingController.dispose();
    // recurringTripsTextEditingController.dispose();
  }

  void validateField(String fieldName, String fieldValue, {String type = 'string', bool isRequired = true, int wordsLimit = 50}) {
    errors.removeWhere((element) => element['title'] == fieldName);
    List<String> errorList = [];

    if (isRequired && fieldValue.isEmpty) {
      var message = validationMessageDetail['required'];
      if(fieldName == "from"){
        message = message.replaceAll(":Attribute", labelTextDetail['from_error'] ?? "From");
      }else if(fieldName == "to"){
        message = message.replaceAll(":Attribute", labelTextDetail['to_error'] ?? "To");
      }else if(fieldName == "pickup"){
        message = message.replaceAll(":Attribute", labelTextDetail['pick_up_error'] ?? "Pickup");
      }else if(fieldName == "dropoff"){
        message = message.replaceAll(":Attribute", labelTextDetail['drop_off_error'] ?? "Dropoff");
      }else if(fieldName == "details"){
        message = message.replaceAll(":Attribute", labelTextDetail['meeting_drop_off_description_error'] ?? "Details");
      }else if(fieldName == "make"){
        message = message.replaceAll(":Attribute", labelTextDetail['make_error'] ?? "Make");
      }else if(fieldName == "model"){
        message = message.replaceAll(":Attribute", labelTextDetail['model_error'] ?? "Model");
      }else if(fieldName == "license_no"){
        message = message.replaceAll(":Attribute", labelTextDetail['license_error'] ?? "License no");
      }else if(fieldName == "color"){
        message = message.replaceAll(":Attribute", labelTextDetail['color_error'] ?? "Color");
      }else if(fieldName == "year"){
        message = message.replaceAll(":Attribute", labelTextDetail['year_error'] ?? "Year");
      }else if(fieldName == "price"){
        message = message.replaceAll(":Attribute", labelTextDetail['price_error'] ?? "Price");
      }
      errorList.add(message);
      errors.add({
        'title': fieldName,
        'eList': errorList,
      });
      return;
    }

    switch (type) {
      case 'numeric':
        if (fieldValue.isNotEmpty && double.tryParse(fieldValue) == null) {
          var message = validationMessageDetail['numeric'];
          if(fieldName == "price"){
              message = message.replaceAll(":attribute", labelTextDetail['price_error'] ?? "price");
          }else if(fieldName == "year"){
              message = message.replaceAll(":attribute", labelTextDetail['year_error'] ?? "year");
          }
          errorList.add(message);
        }
        break;
      case 'date':
        if (fieldValue.isNotEmpty && DateTime.tryParse(fieldValue) == null) {
          var message = validationMessageDetail['date'];
          message = message.replaceAll(":attribute", labelTextDetail['date_error'] ?? "date");
          errorList.add(message);
        }
        break;
      case 'time':
        if (fieldValue.isNotEmpty && !RegExp(r'^\d{2}:\d{2}$').hasMatch(fieldValue)) {
          var message = validationMessageDetail['date_format'];
          message = message.replaceAll(":attribute", labelTextDetail['time_error'] ?? "time");
          message = message.replaceAll(":format", 'HH:MM');
          errorList.add(message);
        }
        break;
      case 'max_words':
        if (fieldValue.isNotEmpty && fieldValue.split(' ').length > wordsLimit) {
          var message = validationMessageDetail['max_words'];
          message = message.replaceAll(":attribute", fieldName);
          message = message.replaceAll(":max", wordsLimit);
          errorList.add(message);
        }
        break;
      default:
        break;
    }

    if (errorList.isNotEmpty) {
      errors.add({
        'title': fieldName,
        'eList': errorList,
      });
    }
    update();
  }

  /// Consolidated API call that fetches all Post Ride initialization data in one request.
  /// This replaces 11 separate API calls for better performance.
  getPostRideInitData() async {
    try {
      isLoading(true);
      await PostRideProvider().getPostRideInitData(
        serviceController.token,
        serviceController.langId.value,
        rideId: rideId.value != 0 ? rideId.value : null,
        rideType: rideId.value != 0 ? rideType.value : null,
      ).then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          final data = resp['data'];

          // 1. Process labels data
          if (data['labels'] != null) {
            if (data['labels']['postRidePage'] != null) {
              labelTextDetail.addAll(data['labels']['postRidePage']);
              // Build vehicle type lists
              vehicleTypeLabelList.add(labelTextDetail['vehicle_type_convertible_text'] ?? "Convertable");
              vehicleTypeList.add(labelTextDetail['vehicle_type_convertible_value']);
              vehicleTypeLabelList.add(labelTextDetail['vehicle_type_coupe_text'] ?? "Coupe");
              vehicleTypeList.add(labelTextDetail['vehicle_type_coupe_value']);
              vehicleTypeLabelList.add(labelTextDetail['vehicle_type_hatchback_text'] ?? "Hatchback");
              vehicleTypeList.add(labelTextDetail['vehicle_type_hatchback_value']);
              vehicleTypeLabelList.add(labelTextDetail['vehicle_type_minivan_text'] ?? "Minivan");
              vehicleTypeList.add(labelTextDetail['vehicle_type_minivan_value']);
              vehicleTypeLabelList.add(labelTextDetail['vehicle_type_sedan_text'] ?? "Sedan");
              vehicleTypeList.add(labelTextDetail['vehicle_type_sedan_value']);
              vehicleTypeLabelList.add(labelTextDetail['vehicle_type_station_wagon_text'] ?? "Station wagon");
              vehicleTypeList.add(labelTextDetail['vehicle_type_station_wagon_value']);
              vehicleTypeLabelList.add(labelTextDetail['vehicle_type_suv_text'] ?? "SUV");
              vehicleTypeList.add(labelTextDetail['vehicle_type_suv_value']);
              vehicleTypeLabelList.add(labelTextDetail['vehicle_type_truck_text'] ?? "Truck");
              vehicleTypeList.add(labelTextDetail['vehicle_type_truck_value']);
              vehicleTypeLabelList.add(labelTextDetail['vehicle_type_van_text'] ?? "Van");
              vehicleTypeList.add(labelTextDetail['vehicle_type_van_value']);
            }
            if (data['labels']['messages'] != null) {
              popupTextDetail.addAll(data['labels']['messages']);
            }
            if (data['labels']['validationMessages'] != null) {
              validationMessageDetail.addAll(data['labels']['validationMessages']);
            }
          }

          // 2. Process user vehicles data
          if (data['userVehicles'] != null) {
            if (data['userVehicles']['vehicles'] != null) {
              vehicleList.addAll(data['userVehicles']['vehicles']);
              var res = vehicleList.firstWhereOrNull((element) => element['primary_vehicle'] == '1');
              if (res != null) {
                vehicleId.value = res['id'].toString();
                alreadyAdded.value = true;
              } else if (vehicleList.isNotEmpty && vehicleList.length == 1) {
                vehicleId.value = vehicleList[0]['id'].toString();
                alreadyAdded.value = true;
              }
              fuel.value = "Gas";
              bookingType.value = "36";
            }
            if (data['userVehicles']['overallRating'] != null) {
              overallRating.value = double.parse(data['userVehicles']['overallRating'].toString());
            }
          }

          // 3. Process preferences data (smoking, pets)
          if (data['preferences'] != null && data['preferences']['preferencesOptions'] != null) {
            var prefs = data['preferences']['preferencesOptions'];
            smokingList.add(prefs['smoking_option1']);
            smokingList.add(prefs['smoking_option2']);
            smoking.value = "21";
            smokingLabelList.add(prefs['smoking_option1_label']);
            smokingLabelList.add(prefs['smoking_option2_label']);
            petList.add(prefs['animals_option1']);
            petList.add(prefs['animals_option2']);
            petList.add(prefs['animals_option3']);
            pet.value = "23";
            petLabelList.add(prefs['animals_option1_label']);
            petLabelList.add(prefs['animals_option2_label']);
            petLabelList.add(prefs['animals_option3_label']);
          }

          // 4. Process ride features
          if (data['rideFeatures'] != null) {
            if (data['rideFeatures']['featuresOptions'] != null) {
              rideFeatureList.addAll(data['rideFeatures']['featuresOptions']);
            }
            if (data['rideFeatures']['featuresLabels'] != null) {
              rideFeatureLabelList.addAll(data['rideFeatures']['featuresLabels']);
            }
          }

          // 5. Process pink ride info
          if (data['pinkRide'] != null) {
            _processPinkRideData(data['pinkRide']);
          }

          // 6. Process extra care ride info
          if (data['extraCareRide'] != null) {
            _processExtraCareRideData(data['extraCareRide']);
          }

          // 7. Process booking options
          if (data['bookingOptions'] != null) {
            if (data['bookingOptions']['bookingOptions'] != null) {
              bookingOptionList.addAll(data['bookingOptions']['bookingOptions']);
            }
            if (data['bookingOptions']['bookingTooltips'] != null) {
              bookingOptionToolTipList.addAll(data['bookingOptions']['bookingTooltips']);
            }
            if (data['bookingOptions']['bookingLabels'] != null) {
              bookingOptionLabelList.addAll(data['bookingOptions']['bookingLabels']);
            }
          }

          // 8. Process cancellation options
          if (data['cancellationOptions'] != null) {
            if (data['cancellationOptions']['cancellationOptions'] != null) {
              cancellationOptionList.addAll(data['cancellationOptions']['cancellationOptions']);
            }
            if (data['cancellationOptions']['cancellationTooltips'] != null) {
              cancellationOptionToolTipList.addAll(data['cancellationOptions']['cancellationTooltips']);
            }
            if (data['cancellationOptions']['cancellationLabels'] != null) {
              cancellationOptionLabelList.addAll(data['cancellationOptions']['cancellationLabels']);
            }
          }

          // 9. Process luggage options
          if (data['luggage'] != null) {
            if (data['luggage']['luggageOptions'] != null) {
              luggageList.addAll(data['luggage']['luggageOptions']);
            }
            if (data['luggage']['luggageTooltips'] != null) {
              luggageListToolTip.addAll(data['luggage']['luggageTooltips']);
            }
            if (data['luggage']['luggageLabels'] != null) {
              luggageListLabel.addAll(data['luggage']['luggageLabels']);
            }
          }

          // 10. Process payment options
          if (data['paymentOptions'] != null) {
            if (data['paymentOptions']['paymentOptions'] != null) {
              paymentOptionList.addAll(data['paymentOptions']['paymentOptions']);
            }
            if (data['paymentOptions']['paymentTooltips'] != null) {
              paymentOptionToolTipList.addAll(data['paymentOptions']['paymentTooltips']);
            }
            if (data['paymentOptions']['paymentLabels'] != null) {
              paymentOptionLabelList.addAll(data['paymentOptions']['paymentLabels']);
            }
          }
        }
        isLoading(false);
      }, onError: (err) {
        isLoading(false);
        serviceController.showDialogue(err.toString());
      });
    } catch (exception) {
      isLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }

  /// Helper method to process pink ride data
  void _processPinkRideData(Map<String, dynamic> pinkRideData) {
    if (pinkRideData['user'] != null && pinkRideData['user']['pink_ride'] == "1") {
      pinkRideToolTipText.value = "${labelTextDetail['pink_ride_tooltip_admin_enable_text'] ?? "Admin allow user to select pink ride"}";
    } else if (pinkRideData['user'] != null && pinkRideData['user']['pink_ride'] == "0") {
      pinkRideToolTipText.value = "${labelTextDetail['pink_ride_tooltip_admin_disable_text'] ?? "Admin disable user to select pink ride"}";
    } else if (pinkRideData['pinkRideSetting'] != null) {
      pinkRideToolTipText.value = "${labelTextDetail['pink_ride_tooltip_only_text'] ?? "Only"}";
      if (pinkRideData['pinkRideSetting']['female'] == "1") {
        pinkRideToolTipText.value = "${pinkRideToolTipText.value} ${labelTextDetail['pink_ride_tooltip_female_text'] ?? "female"}";
      }
      pinkRideToolTipText.value = "${pinkRideToolTipText.value} ${labelTextDetail['pink_ride_tooltip_driver_text'] ?? "driver"}";

      if (pinkRideData['pinkRideSetting']['verfiy_phone'] == "1" ||
          pinkRideData['pinkRideSetting']['verfiy_email'] == "1" ||
          pinkRideData['pinkRideSetting']['driver_license'] == "1") {
        pinkRideToolTipText.value = "${pinkRideToolTipText.value} ${labelTextDetail['pink_ride_tooltip_with_text'] ?? "with"}";
        if (pinkRideData['pinkRideSetting']['verfiy_phone'] == "1") {
          pinkRideToolTipText.value = "${pinkRideToolTipText.value} ${labelTextDetail['pink_ride_tooltip_phone_number_text'] ?? "phone number"},";
        }
        if (pinkRideData['pinkRideSetting']['verfiy_email'] == "1") {
          pinkRideToolTipText.value = "${pinkRideToolTipText.value} ${labelTextDetail['pink_ride_tooltip_email_text'] ?? "email"},";
        }
        if (pinkRideData['pinkRideSetting']['driver_license'] == "1") {
          pinkRideToolTipText.value = "${pinkRideToolTipText.value} ${labelTextDetail['pink_ride_tooltip_driver_license_text'] ?? "driver license"}";
        }
        pinkRideToolTipText.value = "${pinkRideToolTipText.value} ${labelTextDetail['pink_ride_tooltip_verified_text'] ?? "verified"}";
      }
      pinkRideToolTipText.value = "${pinkRideToolTipText.value} ${labelTextDetail['pink_ride_tooltip_select_this_ride_text'] ?? "can select this ride"}";
    }
  }

  /// Helper method to process extra care ride data
  void _processExtraCareRideData(Map<String, dynamic> extraCareData) {
    if (extraCareData['user'] != null && extraCareData['user']['folks_ride'] == "1") {
      extraCareRideToolTipText.value = "${labelTextDetail['extra_care_tooltip_admin_enable_text'] ?? "Admin allow user to select extra care ride"}";
    } else if (extraCareData['user'] != null && extraCareData['user']['folks_ride'] == "0") {
      extraCareRideToolTipText.value = "${labelTextDetail['extra_care_tooltip_admin_disable_text'] ?? "Admin disable user to select extra care ride"}";
    } else if (extraCareData['folkRideSetting'] != null) {
      extraCareRideToolTipText.value = "${labelTextDetail['extra_care_tooltip_driver_review_text'] ?? "Driver whose review is"}";
      if (extraCareData['folkRideSetting']['average_rating'] != "null" && extraCareData['folkRideSetting']['average_rating'] != null) {
        extraCareRideToolTipText.value = "${extraCareRideToolTipText.value} ${extraCareData['folkRideSetting']['average_rating']}";
      } else {
        extraCareRideToolTipText.value = "${extraCareRideToolTipText.value} 0";
      }
      extraCareRideToolTipText.value = "${extraCareRideToolTipText.value} ${labelTextDetail['extra_care_tooltip_greater_age_text'] ?? "or greater and his age is"}";

      if (extraCareData['folkRideSetting']['driver_age'] != "null" && extraCareData['folkRideSetting']['driver_age'] != null) {
        extraCareRideToolTipText.value = "${extraCareRideToolTipText.value} ${extraCareData['folkRideSetting']['driver_age']}";
      } else {
        extraCareRideToolTipText.value = "${extraCareRideToolTipText.value} 0";
      }
      extraCareRideToolTipText.value = "${extraCareRideToolTipText.value} ${labelTextDetail['extra_care_tooltip_greater_text'] ?? "or greater"}";

      if (extraCareData['folkRideSetting']['verfiy_phone'] == "1" ||
          extraCareData['folkRideSetting']['verify_email'] == "1" ||
          extraCareData['folkRideSetting']['driver_license'] == "1") {
        extraCareRideToolTipText.value = "${extraCareRideToolTipText.value} ${labelTextDetail['extra_care_tooltip_and_his_text'] ?? "and his"}";
        if (extraCareData['folkRideSetting']['verfiy_phone'] == "1") {
          extraCareRideToolTipText.value = "${extraCareRideToolTipText.value} ${labelTextDetail['extra_care_tooltip_phone_number_text'] ?? "phone number"},";
        }
        if (extraCareData['folkRideSetting']['verify_email'] == "1") {
          extraCareRideToolTipText.value = "${extraCareRideToolTipText.value} ${labelTextDetail['extra_care_tooltip_email_text'] ?? "email"},";
        }
        if (extraCareData['folkRideSetting']['driver_license'] == "1") {
          extraCareRideToolTipText.value = "${extraCareRideToolTipText.value} ${labelTextDetail['extra_care_tooltip_driver_license_text'] ?? "driver license"}";
        }
        extraCareRideToolTipText.value = "${extraCareRideToolTipText.value} ${labelTextDetail['extra_care_tooltip_verified_text'] ?? "verified"}";
      }
      extraCareRideToolTipText.value = "${extraCareRideToolTipText.value} ${labelTextDetail['extra_care_tooltip_eligible_text'] ?? "is eligible for extra care ride"}";
    }
  }

  // Keep individual methods below for backward compatibility and when only specific data is needed

  getLabelTextDetail() async{
    try{
      isLoading(true);
      await PostRideProvider().getLabelTextDetail(
        serviceController.langId
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['postRidePage'] != null){
            labelTextDetail.addAll(resp['data']['postRidePage']);
            vehicleTypeLabelList.add(labelTextDetail['vehicle_type_convertible_text'] ?? "Convertable");
            vehicleTypeList.add(labelTextDetail['vehicle_type_convertible_value']);
            vehicleTypeLabelList.add(labelTextDetail['vehicle_type_coupe_text'] ?? "Coupe");
            vehicleTypeList.add(labelTextDetail['vehicle_type_coupe_value']);
            vehicleTypeLabelList.add(labelTextDetail['vehicle_type_hatchback_text'] ?? "Hatchback");
            vehicleTypeList.add(labelTextDetail['vehicle_type_hatchback_value']);
            vehicleTypeLabelList.add(labelTextDetail['vehicle_type_minivan_text'] ?? "Minivan");
            vehicleTypeList.add(labelTextDetail['vehicle_type_minivan_value']);
            vehicleTypeLabelList.add(labelTextDetail['vehicle_type_sedan_text'] ?? "Sedan");
            vehicleTypeList.add(labelTextDetail['vehicle_type_sedan_value']);
            vehicleTypeLabelList.add(labelTextDetail['vehicle_type_station_wagon_text'] ?? "Station wagon");
            vehicleTypeList.add(labelTextDetail['vehicle_type_station_wagon_value']);
            vehicleTypeLabelList.add(labelTextDetail['vehicle_type_suv_text'] ?? "SUV");
            vehicleTypeList.add(labelTextDetail['vehicle_type_suv_value']);
            vehicleTypeLabelList.add(labelTextDetail['vehicle_type_truck_text'] ?? "Truck");
            vehicleTypeList.add(labelTextDetail['vehicle_type_truck_value']);
            vehicleTypeLabelList.add(labelTextDetail['vehicle_type_van_text'] ?? "Van");
            vehicleTypeList.add(labelTextDetail['vehicle_type_van_value']);
          }
          if(resp['data'] != null && resp['data']['messages'] != null){
            popupTextDetail.addAll(resp['data']['messages']);
          }

          if(resp['data'] != null && resp['data']['validationMessages'] != null){
            validationMessageDetail.addAll(resp['data']['validationMessages']);
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

  getPostRide() async{
    try{
      await PostRideProvider().getPostRide(
          serviceController.token
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['vehicles'] != null){
            vehicleList.addAll(resp['data']['vehicles']);
            var res = vehicleList.firstWhereOrNull((element) => element['primary_vehicle'] == '1');
            if(res != null){
              vehicleId.value = res['id'].toString();
              alreadyAdded.value = true;
            }else if(vehicleList.isNotEmpty && vehicleList.length == 1){
              vehicleId.value = vehicleList[0]['id'].toString();
              alreadyAdded.value = true;
            }

            fuel.value = "Gas";
            bookingType.value = "36";
          }
          if(resp['data'] != null && resp['data']['overallRating'] != null){
            overallRating.value = double.parse(resp['data']['overallRating'].toString());
          }
        }
      },onError: (err){
        serviceController.showDialogue(err.toString());
      });

    }catch (exception) {
      serviceController.showDialogue(exception.toString());
    }
  }

  getPreferenceOptions() async{
    try{
      await PostRideProvider().getPreferenceOptions(
        serviceController.token,
        serviceController.langId.value
      ).then((resp) async {
         if(resp['status'] != null && resp['status'] == "Success"){
           if(resp['data'] != null && resp['data']['preferencesOptions'] != null){
             smokingList.add(resp['data']['preferencesOptions']['smoking_option1']);
             smokingList.add(resp['data']['preferencesOptions']['smoking_option2']);

             smoking.value = "21";

             smokingLabelList.add(resp['data']['preferencesOptions']['smoking_option1_label']);
             smokingLabelList.add(resp['data']['preferencesOptions']['smoking_option2_label']);

             petList.add(resp['data']['preferencesOptions']['animals_option1']);
             petList.add(resp['data']['preferencesOptions']['animals_option2']);
             petList.add(resp['data']['preferencesOptions']['animals_option3']);

             pet.value = "23";

             petLabelList.add(resp['data']['preferencesOptions']['animals_option1_label']);
             petLabelList.add(resp['data']['preferencesOptions']['animals_option2_label']);
             petLabelList.add(resp['data']['preferencesOptions']['animals_option3_label']);
           }
        }
      },onError: (err){
        serviceController.showDialogue(err.toString());
      });

    }catch (exception) {
      serviceController.showDialogue(exception.toString());
    }
  }

  getRidePreferenceOptions() async{
    try{
      await PostRideProvider().getRidePreferenceOptions(
          serviceController.token,
          serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['featuresOptions'] != null){
            rideFeatureList.addAll(resp['data']['featuresOptions']);
          }
          if(resp['data'] != null && resp['data']['featuresLabels'] != null){
            rideFeatureLabelList.addAll(resp['data']['featuresLabels']);
          }


        }
      },onError: (err){
        serviceController.showDialogue(err.toString());
      });

    }catch (exception) {
      serviceController.showDialogue(exception.toString());
    }
  }

  getPinkRideInfo() async{
    try{
      await PostRideProvider().getPinkRideInfo(
          serviceController.token,
          serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){

          if(resp['data']['user']['pink_ride'] == "1"){
            pinkRideToolTipText.value =
            "${labelTextDetail['pink_ride_tooltip_admin_enable_text'] ?? "Admin allow user to select pink ride"}";
          }else if(resp['data']['user']['pink_ride'] == "0"){
            pinkRideToolTipText.value =
            "${labelTextDetail['pink_ride_tooltip_admin_disable_text'] ?? "Admin disable user to select pink ride"}";
          }else if(resp['data'] != null && resp['data']['pinkRideSetting'] != null){
            pinkRideToolTipText.value = "${labelTextDetail['pink_ride_tooltip_only_text'] ?? "Only"}";
            if(resp['data']['pinkRideSetting']['female'] == "1"){
              pinkRideToolTipText.value = "${pinkRideToolTipText.value} ${labelTextDetail['pink_ride_tooltip_female_text'] ?? "female"}";
            }
            pinkRideToolTipText.value = "${pinkRideToolTipText.value} ${labelTextDetail['pink_ride_tooltip_driver_text'] ?? "driver"}";

            if(resp['data']['pinkRideSetting']['verfiy_phone'] == "1" ||
                resp['data']['pinkRideSetting']['verfiy_email'] == "1" ||
                resp['data']['pinkRideSetting']['driver_license'] == "1") {
              pinkRideToolTipText.value = "${pinkRideToolTipText.value} ${labelTextDetail['pink_ride_tooltip_with_text'] ?? "with"}";
              if (resp['data']['pinkRideSetting']['verfiy_phone'] == "1") {
                pinkRideToolTipText.value =
                "${pinkRideToolTipText.value} ${labelTextDetail['pink_ride_tooltip_phone_number_text'] ?? "phone number"},";
              }

              if (resp['data']['pinkRideSetting']['verfiy_email'] == "1") {
                pinkRideToolTipText.value =
                "${pinkRideToolTipText.value} ${labelTextDetail['pink_ride_tooltip_email_text'] ?? "email"},";
              }

              if (resp['data']['pinkRideSetting']['driver_license'] == "1") {
                pinkRideToolTipText.value =
                "${pinkRideToolTipText.value} ${labelTextDetail['pink_ride_tooltip_driver_license_text'] ?? "driver license"}";
              }
              pinkRideToolTipText.value = "${pinkRideToolTipText.value} ${labelTextDetail['pink_ride_tooltip_verified_text'] ??  "verified"}";
            }
            pinkRideToolTipText.value = "${pinkRideToolTipText.value} ${labelTextDetail['pink_ride_tooltip_select_this_ride_text'] ?? "can select this ride"}";
          }
        }
      },onError: (err){
        serviceController.showDialogue(err.toString());
      });

    }catch (exception) {
      serviceController.showDialogue(exception.toString());
    }
  }

  getExtraCareRideInfo() async{
    try{
      await PostRideProvider().getExtraCareRideInfo(
          serviceController.token,
          serviceController.langId.value
      ).then((resp) async {
        if(resp['data']['user']['folks_ride'] == "1"){
          extraCareRideToolTipText.value =
          "${labelTextDetail['extra_care_tooltip_admin_enable_text'] ??
              "Admin allow user to select extra care ride"}";
        }else if(resp['data']['user']['folks_ride'] == "0"){
          extraCareRideToolTipText.value =
          "${labelTextDetail['extra_care_tooltip_admin_disable_text'] ??
              "Admin disable user to select extra care ride"}";
        }else if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['folkRideSetting'] != null){
            extraCareRideToolTipText.value = "${labelTextDetail['extra_care_tooltip_driver_review_text'] ?? "Driver whose review is"}";
            if(resp['data']['folkRideSetting']['average_rating'] != "null"){
              extraCareRideToolTipText.value = "${extraCareRideToolTipText.value} ${resp['data']['folkRideSetting']['average_rating']}";
            }else{
              extraCareRideToolTipText.value = "${extraCareRideToolTipText.value} 0";
            }
            extraCareRideToolTipText.value = "${extraCareRideToolTipText.value} ${labelTextDetail['extra_care_tooltip_greater_age_text'] ?? "or greater and his age is"}";

            if(resp['data']['folkRideSetting']['driver_age'] != "null"){
              extraCareRideToolTipText.value = "${extraCareRideToolTipText.value} ${resp['data']['folkRideSetting']['driver_age']}";
            }else{
              extraCareRideToolTipText.value = "${extraCareRideToolTipText.value} 0";
            }
            extraCareRideToolTipText.value = "${extraCareRideToolTipText.value} ${labelTextDetail['extra_care_tooltip_greater_text'] ?? "or greater"}";

            if(resp['data']['folkRideSetting']['verfiy_phone'] == "1" ||
                resp['data']['folkRideSetting']['verify_email'] == "1" ||
                resp['data']['folkRideSetting']['driver_license'] == "1") {
              extraCareRideToolTipText.value = "${extraCareRideToolTipText.value} ${labelTextDetail['extra_care_tooltip_and_his_text'] ?? "and his"}";
              if (resp['data']['folkRideSetting']['verfiy_phone'] == "1") {
                extraCareRideToolTipText.value =
                "${extraCareRideToolTipText.value} ${labelTextDetail['extra_care_tooltip_phone_number_text'] ?? "phone number"},";
              }

              if (resp['data']['folkRideSetting']['verify_email'] == "1") {
                extraCareRideToolTipText.value =
                "${extraCareRideToolTipText.value} ${labelTextDetail['extra_care_tooltip_email_text'] ?? "email"},";
              }

              if (resp['data']['folkRideSetting']['driver_license'] == "1") {
                extraCareRideToolTipText.value =
                "${extraCareRideToolTipText.value} ${labelTextDetail['extra_care_tooltip_driver_license_text'] ??  "driver license"}";
              }
              extraCareRideToolTipText.value = "${extraCareRideToolTipText.value} ${labelTextDetail['extra_care_tooltip_verified_text'] ?? "verified"}";
            }
          }
          extraCareRideToolTipText.value = "${extraCareRideToolTipText.value} ${labelTextDetail['extra_care_tooltip_eligible_text'] ?? "is eligible for extra care ride"}";
        }
      },onError: (err){
        serviceController.showDialogue(err.toString());
      });

    }catch (exception) {
      serviceController.showDialogue(exception.toString());
    }
  }

  getBookingOption() async{
    try{
      await PostRideProvider().getBookingOption(
          serviceController.token,
          serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['bookingOptions'] != null){
            bookingOptionList.addAll(resp['data']['bookingOptions']);
          }
          if(resp['data'] != null && resp['data']['bookingTooltips'] != null){
            bookingOptionToolTipList.addAll(resp['data']['bookingTooltips']);
          }

          if(resp['data'] != null && resp['data']['bookingLabels'] != null){
            bookingOptionLabelList.addAll(resp['data']['bookingLabels']);
          }


        }
      },onError: (err){
        serviceController.showDialogue(err.toString());
      });

    }catch (exception) {
      serviceController.showDialogue(exception.toString());
    }
  }


  getCancellationOption() async{
    try{
      await PostRideProvider().getCancellationOption(
          serviceController.token,
          serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['cancellationOptions'] != null){
            cancellationOptionList.addAll(resp['data']['cancellationOptions']);
            print(cancellationOptionList);
          }
          if(resp['data'] != null && resp['data']['cancellationTooltips'] != null){
            cancellationOptionToolTipList.addAll(resp['data']['cancellationTooltips']);
          }

          if(resp['data'] != null && resp['data']['cancellationLabels'] != null){
            cancellationOptionLabelList.addAll(resp['data']['cancellationLabels']);
          }


        }
      },onError: (err){
        serviceController.showDialogue(err.toString());
      });

    }catch (exception) {
      serviceController.showDialogue(exception.toString());
    }
  }

  getLuggage() async{
    try{
      await PostRideProvider().getLuggage(
          serviceController.token,
          serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['luggageOptions'] != null){
            luggageList.addAll(resp['data']['luggageOptions']);
          }

          if(resp['data'] != null && resp['data']['luggageTooltips'] != null){
            luggageListToolTip.addAll(resp['data']['luggageTooltips']);
          }


          if(resp['data'] != null && resp['data']['luggageLabels'] != null){
            luggageListLabel.addAll(resp['data']['luggageLabels']);
          }
        }
      },onError: (err){
        serviceController.showDialogue(err.toString());
      });

    }catch (exception) {
      serviceController.showDialogue(exception.toString());
    }
  }

  getPaymentOptions() async{
  try{
      await PostRideProvider().getPaymentOptions(
          serviceController.token,
          serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){

          if(resp['data'] != null && resp['data']['paymentOptions'] != null){
            paymentOptionList.addAll(resp['data']['paymentOptions']);
          }
          if(resp['data'] != null && resp['data']['paymentTooltips'] != null){
            paymentOptionToolTipList.addAll(resp['data']['paymentTooltips']);
          }
          if(resp['data'] != null && resp['data']['paymentLabels'] != null){
            paymentOptionLabelList.addAll(resp['data']['paymentLabels']);
          }
        }
      },onError: (err){
        serviceController.showDialogue(err.toString());
      });

    }catch (exception) {
    serviceController.showDialogue(exception.toString());
    }
  }

  getPostRideAgainData(id, type) async{
  if(type == "rePostRide"){
    scrollController.animateTo(
      scrollController.position.minScrollExtent,
      curve: Curves.easeOut,
      duration: const Duration(milliseconds: 500),
    );
    isOverlayLoading(true);
  }
    try{
      await PostRideProvider().getPostRideAgainData(
          id,
          serviceController.token,
          serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['ride'] != null){

            if(type == "rePostRide"){
              fromTextEditingController.text = resp['data']['ride']['default_ride_detail'][0]['destination'];
              toTextEditingController.text = resp['data']['ride']['default_ride_detail'][0]['departure'];
              pickUpLocationTextEditingController.text = resp['data']['ride']['dropoff'] ?? "";
              dropOffLocationTextEditingController.text = resp['data']['ride']['pickup'] ?? "";
              var timeFormat = DateFormat("HH:mm:ss");
              var parsedTime = timeFormat.parse(resp['data']['ride']['time'].toString());
              timeTextEditingController.text = DateFormat("HH:mm").format(parsedTime);
            }else{
              fromTextEditingController.text = resp['data']['ride']['default_ride_detail'][0]['departure'];
              toTextEditingController.text = resp['data']['ride']['default_ride_detail'][0]['destination'];
              pickUpLocationTextEditingController.text = resp['data']['ride']['pickup'] ?? "";
              dropOffLocationTextEditingController.text = resp['data']['ride']['dropoff'] ?? "";
              timeTextEditingController.text = "";
            }

            dateTextEditingController.text = "";
            recurring.value = resp['data']['ride']['recurring'] == "1" ? true : false;
            recurringType.value = "";
            recurringTripsTextEditingController.text = "";
            dropOffDescriptionTextEditingController.text = resp['data']['ride']['details'].toString();
            seatAvailable.value = int.parse(resp['data']['ride']['seats'].toString());
            seatMiddle.value = int.parse(resp['data']['ride']['middle_seats'].toString());
            seatBack.value = int.parse(resp['data']['ride']['back_seats'].toString());
            skipNow.value  = resp['data']['ride']['skip_vehicle'] == "1" ? true : false;
            bookingType.value = resp['data']['ride']['booking_type'].toString();

            if(resp['data']['ride']['more_ride_detail'] != null && resp['data']['ride']['more_ride_detail'].length > 0){
              var moreRideDetail = List<dynamic>.empty(growable: true);
              moreRideDetail.addAll(resp['data']['ride']['more_ride_detail']);
              print(moreRideDetail);
              if(moreRideDetail.isNotEmpty){
                spotsCount.value = moreRideDetail.length;
                for(var index = 0; index < moreRideDetail.length; index++){

                  if(moreRideDetail[index]['destination'] != null){
                    if(index != 0){
                      fromSpotControllers.add(TextEditingController());
                      toSpotControllers.add(TextEditingController());
                      priceSpotControllers.add(TextEditingController());
                    }
                    if(type == "rePostRide"){
                      fromSpotControllers[index].text = moreRideDetail[index]['destination'];
                      toSpotControllers[index].text = moreRideDetail[index]['departure'];
                    }else{
                      fromSpotControllers[index].text = moreRideDetail[index]['departure'];
                      toSpotControllers[index].text = moreRideDetail[index]['destination'];
                    }
                    priceSpotControllers[index].text = moreRideDetail[index]['price'];
                    rideDetailIds.add("${moreRideDetail[index]['id']}");
                  }
                }
              }

            }





            if(type == "rePostRide"){

              addNewVehicle.value  = false;
              makeTextEditingController.text = "";
              modelTextEditingController.text  = "";
              licenseNumberTextEditingController.text  = "";
              colorTextEditingController.text = "";
              yearTextEditingController.text = "";
              vehicleType.value = "";
              fuel.value = "";
              carOldImagePath.value = "";

              vehicleList.clear();
              if(vehicleList.isEmpty){
                await PostRideProvider().getPostRide(
                    serviceController.token
                ).then((resp) async {
                  if(resp['status'] != null && resp['status'] == "Success"){
                    if(resp['data'] != null && resp['data']['vehicles'] != null){
                      vehicleList.addAll(resp['data']['vehicles']);
                      var res = vehicleList.firstWhereOrNull((element) => element['primary_vehicle'] == '1');
                      if(res != null){
                        vehicleId.value = res['id'].toString();
                        alreadyAdded.value = false;
                      }
                    }
                  }
                },onError: (err){
                });
              }


            }else{
              if(resp['data']['ride']['add_vehicle'] == "1")
              {
                addNewVehicle.value  = resp['data']['ride']['add_vehicle'] == "1" ? true : false;
                makeTextEditingController.text = resp['data']['ride']['make'].toString();
                modelTextEditingController.text  = resp['data']['ride']['model'].toString();
                licenseNumberTextEditingController.text  = resp['data']['ride']['license_no'].toString();
                colorTextEditingController.text = resp['data']['ride']['color'].toString();
                yearTextEditingController.text = resp['data']['ride']['year'].toString();
                vehicleType.value = resp['data']['ride']['vehicle_type'].toString();
                fuel.value = resp['data']['ride']['car_type'].toString();
                carOldImagePath.value = resp['data']['ride']['vehicle']['image'].toString();
              }

              alreadyAdded.value = resp['data']['ride']['added_vehicle'] == "1" ? true : false;
              if(resp['data']['ride']['added_vehicle'] == "1")
              {
                vehicleId.value = resp['data']['ride']['vehicle_id'] == null ? "" : resp['data']['ride']['vehicle_id'].toString();
              }
            }



            smoking.value = resp['data']['ride']['smoke'].toString();
            pet.value = resp['data']['ride']['animal_friendly'].toString();
            featureList.clear();

            var featureData = List<dynamic>.empty(growable: true);
            List features = resp['data']['ride']['features'];
            List<String> titlesToRemove = [
              "1",
              "2",
            ];
            List filteredFeatures = features.where((feature) {
              return !titlesToRemove.contains(feature['id']);
            }).toList();
            featureData.addAll(filteredFeatures);
            print("test2$featureData");
            for (var element in featureData) {
              featureList.add(element['id'].toString());
            }

            bookingOption.value = resp['data']['ride']['booking_method'].toString();
            luggage.value = resp['data']['ride']['luggage'].toString();
            paymentOption.value = resp['data']['ride']['payment_method'].toString();
            disclaimer.value = false;
            acceptMoreLuggage.value = resp['data']['ride']['accept_more_luggage'].toString();
            openCustomized.value = resp['data']['ride']['open_customized'].toString();
            pricePerSeatTextEditingController.text = resp['data']['ride']['default_ride_detail'][0]['price'].toString();
            anythingTextEditingController.text = resp['data']['ride']['notes'].toString();
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

  void getImage(ImageSource imageSource) async{
    final croppedFile = await serviceController.imageCropper(imageSource);
    if (croppedFile != null) {
      carImagePath.value = croppedFile.path;
      carImageName.value = croppedFile.path
          .split('/')
          .last;
      carImagePathOriginal.value = serviceController.originalImagePath.value;
      serviceController.originalImagePath.value = "";
      carImageNameOriginal.value = serviceController.originalImageName.value;
      serviceController.originalImageName.value = "";
      Get.back();
    }
  }

  scrollError(context, position ,screenHeight){

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

  postRide(context, screenHeight) async{
    try{

      if(carImagePathOriginal.value != ""){
        final file = File(carImagePathOriginal.value);
        int sizeInBytes = file.lengthSync();
        double sizeInMb = sizeInBytes / (1024 * 1024);
        if (sizeInMb > 10){
          var message = validationMessageDetail['max.file'];
          message = message.replaceAll(":max", '10');
          message = message.replaceAll(":attribute", labelTextDetail['photo_error'] ?? 'car image');
          var err = {
            'title': "image",
            'eList' : [message ?? 'Can not upload image size greater than 10MB']
          };
          errors.add(err);
          return;
        }
      }

      scrollField = false;
      isOverlayLoading(true);
      String features = "";
      if(featureList.isNotEmpty){
        for(int i=0;i<featureList.length;i++)
        {
          features+= featureList[i];
          if(i< featureList.length-1) {
            features+= "=";
          }
        }
      }

      if(alreadyAdded.value){
        if(vehicleId.value == ""){

        }
      }

      var fromSpots = [];
      var toSpots = [];
      var priceSpots = [];
      var rideDetailIdsArray = [];
      if(fromSpotControllers.isNotEmpty && fromSpotControllers[0].text != ""){
        for(var fromIndex = 0; fromIndex < fromSpotControllers.length; fromIndex++){
          fromSpots.add(fromSpotControllers[fromIndex].text);
        }

        for(var toIndex = 0; toIndex < fromSpotControllers.length; toIndex++){
          toSpots.add(toSpotControllers[toIndex].text);
        }

        for(var priceIndex = 0; priceIndex < fromSpotControllers.length; priceIndex++){
          priceSpots.add(priceSpotControllers[priceIndex].text);
        }

        for(var rideIndex = 0; rideIndex < rideDetailIds.length; rideIndex++){
          rideDetailIdsArray.add(rideDetailIds[rideIndex]);
        }
      }

      await PostRideProvider().postRide(
        fromTextEditingController.text,
          toTextEditingController.text,
          pickUpLocationTextEditingController.text,
          dropOffLocationTextEditingController.text,
          dateTextEditingController.text,
          timeTextEditingController.text,
          recurring.value,
          recurringType.value,
          recurringTripsTextEditingController.text,
          dropOffDescriptionTextEditingController.text,
          seatAvailable.value,
          seatMiddle.value,
          seatBack.value,
          skipNow.value,
          addNewVehicle.value,
          alreadyAdded.value,
          makeTextEditingController.text.trim(),
          modelTextEditingController.text,
          licenseNumberTextEditingController.text,
          colorTextEditingController.text,
          yearTextEditingController.text,
          vehicleType.value,
          fuel.value,
          carImagePath.value,
          carImageName.value,
        carImagePathOriginal.value,
        carImageNameOriginal.value,
          smoking.value,
          pet.value,
          features,
          bookingOption.value,
          luggage.value,
          paymentOption.value,
          disclaimer.value,
          acceptMoreLuggage.value,
          openCustomized.value,
          pricePerSeatTextEditingController.text,
          anythingTextEditingController.text,
          vehicleId.value,
          serviceController.token,
          rideType.value,
          rideId.value,
          bookingType.value,
          fromSpots,
          toSpots,
          priceSpots,
          rideDetailIdsArray
      ).then((resp) async {
        errorList.clear();
        errors.clear();
        if(resp['status'] != null && resp['status'] == "Error"){
          serviceController.showDialogue(resp['message'].toString());
        }else if(resp['errors'] != null){

          var positionValue = 1;

          if(resp['errors']['from'] != null){
            var err = {
              'title': "from",
              'eList' : resp['errors']['from']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          }else{
            positionValue += 1;
          }if(resp['errors']['to'] != null){
            var err = {
              'title': "to",
              'eList' : resp['errors']['to']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          }else{
            positionValue += 1;
          }if(resp['errors']['pickup'] != null ){
            var err = {
              'title': "pickup",
              'eList' : resp['errors']['pickup']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          }else{
            positionValue += 1;
          }if(resp['errors']['dropoff'] != null){
            var err = {
              'title': "dropoff",
              'eList' : resp['errors']['dropoff']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          }else{
            positionValue += 1;
          }if(resp['errors']['date'] != null){
            var err = {
              'title': "date",
              'eList' : resp['errors']['date']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          }else{
            positionValue += 1;
          }if(resp['errors']['time'] != null){
            var err = {
              'title': "time",
              'eList' : resp['errors']['time']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          }else{
            positionValue += 1;
          }
          if(resp['errors']['recurring_type'] != null){
            var err = {
              'title': "recurring_type",
              'eList' : resp['errors']['recurring_type']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          }else{
            recurring.value == false ? positionValue : positionValue += 1;
          }
          if(resp['errors']['recurring_trips'] != null){
            var err = {
              'title': "recurring_trips",
              'eList' : resp['errors']['recurring_trips']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          }else{
            recurring.value == false ? positionValue : positionValue += 1;
          }
          if(resp['errors']['details'] != null){
            var err = {
              'title': "details",
              'eList' : resp['errors']['details']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          }else{
            positionValue += 1;
          }
          if(resp['errors']['seats'] != null){
            var err = {
              'title': "seats",
              'eList' : resp['errors']['seats']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          }else{
            positionValue += 1;
          }
          if(resp['errors']['middle_seats'] != null){
            var err = {
              'title': "middle_seats",
              'eList' : resp['errors']['middle_seats']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          }
          else{
            positionValue += 1;
          }
          if(resp['errors']['back_seats'] != null){
            var err = {
              'title': "back_seats",
              'eList' : resp['errors']['back_seats']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          }else{
            positionValue += 1;
          }

          if(skipNow.value == false && addNewVehicle.value == false && alreadyAdded.value == false){
            addNewVehicle.value = true;
          }

          if(resp['errors']['vehicle_id'] != null){
            var err = {
              'title': "vehicle_id",
              'eList' : resp['errors']['vehicle_id']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          }else{
            positionValue += 1;
          }
          if(resp['errors']['make'] != null){
            var err = {
              'title': "make",
              'eList' : resp['errors']['make']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          }else{
            addNewVehicle.value == true ? positionValue += 1 : positionValue;
          }
          if(resp['errors']['model'] != null){
            var err = {
              'title': "model",
              'eList' : resp['errors']['model']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          }else{
            addNewVehicle.value == true ? positionValue += 1 : positionValue;
          }
          if(resp['errors']['license_no'] != null){
            var err = {
              'title': "license_no",
              'eList' : resp['errors']['license_no']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          }else{
            addNewVehicle.value == true ? positionValue += 1 : positionValue;
          }
          if(resp['errors']['color'] != null){
            var err = {
              'title': "color",
              'eList' : resp['errors']['color']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          }else{
            addNewVehicle.value == true ? positionValue += 1 : positionValue;
          }
          if(resp['errors']['year'] != null){
            var err = {
              'title': "year",
              'eList' : resp['errors']['year']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          }else{
            addNewVehicle.value == true ? positionValue += 1 : positionValue;
          }
          if(resp['errors']['vehicle_type'] != null){
            var err = {
              'title': "vehicle_type",
              'eList' : resp['errors']['vehicle_type']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          }else{
            addNewVehicle.value == true ? positionValue += 1 : positionValue;
          }
          if(resp['errors']['car_type'] != null){
            var err = {
              'title': "car_type",
              'eList' : resp['errors']['car_type']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          }else{
            addNewVehicle.value == true ? positionValue += 1 : positionValue;
          }
          if(resp['errors']['image'] != null){
            var err = {
              'title': "image",
              'eList' : resp['errors']['image']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          }else{
            addNewVehicle.value == true ? positionValue += 1 : positionValue;
          }
          if(resp['errors']['smoke'] != null){
            var err = {
              'title': "smoke",
              'eList' : resp['errors']['smoke']
            };
            if(scrollField == false){
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
            errors.add(err);
          }else{
            positionValue += 1;
          }
          if(resp['errors']['animal_friendly'] != null){
            var err = {
              'title': "animal_friendly",
              'eList' : resp['errors']['animal_friendly']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          }else{
            positionValue += 1;
          }

          if(resp['errors']['features'] != null){
            var err = {
              'title': "features",
              'eList' : resp['errors']['features']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue * 1.8, screenHeight);
              scrollField = true;
            }
          }else{
            positionValue += 1;
          }
          if(resp['errors']['booking_method'] != null){
            var err = {
              'title': "booking_method",
              'eList' : resp['errors']['booking_method']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue * 1.8, screenHeight);
              scrollField = true;
            }
          }else{
            positionValue += 1;
          }
          if(resp['errors']['luggage'] != null){
            var err = {
              'title': "luggage",
              'eList' : resp['errors']['luggage']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue * 1.8, screenHeight);
              scrollField = true;
            }
          }else{
            positionValue += 1;
          }
          if(resp['errors']['price'] != null){
            var err = {
              'title': "price",
              'eList' : resp['errors']['price']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue * 1.8, screenHeight);
              scrollField = true;
            }
          }else{
            positionValue += 1;
          }
          if(resp['errors']['payment_method'] != null){
            var err = {
              'title': "payment_method",
              'eList' : resp['errors']['payment_method']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue * 1.8, screenHeight);
              scrollField = true;
            }
          }else{
            positionValue += 1;
          }
          if(resp['errors']['booking_type'] != null){
            var err = {
              'title': "booking_type",
              'eList' : resp['errors']['booking_type']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, positionValue * 1.8, screenHeight);
              scrollField = true;
            }
          }else{
            positionValue += 1;
          }
          if(resp['errors']['notes'] != null){
            var err = {
              'title': "notes",
              'eList' : resp['errors']['notes']
            };
            errors.add(err);
          }
          if(resp['errors']['agree_terms'] != null){
            var err = {
              'title': "agree_terms",
              'eList' : resp['errors']['agree_terms']
            };
            errors.add(err);
          }
        }else if(resp['status'] != null && resp['status'] == "Success"){
            serviceController.navigationIndex.value = 0;
            await Get.defaultDialog(
              title: '',
              titlePadding: EdgeInsets.zero,
              middleText: resp['message'].toString(),
              barrierDismissible: false,
              middleTextStyle: const TextStyle(fontSize: 20),
              actions: [
                ElevatedButton(
                  onPressed: (){
                    isOverlayLoading(false);
                    Get.offAllNamed("/navigation");
                  },
                  style: ElevatedButton.styleFrom(
                      backgroundColor: primaryColor,
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(5)
                      )
                  ),
                  child: txt16SizeWithOutContext(title: serviceController.closeBtnLabel.value, textColor: Colors.white, fontFamily: regular),
                ),
                ElevatedButton(
                  onPressed: () async{
                    isOverlayLoading(false);
                    Get.back();
                    await getPostRideAgainData(resp['data']['ride']['id'], 'rePostRide');
                  },
                  style: ElevatedButton.styleFrom(
                      backgroundColor: btnPrimaryColor,
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(5)
                      )
                  ),
                  child: txt16SizeWithOutContext(title: "${labelTextDetail['repost_ride_btn_label'] ?? "Re-post ride"}", textColor: Colors.white, fontFamily: regular),
                ),
              ],
            );
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

  editPostRideData(id) async{
    try{
      await PostRideProvider().editPostRideData(
          id,
          serviceController.token,
          serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['ride'] != null){
            if(resp['data']['ride']['bookings'].isNotEmpty){
              bookings.value = true;
            }
            fromTextEditingController.text = resp['data']['ride']['default_ride_detail'][0]['departure'];
            toTextEditingController.text = resp['data']['ride']['default_ride_detail'][0]['destination'];
            pickUpLocationTextEditingController.text = resp['data']['ride']['pickup'] ?? "";
            dropOffLocationTextEditingController.text = resp['data']['ride']['dropoff'] ?? "";
            if(resp['data']['ride']['date'] != null){
              DateTime parsedDate = DateTime.parse(resp['data']['ride']['date']);
              DateFormat outputFormat = DateFormat('MMMM d, yyyy');
              dateTextEditingController.text  = outputFormat.format(parsedDate);
            }

            if(resp['data']['ride']['time'] != null) {
              DateTime parsedTime = DateFormat("HH:mm:ss").parse(resp['data']['ride']['time']);
              DateFormat outputTimeFormat = DateFormat("hh:mm");
              timeTextEditingController.text = outputTimeFormat.format(parsedTime);
            }

            if(resp['data']['ride']['more_ride_detail'] != null && resp['data']['ride']['more_ride_detail'].length > 0){
              var moreRideDetail = List<dynamic>.empty(growable: true);
              moreRideDetail.addAll(resp['data']['ride']['more_ride_detail']);
              if(moreRideDetail.isNotEmpty){
                spotsCount.value = moreRideDetail.length;
                for(var index = 0; index < moreRideDetail.length; index++){
                  if(index != 0){
                    fromSpotControllers.add(TextEditingController());
                    toSpotControllers.add(TextEditingController());
                    priceSpotControllers.add(TextEditingController());
                  }

                  fromSpotControllers[index].text = moreRideDetail[index]['departure'];
                  toSpotControllers[index].text = moreRideDetail[index]['destination'];
                  priceSpotControllers[index].text = moreRideDetail[index]['price'];
                  rideDetailIds.add("${moreRideDetail[index]['id']}");


                }
              }

            }


            recurring.value = resp['data']['ride']['recurring'] == "1" ? true : false;
            recurringType.value = "";
            recurringTripsTextEditingController.text = "";
            dropOffDescriptionTextEditingController.text = resp['data']['ride']['details'].toString();
            seatAvailable.value = int.parse(resp['data']['ride']['seats'].toString());
            seatMiddle.value = int.parse(resp['data']['ride']['middle_seats'].toString());
            seatBack.value = int.parse(resp['data']['ride']['back_seats'].toString());
            skipNow.value  = resp['data']['ride']['skip_vehicle'] == "1" ? true : false;
            bookingType.value = resp['data']['ride']['booking_type'].toString();


            addNewVehicle.value  = resp['data']['ride']['add_vehicle'] == "1" ? true : false;
            if(resp['data']['ride']['add_vehicle'] == "1")
            {
              makeTextEditingController.text = resp['data']['ride']['make'].toString();
              modelTextEditingController.text  = resp['data']['ride']['model'].toString();
              licenseNumberTextEditingController.text  = resp['data']['ride']['license_no'].toString();
              colorTextEditingController.text = resp['data']['ride']['color'].toString();
              yearTextEditingController.text = resp['data']['ride']['year'].toString();
              vehicleType.value = resp['data']['ride']['vehicle_type'].toString();
              fuel.value = resp['data']['ride']['car_type'].toString();
              carOldImagePath.value = resp['data']['ride']['car_image'].toString();
            }

            alreadyAdded.value  = resp['data']['ride']['added_vehicle'] == "1" ? true : false;
            if(resp['data']['ride']['added_vehicle'] == "1") {
              vehicleId.value = resp['data']['ride']['vehicle_id'] == null ? "" : resp['data']['ride']['vehicle_id'].toString();
            }


            smoking.value = resp['data']['ride']['smoke'].toString();
            pet.value = resp['data']['ride']['animal_friendly'].toString();
            featureList.clear();
            var featureData = List<dynamic>.empty(growable: true);
            featureData.addAll(resp['data']['ride']['features']);
            for (var element in featureData) {
              featureList.add(element['title']);
            }
            bookingOption.value = resp['data']['ride']['booking_method'].toString();
            luggage.value = resp['data']['ride']['luggage'].toString();
            paymentOption.value = resp['data']['ride']['payment_method'].toString();
            disclaimer.value = false;
            acceptMoreLuggage.value = resp['data']['ride']['accept_more_luggage'].toString();
            openCustomized.value = resp['data']['ride']['open_customized'].toString();
            pricePerSeatTextEditingController.text = resp['data']['ride']['default_ride_detail'][0]['price'].toString();
            anythingTextEditingController.text = resp['data']['ride']['0'].toString();
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


  getPostRideSetting() async{
    try{
      await PostRideProvider().getPostRideSetting(
          serviceController.token,
        serviceController.langId.value
      ).then((resp) async {

        bool pinkRide = true;
        bool extraCare = true;

        if(resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null && resp['data']['siteSetting'] != null) {
            firmCancellationPrice.value = int.parse(
                resp['data']['siteSetting']['frim_discount'].toString());
          }

          if(resp['data']['user']['pink_ride'] == "1"){
            pinkRide = true;
          }else if(resp['data']['user']['pink_ride'] == "0"){
            pinkRide = false;
          }else if (resp['data'] != null && resp['data']['pinkRideSetting'] != null) {
            if (resp['data']['pinkRideSetting']['female'] == "1" &&
                resp['data']['user']['gender'].toString().toLowerCase() !=
                    "female") {
              pinkRide = false;
            }
            if (resp['data']['pinkRideSetting']['verify_phone'] == "1" &&
                resp['data']['user']['phone_verified'] != "1") {
              pinkRide = false;
            }
            if (resp['data']['pinkRideSetting']['verify_email'] == "1" &&
                resp['data']['user']['email_verified'] != "1") {
              pinkRide = false;
            }
            if (resp['data']['pinkRideSetting']['driver_license'] == "1" &&
                resp['data']['user']['driver'] != "1") {}
            if (resp['data']['user']['profile_complete'] == "0") {
              pinkRide = false;
            }
          }
          

          if(resp['data']['user']['folks_ride'] == "1"){
            extraCare = true;
          }else if(resp['data']['user']['folks_ride'] == "0"){
            extraCare = false;
          }else if (resp['data'] != null && resp['data']['folkRideSetting'] != null) {
            if (double.parse(
                resp['data']['folkRideSetting']['average_rating'] ?? "0") > double.parse(
                resp['data']['user']['average_rating'] != null
                    ? resp['data']['user']['average_rating'].toString()
                    : "0")) {
              extraCare = false;
            }
            if (int.parse(
                resp['data']['folkRideSetting']['driver_age'] ?? "0") >
                int.parse(resp['data']['user']['age'].toString() ?? "0")) {
              extraCare = false;
            }
            if (int.parse(resp['data']['totalNoOfRides'].toString() ?? "0") < int.parse(
                resp['data']['folkRideSetting']['extra_rides_trip_limit'].toString() ?? "0")) {
              extraCare = false;
            }
            if (int.parse(resp['data']['noShowsCount'].toString() ?? "0") > 0) {
              extraCare = false;
            }
            if (int.parse(resp['data']['cancellationCount'].toString() ?? "0") > 0) {
              extraCare = false;
            }
            if (resp['data']['folkRideSetting']['verify_phone'] == "1" &&
                resp['data']['user']['phone_verified'] != "1") {
              extraCare = false;
            }
            if (resp['data']['folkRideSetting']['verify_email'] == "1" &&
                resp['data']['user']['email_verified'] != "1") {
              extraCare = false;
            }
            if (resp['data']['folkRideSetting']['driver_license'] == "1" &&
                resp['data']['user']['driver'] != "1") {
              extraCare = false;
            }
            if (resp['data']['user']['profile_complete'] == "0") {
              extraCare = false;
            }
          }

          if (pinkRide == false) {
            pinkRideReadOnly.value = true;
          }
          if (extraCare == false) {
            extraCareRideReadOnly.value = true;
          }


          //ToolTip Pink Ride

          if(resp['data']['user']['pink_ride'] == "1"){
            pinkRideToolTipText.value =
            "${labelTextDetail['pink_ride_tooltip_admin_enable_text'] ?? "Admin allow user to select pink ride"}";
          }else if(resp['data']['user']['pink_ride'] == "0"){
            pinkRideToolTipText.value =
            "${labelTextDetail['pink_ride_tooltip_admin_disable_text'] ?? "Admin disable user to select pink ride"}";
          }else if (resp['data'] != null && resp['data']['pinkRideSetting'] != null) {
            pinkRideToolTipText.value =
            "${labelTextDetail['pink_ride_tooltip_only_text'] ?? "Only"}";
            if (resp['data']['pinkRideSetting']['female'] == "1") {
              pinkRideToolTipText.value = "${pinkRideToolTipText
                  .value} ${labelTextDetail['pink_ride_tooltip_female_text'] ??
                  "female"}";
            }
            pinkRideToolTipText.value = "${pinkRideToolTipText
                .value} ${labelTextDetail['pink_ride_tooltip_driver_text'] ??
                "driver"}";

            if (resp['data']['pinkRideSetting']['verfiy_phone'] == "1" ||
                resp['data']['pinkRideSetting']['verfiy_email'] == "1" ||
                resp['data']['pinkRideSetting']['driver_license'] == "1") {
              pinkRideToolTipText.value = "${pinkRideToolTipText
                  .value} ${labelTextDetail['pink_ride_tooltip_with_text'] ??
                  "with"}";
              if (resp['data']['pinkRideSetting']['verfiy_phone'] == "1") {
                pinkRideToolTipText.value =
                "${pinkRideToolTipText
                    .value} ${labelTextDetail['pink_ride_tooltip_phone_number_text'] ??
                    "phone number"},";
              }

              if (resp['data']['pinkRideSetting']['verfiy_email'] == "1") {
                pinkRideToolTipText.value =
                "${pinkRideToolTipText
                    .value} ${labelTextDetail['pink_ride_tooltip_email_text'] ??
                    "email"},";
              }
              if (resp['data']['pinkRideSetting']['driver_license'] == "1") {
                pinkRideToolTipText.value =
                "${pinkRideToolTipText
                    .value} ${labelTextDetail['pink_ride_tooltip_driver_license_text'] ??
                    "driver license"}";
              }
              pinkRideToolTipText.value = "${pinkRideToolTipText
                  .value} ${labelTextDetail['pink_ride_tooltip_verified_text'] ??
                  "verified"}";
            }
            pinkRideToolTipText.value = "${pinkRideToolTipText
                .value} ${labelTextDetail['pink_ride_tooltip_select_this_ride_text'] ??
                "can select this ride"}";
          }


          //ToolTip ExtraCare Ride

          if(resp['data']['user']['folks_ride'] == "1"){
            extraCareRideToolTipText.value =
            "${labelTextDetail['extra_care_tooltip_admin_enable_text'] ??
                "Admin allow user to select extra care ride"}";
          }else if(resp['data']['user']['folks_ride'] == "0"){
            extraCareRideToolTipText.value =
            "${labelTextDetail['extra_care_tooltip_admin_disable_text'] ??
                "Admin disable user to select extra care ride"}";
          }else if (resp['data'] != null && resp['data']['folkRideSetting'] != null) {
            extraCareRideToolTipText.value =
            "${labelTextDetail['extra_care_tooltip_driver_review_text'] ??
                "Driver whose review is"}";

            if (resp['data']['folkRideSetting']['average_rating'] != "null") {
              extraCareRideToolTipText.value = "${extraCareRideToolTipText
                  .value} ${resp['data']['folkRideSetting']['average_rating']}";
            } else {
              extraCareRideToolTipText.value =
              "${extraCareRideToolTipText.value} 0";
            }
            extraCareRideToolTipText.value = "${extraCareRideToolTipText
                .value} ${labelTextDetail['extra_care_tooltip_greater_age_text'] ??
                "or greater and his age is"}";

            if (resp['data']['folkRideSetting']['driver_age'] != "null") {
              extraCareRideToolTipText.value = "${extraCareRideToolTipText
                  .value} ${resp['data']['folkRideSetting']['driver_age']}";
            } else {
              extraCareRideToolTipText.value =
              "${extraCareRideToolTipText.value} 0";
            }
            extraCareRideToolTipText.value = "${extraCareRideToolTipText
                .value} ${labelTextDetail['extra_care_tooltip_greater_text'] ??
                "or greater"}";

            if (resp['data']['folkRideSetting']['verfiy_phone'] == "1" ||
                resp['data']['folkRideSetting']['verify_email'] == "1" ||
                resp['data']['folkRideSetting']['driver_license'] == "1") {
              extraCareRideToolTipText.value = "${extraCareRideToolTipText
                  .value} ${labelTextDetail['extra_care_tooltip_and_his_text'] ??
                  "and his"}";
              if (resp['data']['folkRideSetting']['verfiy_phone'] == "1") {
                extraCareRideToolTipText.value =
                "${extraCareRideToolTipText
                    .value} ${labelTextDetail['extra_care_tooltip_phone_number_text'] ??
                    "phone number"},";
              }

              if (resp['data']['folkRideSetting']['verify_email'] == "1") {
                extraCareRideToolTipText.value =
                "${extraCareRideToolTipText
                    .value} ${labelTextDetail['extra_care_tooltip_email_text'] ??
                    "email"},";
              }

              if (resp['data']['folkRideSetting']['driver_license'] == "1") {
                extraCareRideToolTipText.value =
                "${extraCareRideToolTipText
                    .value} ${labelTextDetail['extra_care_tooltip_driver_license_text'] ??
                    "driver license"}";
              }
              extraCareRideToolTipText.value = "${extraCareRideToolTipText
                  .value} ${labelTextDetail['extra_care_tooltip_verified_text'] ??
                  "verified"}";
            }
          }
          extraCareRideToolTipText.value = "${extraCareRideToolTipText
              .value} ${labelTextDetail['extra_care_tooltip_eligible_text'] ??
              "is eligible for extra care ride"}";
        }
      },onError: (err){
        serviceController.showDialogue(err.toString());
      });

    }catch (exception) {
      serviceController.showDialogue(exception.toString());
    }
  }

  getCitiesDistance() async{
    if(fromTextEditingController.text == "" && toTextEditingController.text == ""){
      return;
    }
    try{
      isOverlayLoading(true);
      await PostRideProvider().getCitiesDistance(
          serviceController.token,
          serviceController.langId,
          fromTextEditingController.text,
          toTextEditingController.text
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['pricePerKm'] != null){
            pricePerSeatTextEditingController.text = resp['data']['pricePerKm'].toString();
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


  getCitiesSpotsDistance(index) async{
    if(fromSpotControllers[index].text == "" && toSpotControllers[index].text == ""){
      return;
    }
    try{
      isOverlayLoading(true);
      await PostRideProvider().getCitiesDistance(
          serviceController.token,
          serviceController.langId,
          fromSpotControllers[index].text,
          toSpotControllers[index].text
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['pricePerKm'] != null){
            priceSpotControllers[index].text = resp['data']['pricePerKm'].toString();
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

  addNewSpot() async{

  var showIndex = spotsCount.value - 1;
  if(fromSpotControllers[showIndex].text == "" || toSpotControllers[showIndex].text == "" || priceSpotControllers[showIndex].text == "" ){
    showErrorSpot.value = true;
    showErrorSpot.refresh();
    return;
  }

    fromSpotControllers.add(TextEditingController());
    toSpotControllers.add(TextEditingController());
    priceSpotControllers.add(TextEditingController());
    rideDetailIds.add("0");
    spotsCount.value = spotsCount.value + 1;
    spotsCount.refresh();
  }

  removeNewSpot(index) async{
    fromSpotControllers.removeAt(index);
    toSpotControllers.removeAt(index);
    priceSpotControllers.removeAt(index);
    rideDetailIds.removeAt(index);
    spotsCount.value = spotsCount.value - 1;
    spotsCount.refresh();
  }


}