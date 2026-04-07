
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/messaging_page/widgets/message_container.dart';
import 'package:proximaride_app/pages/widgets/error_state_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import '../widgets/network_cache_image_widget.dart';
import '../widgets/progress_circular_widget.dart';
import 'MessagingController.dart';

class MessagingPage extends StatelessWidget {
  const MessagingPage({super.key});

  MessagingController get controller {
    if (Get.isRegistered<MessagingController>()) {
      return Get.find<MessagingController>();
    }
    return Get.put(MessagingController());
  }

  List<dynamic> _sortedMessages() {
    final sorted = List<dynamic>.from(controller.messagesList);
    sorted.sort((a, b) {
      try {
        return DateTime.parse(a['created_at'].toString())
            .compareTo(DateTime.parse(b['created_at'].toString()));
      } catch (_) {
        return a['created_at']
            .toString()
            .compareTo(b['created_at'].toString());
      }
    });
    return sorted;
  }

  String _dateKey(dynamic message) {
    try {
      final parsed = DateTime.parse(message['created_at'].toString()).toLocal();
      return DateFormat('yyyy-MM-dd').format(parsed);
    } catch (_) {
      return 'unknown';
    }
  }

  String _dateLabel(String key) {
    if (key == 'unknown') {
      return '';
    }

    try {
      final parsed = DateFormat('yyyy-MM-dd').parse(key);
      return DateFormat('MMMM d, yyyy').format(parsed);
    } catch (_) {
      return key;
    }
  }

  String _timeLabel(dynamic message) {
    try {
      final parsed = DateTime.parse(message['created_at'].toString()).toLocal();
      return DateFormat('HH:mm').format(parsed);
    } catch (_) {
      final value = message['created_at']?.toString() ?? '';
      return value.length >= 16 ? value.substring(11, 16) : '';
    }
  }

  int _senderId(dynamic message) {
    final sender = message['sender'];
    if (sender is Map) {
      return int.tryParse(sender['id'].toString()) ?? 0;
    }
    return int.tryParse(sender.toString()) ?? 0;
  }

  dynamic _rideDetail(dynamic message) {
    final rideDetail = message['ride_detail'];
    return rideDetail is Map ? rideDetail : {};
  }

  @override
  Widget build(BuildContext context) {
    controller.ensureRouteState();

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        surfaceTintColor: Colors.transparent,
        leading: IconButton(
          onPressed: () => Get.back(),
          icon: const Icon(Icons.arrow_back_ios_new_rounded,
              color: Color(0xFF0F172A)),
        ),
        titleSpacing: 0,
        title: Obx(() {
          return Row(
            children: [
              Container(
                width: 46,
                height: 46,
                decoration: BoxDecoration(
                  color: const Color(0xFFDBEAFE),
                  borderRadius: BorderRadius.circular(18),
                ),
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(18),
                  child: networkCacheImageWidget(
                    controller.chatUserInfo['profile_image'] ?? "",
                    BoxFit.cover,
                    46.0,
                    46.0,
                  ),
                ),
              ),
              12.widthBox,
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Text(
                      controller.labelTextDetail['driver_chat_with'] ??
                          'Chat with',
                      style: const TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.w700,
                        letterSpacing: 1.4,
                        color: Color(0xFF94A3B8),
                      ),
                    ),
                    Text(
                      "${controller.chatUserInfo['first_name'] ?? ""}"
                          .trim(),
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(
                        fontSize: 20,
                        fontWeight: FontWeight.w700,
                        color: Color(0xFF0F172A),
                        fontFamily: regular,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          );
        }),
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(1),
          child: Container(
            height: 1,
            color: const Color(0xFFE2E8F0),
          ),
        ),
      ),
      body: Obx(() {
        if (controller.errorStateManager.isLoading.value) {
          return Center(child: progressCircularWidget(context));
        }

        if (controller.errorStateManager.hasError.value) {
          return ErrorStateWidget(
            message: controller.errorStateManager.errorMessage.value,
            errorType: controller.errorStateManager.errorType.value,
            onRetry: () {
              if (controller.errorStateManager.onRetry.value != null) {
                controller.errorStateManager.onRetry.value!();
              }
            },
          );
        }

        if (controller.isLoading.value) {
          return Center(child: progressCircularWidget(context));
        }

        final sortedMessages = _sortedMessages();
        final groupedMessages = <String, List<dynamic>>{};

        for (final message in sortedMessages) {
          groupedMessages.putIfAbsent(_dateKey(message), () => []).add(message);
        }

        return Stack(
          children: [
            Column(
              children: [
                Expanded(
                  child: Container(
                    decoration: const BoxDecoration(
                      gradient: LinearGradient(
                        begin: Alignment.topCenter,
                        end: Alignment.bottomCenter,
                        colors: [
                          Color(0xFFF8FBFF),
                          Color(0xFFFFFFFF),
                        ],
                      ),
                    ),
                    child: ListView(
                      controller: controller.scrollController,
                      padding: const EdgeInsets.fromLTRB(16, 18, 16, 28),
                      children: groupedMessages.entries.map((entry) {
                        return Column(
                          children: [
                            if (_dateLabel(entry.key).isNotEmpty) ...[
                              Center(
                                child: Container(
                                  margin: const EdgeInsets.only(bottom: 18),
                                  padding: const EdgeInsets.symmetric(
                                      horizontal: 14, vertical: 7),
                                  decoration: BoxDecoration(
                                    color: const Color(0xFFE5E7EB),
                                    borderRadius: BorderRadius.circular(999),
                                  ),
                                  child: Text(
                                    _dateLabel(entry.key),
                                    style: const TextStyle(
                                      fontSize: 12,
                                      fontWeight: FontWeight.w700,
                                      color: Color(0xFF475569),
                                    ),
                                  ),
                                ),
                              ),
                            ],
                            ...entry.value.map((message) {
                              final msgType =
                                  _senderId(message) == controller.userId ? 1 : 2;
                              final rideDetail = _rideDetail(message);

                              return Padding(
                                padding: const EdgeInsets.only(bottom: 12),
                                child: Align(
                                  alignment: msgType == 1
                                      ? Alignment.centerRight
                                      : Alignment.centerLeft,
                                  child: messageContainer(
                                    context: context,
                                    message: message['message'] ?? "",
                                    deliveryStatus:
                                        message['delivery_status'] ?? 'sent',
                                    from: message['redirect'].toString() == "1"
                                        ? rideDetail['departure'].toString()
                                        : "",
                                    to: message['redirect'].toString() == "1"
                                        ? rideDetail['destination'].toString()
                                        : "",
                                    date: message['redirect'].toString() == "1"
                                        ? rideDetail['date'].toString()
                                        : "",
                                    rideTime:
                                        message['redirect'].toString() == "1"
                                            ? rideDetail['time'].toString()
                                            : "",
                                    onTap: message['redirect'].toString() == "1"
                                        ? () {
                                            Get.toNamed(
                                              '/trip_detail/${message['ride_id']}/findRide/findRide',
                                            );
                                          }
                                        : null,
                                    time: _timeLabel(message),
                                    msgType: msgType,
                                  ),
                                ),
                              );
                            }),
                          ],
                        );
                      }).toList(),
                    ),
                  ),
                ),
                if (controller.type != "old")
                  SafeArea(
                    top: false,
                    child: Container(
                      padding: const EdgeInsets.fromLTRB(16, 10, 16, 14),
                      decoration: const BoxDecoration(
                        color: Colors.white,
                        border: Border(
                          top: BorderSide(color: Color(0xFFE2E8F0)),
                        ),
                      ),
                      child: Container(
                        padding: const EdgeInsets.fromLTRB(16, 8, 8, 8),
                        decoration: BoxDecoration(
                          color: const Color(0xFFF8FAFC),
                          borderRadius: BorderRadius.circular(26),
                          border: Border.all(color: const Color(0xFFE2E8F0)),
                        ),
                        child: Row(
                          crossAxisAlignment: CrossAxisAlignment.end,
                          children: [
                            Expanded(
                              child: TextFormField(
                                minLines: 1,
                                maxLines: 4,
                                controller: controller.typedMessageController,
                                decoration: InputDecoration(
                                  hintText:
                                      "${controller.labelTextDetail['type_message_placeholder'] ?? "Please avoid sharing any contact details such as phone numbers, email addresses, or website links. Do not offer or agree to communicate or arrange payments outside the ProximaRide platform."}",
                                  hintStyle:
                                      appPlaceholderTextStyle().copyWith(
                                    fontSize: 15,
                                    fontFamily: carlito,
                                  ),
                                  border: InputBorder.none,
                                  enabledBorder: InputBorder.none,
                                  focusedBorder: InputBorder.none,
                                  isCollapsed: true,
                                ),
                                style: const TextStyle(
                                  fontSize: 16,
                                  fontFamily: carlito,
                                  height: 1.45,
                                ),
                                keyboardType: TextInputType.multiline,
                                textInputAction: TextInputAction.newline,
                              ),
                            ),
                            10.widthBox,
                            InkWell(
                              onTap: controller.sendMessage,
                              borderRadius: BorderRadius.circular(999),
                              child: Container(
                                width: 48,
                                height: 48,
                                decoration: const BoxDecoration(
                                  color: Color(0xFF3B82F6),
                                  shape: BoxShape.circle,
                                ),
                                child: Center(
                                  child: Image.asset(
                                    sendMessageIcon,
                                    width: 20,
                                    height: 20,
                                    color: Colors.white,
                                  ),
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),
              ],
            ),
            if (controller.isOverlayLoading.value) overlayWidget(context),
          ],
        );
      }),
    );
  }
}
