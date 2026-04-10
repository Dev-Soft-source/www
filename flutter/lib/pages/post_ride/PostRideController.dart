import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/post_ride/PostRideProvider.dart';
import 'package:proximaride_app/pages/post_ride/widget/stop_form_page.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

class PostRideController extends GetxController {
  static const double errorTriggeringCap = 0.72;
  static const double softWarningCap = 0.66;

  final serviceController = Get.find<Service>();
  ScrollController scrollController = ScrollController();

  var bookingType = "".obs;
  var bookingTypeList = ["standard", "firm"].obs;

  var isLoading = false.obs;
  var isOverlayLoading = false.obs;
  var seatAvailable = 1.obs;
  var seatMiddle = 2.obs;
  var seatBack = 2.obs;
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
  var fromCityId = 0.obs;
  var toCityId = 0.obs;
  var pinkRideReadOnly = false.obs;
  var extraCareRideReadOnly = false.obs;
  var labelTextDetail = {}.obs;
  var popupTextDetail = {}.obs;
  var validationMessageDetail = {}.obs;
  DateTime? minimumReturnRideDateTime;

  void _applyPinkRideAvailability(dynamic pinkRideData, dynamic userData) {
    pinkRideReadOnly.value = pinkRideData?['canUse'] != true;

    final tooltip = pinkRideData?['tooltip']?.toString().trim() ?? '';
    if (tooltip.isNotEmpty) {
      pinkRideToolTipText.value = tooltip;
      return;
    }

    if (userData?['pink_ride'] == "1") {
      pinkRideToolTipText.value =
          "${labelTextDetail['pink_ride_tooltip_admin_enable_text'] ?? "Admin allow user to select pink ride"}";
    } else if (userData?['pink_ride'] == "0") {
      pinkRideToolTipText.value =
          "${labelTextDetail['pink_ride_tooltip_admin_disable_text'] ?? "Admin disable user to select pink ride"}";
    } else {
      pinkRideToolTipText.value =
          pinkRideData?['eligibilityError']?.toString() ?? '';
    }
  }

  void _applyExtraCareRideAvailability(
      dynamic extraCareRideData, dynamic userData) {
    extraCareRideReadOnly.value = extraCareRideData?['canUse'] != true;

    final tooltip = extraCareRideData?['tooltip']?.toString().trim() ?? '';
    if (tooltip.isNotEmpty) {
      extraCareRideToolTipText.value = tooltip;
      return;
    }

    if (userData?['folks_ride'] == "1") {
      extraCareRideToolTipText.value =
          "${labelTextDetail['extra_care_tooltip_admin_enable_text'] ?? "Admin allow user to select extra care ride"}";
    } else if (userData?['folks_ride'] == "0") {
      extraCareRideToolTipText.value =
          "${labelTextDetail['extra_care_tooltip_admin_disable_text'] ?? "Admin disable user to select extra care ride"}";
    } else {
      extraCareRideToolTipText.value =
          extraCareRideData?['eligibilityError']?.toString() ?? '';
    }
  }

  Future<void> _refreshPostRideUserInfo(dynamic userData) async {
    if (userData is! Map) {
      return;
    }

    final refreshedUser = <String, Object>{};

    serviceController.loginUserDetail.forEach((key, value) {
      final nextValue = userData.containsKey(key) ? userData[key] : value;
      refreshedUser[key] = nextValue ?? value;
    });

    for (final entry in userData.entries) {
      if (entry.value != null) {
        refreshedUser[entry.key.toString()] = entry.value as Object;
      }
    }

    if (refreshedUser['langId'] == null || refreshedUser['langId'] == '') {
      refreshedUser['langId'] = serviceController.langId.value.toString();
    }

    serviceController.loginUserDetail.assignAll(refreshedUser);
    await serviceController.secureStorage.write(
      key: "userInfo",
      value: jsonEncode(serviceController.loginUserDetail),
    );
  }

  late TextEditingController fromTextEditingController,
      toTextEditingController,
      pickUpLocationTextEditingController,
      dropOffLocationTextEditingController,
      dateTextEditingController,
      timeTextEditingController,
      dropOffDescriptionTextEditingController,
      makeTextEditingController,
      modelTextEditingController,
      licenseNumberTextEditingController,
      yearTextEditingController,
      colorTextEditingController,
      pricePerSeatTextEditingController,
      anythingTextEditingController,
      recurringTripsTextEditingController;

  final RxInt stopFormPrerequisiteVersion = 0.obs;

  final Map<String, FocusNode> focusNodes = {};

  List<TextEditingController> fromSpotControllers = [];
  List<TextEditingController> toSpotControllers = [];
  List<TextEditingController> priceSpotControllers = [];
  List<TextEditingController> pickupSpotControllers = [];
  List<TextEditingController> dropoffSpotControllers = [];
  List<TextEditingController> dateSpotControllers = [];
  List<TextEditingController> timeSpotControllers = [];
  List<int> stopCityIds = [];
  var rideDetailIds = [];
  var routePriceEntries = <Map<String, dynamic>>[].obs;
  var routeSegmentDistances = <String, int>{}.obs;
  var routeSegmentDurations = <String, int>{}.obs;
  var routeDistanceLoading = false.obs;
  Timer? _routeDistanceDebounceTimer;
  String _routeDistanceRequestKey = "";
  final Set<String> _acknowledgedPriceWarningSignatures = <String>{};

  var spotsCount = 0.obs;
  var showErrorSpot = false.obs;

  var reviews = List<dynamic>.empty(growable: true).obs;
  @override
  void onInit() async {
    super.onInit();
    fromTextEditingController = TextEditingController();
    toTextEditingController = TextEditingController();
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

    fromTextEditingController.addListener(_refreshStopFormPrerequisites);
    toTextEditingController.addListener(_refreshStopFormPrerequisites);
    pickUpLocationTextEditingController.addListener(_refreshStopFormPrerequisites);
    dropOffLocationTextEditingController.addListener(_refreshStopFormPrerequisites);
    dateTextEditingController.addListener(_refreshStopFormPrerequisites);
    timeTextEditingController.addListener(_refreshStopFormPrerequisites);

    rideId.value = int.parse(Get.parameters['id'].toString());
    rideType.value = Get.parameters['type'].toString();

    for (int i = 1; i <= 12; i++) {
      focusNodes[i.toString()] = FocusNode();
      // Attach the onFocusChange listener
      focusNodes[i.toString()]?.addListener(() {
        if (!focusNodes[i.toString()]!.hasFocus) {
          // Field has lost focus, trigger validation
          if (i == 1) {
            validateField(
              'from',
              fromTextEditingController.text,
            );
          } else if (i == 2) {
            validateField(
              'to',
              toTextEditingController.text,
            );
          } else if (i == 3) {
            validateField('pickup', pickUpLocationTextEditingController.text);
          } else if (i == 4) {
            validateField(
              'dropoff',
              dropOffLocationTextEditingController.text,
            );
          } else if (i == 5) {
            validateField(
              'details',
              dropOffDescriptionTextEditingController.text,
            );
          } else if (i == 6) {
            validateField(
              'make',
              makeTextEditingController.text,
            );
          } else if (i == 7) {
            validateField(
              'model',
              modelTextEditingController.text,
            );
          } else if (i == 8) {
            validateField(
              'license_no',
              licenseNumberTextEditingController.text,
            );
          } else if (i == 9) {
            validateField(
              'color',
              colorTextEditingController.text,
            );
          } else if (i == 10) {
            validateField('year', yearTextEditingController.text,
                type: 'numeric');
          } else if (i == 11) {
            validateField('price', pricePerSeatTextEditingController.text,
                type: 'numeric');
          }
        }
      });
    }

    // OPTIMIZED: Single API call instead of 11 sequential calls
    isOverlayLoading(true);
    await getPostRideInitData();
    isOverlayLoading(false);
  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
    _routeDistanceDebounceTimer?.cancel();
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

  String formatSpotDateValue(dynamic value) {
    if (value == null || value.toString().isEmpty) {
      return "";
    }

    final parsedDate = DateTime.tryParse(value.toString());
    if (parsedDate == null) {
      return value.toString();
    }

    return DateFormat('MMMM dd, yyyy').format(parsedDate);
  }

  bool _isTruthyFlag(dynamic value) {
    if (value == null) {
      return false;
    }

    final normalized = value.toString().trim().toLowerCase();
    return normalized == "1" ||
        normalized == "true" ||
        normalized == "yes";
  }

  bool _hasVehicleIdValue(dynamic value) {
    if (value == null) {
      return false;
    }

    final normalized = value.toString().trim().toLowerCase();
    return normalized.isNotEmpty &&
        normalized != "null" &&
        normalized != "0";
  }

  void _syncVehicleSelectionFromRide(Map rideData) {
    final skipVehicle = _isTruthyFlag(rideData['skip_vehicle']);
    final addVehicle = _isTruthyFlag(rideData['add_vehicle']);
    final addedVehicle = _isTruthyFlag(rideData['added_vehicle']);
    final rideVehicleId = _hasVehicleIdValue(rideData['vehicle_id'])
        ? rideData['vehicle_id'].toString()
        : "";

    skipNow.value = skipVehicle;
    addNewVehicle.value = !skipVehicle && addVehicle;
    vehicleId.value = rideVehicleId;
    alreadyAdded.value = !skipVehicle &&
        !addNewVehicle.value &&
        (addedVehicle || rideVehicleId.isNotEmpty);

    if (alreadyAdded.value && vehicleId.value.isEmpty && vehicleList.isNotEmpty) {
      final selectedVehicle = vehicleList.firstWhereOrNull(
            (element) => element['primary_vehicle'].toString() == '1',
          ) ??
          vehicleList.first;
      vehicleId.value = selectedVehicle['id'].toString();
    }

    if (skipNow.value) {
      addNewVehicle.value = false;
      alreadyAdded.value = false;
      vehicleId.value = "";
    }
  }

  /// When "existing vehicle" is selected, default [vehicleId] from primary or first list item if still empty.
  void ensureExistingVehicleIdSelected() {
    if (!alreadyAdded.value ||
        skipNow.value ||
        addNewVehicle.value ||
        vehicleList.isEmpty) {
      return;
    }
    if (vehicleId.value.trim().isNotEmpty) {
      return;
    }
    final selectedVehicle = vehicleList.firstWhereOrNull(
          (element) => element['primary_vehicle'].toString() == '1',
        ) ??
        vehicleList.first;
    vehicleId.value = selectedVehicle['id'].toString();
  }

  String formatSpotTimeValue(dynamic value) {
    if (value == null || value.toString().isEmpty) {
      return "";
    }

    final timeText = value.toString();
    final parsedTime =
        DateFormat("HH:mm:ss").tryParse(timeText) ?? DateFormat("HH:mm").tryParse(timeText);

    if (parsedTime == null) {
      return timeText;
    }

    return DateFormat("HH:mm").format(parsedTime);
  }

  String formatRideDateValue(dynamic value) {
    if (value == null || value.toString().isEmpty) {
      return "";
    }

    final parsedDate = DateTime.tryParse(value.toString());
    if (parsedDate == null) {
      return value.toString();
    }

    return DateFormat('MMMM d, yyyy').format(parsedDate);
  }

  String formatRideTimeValue(dynamic value) {
    if (value == null || value.toString().isEmpty) {
      return "";
    }

    final timeText = value.toString();
    final parsedTime = DateFormat("HH:mm:ss").tryParse(timeText) ??
        DateFormat("HH:mm").tryParse(timeText);

    if (parsedTime == null) {
      return timeText;
    }

    return DateFormat("HH:mm").format(parsedTime);
  }

  String formatMinorPriceForDisplay(dynamic value) {
    if (value == null || value.toString().trim().isEmpty) {
      return "";
    }

    final parsed = num.tryParse(value.toString());
    if (parsed == null) {
      return value.toString();
    }

    final major = parsed / 100;
    return major % 1 == 0 ? major.toInt().toString() : major.toStringAsFixed(2);
  }

  /// Primary price-per-seat field: show empty instead of "0".
  String _primarySeatPriceFieldText(String text) {
    final trimmed = text.trim();
    if (trimmed.isEmpty) return '';
    final n = num.tryParse(trimmed);
    if (n != null && n == 0) return '';
    return trimmed;
  }

  String _formatMinorPriceForSeatField(dynamic value) {
    return _primarySeatPriceFieldText(formatMinorPriceForDisplay(value));
  }

  String buildRoutePriceKey(String fromLabel, String toLabel) {
    return "${fromLabel.trim().toLowerCase()}__${toLabel.trim().toLowerCase()}";
  }

  String shortLocationLabel(String value) {
    final normalized = value.trim();
    if (normalized.isEmpty) {
      return "";
    }

    return normalized.split(',').first.trim();
  }

  List<Map<String, dynamic>> buildOrderedRouteNodes() {
    final nodes = <Map<String, dynamic>>[];

    final fromLabel = fromTextEditingController.text.trim();
    if (fromLabel.isNotEmpty) {
      nodes.add({
        'label': fromLabel,
        'cityId': fromCityId.value,
      });
    }

    for (var index = 0; index < fromSpotControllers.length; index++) {
      final stopLabel = fromSpotControllers[index].text.trim();
      if (stopLabel.isEmpty) {
        continue;
      }

      nodes.add({
        'label': stopLabel,
        'cityId': stopCityIds[index],
      });
    }

    final toLabel = toTextEditingController.text.trim();
    if (toLabel.isNotEmpty) {
      nodes.add({
        'label': toLabel,
        'cityId': toCityId.value,
      });
    }

    return nodes;
  }

  bool get hasRoutePriceEntries => routePriceEntries.isNotEmpty;

  Map<String, String> captureRoutePriceValues() {
    final values = <String, String>{};

    for (final entry in routePriceEntries) {
      final key = entry['key']?.toString() ?? '';
      final controller = entry['controller'] as TextEditingController?;
      if (key.isEmpty || controller == null) {
        continue;
      }

      values[key] = controller.text;
    }

    final nodes = buildOrderedRouteNodes();
    if (nodes.length >= 2) {
      values[buildRoutePriceKey(
        nodes.first['label'].toString(),
        nodes.last['label'].toString(),
      )] = pricePerSeatTextEditingController.text;
    }

    return values;
  }

  void clearRoutePriceEntries() {
    for (final entry in routePriceEntries) {
      final controller = entry['controller'] as TextEditingController?;
      controller?.dispose();
    }
    routePriceEntries.clear();
  }

  void clearRouteDistanceState() {
    _routeDistanceRequestKey = "";
    routeSegmentDistances.clear();
    routeSegmentDurations.clear();
    routeDistanceLoading(false);
  }

  int get totalOrderedRouteDistanceMeters {
    final nodes = buildOrderedRouteNodes();
    if (nodes.length < 2) {
      return 0;
    }

    var totalDistance = 0;
    for (var index = 0; index < nodes.length - 1; index++) {
      final key = buildRoutePriceKey(
        nodes[index]['label'].toString(),
        nodes[index + 1]['label'].toString(),
      );
      totalDistance += routeSegmentDistances[key] ?? 0;
    }

    return totalDistance;
  }

  int get totalOrderedRouteDurationSeconds {
    final nodes = buildOrderedRouteNodes();
    if (nodes.length < 2) {
      return 0;
    }

    var totalDuration = 0;
    for (var index = 0; index < nodes.length - 1; index++) {
      final key = buildRoutePriceKey(
        nodes[index]['label'].toString(),
        nodes[index + 1]['label'].toString(),
      );
      totalDuration += routeSegmentDurations[key] ?? 0;
    }

    return totalDuration;
  }

  List<String> getOrderedRouteLabels() {
    return buildOrderedRouteNodes()
        .map((node) => node['label']?.toString().trim() ?? '')
        .where((label) => label.isNotEmpty)
        .toList();
  }

  String directRouteDistanceHint() {
    final labels = getOrderedRouteLabels();
    if (labels.length < 2) {
      return "";
    }

    final key = buildRoutePriceKey(labels.first, labels.last);
    final distanceMeters = routeSegmentDistances[key] ?? 0;

    if (routeDistanceLoading.value && distanceMeters <= 0) {
      return "Calculating route distance...";
    }

    if (distanceMeters <= 0) {
      return "Warning and max price appear when distance is available.";
    }

    final seats = seatAvailable.value <= 0 ? 1 : seatAvailable.value;
    final distanceKm = distanceMeters / 1000;
    final warningPrice = (distanceKm * softWarningCap) / seats;
    final maxPrice = (distanceKm * errorTriggeringCap) / seats;

    return "Warning: \$${formatRouteCapPrice(warningPrice)} | Max: \$${formatRouteCapPrice(maxPrice)} (${distanceKm.toStringAsFixed(1)} km)";
  }

  String formatRouteCapPrice(double value) {
    return value % 1 == 0 ? value.toInt().toString() : value.toStringAsFixed(2);
  }

  double? parsePricePerSeat(String value) {
    final normalized = value.trim().replaceAll(',', '.');
    if (normalized.isEmpty) {
      return null;
    }

    final parsed = double.tryParse(normalized);
    if (parsed == null || parsed <= 0) {
      return null;
    }

    return parsed;
  }

  Map<String, dynamic> validatePriceAgainstDistance({
    required String priceText,
    required int distanceMeters,
    required int seats,
    required String routeLabel,
  }) {
    final pricePerSeat = parsePricePerSeat(priceText);
    if (pricePerSeat == null || distanceMeters <= 0 || seats <= 0) {
      return {
        'type': null,
        'routeLabel': routeLabel,
      };
    }

    final distanceKm = distanceMeters / 1000;
    final maxPricePerSeat = (distanceKm * errorTriggeringCap) / seats;
    final softWarningPricePerSeat = (distanceKm * softWarningCap) / seats;

    if (pricePerSeat > maxPricePerSeat) {
      return {
        'type': 'error',
        'routeLabel': routeLabel,
        'maxPricePerSeat': formatRouteCapPrice(maxPricePerSeat),
        'distanceKm': distanceKm.toStringAsFixed(2),
        'pricePerSeat': pricePerSeat.toStringAsFixed(2),
      };
    }

    if (pricePerSeat > softWarningPricePerSeat) {
      return {
        'type': 'warning',
        'routeLabel': routeLabel,
        'softWarningPrice': formatRouteCapPrice(softWarningPricePerSeat),
        'distanceKm': distanceKm.toStringAsFixed(2),
        'pricePerSeat': pricePerSeat.toStringAsFixed(2),
      };
    }

    return {
      'type': null,
      'routeLabel': routeLabel,
    };
  }

  String _buildPriceValidationSignature(Map<String, dynamic> validation) {
    return [
      validation['type']?.toString() ?? '',
      validation['routeLabel']?.toString() ?? '',
      validation['pricePerSeat']?.toString() ?? '',
      validation['distanceKm']?.toString() ?? '',
      seatAvailable.value.toString(),
      validation['maxPricePerSeat']?.toString() ?? '',
      validation['softWarningPrice']?.toString() ?? '',
    ].join('|');
  }

  Future<bool> _showPriceErrorDialog(Map<String, dynamic> validation) async {
    final heading = labelTextDetail['price_error_heading'] ??
        'Price Limit Exceeded';
    final message1 = labelTextDetail['carpool_regulation_limit_message'] ??
        'To comply with Canadian and Quebec carpooling regulations, the total amount collected for a trip cannot exceed the official 2026 reimbursement rate of \$0.72/km.';
    final message2Template = labelTextDetail['max_price_per_seat_message'] ??
        'The maximum allowed for this trip is \$:max_per_seat per seat.';
    final message2 = message2Template.replaceAll(
      ':max_per_seat',
      validation['maxPricePerSeat']?.toString() ?? '0',
    );
    final message3 = labelTextDetail['non_commercial_carpool_requirement_message'] ??
        'This limit is mandatory to ensure your ride is classified as a non-commercial carpool, protecting your insurance coverage and maintaining the cost-sharing status of your contributions.';

    await Get.dialog(
      AlertDialog(
        title: Text(heading.toString()),
        content: DefaultTextStyle(
          style: const TextStyle(
            fontFamily: carlito,
            fontSize: 16,
            height: 1.4,
            color: textColor,
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(message1.toString()),
              const SizedBox(height: 12),
              Text(message2),
              const SizedBox(height: 12),
              Text(message3.toString()),
            ],
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Get.back(),
            child: Text(
              (labelTextDetail['price_error_adjust_btn_label'] ?? 'Adjust Price')
                  .toString(),
            ),
          ),
        ],
      ),
      barrierDismissible: true,
    );

    return false;
  }

  Future<bool> _showPriceWarningDialog(Map<String, dynamic> validation) async {
    final signature = _buildPriceValidationSignature(validation);
    if (_acknowledgedPriceWarningSignatures.contains(signature)) {
      return true;
    }

    final result = await Get.dialog<bool>(
      AlertDialog(
        title: Text(
          (labelTextDetail['price_warning_heading'] ??
                  'Recommended Contribution Limit')
              .toString(),
        ),
        content: DefaultTextStyle(
          style: const TextStyle(
            fontFamily: carlito,
            fontSize: 16,
            height: 1.4,
            color: textColor,
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                (labelTextDetail['price_above_reimbursement_warning'] ??
                        'The price you entered is above the standard reimbursement rate recommended by the CRA and Revenu Quebec.')
                    .toString(),
              ),
              const SizedBox(height: 12),
              Text(
                (labelTextDetail['price_reduction_suggestion_message'] ??
                        'While you can proceed, we suggest reducing the price per seat. This ensures your ride remains a standard carpool even if you drive long distances this year.')
                    .toString(),
              ),
            ],
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Get.back(result: false),
            child: Text(
              (labelTextDetail['price_warning_adjust_btn_label'] ??
                      'Adjust Price')
                  .toString(),
            ),
          ),
          ElevatedButton(
            onPressed: () => Get.back(result: true),
            child: Text(
              (labelTextDetail['price_warning_keep_current_btn_label'] ??
                      'Keep Current Price')
                  .toString(),
            ),
          ),
        ],
      ),
      barrierDismissible: true,
    );

    final shouldContinue = result == true;
    if (shouldContinue) {
      _acknowledgedPriceWarningSignatures.add(signature);
    }
    return shouldContinue;
  }

  Future<bool> handlePriceValidationResult(Map<String, dynamic> validation) async {
    final type = validation['type']?.toString();
    if (type == null || type.isEmpty) {
      return true;
    }

    if (type == 'error') {
      return _showPriceErrorDialog(validation);
    }

    if (type == 'warning') {
      return _showPriceWarningDialog(validation);
    }

    return true;
  }

  Future<bool> handlePrimaryPriceEditingComplete() async {
    final labels = getOrderedRouteLabels();
    if (labels.length < 2) {
      return true;
    }

    final normalized =
        _primarySeatPriceFieldText(pricePerSeatTextEditingController.text);
    if (pricePerSeatTextEditingController.text != normalized) {
      pricePerSeatTextEditingController.value = TextEditingValue(
        text: normalized,
        selection: TextSelection.collapsed(offset: normalized.length),
      );
    }

    final key = buildRoutePriceKey(labels.first, labels.last);
    final validation = validatePriceAgainstDistance(
      priceText: pricePerSeatTextEditingController.text,
      distanceMeters: routeSegmentDistances[key] ?? 0,
      seats: seatAvailable.value <= 0 ? 1 : seatAvailable.value,
      routeLabel: 'this trip',
    );

    return handlePriceValidationResult(validation);
  }

  Future<bool> handleRoutePriceEditingComplete(
    Map<String, dynamic> entry,
  ) async {
    final controller = entry['controller'] as TextEditingController?;
    if (controller == null) {
      return true;
    }

    final validation = validatePriceAgainstDistance(
      priceText: controller.text,
      distanceMeters: routeSegmentDistances[entry['key']?.toString() ?? ''] ?? 0,
      seats: seatAvailable.value <= 0 ? 1 : seatAvailable.value,
      routeLabel:
          '${shortLocationLabel(entry['fromLabel']?.toString() ?? '')} -> ${shortLocationLabel(entry['toLabel']?.toString() ?? '')}',
    );

    return handlePriceValidationResult(validation);
  }

  Future<bool> validateAllPricesBeforeSubmit() async {
    if (getOrderedRouteLabels().length >= 2 && routeDistanceLoading.value) {
      serviceController.showDialogue(
        (labelTextDetail['route_distance_loading_message'] ??
                'Route distance is still loading. Please wait a moment.')
            .toString(),
        type: 'info',
      );
      return false;
    }

    if (hasRoutePriceEntries) {
      Map<String, dynamic>? firstWarning;

      for (final entry in routePriceEntries) {
        final controller = entry['controller'] as TextEditingController?;
        if (controller == null) {
          continue;
        }

        final validation = validatePriceAgainstDistance(
          priceText: controller.text,
          distanceMeters:
              routeSegmentDistances[entry['key']?.toString() ?? ''] ?? 0,
          seats: seatAvailable.value <= 0 ? 1 : seatAvailable.value,
          routeLabel:
              '${shortLocationLabel(entry['fromLabel']?.toString() ?? '')} -> ${shortLocationLabel(entry['toLabel']?.toString() ?? '')}',
        );

        if (validation['type'] == 'error') {
          return handlePriceValidationResult(validation);
        }

        if (validation['type'] == 'warning' && firstWarning == null) {
          firstWarning = validation;
        }
      }

      if (firstWarning != null) {
        return handlePriceValidationResult(firstWarning);
      }

      return true;
    }

    return handlePrimaryPriceEditingComplete();
  }

  String routeDistanceHint(Map<String, dynamic> entry) {
    final key = entry['key']?.toString() ?? '';
    final distanceMeters = routeSegmentDistances[key] ?? 0;

    if (routeDistanceLoading.value && distanceMeters <= 0) {
      return "Calculating route distance...";
    }

    if (distanceMeters <= 0) {
      return "Warning and max price appear when distance is available.";
    }

    final seats = seatAvailable.value <= 0 ? 1 : seatAvailable.value;
    final distanceKm = distanceMeters / 1000;
    final warningPrice = (distanceKm * softWarningCap) / seats;
    final maxPrice = (distanceKm * errorTriggeringCap) / seats;

    return "Warning: \$${formatRouteCapPrice(warningPrice)} | Max: \$${formatRouteCapPrice(maxPrice)} (${distanceKm.toStringAsFixed(1)} km)";
  }

  void scheduleRouteDistanceEstimates() {
    _routeDistanceDebounceTimer?.cancel();
    _routeDistanceDebounceTimer = Timer(const Duration(milliseconds: 350), () {
      fetchRouteDistanceEstimates();
    });
  }

  Future<void> checkPosting() async {
    try {
      PostRideProvider().checkPosting(serviceController.token).then(
          (resp) async {
        if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message'].toString());
        } else if (resp['status'] != null && resp['status'] == "Success") {
          Get.toNamed("/post_ride/0/new");
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

  Future<void> fetchRouteDistanceEstimates() async {
    final pointLabels = getOrderedRouteLabels();
    if (pointLabels.length < 2) {
      clearRouteDistanceState();
      return;
    }

    final requestKey = pointLabels.join('||');
    if (_routeDistanceRequestKey == requestKey && routeSegmentDistances.isNotEmpty) {
      return;
    }

    _routeDistanceRequestKey = requestKey;
    routeDistanceLoading(true);

    try {
      final resp = await PostRideProvider()
          .getSegmentDistanceEstimates(serviceController.token, pointLabels);

      final payload = resp is Map && resp['segment_distances_meters'] is Map
          ? Map<String, dynamic>.from(resp['segment_distances_meters'])
          : <String, dynamic>{};
      final durationPayload = resp is Map && resp['segment_durations_seconds'] is Map
          ? Map<String, dynamic>.from(resp['segment_durations_seconds'])
          : <String, dynamic>{};

      final nextDistances = <String, int>{};
      final nextDurations = <String, int>{};
      final nodes = buildOrderedRouteNodes();
      for (var fromIndex = 0; fromIndex < nodes.length - 1; fromIndex++) {
        for (var toIndex = fromIndex + 1; toIndex < nodes.length; toIndex++) {
          final key = buildRoutePriceKey(
            nodes[fromIndex]['label'].toString(),
            nodes[toIndex]['label'].toString(),
          );
          nextDistances[key] =
              int.tryParse((payload['$fromIndex:$toIndex'] ?? 0).toString()) ?? 0;
          nextDurations[key] =
              int.tryParse((durationPayload['$fromIndex:$toIndex'] ?? 0).toString()) ?? 0;
        }
      }

      if (_routeDistanceRequestKey == requestKey) {
        routeSegmentDistances.assignAll(nextDistances);
        routeSegmentDurations.assignAll(nextDurations);
      }
    } catch (_) {
      if (_routeDistanceRequestKey == requestKey) {
        routeSegmentDistances.clear();
        routeSegmentDurations.clear();
      }
    } finally {
      if (_routeDistanceRequestKey == requestKey) {
        routeDistanceLoading(false);
      }
    }
  }

  void handleRoutePriceChanged(Map<String, dynamic> entry, String value) {
    final normalized = _primarySeatPriceFieldText(value);
    if (entry['isDirect'] == true &&
        pricePerSeatTextEditingController.text != normalized) {
      pricePerSeatTextEditingController.text = normalized;
    }

    if (errors.any((error) => error['title'] == "price")) {
      errors.removeWhere((error) => error['title'] == "price");
    }
  }

  String routePriceValueFor(String fromLabel, String toLabel) {
    final key = buildRoutePriceKey(fromLabel, toLabel);

    for (final entry in routePriceEntries) {
      if (entry['key'] == key) {
        final controller = entry['controller'] as TextEditingController?;
        return controller?.text ?? "";
      }
    }

    return "";
  }

  void rebuildRoutePriceEntries({Map<String, String>? seedValues}) {
    final nodes = buildOrderedRouteNodes();
    final existingValues = seedValues ?? captureRoutePriceValues();
    final currentMainPrice = pricePerSeatTextEditingController.text;

    clearRoutePriceEntries();

    if (nodes.length < 3) {
      scheduleRouteDistanceEstimates();
      routePriceEntries.refresh();
      update();
      return;
    }

    final directKey = buildRoutePriceKey(
      nodes.first['label'].toString(),
      nodes.last['label'].toString(),
    );

    final entries = <Map<String, dynamic>>[];
    for (var fromIndex = 0; fromIndex < nodes.length - 1; fromIndex++) {
      for (var toIndex = fromIndex + 1; toIndex < nodes.length; toIndex++) {
        final fromLabel = nodes[fromIndex]['label'].toString();
        final toLabel = nodes[toIndex]['label'].toString();
        final key = buildRoutePriceKey(fromLabel, toLabel);
        final initialValue =
            existingValues[key] ?? currentMainPrice;

        final controller = TextEditingController(text: initialValue);
        final entry = <String, dynamic>{
          'key': key,
          'fromLabel': fromLabel,
          'toLabel': toLabel,
          'controller': controller,
          'isDirect': key == directKey,
        };

        controller.addListener(() {
          handleRoutePriceChanged(entry, controller.text);
        });

        entries.add(entry);
      }
    }

    routePriceEntries.assignAll(entries);

    for (final entry in routePriceEntries) {
      if (entry['isDirect'] == true) {
        final controller = entry['controller'] as TextEditingController?;
        pricePerSeatTextEditingController.text =
            _primarySeatPriceFieldText(controller?.text ?? "");
        break;
      }
    }

    routePriceEntries.refresh();
    scheduleRouteDistanceEstimates();
    update();
  }

  List<dynamic> extractAdditionalRideDetails(dynamic ride) {
    if (ride is! Map) {
      return <dynamic>[];
    }

    final dynamic moreRideDetail =
        ride['more_ride_detail'] ?? ride['moreRideDetail'];
    if (moreRideDetail is List) {
      return List<dynamic>.from(moreRideDetail);
    }

    final dynamic intermediateStops = ride['intermediate_stops'];
    if (intermediateStops is List) {
      return intermediateStops.map((stop) {
        if (stop is! Map) {
          return stop;
        }

        return <String, dynamic>{
          'id': stop['id'] ?? 0,
          'departure': stop['label'],
          'destination': stop['label'],
          'city_id': stop['city_id'] ?? 0,
          'pickup': stop['pickup_dropoff_location'] ??
              stop['pickup_location'] ??
              stop['dropoff_location'] ??
              "",
          'dropoff': stop['pickup_dropoff_location'] ??
              stop['dropoff_location'] ??
              stop['pickup_location'] ??
              "",
          'date': stop['depature_date'],
          'time': stop['depature_time'],
          'price': stop['price'] ?? stop['price_delta_minor'] ?? "",
        };
      }).toList();
    }

    return <dynamic>[];
  }

  Map<String, dynamic> extractPrimaryRideDetail(dynamic ride,
      {dynamic fallbackDetail}) {
    if (fallbackDetail is Map) {
      return Map<String, dynamic>.from(fallbackDetail);
    }

    if (ride is! Map) {
      return <String, dynamic>{};
    }

    final dynamic primaryDetail =
        ride['detail'] ?? ride['defaultRideDetail'] ?? ride['ride_detail'];

    if (primaryDetail is Map) {
      return Map<String, dynamic>.from(primaryDetail);
    }

    return <String, dynamic>{};
  }

  void _refreshStopFormPrerequisites() {
    stopFormPrerequisiteVersion.value++;
  }

  bool get canAddMoreStops {
    return fromTextEditingController.text.trim().isNotEmpty &&
        toTextEditingController.text.trim().isNotEmpty &&
        pickUpLocationTextEditingController.text.trim().isNotEmpty &&
        dropOffLocationTextEditingController.text.trim().isNotEmpty &&
        dateTextEditingController.text.trim().isNotEmpty &&
        timeTextEditingController.text.trim().isNotEmpty;
  }

  String normalizeStopCity(String value) {
    return value.trim().toLowerCase();
  }

  bool isDuplicateStopCity(int index, String cityLabel) {
    final normalizedCity = normalizeStopCity(cityLabel);
    if (normalizedCity.isEmpty) {
      return false;
    }

    if (normalizeStopCity(fromTextEditingController.text) == normalizedCity ||
        normalizeStopCity(toTextEditingController.text) == normalizedCity) {
      return true;
    }

    for (var i = 0; i < fromSpotControllers.length; i++) {
      if (i == index) {
        continue;
      }
      if (normalizeStopCity(fromSpotControllers[i].text) == normalizedCity) {
        return true;
      }
    }

    return false;
  }

  DateTime? parseDisplayDateTime(String dateText, String timeText) {
    final normalizedDate = dateText.trim();
    final normalizedTime = timeText.trim();
    if (normalizedDate.isEmpty || normalizedTime.isEmpty) {
      return null;
    }

    final datePatterns = ['MMMM dd, yyyy', 'MMMM d, yyyy'];
    final timePatterns = ['HH:mm', 'hh:mm', 'h:mm a', 'hh:mm a'];

    for (final datePattern in datePatterns) {
      for (final timePattern in timePatterns) {
        try {
          return DateFormat('$datePattern $timePattern')
              .parseStrict('$normalizedDate $normalizedTime');
        } catch (_) {}
      }
    }

    return null;
  }

  String get returnRideAfterOriginalMessage =>
      "Return ride date and time must be after the original departure";

  String? validateReturnRideDateTime({
    String? dateText,
    String? timeText,
  }) {
    if (minimumReturnRideDateTime == null) {
      return null;
    }

    final candidate = parseDisplayDateTime(
      dateText ?? dateTextEditingController.text,
      timeText ?? timeTextEditingController.text,
    );

    if (candidate == null) {
      return null;
    }

    if (!candidate.isAfter(minimumReturnRideDateTime!)) {
      return returnRideAfterOriginalMessage;
    }

    return null;
  }

  String? validateStopDateTime(
    int index, {
    required String dateText,
    required String timeText,
  }) {
    final stopDateTime = parseDisplayDateTime(dateText, timeText);
    if (stopDateTime == null) {
      return null;
    }

    final rideDateTime = parseDisplayDateTime(
      dateTextEditingController.text,
      timeTextEditingController.text,
    );
    if (rideDateTime != null && !stopDateTime.isAfter(rideDateTime)) {
      return "Stop date and time must be after the ride date and time";
    }

    if (index > 0) {
      final previousStopDateTime = parseDisplayDateTime(
        dateSpotControllers[index - 1].text,
        timeSpotControllers[index - 1].text,
      );
      if (previousStopDateTime != null &&
          !stopDateTime.isAfter(previousStopDateTime)) {
        return "Stop date and time must be after the previous stop";
      }
    }

    if (index < dateSpotControllers.length - 1) {
      final nextStopDateTime = parseDisplayDateTime(
        dateSpotControllers[index + 1].text,
        timeSpotControllers[index + 1].text,
      );
      if (nextStopDateTime != null && !stopDateTime.isBefore(nextStopDateTime)) {
        return "Stop date and time must be before the next stop";
      }
    }

    return null;
  }

  void _appendEmptySpot() {
    fromSpotControllers.add(TextEditingController());
    toSpotControllers.add(TextEditingController());
    priceSpotControllers
        .add(TextEditingController(text: pricePerSeatTextEditingController.text));
    pickupSpotControllers.add(TextEditingController());
    dropoffSpotControllers.add(TextEditingController());
    dateSpotControllers.add(TextEditingController());
    timeSpotControllers.add(TextEditingController());
    stopCityIds.add(0);
    rideDetailIds.add("0");
    spotsCount.value = fromSpotControllers.length;
  }

  void _setSpotValues(
    int index, {
    required String stop,
    required String pickupOff,
    required String date,
    required String time,
    int cityId = 0,
  }) {
    fromSpotControllers[index].text = stop;
    toSpotControllers[index].text = stop;
    priceSpotControllers[index].text = pricePerSeatTextEditingController.text;
    pickupSpotControllers[index].text = pickupOff;
    dropoffSpotControllers[index].text = pickupOff;
    dateSpotControllers[index].text = date;
    timeSpotControllers[index].text = time;
    stopCityIds[index] = cityId;
    rebuildRoutePriceEntries();
  }

  Future<void> openStopForm(BuildContext context, {int? index}) async {
    if (!canAddMoreStops) {
      serviceController.showDialogue(
          "Please complete From, To, Pick-up and Drop-off first",
          type: "error");
      return;
    }

    final editIndex = index;
    final createdDraft = editIndex == null;
    if (createdDraft) {
      _appendEmptySpot();
    }
    final workingIndex = editIndex ?? (fromSpotControllers.length - 1);
    final result = await Get.to<Map<String, dynamic>>(
      () => StopFormPage(
        stopIndex: workingIndex,
        initialStop: fromSpotControllers[workingIndex].text,
        initialPickupOff: pickupSpotControllers[workingIndex].text,
        initialDate: dateSpotControllers[workingIndex].text,
        initialTime: timeSpotControllers[workingIndex].text,
        labelTextDetail: Map.of(labelTextDetail),
        popupTextDetail: Map.of(popupTextDetail),
        isEditing: editIndex != null,
      ),
    );

    if (result == null) {
      if (createdDraft) {
        removeNewSpot(workingIndex);
      }
      return;
    }

    _setSpotValues(
      workingIndex,
      stop: (result['stop'] ?? '').toString(),
      pickupOff: (result['pickup_off'] ?? '').toString(),
      date: (result['date'] ?? '').toString(),
      time: (result['time'] ?? '').toString(),
      cityId: int.tryParse((result['city_id'] ?? 0).toString()) ?? 0,
    );
    spotsCount.refresh();
    showErrorSpot.value = false;
    update();
  }

  void setOriginLocation({required String label, required int cityId}) {
    fromTextEditingController.text = label;
    fromCityId.value = cityId;
    errors.removeWhere((error) => error['title'] == "from");
    rebuildRoutePriceEntries();
  }

  void setDestinationLocation({required String label, required int cityId}) {
    toTextEditingController.text = label;
    toCityId.value = cityId;
    errors.removeWhere((error) => error['title'] == "to");
    rebuildRoutePriceEntries();
  }

  void setStopLocation({
    required int index,
    required String label,
    required int cityId,
  }) {
    if (index < 0 || index >= fromSpotControllers.length) {
      return;
    }

    fromSpotControllers[index].text = label;
    toSpotControllers[index].text = label;
    stopCityIds[index] = cityId;
    rebuildRoutePriceEntries();
    spotsCount.refresh();
    update();
  }

  void validateField(String fieldName, String fieldValue,
      {String type = 'string', bool isRequired = true, int wordsLimit = 50}) {
    errors.removeWhere((element) => element['title'] == fieldName);
    List<String> errorList = [];
    String stringOrFallback(dynamic value, String fallback) {
      final normalized = value?.toString().trim() ?? "";
      return normalized.isNotEmpty ? normalized : fallback;
    }

    if (isRequired && fieldValue.isEmpty) {
      var message = stringOrFallback(
        validationMessageDetail['required'],
        "This field is required.",
      );
      // if (fieldName == "from") {
      //   // message = stringOrFallback(labelTextDetail['origin'], "Origin is required.");
      // } else if (fieldName == "to") {
      //   // message = stringOrFallback(labelTextDetail['destination'], "Destination is required.");
      // } else if (fieldName == "pickup") {
      //   message = stringOrFallback(labelTextDetail['pickup'], "Pickup is required.");
      // } else if (fieldName == "dropoff") {
      //   message = stringOrFallback(labelTextDetail['dropoff'], "Dropoff is required.");
      // } else if (fieldName == "details") {
      //   message = stringOrFallback(labelTextDetail['details'], "Details are required.");
      // } else if (fieldName == "make") {
      //   message = message.replaceAll(
      //       ":Attribute", labelTextDetail['make_error'] ?? "Make");
      // } else if (fieldName == "model") {
      //   message = message.replaceAll(
      //       ":Attribute", labelTextDetail['model_error'] ?? "Model");
      // } else if (fieldName == "license_no") {
      //   message = message.replaceAll(
      //       ":Attribute", labelTextDetail['license_error'] ?? "License no");
      // } else if (fieldName == "color") {
      //   message = message.replaceAll(
      //       ":Attribute", labelTextDetail['color_error'] ?? "Color");
      // } else if (fieldName == "year") {
      //   message = message.replaceAll(
      //       ":Attribute", labelTextDetail['year_error'] ?? "Year");
      // } else if (fieldName == "price") {
      //   message = labelTextDetail['price'];
      // }
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
          var message = stringOrFallback(
            validationMessageDetail['numeric'],
            "The :attribute field must be a number.",
          );
          if (fieldName == "price") {
            message = message.replaceAll(
                ":attribute", labelTextDetail['price_error'] ?? "price");
          } else if (fieldName == "year") {
            message = message.replaceAll(
                ":attribute", labelTextDetail['year_error'] ?? "year");
          }
          errorList.add(message);
        }
        break;
      case 'date':
        if (fieldValue.isNotEmpty && DateTime.tryParse(fieldValue) == null) {
          var message = stringOrFallback(
            validationMessageDetail['date'],
            "The :attribute is not a valid date.",
          );
          message = message.replaceAll(
              ":attribute", labelTextDetail['date_error'] ?? "date");
          errorList.add(message);
        }
        break;
      case 'time':
        if (fieldValue.isNotEmpty &&
            !RegExp(r'^\d{2}:\d{2}$').hasMatch(fieldValue)) {
          var message = stringOrFallback(
            validationMessageDetail['date_format'],
            "The :attribute does not match the format :format.",
          );
          message = message.replaceAll(
              ":attribute", labelTextDetail['time_error'] ?? "time");
          message = message.replaceAll(":format", 'HH:MM');
          errorList.add(message);
        }
        break;
      case 'max_words':
        if (fieldValue.isNotEmpty &&
            fieldValue.split(' ').length > wordsLimit) {
          var message = stringOrFallback(
            validationMessageDetail['max_words'],
            "The :attribute may not be greater than :max words.",
          );
          message = message.replaceAll(":attribute", fieldName);
          message = message.replaceAll(":max", wordsLimit.toString());
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

  getLabelTextDetail() async {
    try {
      isLoading(true);
      await PostRideProvider()
          .getLabelTextDetail(serviceController.langId)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null && resp['data']['postRidePage'] != null) {
            logger.info(
                "Post Ride Page: ${resp['data']['postRidePage']['add_spot_button_label'].toString()}");
            labelTextDetail.addAll(resp['data']['postRidePage']);
            vehicleTypeLabelList.add(
                labelTextDetail['vehicle_type_convertible_text'] ??
                    "Convertable");
            vehicleTypeList
                .add(labelTextDetail['vehicle_type_convertible_value']);
            vehicleTypeLabelList
                .add(labelTextDetail['vehicle_type_coupe_text'] ?? "Coupe");
            vehicleTypeList.add(labelTextDetail['vehicle_type_coupe_value']);
            vehicleTypeLabelList.add(
                labelTextDetail['vehicle_type_hatchback_text'] ?? "Hatchback");
            vehicleTypeList
                .add(labelTextDetail['vehicle_type_hatchback_value']);
            vehicleTypeLabelList
                .add(labelTextDetail['vehicle_type_minivan_text'] ?? "Minivan");
            vehicleTypeList.add(labelTextDetail['vehicle_type_minivan_value']);
            vehicleTypeLabelList
                .add(labelTextDetail['vehicle_type_sedan_text'] ?? "Sedan");
            vehicleTypeList.add(labelTextDetail['vehicle_type_sedan_value']);
            vehicleTypeLabelList.add(
                labelTextDetail['vehicle_type_station_wagon_text'] ??
                    "Station wagon");
            vehicleTypeList
                .add(labelTextDetail['vehicle_type_station_wagon_value']);
            vehicleTypeLabelList
                .add(labelTextDetail['vehicle_type_suv_text'] ?? "SUV");
            vehicleTypeList.add(labelTextDetail['vehicle_type_suv_value']);
            vehicleTypeLabelList
                .add(labelTextDetail['vehicle_type_truck_text'] ?? "Truck");
            vehicleTypeList.add(labelTextDetail['vehicle_type_truck_value']);
            vehicleTypeLabelList
                .add(labelTextDetail['vehicle_type_van_text'] ?? "Van");
            vehicleTypeList.add(labelTextDetail['vehicle_type_van_value']);
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
        serviceController.showDialogue(err.toString(), type: "error");
      });
    } catch (exception) {
      isLoading(false);
      serviceController.showDialogue(exception.toString(), type: "error");
    }
  }

  getPostRide() async {
    try {
      await PostRideProvider().getPostRide(serviceController.token).then(
          (resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null && resp['data']['vehicles'] != null) {
            vehicleList.addAll(resp['data']['vehicles']);
            var res = vehicleList.firstWhereOrNull(
                (element) => element['primary_vehicle'].toString() == '1');
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
          if (resp['data'] != null && resp['data']['overallRating'] != null) {
            overallRating.value =
                double.parse(resp['data']['overallRating'].toString());
          }
        }
      }, onError: (err) {
        serviceController.showDialogue(err.toString(), type: "error");
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString(), type: "error");
    }
  }

  getPreferenceOptions() async {
    try {
      await PostRideProvider()
          .getPreferenceOptions(
              serviceController.token, serviceController.langId.value)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null &&
              resp['data']['preferencesOptions'] != null) {
            smokingList
                .add(resp['data']['preferencesOptions']['smoking_option1']);
            smokingList
                .add(resp['data']['preferencesOptions']['smoking_option2']);

            smoking.value = "21";

            smokingLabelList.add(
                resp['data']['preferencesOptions']['smoking_option1_label']);
            smokingLabelList.add(
                resp['data']['preferencesOptions']['smoking_option2_label']);

            petList.add(resp['data']['preferencesOptions']['animals_option1']);
            petList.add(resp['data']['preferencesOptions']['animals_option2']);
            petList.add(resp['data']['preferencesOptions']['animals_option3']);

            pet.value = "23";

            petLabelList.add(
                resp['data']['preferencesOptions']['animals_option1_label']);
            petLabelList.add(
                resp['data']['preferencesOptions']['animals_option2_label']);
            petLabelList.add(
                resp['data']['preferencesOptions']['animals_option3_label']);
          }
        }
      }, onError: (err) {
        serviceController.showDialogue(err.toString(), type: "error");
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString(), type: "error");
    }
  }

  getRidePreferenceOptions() async {
    try {
      await PostRideProvider()
          .getRidePreferenceOptions(
              serviceController.token, serviceController.langId.value)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null && resp['data']['featuresOptions'] != null) {
            rideFeatureList.addAll(resp['data']['featuresOptions']);
          }
          if (resp['data'] != null && resp['data']['featuresLabels'] != null) {
            rideFeatureLabelList.addAll(resp['data']['featuresLabels']);
          }
        }
      }, onError: (err) {
        serviceController.showDialogue(err.toString(), type: "error");
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString(), type: "error");
    }
  }

  getPinkRideInfo() async {
    try {
      await PostRideProvider()
          .getPinkRideInfo(
              serviceController.token, serviceController.langId.value)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          _applyPinkRideAvailability(resp['data'], resp['data']['user']);
        }
      }, onError: (err) {
        serviceController.showDialogue(err.toString(), type: "error");
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString(), type: "error");
    }
  }

  getExtraCareRideInfo() async {
    try {
      await PostRideProvider()
          .getExtraCareRideInfo(
              serviceController.token, serviceController.langId.value)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          _applyExtraCareRideAvailability(resp['data'], resp['data']['user']);
        }
      }, onError: (err) {
        serviceController.showDialogue(err.toString(), type: "error");
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString(), type: "error");
    }
  }

  getBookingOption() async {
    try {
      await PostRideProvider()
          .getBookingOption(
              serviceController.token, serviceController.langId.value)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null && resp['data']['bookingOptions'] != null) {
            bookingOptionList.addAll(resp['data']['bookingOptions']);
          }
          if (resp['data'] != null && resp['data']['bookingTooltips'] != null) {
            bookingOptionToolTipList.addAll(resp['data']['bookingTooltips']);
          }

          if (resp['data'] != null && resp['data']['bookingLabels'] != null) {
            bookingOptionLabelList.addAll(resp['data']['bookingLabels']);
          }
        }
      }, onError: (err) {
        serviceController.showDialogue(err.toString(), type: "error");
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString(), type: "error");
    }
  }

  getCancellationOption() async {
    try {
      await PostRideProvider()
          .getCancellationOption(
              serviceController.token, serviceController.langId.value)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null &&
              resp['data']['cancellationOptions'] != null) {
            cancellationOptionList.addAll(resp['data']['cancellationOptions']);
            logger.info(cancellationOptionList.toString());
          }
          if (resp['data'] != null &&
              resp['data']['cancellationTooltips'] != null) {
            cancellationOptionToolTipList
                .addAll(resp['data']['cancellationTooltips']);
          }

          if (resp['data'] != null &&
              resp['data']['cancellationLabels'] != null) {
            cancellationOptionLabelList
                .addAll(resp['data']['cancellationLabels']);
          }
        }
      }, onError: (err) {
        serviceController.showDialogue(err.toString(), type: "error");
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString(), type: "error");
    }
  }

  getLuggage() async {
    try {
      await PostRideProvider()
          .getLuggage(serviceController.token, serviceController.langId.value)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null && resp['data']['luggageOptions'] != null) {
            luggageList.addAll(resp['data']['luggageOptions']);
          }

          if (resp['data'] != null && resp['data']['luggageTooltips'] != null) {
            luggageListToolTip.addAll(resp['data']['luggageTooltips']);
            if (luggageListToolTip.isNotEmpty &&
                luggageListToolTip.length >= 4) {
              luggageListToolTip[4] =
                  "Extra space for oversized luggage or multiple bags. Must be agreed upon with the driver BEFORE booking";
            }
          }

          if (resp['data'] != null && resp['data']['luggageLabels'] != null) {
            luggageListLabel.addAll(resp['data']['luggageLabels']);
            if (luggageListLabel.isNotEmpty && luggageListLabel.length >= 4) {
              luggageListLabel[4] = "XL and Multiple";
            }
          }
        }
      }, onError: (err) {
        serviceController.showDialogue(err.toString(), type: "error");
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString(), type: "error");
    }
  }

  getPaymentOptions() async {
    try {
      await PostRideProvider()
          .getPaymentOptions(
              serviceController.token, serviceController.langId.value)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null && resp['data']['paymentOptions'] != null) {
            paymentOptionList.addAll(resp['data']['paymentOptions']);
          }
          if (resp['data'] != null && resp['data']['paymentTooltips'] != null) {
            paymentOptionToolTipList.addAll(resp['data']['paymentTooltips']);
          }
          if (resp['data'] != null && resp['data']['paymentLabels'] != null) {
            paymentOptionLabelList.addAll(resp['data']['paymentLabels']);
          }
        }
      }, onError: (err) {
        serviceController.showDialogue(err.toString(), type: "error");
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString(), type: "error");
    }
  }

  getPostRideInitData() async {
    try {
      minimumReturnRideDateTime = null;
      int? rideIdParam = rideId.value != 0 ? rideId.value : null;
      String? rideTypeParam = rideId.value != 0 ? rideType.value : null;

      await PostRideProvider()
          .getPostRideInitData(
              serviceController.token, serviceController.langId.value,
              rideId: rideIdParam, rideType: rideTypeParam)
          .then((resp) async {
        if (resp['status'] != null &&
            (resp['status'] == "Success" ||
                resp['status'] == "Partial Success")) {
          final data = resp['data'];

          if (data['user'] != null) {
            await _refreshPostRideUserInfo(data['user']);
          }

          // 1. Parse labels
          if (data['labels'] != null) {
            if (data['labels']['postRidePage'] != null) {
              labelTextDetail.addAll(data['labels']['postRidePage']);

              // Setup vehicle type lists
              vehicleTypeLabelList.add(
                  labelTextDetail['vehicle_type_convertible_text'] ??
                      "Convertable");
              vehicleTypeList
                  .add(labelTextDetail['vehicle_type_convertible_value']);
              vehicleTypeLabelList
                  .add(labelTextDetail['vehicle_type_coupe_text'] ?? "Coupe");
              vehicleTypeList.add(labelTextDetail['vehicle_type_coupe_value']);
              vehicleTypeLabelList.add(
                  labelTextDetail['vehicle_type_hatchback_text'] ??
                      "Hatchback");
              vehicleTypeList
                  .add(labelTextDetail['vehicle_type_hatchback_value']);
              vehicleTypeLabelList.add(
                  labelTextDetail['vehicle_type_minivan_text'] ?? "Minivan");
              vehicleTypeList
                  .add(labelTextDetail['vehicle_type_minivan_value']);
              vehicleTypeLabelList
                  .add(labelTextDetail['vehicle_type_sedan_text'] ?? "Sedan");
              vehicleTypeList.add(labelTextDetail['vehicle_type_sedan_value']);
              vehicleTypeLabelList.add(
                  labelTextDetail['vehicle_type_station_wagon_text'] ??
                      "Station wagon");
              vehicleTypeList
                  .add(labelTextDetail['vehicle_type_station_wagon_value']);
              vehicleTypeLabelList
                  .add(labelTextDetail['vehicle_type_suv_text'] ?? "SUV");
              vehicleTypeList.add(labelTextDetail['vehicle_type_suv_value']);
              vehicleTypeLabelList
                  .add(labelTextDetail['vehicle_type_truck_text'] ?? "Truck");
              vehicleTypeList.add(labelTextDetail['vehicle_type_truck_value']);
              vehicleTypeLabelList
                  .add(labelTextDetail['vehicle_type_van_text'] ?? "Van");
              vehicleTypeList.add(labelTextDetail['vehicle_type_van_value']);
            }
            if (data['labels']['messages'] != null) {
              popupTextDetail.addAll(data['labels']['messages']);
            }
            if (data['labels']['validationMessages'] != null) {
              validationMessageDetail
                  .addAll(data['labels']['validationMessages']);
            }
          }

          // 2. Parse user vehicles
          if (data['userVehicles'] != null) {
            if (data['userVehicles']['vehicles'] != null) {
              vehicleList.addAll(data['userVehicles']['vehicles']);
              var res = vehicleList.firstWhereOrNull(
                  (element) => element['primary_vehicle'].toString() == '1');
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
              overallRating.value = double.parse(
                  data['userVehicles']['overallRating'].toString());
            }
          }

          // 3. Parse preferences
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

          // 4. Parse ride features
          if (data['rideFeatures'] != null) {
            if (data['rideFeatures']['featuresOptions'] != null) {
              rideFeatureList.addAll(data['rideFeatures']['featuresOptions']);
            }
            if (data['rideFeatures']['featuresLabels'] != null) {
              rideFeatureLabelList
                  .addAll(data['rideFeatures']['featuresLabels']);
            }
          }

          // 5. Parse pink ride info
          if (data['pinkRide'] != null) {
            _applyPinkRideAvailability(data['pinkRide'], data['user']);
          }

          // 6. Parse extra care ride info
          if (data['extraCareRide'] != null) {
            _applyExtraCareRideAvailability(
                data['extraCareRide'], data['user']);
          }

          // 7. Parse booking options
          if (data['bookingOptions'] != null) {
            if (data['bookingOptions']['bookingOptions'] != null) {
              bookingOptionList
                  .addAll(data['bookingOptions']['bookingOptions']);
            }
            if (data['bookingOptions']['bookingTooltips'] != null) {
              bookingOptionToolTipList
                  .addAll(data['bookingOptions']['bookingTooltips']);
            }
            if (data['bookingOptions']['bookingLabels'] != null) {
              bookingOptionLabelList
                  .addAll(data['bookingOptions']['bookingLabels']);
            }
          }

          // 8. Parse cancellation options
          if (data['cancellationOptions'] != null) {
            if (data['cancellationOptions']['cancellationOptions'] != null) {
              cancellationOptionList
                  .addAll(data['cancellationOptions']['cancellationOptions']);
            }
            if (data['cancellationOptions']['cancellationTooltips'] != null) {
              cancellationOptionToolTipList
                  .addAll(data['cancellationOptions']['cancellationTooltips']);
            }
            if (data['cancellationOptions']['cancellationLabels'] != null) {
              cancellationOptionLabelList
                  .addAll(data['cancellationOptions']['cancellationLabels']);
            }
          }

          // 9. Parse luggage options
          if (data['luggage'] != null) {
            if (data['luggage']['luggageOptions'] != null) {
              luggageList.addAll(data['luggage']['luggageOptions']);
            }
            if (data['luggage']['luggageTooltips'] != null) {
              luggageListToolTip.addAll(data['luggage']['luggageTooltips']);
              if (luggageListToolTip.isNotEmpty &&
                  luggageListToolTip.length >= 4) {
                luggageListToolTip[4] =
                    "Extra space for oversized luggage or multiple bags. Must be agreed upon with the driver BEFORE booking";
              }
            }
            if (data['luggage']['luggageLabels'] != null) {
              luggageListLabel.addAll(data['luggage']['luggageLabels']);
              if (luggageListLabel.isNotEmpty && luggageListLabel.length >= 4) {
                luggageListLabel[4] = "XL and Multiple";
              }
            }
          }

          // 10. Parse payment options
          if (data['paymentOptions'] != null) {
            if (data['paymentOptions']['paymentOptions'] != null) {
              paymentOptionList
                  .addAll(data['paymentOptions']['paymentOptions']);
            }
            if (data['paymentOptions']['paymentTooltips'] != null) {
              paymentOptionToolTipList
                  .addAll(data['paymentOptions']['paymentTooltips']);
            }
            if (data['paymentOptions']['paymentLabels'] != null) {
              paymentOptionLabelList
                  .addAll(data['paymentOptions']['paymentLabels']);
            }
          }

          // 11. Parse ride data (if editing/duplicating)
          if (data['rideData'] != null && data['rideData']['ride'] != null) {
            var ride = data['rideData']['ride'];
            final rideDetail = extractPrimaryRideDetail(
              ride,
              fallbackDetail: data['rideData']['detail'],
            );

            fromTextEditingController.text =
                rideDetail['departure']?.toString() ?? "";
            toTextEditingController.text =
                rideDetail['destination']?.toString() ?? "";
            fromCityId.value =
                int.tryParse(rideDetail['origin_city_id']?.toString() ?? "") ??
                    0;
            toCityId.value =
                int.tryParse(
                        rideDetail['destination_city_id']?.toString() ?? "") ??
                    0;
            pickUpLocationTextEditingController.text = ride['pickup'] ?? "";
            dropOffLocationTextEditingController.text = ride['dropoff'] ?? "";
            timeTextEditingController.text =
                rideTypeParam == "new" ? "" : formatRideTimeValue(ride['time']);
            dateTextEditingController.text = rideTypeParam == "new"
                ? ""
                : formatRideDateValue(ride['date']);
            recurring.value = ride['recurring'] == "1" ? true : false;
            recurringType.value = "";
            recurringTripsTextEditingController.text = "";
            dropOffDescriptionTextEditingController.text =
                ride['details'].toString();

            seatAvailable.value = int.parse(ride['seats'].toString());
            seatMiddle.value = int.parse(ride['middle_seats'].toString());
            seatBack.value = int.parse(ride['back_seats'].toString());
            bookingType.value = ride['booking_type'].toString();

            // Handle multiple spots
            final moreRideDetail = extractAdditionalRideDetails(ride);
            if (moreRideDetail.isNotEmpty) {
              logger.info(moreRideDetail.toString());
              for (var index = 0; index < moreRideDetail.length; index++) {
                final stopDetail = moreRideDetail[index];
                if (stopDetail is! Map) {
                  continue;
                }

                if (stopDetail['departure'] == null &&
                    stopDetail['destination'] == null) {
                  continue;
                }

                _appendEmptySpot();
                final stopValue = stopDetail['departure']?.toString() ??
                    stopDetail['destination']?.toString() ??
                    "";
                final pickupOffValue =
                    stopDetail['pickup']?.toString() ??
                        stopDetail['dropoff']?.toString() ??
                        "";
                _setSpotValues(
                  index,
                  stop: stopValue,
                  pickupOff: pickupOffValue,
                  date: formatSpotDateValue(stopDetail['date']),
                  time: formatSpotTimeValue(stopDetail['time']),
                  cityId: int.tryParse(stopDetail['city_id'].toString()) ?? 0,
                );
                priceSpotControllers[index].text =
                    formatMinorPriceForDisplay(stopDetail['price']);
                rideDetailIds[index] = "${stopDetail['id'] ?? 0}";
              }
            }

            // Handle vehicle data
            if (ride['add_vehicle'] == "1") {
              addNewVehicle.value = true;
              makeTextEditingController.text = ride['make'].toString();
              modelTextEditingController.text = ride['model'].toString();
              licenseNumberTextEditingController.text =
                  ride['license_no'].toString();
              colorTextEditingController.text = ride['color'].toString();
              yearTextEditingController.text = ride['year'].toString();
              vehicleType.value = ride['vehicle_type'].toString();
              fuel.value = ride['car_type'].toString();
              carOldImagePath.value = ride['vehicle']['image'].toString();
            }
            _syncVehicleSelectionFromRide(ride);

            smoking.value = ride['smoke'].toString();
            pet.value = ride['animal_friendly'].toString();

            if (ride['features'] != null && ride['features'].isNotEmpty) {
              featureList.value = ride['features']
                  .toString()
                  .split(',')
                  .map((feature) => feature.toString().trim())
                  .where((feature) => feature.isNotEmpty)
                  .toList();
            }

            bookingOption.value = ride['booking_method'].toString();
            luggage.value = ride['luggage'].toString();
            paymentOption.value = ride['payment_method'].toString();
            acceptMoreLuggage.value = ride['accept_more_luggage'].toString();
            openCustomized.value = ride['open_customized'].toString();
            final detailPrice = rideDetail['price'];
            final ridePrice = ride['price'];
            pricePerSeatTextEditingController.text =
                (detailPrice != null && detailPrice.toString().isNotEmpty)
                    ? _formatMinorPriceForSeatField(detailPrice)
                    : _formatMinorPriceForSeatField(ridePrice);
            anythingTextEditingController.text = ride['notes'] ?? "";

            final routePriceSeed = <String, String>{};
            if (ride['route_price_segments'] is List) {
              for (final segment in ride['route_price_segments']) {
                if (segment is! Map) {
                  continue;
                }

                final fromLabel = segment['from_label']?.toString() ?? "";
                final toLabel = segment['to_label']?.toString() ?? "";
                if (fromLabel.isEmpty || toLabel.isEmpty) {
                  continue;
                }

                routePriceSeed[buildRoutePriceKey(fromLabel, toLabel)] =
                    formatMinorPriceForDisplay(segment['price_minor']);
              }
            }

            if (fromSpotControllers.isNotEmpty) {
              rebuildRoutePriceEntries(
                  seedValues: routePriceSeed.isNotEmpty ? routePriceSeed : null);
            } else {
              scheduleRouteDistanceEstimates();
            }

            if (recurring.value) {
              recurringType.value = ride['recurring_type'].toString();
              recurringTripsTextEditingController.text =
                  ride['recurring_trips'].toString();
            }
          }
        }
      }, onError: (err) {
        serviceController.showDialogue(err.toString(), type: "error");
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString(), type: "error");
    }
  }

  getPostRideAgainData(id, type) async {
    if (type == "rePostRide") {
      scrollController.animateTo(
        scrollController.position.minScrollExtent,
        curve: Curves.easeOut,
        duration: const Duration(milliseconds: 500),
      );
      isOverlayLoading(true);
      minimumReturnRideDateTime = null;
    }
    try {
      await PostRideProvider()
          .getPostRideAgainData(
              id, serviceController.token, serviceController.langId.value)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null && resp['data']['ride'] != null) {
            final ride = resp['data']['ride'];
            final rideDetail = extractPrimaryRideDetail(ride);
            minimumReturnRideDateTime = type == "rePostRide"
                ? parseDisplayDateTime(
                    formatRideDateValue(ride['date']),
                    formatRideTimeValue(ride['time']),
                  )
                : null;

            if (type == "rePostRide") {
              final seededReturnDateTime =
                  minimumReturnRideDateTime?.add(const Duration(days: 1));
              fromTextEditingController.text =
                  rideDetail['destination']?.toString() ??
                      ride['destination']?.toString() ??
                      "";
              toTextEditingController.text =
                  rideDetail['departure']?.toString() ??
                      ride['departure']?.toString() ??
                      "";
              fromCityId.value = int.tryParse(
                      rideDetail['destination_city_id']?.toString() ?? "") ??
                  0;
              toCityId.value = int.tryParse(
                      rideDetail['origin_city_id']?.toString() ?? "") ??
                  0;
              pickUpLocationTextEditingController.text =
                  ride['dropoff'] ?? "";
              dropOffLocationTextEditingController.text =
                  ride['pickup'] ?? "";
              dateTextEditingController.text = seededReturnDateTime != null
                  ? DateFormat('MMMM d, yyyy').format(seededReturnDateTime)
                  : "";
              timeTextEditingController.text = seededReturnDateTime != null
                  ? DateFormat("HH:mm").format(seededReturnDateTime)
                  : "";
            } else {
              fromTextEditingController.text =
                  rideDetail['departure']?.toString() ?? "";
              toTextEditingController.text =
                  rideDetail['destination']?.toString() ?? "";
              fromCityId.value = int.tryParse(
                      rideDetail['origin_city_id']?.toString() ?? "") ??
                  0;
              toCityId.value = int.tryParse(
                      rideDetail['destination_city_id']?.toString() ?? "") ??
                  0;
              pickUpLocationTextEditingController.text =
                  ride['pickup'] ?? "";
              dropOffLocationTextEditingController.text =
                  ride['dropoff'] ?? "";
              dateTextEditingController.text = "";
              timeTextEditingController.text = "";
            }
            recurring.value = ride['recurring'] == "1" ? true : false;
            recurringType.value = "";
            recurringTripsTextEditingController.text = "";
            if (type == "rePostRide") {
              dropOffDescriptionTextEditingController.text = "";
            } else {
              dropOffDescriptionTextEditingController.text =
                  ride['details'].toString();
            }
            seatAvailable.value = int.parse(ride['seats'].toString());
            seatMiddle.value = int.parse(ride['middle_seats'].toString());
            seatBack.value = int.parse(ride['back_seats'].toString());
            bookingType.value = ride['booking_type'].toString();

            final additionalRideDetails = extractAdditionalRideDetails(ride);
            if (additionalRideDetails.isNotEmpty) {
              final orderedRideDetails = type == "rePostRide"
                  ? additionalRideDetails.reversed.toList()
                  : additionalRideDetails;
              logger.info(orderedRideDetails.toString());
              for (var index = 0;
                  index < orderedRideDetails.length;
                  index++) {
                if (orderedRideDetails[index]['destination'] != null) {
                  _appendEmptySpot();
                  final stopValue = type == "rePostRide"
                      ? (orderedRideDetails[index]['departure']?.toString() ??
                          orderedRideDetails[index]['destination']
                                  ?.toString() ??
                          "")
                      : (orderedRideDetails[index]['departure']
                                  ?.toString() ??
                              orderedRideDetails[index]['destination']
                                  ?.toString() ??
                              "");
                  final pickupOffValue = type == "rePostRide"
                      ? (orderedRideDetails[index]['dropoff']?.toString() ??
                          orderedRideDetails[index]['pickup']?.toString() ??
                          "")
                      : (orderedRideDetails[index]['pickup']?.toString() ??
                          orderedRideDetails[index]['dropoff']?.toString() ??
                          "");
                  _setSpotValues(
                    index,
                    stop: stopValue,
                    pickupOff: pickupOffValue,
                    date: type == "rePostRide"
                        ? ""
                        : formatSpotDateValue(orderedRideDetails[index]['date']),
                    time: type == "rePostRide"
                        ? ""
                        : formatSpotTimeValue(orderedRideDetails[index]['time']),
                    cityId: int.tryParse(
                            orderedRideDetails[index]['city_id']?.toString() ??
                                "") ??
                        0,
                  );
                  priceSpotControllers[index].text = formatMinorPriceForDisplay(
                      orderedRideDetails[index]['price']);
                  rideDetailIds[index] = type == "rePostRide"
                      ? "0"
                      : "${orderedRideDetails[index]['id']}";
                }
              }
            }

            if (type == "rePostRide") {
              addNewVehicle.value = false;
              makeTextEditingController.text = "";
              modelTextEditingController.text = "";
              licenseNumberTextEditingController.text = "";
              colorTextEditingController.text = "";
              yearTextEditingController.text = "";
              vehicleType.value = "";
              fuel.value = "";
              carOldImagePath.value = "";

              vehicleList.clear();
              if (vehicleList.isEmpty) {
                await PostRideProvider()
                    .getPostRide(serviceController.token)
                    .then((resp) async {
                  if (resp['status'] != null && resp['status'] == "Success") {
                    if (resp['data'] != null &&
                        resp['data']['vehicles'] != null) {
                      vehicleList.addAll(resp['data']['vehicles']);
                      var res = vehicleList.firstWhereOrNull(
                          (element) =>
                              element['primary_vehicle'].toString() == '1');
                      if (res != null) {
                        vehicleId.value = res['id'].toString();
                        alreadyAdded.value = true;
                      } else if (vehicleList.isNotEmpty &&
                          vehicleList.length == 1) {
                        vehicleId.value = vehicleList[0]['id'].toString();
                        alreadyAdded.value = true;
                      }
                    }
                  }
                }, onError: (err) {});
              }
            } else {
              if (resp['data']['ride']['add_vehicle'] == "1") {
                addNewVehicle.value =
                    resp['data']['ride']['add_vehicle'] == "1" ? true : false;
                makeTextEditingController.text =
                    resp['data']['ride']['make'].toString();
                modelTextEditingController.text =
                    resp['data']['ride']['model'].toString();
                licenseNumberTextEditingController.text =
                    resp['data']['ride']['license_no'].toString();
                colorTextEditingController.text =
                    resp['data']['ride']['color'].toString();
                yearTextEditingController.text =
                    resp['data']['ride']['year'].toString();
                vehicleType.value =
                    resp['data']['ride']['vehicle_type'].toString();
                fuel.value = resp['data']['ride']['car_type'].toString();
                carOldImagePath.value =
                    resp['data']['ride']['vehicle']['image'].toString();
              }

              _syncVehicleSelectionFromRide(resp['data']['ride']);
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
            logger.info("test2$featureData");
            for (var element in featureData) {
              featureList.add(element['id'].toString());
            }

            bookingOption.value =
                ride['booking_method'].toString();
            luggage.value = ride['luggage'].toString();
            paymentOption.value = ride['payment_method'].toString();
            disclaimer.value = false;
            acceptMoreLuggage.value = ride['accept_more_luggage'].toString();
            openCustomized.value = ride['open_customized'].toString();
            pricePerSeatTextEditingController.text =
                _formatMinorPriceForSeatField(
                    rideDetail['price'] ?? ride['price']);
            anythingTextEditingController.text = ride['notes'].toString();
          }
        }
        isOverlayLoading(false);
      }, onError: (err) {
        isOverlayLoading(false);
        serviceController.showDialogue(err.toString(), type: "error");
      });
    } catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString(), type: "error");
    }
  }

  void getImage(ImageSource imageSource) async {
    final croppedFile = await serviceController.imageCropper(imageSource);
    if (croppedFile != null) {
      carImagePath.value = croppedFile.path;
      carImageName.value = croppedFile.path.split('/').last;
      carImagePathOriginal.value = serviceController.originalImagePath.value;
      serviceController.originalImagePath.value = "";
      carImageNameOriginal.value = serviceController.originalImageName.value;
      serviceController.originalImageName.value = "";
      Get.back();
    }
  }

  scrollError(context, position, screenHeight) {
    position = position * 100.0;

    // Get the app bar height and safe area padding
    final MediaQueryData mediaQuery = MediaQuery.of(context);
    final double statusBarHeight = mediaQuery.padding.top;
    final double appBarHeight = 56.0; // Standard AppBar height
    final double safeAreaPadding = 15.0; // Padding from the container

    // Calculate offset to position field at the top of the visible page
    // Account for app bar, status bar, and padding
    double topOffset = appBarHeight + statusBarHeight + safeAreaPadding;

    if (mediaQuery.viewInsets.bottom > 0) {
      // Keyboard is visible, we still want field at top but account for keyboard
      // The offset remains the same, but we might need to adjust if field is too low
    }

    // Scroll to position the field at the top of the visible area
    // Ensure we don't scroll to negative values
    double scrollPosition = (position - topOffset).clamp(0.0, double.infinity);

    scrollController.animateTo(
      scrollPosition,
      duration: Duration(milliseconds: 300),
      curve: Curves.easeInOut,
    );
  }

  postRide(context, screenHeight) async {
    try {
      if (carImagePathOriginal.value != "") {
        final file = File(carImagePathOriginal.value);
        int sizeInBytes = file.lengthSync();
        double sizeInMb = sizeInBytes / (1024 * 1024);
        if (sizeInMb > 10) {
          var message = validationMessageDetail['max.file'];
          message = message.replaceAll(":max", '10');
          message = message.replaceAll(
              ":attribute", labelTextDetail['photo_error'] ?? 'car image');
          var err = {
            'title': "image",
            'eList': [message ?? 'Can not upload image size greater than 10MB']
          };
          errors.add(err);
          return;
        }
      }

      final canContinueWithPrice = await validateAllPricesBeforeSubmit();
      if (!canContinueWithPrice) {
        return;
      }

      final returnRideValidationMessage = validateReturnRideDateTime();
      if (returnRideValidationMessage != null) {
        serviceController.showDialogue(
          returnRideValidationMessage,
          type: "error",
        );
        return;
      }

      scrollField = false;
      isOverlayLoading(true);
      String features = "";
      if (featureList.isNotEmpty) {
        for (int i = 0; i < featureList.length; i++) {
          features += featureList[i];
          if (i < featureList.length - 1) {
            features += "=";
          }
        }
      }

      ensureExistingVehicleIdSelected();

      var fromSpots = [];
      var toSpots = [];
      var priceSpots = [];
      var pickupSpots = [];
      var dropoffSpots = [];
      var dateSpots = [];
      var timeSpots = [];
      var stopCityIdsArray = [];
      var rideDetailIdsArray = [];
      var routeFroms = [];
      var routeTos = [];
      var routePrices = [];
      if (fromSpotControllers.isNotEmpty) {
        for (var fromIndex = 0;
            fromIndex < fromSpotControllers.length;
            fromIndex++) {
          fromSpots.add(fromSpotControllers[fromIndex].text);
        }

        for (var toIndex = 0; toIndex < fromSpotControllers.length; toIndex++) {
          toSpots.add(toSpotControllers[toIndex].text.isNotEmpty
              ? toSpotControllers[toIndex].text
              : fromSpotControllers[toIndex].text);
        }

        for (var priceIndex = 0;
            priceIndex < fromSpotControllers.length;
            priceIndex++) {
          final previousLabel = priceIndex == 0
              ? fromTextEditingController.text
              : fromSpotControllers[priceIndex - 1].text;
          final currentLabel = fromSpotControllers[priceIndex].text;
          final adjacentRoutePrice =
              routePriceValueFor(previousLabel, currentLabel);

          priceSpots.add(adjacentRoutePrice.isNotEmpty
              ? adjacentRoutePrice
              : (priceSpotControllers[priceIndex].text.isNotEmpty
                  ? priceSpotControllers[priceIndex].text
                  : pricePerSeatTextEditingController.text));
        }

        for (var pickupIndex = 0;
            pickupIndex < fromSpotControllers.length;
            pickupIndex++) {
          pickupSpots.add(pickupSpotControllers[pickupIndex].text);
        }

        for (var dropoffIndex = 0;
            dropoffIndex < fromSpotControllers.length;
            dropoffIndex++) {
          dropoffSpots.add(dropoffSpotControllers[dropoffIndex].text.isNotEmpty
              ? dropoffSpotControllers[dropoffIndex].text
              : pickupSpotControllers[dropoffIndex].text);
        }

        for (var dateIndex = 0; dateIndex < fromSpotControllers.length; dateIndex++) {
          dateSpots.add(dateSpotControllers[dateIndex].text);
        }

        for (var timeIndex = 0; timeIndex < fromSpotControllers.length; timeIndex++) {
          timeSpots.add(timeSpotControllers[timeIndex].text);
        }

        for (var stopCityIndex = 0;
            stopCityIndex < stopCityIds.length;
            stopCityIndex++) {
          stopCityIdsArray.add(stopCityIds[stopCityIndex]);
        }

        for (var rideIndex = 0; rideIndex < rideDetailIds.length; rideIndex++) {
          rideDetailIdsArray.add(rideDetailIds[rideIndex]);
        }

        for (final routeEntry in routePriceEntries) {
          routeFroms.add(routeEntry['fromLabel']);
          routeTos.add(routeEntry['toLabel']);
          final routeController =
              routeEntry['controller'] as TextEditingController?;
          routePrices.add(routeController?.text ?? "");
        }
      }

      await PostRideProvider()
          .postRide(
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
              fromCityId.value,
              toCityId.value,
              fromSpots,
              toSpots,
              stopCityIdsArray,
              priceSpots,
              pickupSpots,
              dropoffSpots,
              dateSpots,
              timeSpots,
              rideDetailIdsArray,
              routeFroms,
              routeTos,
              routePrices,
              totalOrderedRouteDistanceMeters,
              totalOrderedRouteDurationSeconds)
          .then((resp) async {
        errorList.clear();
        errors.clear();
        final dynamic validationErrorsRaw = resp['errors'] ??
            (resp['data'] is Map ? (resp['data'] as Map)['errors'] : null);
        final Map<String, dynamic>? validationErrorsMap =
            validationErrorsRaw is Map
                ? Map<String, dynamic>.from(validationErrorsRaw as Map)
                : null;
        if (validationErrorsMap != null) {
          var positionValue = 1;

          if (validationErrorsMap['from'] != null) {
            var err = {'title': "from", 'eList': validationErrorsMap['from']};
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          } else {
            positionValue += 1;
          }
          if (validationErrorsMap['to'] != null) {
            var err = {'title': "to", 'eList': validationErrorsMap['to']};
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          } else {
            positionValue += 1;
          }
          if (validationErrorsMap['pickup'] != null) {
            var err = {'title': "pickup", 'eList': validationErrorsMap['pickup']};
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          } else {
            positionValue += 1;
          }
          if (validationErrorsMap['dropoff'] != null) {
            var err = {'title': "dropoff", 'eList': validationErrorsMap['dropoff']};
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          } else {
            positionValue += 1;
          }
          if (validationErrorsMap['date'] != null) {
            var err = {'title': "date", 'eList': validationErrorsMap['date']};
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          } else {
            positionValue += 1;
          }
          if (validationErrorsMap['time'] != null) {
            var err = {'title': "time", 'eList': validationErrorsMap['time']};
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          } else {
            positionValue += 1;
          }
          if (validationErrorsMap['recurring_type'] != null) {
            var err = {
              'title': "recurring_type",
              'eList': validationErrorsMap['recurring_type']
            };
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          } else {
            recurring.value == false ? positionValue : positionValue += 1;
          }
          if (validationErrorsMap['recurring_trips'] != null) {
            var err = {
              'title': "recurring_trips",
              'eList': validationErrorsMap['recurring_trips']
            };
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          } else {
            recurring.value == false ? positionValue : positionValue += 1;
          }
          if (validationErrorsMap['details'] != null) {
            var err = {'title': "details", 'eList': validationErrorsMap['details']};
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          } else {
            positionValue += 1;
          }
          if (validationErrorsMap['seats'] != null) {
            var err = {'title': "seats", 'eList': validationErrorsMap['seats']};
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          } else {
            positionValue += 1;
          }
          if (validationErrorsMap['middle_seats'] != null) {
            var err = {
              'title': "middle_seats",
              'eList': validationErrorsMap['middle_seats']
            };
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          } else {
            positionValue += 1;
          }
          if (validationErrorsMap['back_seats'] != null) {
            var err = {
              'title': "back_seats",
              'eList': validationErrorsMap['back_seats']
            };
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          } else {
            positionValue += 1;
          }

          if (skipNow.value == false &&
              addNewVehicle.value == false &&
              alreadyAdded.value == false) {
            addNewVehicle.value = true;
          }

          if (validationErrorsMap['vehicle_id'] != null) {
            var err = {
              'title': "vehicle_id",
              'eList': validationErrorsMap['vehicle_id']
            };
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          } else {
            positionValue += 1;
          }
          if (validationErrorsMap['make'] != null) {
            var err = {'title': "make", 'eList': validationErrorsMap['make']};
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          } else {
            addNewVehicle.value == true ? positionValue += 1 : positionValue;
          }
          if (validationErrorsMap['model'] != null) {
            var err = {'title': "model", 'eList': validationErrorsMap['model']};
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          } else {
            addNewVehicle.value == true ? positionValue += 1 : positionValue;
          }
          if (validationErrorsMap['license_no'] != null) {
            var err = {
              'title': "license_no",
              'eList': validationErrorsMap['license_no']
            };
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          } else {
            addNewVehicle.value == true ? positionValue += 1 : positionValue;
          }
          if (validationErrorsMap['color'] != null) {
            var err = {'title': "color", 'eList': validationErrorsMap['color']};
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          } else {
            addNewVehicle.value == true ? positionValue += 1 : positionValue;
          }
          if (validationErrorsMap['year'] != null) {
            var err = {'title': "year", 'eList': validationErrorsMap['year']};
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          } else {
            addNewVehicle.value == true ? positionValue += 1 : positionValue;
          }
          if (validationErrorsMap['vehicle_type'] != null) {
            var err = {
              'title': "vehicle_type",
              'eList': validationErrorsMap['vehicle_type']
            };
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          } else {
            addNewVehicle.value == true ? positionValue += 1 : positionValue;
          }
          if (validationErrorsMap['car_type'] != null) {
            var err = {
              'title': "car_type",
              'eList': validationErrorsMap['car_type']
            };
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          } else {
            addNewVehicle.value == true ? positionValue += 1 : positionValue;
          }
          if (validationErrorsMap['image'] != null) {
            var err = {'title': "image", 'eList': validationErrorsMap['image']};
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          } else {
            addNewVehicle.value == true ? positionValue += 1 : positionValue;
          }
          if (validationErrorsMap['smoke'] != null) {
            var err = {'title': "smoke", 'eList': validationErrorsMap['smoke']};
            if (scrollField == false) {
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
            errors.add(err);
          } else {
            positionValue += 1;
          }
          if (validationErrorsMap['animal_friendly'] != null) {
            var err = {
              'title': "animal_friendly",
              'eList': validationErrorsMap['animal_friendly']
            };
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue, screenHeight);
              scrollField = true;
            }
          } else {
            positionValue += 1;
          }

          if (validationErrorsMap['features'] != null) {
            var err = {
              'title': "features",
              'eList': validationErrorsMap['features']
            };
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue * 1.8, screenHeight);
              scrollField = true;
            }
          } else {
            positionValue += 1;
          }
          if (validationErrorsMap['booking_method'] != null) {
            var err = {
              'title': "booking_method",
              'eList': validationErrorsMap['booking_method']
            };
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue * 1.8, screenHeight);
              scrollField = true;
            }
          } else {
            positionValue += 1;
          }
          if (validationErrorsMap['luggage'] != null) {
            var err = {'title': "luggage", 'eList': validationErrorsMap['luggage']};
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue * 1.8, screenHeight);
              scrollField = true;
            }
          } else {
            positionValue += 1;
          }
          if (validationErrorsMap['price'] != null) {
            var err = {'title': "price", 'eList': validationErrorsMap['price']};
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue * 1.8, screenHeight);
              scrollField = true;
            }
          } else {
            positionValue += 1;
          }
          if (validationErrorsMap['payment_method'] != null) {
            var err = {
              'title': "payment_method",
              'eList': validationErrorsMap['payment_method']
            };
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue * 1.8, screenHeight);
              scrollField = true;
            }
          } else {
            positionValue += 1;
          }
          if (validationErrorsMap['booking_type'] != null) {
            var err = {
              'title': "booking_type",
              'eList': validationErrorsMap['booking_type']
            };
            errors.add(err);
            if (scrollField == false) {
              scrollError(context, positionValue * 1.8, screenHeight);
              scrollField = true;
            }
          } else {
            positionValue += 1;
          }
          if (validationErrorsMap['notes'] != null) {
            var err = {'title': "notes", 'eList': validationErrorsMap['notes']};
            errors.add(err);
          }
          if (validationErrorsMap['agree_terms'] != null) {
            var err = {
              'title': "agree_terms",
              'eList': validationErrorsMap['agree_terms']
            };
            errors.add(err);
          }
        } else if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message'].toString(),
              type: "error");
        } else if (resp['status'] != null && resp['status'] == "Success") {
          serviceController.navigationIndex.value = 0;
          await Get.defaultDialog(
            title: '',
            titlePadding: EdgeInsets.zero,
            middleText: resp['message'].toString(),
            barrierDismissible: false,
            middleTextStyle: const TextStyle(fontSize: 20, fontFamily: carlito),
            actions: [
              ElevatedButton(
                onPressed: () {
                  isOverlayLoading(false);
                  Get.offAllNamed("/navigation");
                },
                style: ElevatedButton.styleFrom(
                    backgroundColor: primaryColor,
                    shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(5))),
                child: txt16SizeWithOutContext(
                    title: serviceController.closeBtnLabel.value,
                    textColor: Colors.white,
                    fontFamily: regular),
              ),
              ElevatedButton(
                onPressed: () async {
                  isOverlayLoading(false);
                  Get.back();
                  await getPostRideAgainData(
                      resp['data']['ride']['id'], 'rePostRide');
                },
                style: ElevatedButton.styleFrom(
                    backgroundColor: btnPrimaryColor,
                    shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(5))),
                child: txt16SizeWithOutContext(
                    title: "Post a Return Ride",
                    // title:
                    //     "${labelTextDetail['repost_ride_btn_label'] ?? "Post a Return Ride"}",
                    textColor: Colors.white,
                    fontFamily: regular),
              ),
            ],
          );
        }
        isOverlayLoading(false);
      }, onError: (err) {
        isOverlayLoading(false);
        serviceController.showDialogue(err.toString(), type: "error");
      });
    } catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString(), type: "error");
    }
  }

  editPostRideData(id) async {
    try {
      minimumReturnRideDateTime = null;
      await PostRideProvider()
          .editPostRideData(
              id, serviceController.token, serviceController.langId.value)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null && resp['data']['ride'] != null) {
            final ride = resp['data']['ride'];
            final rideDetail = extractPrimaryRideDetail(ride);

            if (ride['bookings'].isNotEmpty) {
              bookings.value = true;
            }
            fromTextEditingController.text =
                rideDetail['departure']?.toString() ?? "";
            toTextEditingController.text =
                rideDetail['destination']?.toString() ?? "";
            fromCityId.value = int.tryParse(
                    rideDetail['origin_city_id']?.toString() ?? "") ??
                0;
            toCityId.value = int.tryParse(
                    rideDetail['destination_city_id']?.toString() ?? "") ??
                0;
            pickUpLocationTextEditingController.text =
                ride['pickup'] ?? "";
            dropOffLocationTextEditingController.text =
                ride['dropoff'] ?? "";
            if (ride['date'] != null) {
              DateTime parsedDate = DateTime.parse(ride['date']);
              DateFormat outputFormat = DateFormat('MMMM d, yyyy');
              dateTextEditingController.text = outputFormat.format(parsedDate);
            }

            if (ride['time'] != null) {
              DateTime parsedTime = DateFormat("HH:mm:ss").parse(ride['time']);
              DateFormat outputTimeFormat = DateFormat("hh:mm");
              timeTextEditingController.text =
                  outputTimeFormat.format(parsedTime);
            }

            final moreRideDetail = extractAdditionalRideDetails(ride);
            if (moreRideDetail.isNotEmpty) {
              if (moreRideDetail.isNotEmpty) {
                for (var index = 0; index < moreRideDetail.length; index++) {
                  _appendEmptySpot();
                  final stopValue =
                      moreRideDetail[index]['departure']?.toString() ??
                          moreRideDetail[index]['destination']?.toString() ??
                          "";
                  final pickupOffValue =
                      moreRideDetail[index]['pickup']?.toString() ??
                          moreRideDetail[index]['dropoff']?.toString() ??
                          "";
                  _setSpotValues(
                    index,
                    stop: stopValue,
                    pickupOff: pickupOffValue,
                    date: formatSpotDateValue(moreRideDetail[index]['date']),
                    time: formatSpotTimeValue(moreRideDetail[index]['time']),
                    cityId: int.tryParse(
                            moreRideDetail[index]['city_id'].toString()) ??
                        0,
                  );
                  priceSpotControllers[index].text =
                      formatMinorPriceForDisplay(moreRideDetail[index]['price']);
                  rideDetailIds[index] = "${moreRideDetail[index]['id']}";
                }
              }
            }

            recurring.value = ride['recurring'] == "1" ? true : false;
            recurringType.value = "";
            recurringTripsTextEditingController.text = "";
            dropOffDescriptionTextEditingController.text =
                ride['details'].toString();
            seatAvailable.value = int.parse(ride['seats'].toString());
            seatMiddle.value = int.parse(ride['middle_seats'].toString());
            seatBack.value = int.parse(ride['back_seats'].toString());
            bookingType.value = ride['booking_type'].toString();

            addNewVehicle.value = ride['add_vehicle'] == "1" ? true : false;
            if (ride['add_vehicle'] == "1") {
              makeTextEditingController.text = ride['make'].toString();
              modelTextEditingController.text = ride['model'].toString();
              licenseNumberTextEditingController.text =
                  ride['license_no'].toString();
              colorTextEditingController.text = ride['color'].toString();
              yearTextEditingController.text = ride['year'].toString();
              vehicleType.value = ride['vehicle_type'].toString();
              fuel.value = ride['car_type'].toString();
              carOldImagePath.value = ride['car_image'].toString();
            }

            _syncVehicleSelectionFromRide(ride);

            smoking.value = ride['smoke'].toString();
            pet.value = ride['animal_friendly'].toString();
            featureList.clear();
            var featureData = List<dynamic>.empty(growable: true);
            featureData.addAll(ride['features']);
            for (var element in featureData) {
              featureList.add(
                  (element['id'] ?? element['title']).toString());
            }
            bookingOption.value = ride['booking_method'].toString();
            luggage.value = ride['luggage'].toString();
            paymentOption.value = ride['payment_method'].toString();
            disclaimer.value = false;
            acceptMoreLuggage.value = ride['accept_more_luggage'].toString();
            openCustomized.value = ride['open_customized'].toString();
            pricePerSeatTextEditingController.text =
                _formatMinorPriceForSeatField(
                    rideDetail['price'] ?? ride['price']);
            anythingTextEditingController.text =
                ride['notes']?.toString() ?? "";

            if (fromSpotControllers.isNotEmpty) {
              rebuildRoutePriceEntries();
            } else {
              scheduleRouteDistanceEstimates();
            }
          }
        }
        isOverlayLoading(false);
      }, onError: (err) {
        isOverlayLoading(false);
        serviceController.showDialogue(err.toString(), type: "error");
      });
    } catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString(), type: "error");
    }
  }

  /// Fetches suggested price-per-km from the API in the background (no full-screen overlay).
  /// Route segment distances use [routeDistanceLoading] + hints under the price fields instead.
  getCitiesDistance() async {
    if (fromTextEditingController.text == "" &&
        toTextEditingController.text == "") {
      return;
    }
    try {
      await PostRideProvider()
          .getCitiesDistance(serviceController.token, serviceController.langId,
              fromTextEditingController.text, toTextEditingController.text)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null && resp['data']['pricePerKm'] != null) {
            final updatedPrice = resp['data']['pricePerKm'].toString();
            if (hasRoutePriceEntries) {
              final orderedNodes = buildOrderedRouteNodes();
              if (orderedNodes.length >= 2) {
                final directKey = buildRoutePriceKey(
                  orderedNodes.first['label'].toString(),
                  orderedNodes.last['label'].toString(),
                );

                for (final routeEntry in routePriceEntries) {
                  if (routeEntry['key'] == directKey) {
                    final controller =
                        routeEntry['controller'] as TextEditingController?;
                    if (controller != null) {
                      controller.text = updatedPrice;
                    }
                    break;
                  }
                }
              }
            } else {
              pricePerSeatTextEditingController.text =
                  _primarySeatPriceFieldText(updatedPrice);
            }
          }
        }
      }, onError: (err) {
        serviceController.showDialogue(err.toString(), type: "error");
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString(), type: "error");
    }
  }

  getCitiesSpotsDistance(index) async {
    if (fromSpotControllers[index].text == "" &&
        toSpotControllers[index].text == "") {
      return;
    }
    try {
      await PostRideProvider()
          .getCitiesDistance(serviceController.token, serviceController.langId,
              fromSpotControllers[index].text, toSpotControllers[index].text)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null && resp['data']['pricePerKm'] != null) {
            priceSpotControllers[index].text =
                resp['data']['pricePerKm'].toString();
          }
        }
      }, onError: (err) {
        serviceController.showDialogue(err.toString(), type: "error");
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString(), type: "error");
    }
  }

  addNewSpot() async {
    final context = Get.context;
    if (context == null) {
      return;
    }
    await openStopForm(context);
  }

  void removeNewSpot(index) {
    fromSpotControllers.removeAt(index);
    toSpotControllers.removeAt(index);
    priceSpotControllers.removeAt(index);
    pickupSpotControllers.removeAt(index);
    dropoffSpotControllers.removeAt(index);
    dateSpotControllers.removeAt(index);
    timeSpotControllers.removeAt(index);
    stopCityIds.removeAt(index);
    rideDetailIds.removeAt(index);
    spotsCount.value = fromSpotControllers.length;
    spotsCount.refresh();
    rebuildRoutePriceEntries();
  }

  
}
