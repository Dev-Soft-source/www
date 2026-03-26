import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/post_ride/PostRideController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/date_field_widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/prefix_icon_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/services/service.dart';

class StopFormPage extends StatefulWidget {
  const StopFormPage({
    super.key,
    required this.stopIndex,
    required this.initialStop,
    required this.initialPickupOff,
    required this.initialDate,
    required this.initialTime,
    required this.labelTextDetail,
    required this.popupTextDetail,
    this.isEditing = false,
  });

  final int stopIndex;
  final String initialStop;
  final String initialPickupOff;
  final String initialDate;
  final String initialTime;
  final Map<dynamic, dynamic> labelTextDetail;
  final Map<dynamic, dynamic> popupTextDetail;
  final bool isEditing;

  @override
  State<StopFormPage> createState() => _StopFormPageState();
}

class _StopFormPageState extends State<StopFormPage> {
  final serviceController = Get.find<Service>();
  final postRideController = Get.find<PostRideController>();

  late final TextEditingController stopController;
  late final TextEditingController pickupOffController;
  late final TextEditingController dateController;
  late final TextEditingController timeController;

  @override
  void initState() {
    super.initState();
    stopController = TextEditingController(text: widget.initialStop);
    pickupOffController = TextEditingController(text: widget.initialPickupOff);
    dateController = TextEditingController(text: widget.initialDate);
    timeController = TextEditingController(text: widget.initialTime);
  }

  @override
  void dispose() {
    stopController.dispose();
    pickupOffController.dispose();
    dateController.dispose();
    timeController.dispose();
    super.dispose();
  }

  void _save() {
    if (stopController.text.trim().isEmpty ||
        pickupOffController.text.trim().isEmpty ||
        dateController.text.trim().isEmpty ||
        timeController.text.trim().isEmpty) {
      serviceController.showDialogue(
          widget.popupTextDetail['required_message'] ??
              'Please complete all stop fields',
          type: "error");
      return;
    }

    final dateTimeValidationMessage = postRideController.validateStopDateTime(
      widget.stopIndex,
      dateText: dateController.text.trim(),
      timeText: timeController.text.trim(),
    );
    if (dateTimeValidationMessage != null) {
      serviceController.showDialogue(dateTimeValidationMessage, type: "error");
      return;
    }

    Get.back(result: {
      'stop': stopController.text.trim(),
      'pickup_off': pickupOffController.text.trim(),
      'date': dateController.text.trim(),
      'time': timeController.text.trim(),
      'city_id': postRideController.stopCityIds[widget.stopIndex],
    });
  }

  Future<void> _selectStopCity() async {
    await Get.toNamed("/city/origin/${widget.stopIndex}/0/yes");
    if (!mounted) {
      return;
    }

    setState(() {
      stopController.text =
          postRideController.fromSpotControllers[widget.stopIndex].text;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: secondAppBarWidget(
          title: widget.isEditing
              ? (widget.labelTextDetail['edit_stop_heading'] ?? 'Edit stop')
              : (widget.labelTextDetail['add_stop_heading'] ?? 'Add stop'),
          context: context,
        ),
        leading: safeBackButton(context),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: EdgeInsets.all(getValueForScreenType<double>(
            context: context,
            mobile: 15.0,
            tablet: 15.0,
          )),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              txt20Size(title: "Stop", context: context),
              3.heightBox,
              fieldsWidget(
                textController: stopController,
                fieldType: "text",
                readonly: true,
                fontFamily: regular,
                fontSize: 18.0,
                prefixIcon:
                    preFixIconWidget(context: context, imagePath: fromLocationImage),
                placeHolder: widget.labelTextDetail['stop_placeholder'] ?? "Stop",
                onTap: _selectStopCity,
                onChanged: (value) {},
              ),
              10.heightBox,
              txt20Size(title: "Pickup/off location", context: context),
              3.heightBox,
              fieldsWidget(
                textController: pickupOffController,
                fieldType: "text",
                readonly: false,
                fontFamily: regular,
                fontSize: 18.0,
                placeHolder: widget.labelTextDetail['pickup_off_placeholder'] ??
                    "Describe the pickup/off point",
                onChanged: (value) {},
              ),
              10.heightBox,
              txt20Size(
                title:
                    "${widget.labelTextDetail['date_time_label'] ?? "Date & time"}",
                context: context,
              ),
              3.heightBox,
              Row(
                children: [
                  Expanded(
                    child: dateFieldWidget(
                      textController: dateController,
                      fontFamily: regular,
                      fontSize: 16.0,
                      onTap: () async {
                        DateTime? pickedDate = await serviceController.datePicker(
                          context,
                          allowPast: false,
                        );
                        if (pickedDate == null) return;
                        dateController.text =
                            DateFormat("MMMM dd, yyyy").format(pickedDate);
                        timeController.text = "";
                      },
                      prefixIcon: preFixIconWidget(
                          context: context, imagePath: calenderImage),
                      isError: false,
                    ),
                  ),
                  5.widthBox,
                  txt20Size(
                    title: "${widget.labelTextDetail['at_label'] ?? "at"}",
                    context: context,
                    fontFamily: regular,
                  ),
                  5.widthBox,
                  Expanded(
                    child: dateFieldWidget(
                      textController: timeController,
                      fontFamily: regular,
                      fontSize: 16.0,
                      onTap: () async {
                        if (dateController.text.isEmpty) {
                          serviceController.showDialogue(
                              "${widget.popupTextDetail['past_date_message'] ?? 'Please select date first'}");
                          return;
                        }

                        TimeOfDay? pickedTime =
                            await serviceController.timePicker(context);
                        if (pickedTime == null) return;

                        final now = DateTime.now();
                        final selectedDate =
                            DateFormat("MMMM dd, yyyy").parse(dateController.text);
                        final currentDateOnly =
                            DateTime(now.year, now.month, now.day);

                        if (selectedDate.isAtSameMomentAs(currentDateOnly) &&
                            (pickedTime.hour < now.hour ||
                                (pickedTime.hour == now.hour &&
                                    pickedTime.minute < now.minute))) {
                          serviceController.showDialogue(
                              "${widget.popupTextDetail['past_time_message'] ?? 'Can not pick a time in the past'}");
                          timeController.text = "";
                          return;
                        }

                        final selectedDateTime = DateTime(
                          now.year,
                          now.month,
                          now.day,
                          pickedTime.hour,
                          pickedTime.minute,
                        );

                        timeController.text =
                            DateFormat('HH:mm').format(selectedDateTime);

                        final dateTimeValidationMessage =
                            postRideController.validateStopDateTime(
                          widget.stopIndex,
                          dateText: dateController.text.trim(),
                          timeText: timeController.text.trim(),
                        );
                        if (dateTimeValidationMessage != null) {
                          serviceController.showDialogue(
                            dateTimeValidationMessage,
                            type: "error",
                          );
                          timeController.text = "";
                        }
                      },
                      prefixIcon:
                          preFixIconWidget(context: context, imagePath: clockImage),
                      isError: false,
                    ),
                  ),
                ],
              ),
              20.heightBox,
              SizedBox(
                width: context.screenWidth,
                child: elevatedButtonWidget(
                  textWidget: txt22Size(
                    title: widget.isEditing
                        ? (widget.labelTextDetail['update_button_label'] ?? "Update")
                        : (widget.labelTextDetail['add_stop_btn_label'] ?? "Add"),
                    context: context,
                    textColor: Colors.white,
                  ),
                  context: context,
                  onPressed: _save,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
