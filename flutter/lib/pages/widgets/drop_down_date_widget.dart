import 'package:dropdown_button2/dropdown_button2.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import '../../consts/constFileLink.dart';

const List<Map<String, String>> _monthOptions = [
  {'value': '01', 'label': 'January'},
  {'value': '02', 'label': 'February'},
  {'value': '03', 'label': 'March'},
  {'value': '04', 'label': 'April'},
  {'value': '05', 'label': 'May'},
  {'value': '06', 'label': 'June'},
  {'value': '07', 'label': 'July'},
  {'value': '08', 'label': 'August'},
  {'value': '09', 'label': 'September'},
  {'value': '10', 'label': 'October'},
  {'value': '11', 'label': 'November'},
  {'value': '12', 'label': 'December'},
];

Widget dropdownDayWidget({controller, context}) {
  return DropdownButtonFormField(
      isExpanded: true,
      elevation: 2,
      decoration: InputDecoration(
          enabledBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(5.0),
              borderSide: BorderSide(
                  color: Colors.grey.shade400,
                  style: BorderStyle.solid,
                  width: 1)),
          focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(5.0),
              borderSide: const BorderSide(color: primaryColor)),
          contentPadding:
              const EdgeInsets.symmetric(vertical: 0.0, horizontal: 8.0),
          fillColor: placeHolderColor),
      value: controller.day.value,
      items: [
        DropdownMenuItem(
          value: "",
          child: txt18Size(
              title: "Select day", context: context, fontFamily: bold),
        ),
        for (var i = 1; i <= controller.daysLength.value; i++) ...[
          DropdownMenuItem(
            value: "${i <= 9 ? "0$i" : i}",
            child: txt18Size(
                title: "${i <= 9 ? "0$i" : i}",
                context: context,
                fontFamily: bold),
          ),
        ],
      ],
      onChanged: (data) {
        controller.day.value = data!;
      });
}

Widget dropdownMonthWidget(
    {controller,
    context,
    screenHeight,
    screenWidth,
    String monthPlaceholder = "Month",
    String type = ""}) {
  final now = DateTime.now();
  final currentYear = now.year;
  final nextMonth = now.month + 1;
  final selectedYear = int.tryParse(controller.year.value);

  final allowedMonths = type == "student" && selectedYear == currentYear
      ? _monthOptions
          .where((month) => int.parse(month['value']!) >= nextMonth)
          .toList()
      : _monthOptions;

  final allowedMonthValues =
      allowedMonths.map((month) => month['value']).whereType<String>().toSet();

  if (type == "student" &&
      controller.month.value.isNotEmpty &&
      !allowedMonthValues.contains(controller.month.value)) {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      controller.month.value = "";
    });
  }

  return DropdownButtonFormField2(
    isExpanded: true,
    decoration: InputDecoration(
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(5.0),
        borderSide: const BorderSide(
          color: primaryColor,
          style: BorderStyle.solid,
          width: 1,
        ),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(5.0),
        borderSide: const BorderSide(color: primaryColor),
      ),
      contentPadding:
          const EdgeInsets.symmetric(vertical: 0.0, horizontal: 8.0),
      fillColor: inputColor,
    ),
    value: controller.month.value,
    items: [
      DropdownMenuItem(
        value: "",
        child: controller.month.value == ""
            ? Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  txt18Size(
                      title: monthPlaceholder,
                      context: context,
                      fontFamily: bold),
                  Icon(Icons.check, color: btnPrimaryColor, size: 20)
                ],
              )
            : txt18Size(
                title: monthPlaceholder, context: context, fontFamily: bold),
      ),
      for (final monthOption in allowedMonths) ...[
        DropdownMenuItem(
          value: monthOption['value'],
          child: controller.month.value == monthOption['value']
              ? Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    txt18Size(
                        title: monthOption['label'] ?? '',
                        context: context,
                        fontFamily: bold),
                    Icon(Icons.check, color: btnPrimaryColor, size: 20)
                  ],
                )
              : txt18Size(
                  title: monthOption['label'] ?? '',
                  context: context,
                  fontFamily: bold),
        ),
      ],
    ],
    onChanged: (data) {
      controller.month.value = data!;
      controller.errors.removeWhere((error) => error['title'] == "month");
      if (type == "student") {
        if (controller.errors
            .any((error) => error['title'] == "student_card_exp_date")) {
          controller.errors.removeWhere(
              (error) => error['title'] == "student_card_exp_date");
        }
      }
    },
    dropdownStyleData: DropdownStyleData(
      maxHeight: screenHeight * 0.3,
      width: screenWidth / 2 - 30,
      // padding: EdgeInsets.only(bottom: 100),
      decoration: BoxDecoration(
        border: Border.all(width: 2, color: primaryColor),
        borderRadius: const BorderRadius.only(
            bottomLeft: Radius.circular(10.0),
            bottomRight: Radius.circular(10.0)),
      ),
    ),
  );
}

Widget dropdownYearWidget(
    {controller,
    context,
    screenHeight,
    screenWidth,
    String yearPlaceholder = "Year",
    String type = "",
    int yearsAhead = 10}) {
  return DropdownButtonFormField2(
    isExpanded: true,
    decoration: InputDecoration(
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(5.0),
        borderSide: const BorderSide(
          color: primaryColor,
          style: BorderStyle.solid,
          width: 1,
        ),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(5.0),
        borderSide: const BorderSide(color: primaryColor),
      ),
      contentPadding:
          const EdgeInsets.symmetric(vertical: 0.0, horizontal: 8.0),
      fillColor: inputColor,
    ),
    value: controller.year.value,
    items: [
      DropdownMenuItem(
        value: "",
        child: controller.year.value == ""
            ? Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  txt18Size(
                      title: yearPlaceholder,
                      context: context,
                      fontFamily: bold),
                  Icon(Icons.check, color: btnPrimaryColor, size: 20)
                ],
              )
            : txt18Size(
                title: yearPlaceholder, context: context, fontFamily: bold),
      ),
      for (var i = 0; i <= yearsAhead; i++) ...[
        DropdownMenuItem(
          value: "${controller.startYear + i}",
          child: controller.year.value == "${controller.startYear + i}"
              ? Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    txt18Size(
                        title: "${controller.startYear + i}",
                        context: context,
                        fontFamily: bold),
                    Icon(Icons.check, color: btnPrimaryColor, size: 20)
                  ],
                )
              : txt18Size(
                  title: "${controller.startYear + i}",
                  context: context,
                  fontFamily: bold),
        ),
      ],
    ],
    onChanged: (data) {
      controller.year.value = data!;
      if (type == "student") {
        final now = DateTime.now();
        final selectedYear = int.tryParse(controller.year.value);
        final nextMonth = now.month + 1;
        final selectedMonth = int.tryParse(controller.month.value);

        if (selectedYear == now.year &&
            selectedMonth != null &&
            selectedMonth < nextMonth) {
          controller.month.value = "";
        }
      }
      controller.errors.removeWhere((error) => error['title'] == "year");
      if (type == "student") {
        if (controller.errors
            .any((error) => error['title'] == "student_card_exp_date")) {
          controller.errors.removeWhere(
              (error) => error['title'] == "student_card_exp_date");
        }
      }
    },
    dropdownStyleData: DropdownStyleData(
      maxHeight: screenHeight * 0.3,
      width: screenWidth / 2 - 30,
      decoration: BoxDecoration(
        border: Border.all(width: 2, color: primaryColor),
        borderRadius: const BorderRadius.only(
            bottomLeft: Radius.circular(10.0),
            bottomRight: Radius.circular(10.0)),
      ),
    ),
  );
}

Widget dropdownCardTypeWidget(
    {controller, context, screenHeight, screenWidth}) {
  return DropdownButtonFormField2(
    isExpanded: true,
    decoration: InputDecoration(
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(5.0),
        borderSide: const BorderSide(
          color: primaryColor,
          style: BorderStyle.solid,
          width: 1,
        ),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(5.0),
        borderSide: const BorderSide(color: primaryColor),
      ),
      contentPadding:
          const EdgeInsets.symmetric(vertical: 0.0, horizontal: 8.0),
      fillColor: inputColor,
    ),
    value: controller.cardType.value,
    items: [
      DropdownMenuItem(
        value: "",
        child: controller.cardType.value == ""
            ? Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  txt18Size(
                      title:
                          "${controller.labelTextDetail['select_card_type_text'] ?? "Select card type"}",
                      context: context,
                      fontFamily: bold),
                  Icon(Icons.check, color: btnPrimaryColor, size: 20)
                ],
              )
            : txt18Size(
                title:
                    "${controller.labelTextDetail['select_card_type_text'] ?? "Select card type"}",
                context: context,
                fontFamily: bold),
      ),
      DropdownMenuItem(
        value: "AmEx",
        child: controller.cardType.value == "AmEx"
            ? Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  txt18Size(
                      title: "American Express",
                      context: context,
                      fontFamily: bold),
                  Icon(Icons.check, color: btnPrimaryColor, size: 20)
                ],
              )
            : txt18Size(
                title: "American Express", context: context, fontFamily: bold),
      ),
      DropdownMenuItem(
        value: "DiC",
        child: controller.cardType.value == "DiC"
            ? Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  txt18Size(
                      title: "Diners Club International",
                      context: context,
                      fontFamily: bold),
                  Icon(Icons.check, color: btnPrimaryColor, size: 20)
                ],
              )
            : txt18Size(
                title: "Diners Club International",
                context: context,
                fontFamily: bold),
      ),
      DropdownMenuItem(
        value: "Dis",
        child: controller.cardType.value == "Dis"
            ? Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  txt18Size(
                      title: "Discover", context: context, fontFamily: bold),
                  Icon(Icons.check, color: btnPrimaryColor, size: 20)
                ],
              )
            : txt18Size(title: "Discover", context: context, fontFamily: bold),
      ),
      DropdownMenuItem(
        value: "JC",
        child: controller.cardType.value == "JC"
            ? Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  txt18Size(
                      title: "JCB International",
                      context: context,
                      fontFamily: bold),
                  Icon(Icons.check, color: btnPrimaryColor, size: 20)
                ],
              )
            : txt18Size(
                title: "JCB International", context: context, fontFamily: bold),
      ),
      DropdownMenuItem(
        value: "MasterCard",
        child: controller.cardType.value == "MasterCard"
            ? Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  txt18Size(
                      title: "Mastercard", context: context, fontFamily: bold),
                  Icon(Icons.check, color: btnPrimaryColor, size: 20)
                ],
              )
            : txt18Size(
                title: "Mastercard", context: context, fontFamily: bold),
      ),
      DropdownMenuItem(
        value: "CUP",
        child: controller.cardType.value == "CUP"
            ? Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  txt18Size(
                      title: "UnionPay", context: context, fontFamily: bold),
                  Icon(Icons.check, color: btnPrimaryColor, size: 20)
                ],
              )
            : txt18Size(title: "UnionPay", context: context, fontFamily: bold),
      ),
      DropdownMenuItem(
          value: "Visa",
          child: Container(
            child: controller.cardType.value == "Visa"
                ? Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      txt18Size(
                          title: "Visa", context: context, fontFamily: bold),
                      Icon(Icons.check, color: btnPrimaryColor, size: 20)
                    ],
                  )
                : txt18Size(title: "Visa", context: context, fontFamily: bold),
          )),
      // DropdownMenuItem(
      //   value: "MTS",
      //   child: txt18Size(title: "Maestro", context: context, fontFamily: bold),
      // ),
      // DropdownMenuItem(
      //   value: "ELO",
      //   child: txt18Size(title: "Elo", context: context, fontFamily: bold),
      // ),
    ],
    onChanged: (data) {
      controller.cardType.value = data!;
      controller.errors.removeWhere((error) => error['title'] == "card_type");
    },
    dropdownStyleData: DropdownStyleData(
      maxHeight: screenHeight * 0.3,
      width: screenWidth - 30,
      decoration: BoxDecoration(
        border: Border.all(width: 2, color: primaryColor),
        borderRadius: const BorderRadius.only(
            bottomLeft: Radius.circular(10.0),
            bottomRight: Radius.circular(10.0)),
      ),
    ),
  );
}
