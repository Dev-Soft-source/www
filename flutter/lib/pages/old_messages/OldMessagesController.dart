import 'dart:async';
import 'dart:io';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/pages/old_messages/OldMessagesProvider.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/connectivity_service.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

class OldMessagesController extends GetxController {
  final serviceController = Get.find<Service>();
  late final ConnectivityService connectivityService;
  final errorStateManager = ErrorStateManager();
  var isLoading = false.obs;
  var oldMessagesList = List<dynamic>.empty(growable: true).obs;
  var chatId = "";
  dynamic userId;
  var labelTextDetail = {}.obs;

  @override
  void onInit() async {
    userId = serviceController.loginUserDetail['id'];
    super.onInit();

    // Initialize connectivity service
    try {
      connectivityService = Get.find<ConnectivityService>();
    } catch (e) {
      connectivityService = Get.put(ConnectivityService());
    }

    // Load initial data
    await loadInitialData();
  }

  Future<void> loadInitialData() async {
    try {
      errorStateManager.setLoading();

      // NO connectivity check - let the API call proceed
      // Only catch exceptions if they actually occur

      // Execute init API calls
      await getOldMessages();
      await getLabelTextDetail();

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
          "Unable to load old messages. Please check your connection and try again.",
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

  Future<void> getOldMessages() async {
    await OldMessagesProvider()
        .getOldChats(
      serviceController.token,
    )
        .then((resp) async {
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['chats'] != null) {
          oldMessagesList.addAll(resp['data']['chats']);
        }
      }
    }, onError: (err) {
      throw err; // Propagate to loadInitialData
    });
  }

  Future<void> getLabelTextDetail() async {
    await StageProvider()
        .getLabelTextDetail(
            serviceController.langId.value, chatPage, serviceController.token)
        .then((resp) async {
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['chatsPage'] != null) {
          labelTextDetail.addAll(resp['data']['chatsPage']);
        }

        var getLanguage = serviceController.languages.firstWhereOrNull(
            (element) => element['id'] == serviceController.langId.value);
        if (getLanguage != null) {
          serviceController.langIcon.value = getLanguage['flag_icon'];
          serviceController.lang.value = getLanguage['abbreviation'];
        }
      }
    }, onError: (error) {
      throw error; // Propagate to loadInitialData
    });
  }
}
