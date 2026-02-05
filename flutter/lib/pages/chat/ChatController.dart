import 'dart:async';
import 'dart:io';

import 'package:get/get.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/connectivity_service.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';
import 'ChatProvider.dart';

class ChatController extends GetxController {
  final serviceController = Get.find<Service>();
  late final ConnectivityService connectivityService;
  final errorStateManager = ErrorStateManager();
  var isLoading = true.obs;
  var isOverlayLoading = false.obs;
  var myChats = List<dynamic>.empty(growable: true).obs;
  var chatId = "";
  var userId;
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
      await getChats();
      await getLabelTextDetail();

      errorStateManager.setSuccess();
      isLoading(false); // Ensure isLoading is set to false
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
          "Unable to load chats. Please check your connection and try again.",
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
  }

  void getSelectedChatId() {
    chatId = Get.parameters['Id'] ?? "";
  }

  Future<void> getChats() async {
    myChats.clear();
    await ChatProvider()
        .getChats(
      serviceController.token,
    )
        .then((resp) async {
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['chats'] != null) {
          logger.info('Chats: ${resp['data']['chats']}');
          myChats.addAll(resp['data']['chats']);
        }

        if (resp['data'] != null && resp['data']['languages'] != null) {
          serviceController.languages.clear();
          serviceController.languages.addAll(resp['data']['languages']);

          var getLanguage = serviceController.languages.firstWhereOrNull(
              (element) => element['id'] == serviceController.langId.value);
          if (getLanguage != null) {
            serviceController.langIcon.value = getLanguage['flag_icon'];
            serviceController.lang.value = getLanguage['abbreviation'];
          }
        }
      }
    }, onError: (error) {
      throw error; // Propagate to loadInitialData
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

          serviceController.imagePreviewLabel.value =
              labelTextDetail['image_preview'] ?? 'Image Preview';
        }

        serviceController.navigationChatLabel.value =
            labelTextDetail['navigation_chat_label'] ?? "Chats";
        serviceController.navigationMyTripLabel.value =
            labelTextDetail['navigation_my_trip_label'] ?? "My trips";
        serviceController.navigationMyProfileLabel.value =
            labelTextDetail['navigation_my_profile_label'] ?? "My profile";

        if (resp['data'] != null && resp['data']['messages'] != null) {
          serviceController.closeBtnLabel.value =
              resp['data']['messages']['popup_close_btn_text'].toString();
        }

        var getLanguage = serviceController.languages.firstWhereOrNull(
            (element) => element['id'] == serviceController.langId.value);
        if (getLanguage != null) {
          serviceController.langIcon.value = getLanguage['flag_icon'];
        }
      }
    }, onError: (error) {
      throw error; // Propagate to loadInitialData
    });
  }

  deleteChat(chatObj) async {
    bool isConfirmed = await serviceController.showConfirmationDialog(
        labelTextDetail['delete_messages_label'] ??
            "Are you sure you want to delete this chat?",
        cancelYesBtn: "Yes,Delete it",
        cancelNoBtn: "No,Take me back");
    if (isConfirmed == false) {
      return;
    }

    // Check connectivity before making API call
    if (!connectivityService.isConnected) {
      serviceController.showDialogue(
        "No internet connection. Please check your network and try again.",
        type: "error",
      );
      return;
    }

    try {
      isOverlayLoading(true);
      await ChatProvider()
          .deleteChat(
              serviceController.langId.value, chatObj, serviceController.token)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          myChats.removeWhere((element) =>
              element['receiver'] == chatObj['receiver'] &&
              element['sender'] == chatObj['sender']);
          myChats.refresh();
        }
        isOverlayLoading(false);
      }, onError: (error) {
        isOverlayLoading(false);
        // Handle structured errors from provider
        if (error is Map && error.containsKey('type')) {
          final errorType = error['type'];
          final errorMessage = error['message'] ?? 'An error occurred';

          if (errorType != 'network') {
            serviceController.showDialogue(errorMessage, type: "error");
          } else {
            serviceController.showDialogue(
              "No internet connection. Please check your network and try again.",
              type: "error",
            );
          }
        } else {
          serviceController.showDialogue(error.toString(), type: "error");
        }
      });
    } catch (exception) {
      isOverlayLoading(false);
      logger.error("Delete chat exception: $exception");
      serviceController.showDialogue(
        "An unexpected error occurred. Please try again.",
        type: "error",
      );
    }
  }
}
