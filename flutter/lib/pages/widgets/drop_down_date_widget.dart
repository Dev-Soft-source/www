import 'package:dropdown_button2/dropdown_button2.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import '../../consts/constFileLink.dart';

Widget dropdownDayWidget({controller, context}){
  return DropdownButtonFormField(
      isExpanded: true,
      elevation: 2,
      decoration: InputDecoration(
          enabledBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(5.0),
              borderSide:BorderSide(color: Colors.grey.shade400,
                  style: BorderStyle.solid, width: 1)
          ),
          focusedBorder:OutlineInputBorder(
              borderRadius: BorderRadius.circular(5.0),
              borderSide:const BorderSide(color: primaryColor)
          ),
          contentPadding: const EdgeInsets.symmetric(vertical: 0.0, horizontal: 8.0),
          fillColor: placeHolderColor
      ),
      value: controller.day.value,
      items: [
        DropdownMenuItem(
          value: "",
          child:  txt18Size(title: "Select day", context: context, fontFamily: bold),
        ),
        for(var i =1; i <= controller.daysLength.value; i++)...[
          DropdownMenuItem(
            value: "${i <= 9 ? "0$i" : i}",
            child:  txt18Size(title: "${ i <= 9 ? "0$i" : i }", context: context, fontFamily: bold),
          ),
        ],
      ],
      onChanged: (data){
        controller.day.value = data!;
      }
  );
}

Widget dropdownMonthWidget({controller, context,screenHeight,screenWidth, String monthPlaceholder = "Month", String type = ""}){
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
      contentPadding: const EdgeInsets.symmetric(vertical: 0.0, horizontal: 8.0),
      fillColor: inputColor,
    ),
      value: controller.month.value,
      items: [
        DropdownMenuItem(
          value: "",
          child: controller.month.value == "" ?
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              txt18Size(title: monthPlaceholder, context: context, fontFamily: bold),
              Icon(Icons.check, color: btnPrimaryColor, size: 20)
            ],
          ) : txt18Size(title: monthPlaceholder, context: context, fontFamily: bold),
        ),
        DropdownMenuItem(
            value: "01",
            child: controller.month.value == "01" ?
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                txt18Size(title: "January", context: context, fontFamily: bold),
                Icon(Icons.check, color: btnPrimaryColor, size: 20)
              ],
            ) : txt18Size(title: "January", context: context, fontFamily: bold),
        ),
        DropdownMenuItem(
          value: "02",
          child: controller.month.value == "02" ?
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              txt18Size(title: "February", context: context, fontFamily: bold),
              Icon(Icons.check, color: btnPrimaryColor, size: 20)
            ],
          ) : txt18Size(title: "February", context: context, fontFamily: bold),
        ),
        DropdownMenuItem(
          value: "03",
          child: controller.month.value == "03" ?
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              txt18Size(title: "March", context: context, fontFamily: bold),
              Icon(Icons.check, color: btnPrimaryColor, size: 20)
            ],
          ) : txt18Size(title: "March", context: context, fontFamily: bold),
        ),
        DropdownMenuItem(
          value: "04",
          child: controller.month.value == "04" ?
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              txt18Size(title: "April", context: context, fontFamily: bold),
              Icon(Icons.check, color: btnPrimaryColor, size: 20)
            ],
          ) : txt18Size(title: "April", context: context, fontFamily: bold),
        ),
        DropdownMenuItem(
          value: "05",
          child: controller.month.value == "05" ?
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              txt18Size(title: "May", context: context, fontFamily: bold),
              Icon(Icons.check, color: btnPrimaryColor, size: 20)
            ],
          ) : txt18Size(title: "May", context: context, fontFamily: bold),
        ),
        DropdownMenuItem(
          value: "06",
          child: controller.month.value == "06" ?
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              txt18Size(title: "June", context: context, fontFamily: bold),
              Icon(Icons.check, color: btnPrimaryColor, size: 20)
            ],
          ) : txt18Size(title: "June", context: context, fontFamily: bold),
        ),
        DropdownMenuItem(
          value: "07",
          child: controller.month.value == "07" ?
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              txt18Size(title: "July", context: context, fontFamily: bold),
              Icon(Icons.check, color: btnPrimaryColor, size: 20)
            ],
          ) : txt18Size(title: "July", context: context, fontFamily: bold),
        ),
        DropdownMenuItem(
          value: "08",
          child: controller.month.value == "08" ?
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              txt18Size(title: "August", context: context, fontFamily: bold),
              Icon(Icons.check, color: btnPrimaryColor, size: 20)
            ],
          ) : txt18Size(title: "August", context: context, fontFamily: bold),
        ),
        DropdownMenuItem(
          value: "09",
          child: controller.month.value == "09" ?
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              txt18Size(title: "September", context: context, fontFamily: bold),
              Icon(Icons.check, color: btnPrimaryColor, size: 20)
            ],
          ) : txt18Size(title: "September", context: context, fontFamily: bold),
        ),
        DropdownMenuItem(
          value: "10",
          child: controller.month.value == "10" ?
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              txt18Size(title: "October", context: context, fontFamily: bold),
              Icon(Icons.check, color: btnPrimaryColor, size: 20)
            ],
          ) : txt18Size(title: "October", context: context, fontFamily: bold),
        ),
        DropdownMenuItem(
          value: "11",
          child: controller.month.value == "11" ?
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              txt18Size(title: "November", context: context, fontFamily: bold),
              Icon(Icons.check, color: btnPrimaryColor, size: 20)
            ],
          ) : txt18Size(title: "November", context: context, fontFamily: bold),
        ),
        DropdownMenuItem(
          value: "12",
          child: controller.month.value == "12" ?
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              txt18Size(title: "December", context: context, fontFamily: bold),
              Icon(Icons.check, color: btnPrimaryColor, size: 20)
            ],
          ) : txt18Size(title: "December", context: context, fontFamily: bold),
        ),
      ],
      onChanged: (data){
        controller.month.value = data!;
        if(type == "student"){
          if (controller.errors.any((error) => error['title'] == "student_card_exp_date")) {
            controller.errors.removeWhere((error) => error['title'] == "student_card_exp_date");
          }
        }
      },
    dropdownStyleData: DropdownStyleData(
      maxHeight: screenHeight * 0.3,
      width: screenWidth/2 - 30,
      // padding: EdgeInsets.only(bottom: 100),
      decoration: BoxDecoration(
        border: Border.all(width: 2,color: primaryColor),
        borderRadius: const BorderRadius.only(bottomLeft: Radius.circular(10.0), bottomRight: Radius.circular(10.0)),
      ),
    ),


  );
}

Widget dropdownYearWidget({controller, context,screenHeight,screenWidth, String yearPlaceholder = "Year", String type = ""}){
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
        contentPadding: const EdgeInsets.symmetric(vertical: 0.0, horizontal: 8.0),
        fillColor: inputColor,
      ),
      value: controller.year.value,
      items: [
        DropdownMenuItem(
          value: "",
          child:  controller.year.value == "" ?
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              txt18Size(title: yearPlaceholder, context: context, fontFamily: bold),
              Icon(Icons.check, color: btnPrimaryColor, size: 20)
            ],
          ) : txt18Size(title: yearPlaceholder, context: context, fontFamily: bold),
        ),
        for(var i =0; i <= controller.totalYear; i++)...[
          DropdownMenuItem(
            value: "${controller.startYear + i}",
            child:  controller.year.value == "${controller.startYear + i}" ?
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                txt18Size(title: "${controller.startYear + i}", context: context, fontFamily: bold),
                Icon(Icons.check, color: btnPrimaryColor, size: 20)
              ],
            ) : txt18Size(title: "${controller.startYear + i}", context: context, fontFamily: bold),
          ),
        ],
      ],
      onChanged: (data){
        controller.year.value = data!;
        if(type == "student"){
          if (controller.errors.any((error) => error['title'] == "student_card_exp_date")) {
            controller.errors.removeWhere((error) => error['title'] == "student_card_exp_date");
          }
        }
      },
    dropdownStyleData: DropdownStyleData(
      maxHeight: screenHeight * 0.3,
      width: screenWidth/2 - 30,
      decoration: BoxDecoration(
        border: Border.all(width: 2,color: primaryColor),
        borderRadius: const BorderRadius.only(bottomLeft: Radius.circular(10.0), bottomRight: Radius.circular(10.0)),
      ),
    ),


  );
}

Widget dropdownCardTypeWidget({controller, context,screenHeight,screenWidth}){
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
      contentPadding: const EdgeInsets.symmetric(vertical: 0.0, horizontal: 8.0),
      fillColor: inputColor,
    ),
      value: controller.cardType.value,
      items: [
        DropdownMenuItem(
          value: "",
          child:  controller.cardType.value == "" ?
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              txt18Size(title: "${controller.labelTextDetail['select_card_type_text'] ?? "Select card type"}", context: context, fontFamily: bold),
              Icon(Icons.check, color: btnPrimaryColor, size: 20)
            ],
          ) : txt18Size(title: "${controller.labelTextDetail['select_card_type_text'] ?? "Select card type"}", context: context, fontFamily: bold),
        ),
        DropdownMenuItem(
            value: "Visa",
            child: Container(
              child: controller.cardType.value == "Visa" ?
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  txt18Size(title: "Visa", context: context, fontFamily: bold),
                  Icon(Icons.check, color: btnPrimaryColor, size: 20)
                ],
              ) : txt18Size(title: "Visa", context: context, fontFamily: bold),
            )
        ),
        DropdownMenuItem(
          value: "MasterCard",
          child: controller.cardType.value == "MasterCard" ?
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              txt18Size(title: "Mastercard", context: context, fontFamily: bold),
              Icon(Icons.check, color: btnPrimaryColor, size: 20)
            ],
          ) : txt18Size(title: "Mastercard", context: context, fontFamily: bold),
        ),
        DropdownMenuItem(
          value: "AmEx",
          child: controller.cardType.value == "AmEx" ?
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              txt18Size(title: "American Express", context: context, fontFamily: bold),
              Icon(Icons.check, color: btnPrimaryColor, size: 20)
            ],
          ) : txt18Size(title: "American Express", context: context, fontFamily: bold),
        ),
        DropdownMenuItem(
          value: "Dis",
          child: controller.cardType.value == "Dis" ?
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              txt18Size(title: "Discover", context: context, fontFamily: bold),
              Icon(Icons.check, color: btnPrimaryColor, size: 20)
            ],
          ) : txt18Size(title: "Discover", context: context, fontFamily: bold),
        ),
        DropdownMenuItem(
          value: "CUP",
          child: controller.cardType.value == "CUP" ?
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              txt18Size(title: "Union Pay", context: context, fontFamily: bold),
              Icon(Icons.check, color: btnPrimaryColor, size: 20)
            ],
          ) : txt18Size(title: "Union Pay", context: context, fontFamily: bold),
        ),
        DropdownMenuItem(
          value: "JC",
          child: controller.cardType.value == "JC" ?
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              txt18Size(title: "JCB International", context: context, fontFamily: bold),
              Icon(Icons.check, color: btnPrimaryColor, size: 20)
            ],
          ) : txt18Size(title: "JCB International", context: context, fontFamily: bold),
        ),
        DropdownMenuItem(
          value: "DiC",
          child: controller.cardType.value == "DiC" ?
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              txt18Size(title: "Diners Club International", context: context, fontFamily: bold),
              Icon(Icons.check, color: btnPrimaryColor, size: 20)
            ],
          ) : txt18Size(title: "Diners Club International", context: context, fontFamily: bold),
        ),
        // DropdownMenuItem(
        //   value: "MTS",
        //   child: txt18Size(title: "Maestro", context: context, fontFamily: bold),
        // ),
        // DropdownMenuItem(
        //   value: "ELO",
        //   child: txt18Size(title: "Elo", context: context, fontFamily: bold),
        // ),
      ],
      onChanged: (data){
        controller.cardType.value = data!;
      },
    dropdownStyleData: DropdownStyleData(
      maxHeight: screenHeight * 0.3,
      width: screenWidth - 30,
      decoration: BoxDecoration(
        border: Border.all(width: 2,color: primaryColor),
        borderRadius: const BorderRadius.only(bottomLeft: Radius.circular(10.0), bottomRight: Radius.circular(10.0)),
      ),
    ),

  );
}

