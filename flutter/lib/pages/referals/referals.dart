import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/referals/ReferralController.dart';
import 'package:proximaride_app/pages/referals/widget/referral_data_widget.dart';
import 'package:proximaride_app/pages/widgets/coming_soon_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

class Referral extends GetView<ReferralController> {
  const Referral({super.key});


  @override
  Widget build(BuildContext context) {
    Get.put(ReferralController());
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: Obx(() => secondAppBarWidget(title: "${controller.labelTextDetail['main_heading'] ?? "Referrals"}", context: context)),
        leading: const BackButton(
          color: Colors.white,
        ),
      ),
      body: Obx(() {
        if(controller.isLoading.value == true){
          return Center(child: progressCircularWidget(context));
        }else{
          return SingleChildScrollView(
            child: Container(
              padding: EdgeInsets.all(10.0),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.start,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  10.heightBox,
                  txt22Size(title: "${controller.labelTextDetail['referral_description'] ?? "Refer a friend using your referral URL and get two rides booking fee waived"}", fontFamily: regular, context: context),
                  10.heightBox,
                  txt20Size(title: "${controller.labelTextDetail['your_referral_url_label'] ?? "Your referral URL"}", fontFamily: regular, context: context),
                  5.heightBox,
                  Container(
                    padding: EdgeInsets.all(15.0),
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(5.0),
                        border: Border.all(color: Colors.grey.shade400)
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Expanded(child: txt14Size(title: controller.referralLink.value, fontFamily: regular, context: context)),
                        InkWell(
                          onTap: () async{
                            await Clipboard.setData(ClipboardData(text: controller.referralLink.value));
                          },
                          child: Image.asset(referralCopyIcon, width: 25, height: 25),
                        )
                      ],
                    ),
                  ),
                  10.heightBox,
                  txt20Size(title: "${controller.labelTextDetail['my_referral_text'] ?? "My referral"}", fontFamily: regular, context: context),
                  5.heightBox,
                  ListView.separated(
                    shrinkWrap: true,
                    physics: NeverScrollableScrollPhysics(),
                    itemCount: controller.myReferralList.length,
                    itemBuilder: (context, index){

                      String createdDate = "";
                      if(controller.myReferralList[index]['created_at'] != null){
                        DateTime parsedDate = DateTime.parse(controller.myReferralList[index]['created_at']);
                        DateFormat outputFormat = DateFormat('MMMM d, yyyy');
                        createdDate = outputFormat.format(parsedDate);
                      }

                      return Card(
                        elevation: 2,
                        surfaceTintColor: Colors.white,
                        color: Colors.white,
                        child: Container(
                          padding: EdgeInsets.only(top: 10.0, bottom: 10.0),
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.start,
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              referralDataWidget(context: context, data: controller.myReferralList[index]['user_id'].toString(), title: "${controller.labelTextDetail['account_id_label'] ?? "Account id#"}"),
                              const Divider(),
                              referralDataWidget(context: context, data: "${controller.myReferralList[index]['user']['first_name']} ${controller.myReferralList[index]['user']['last_name']}", title: "${controller.labelTextDetail['user_label'] ?? "User"}"),
                              const Divider(),
                              referralDataWidget(context: context, data: createdDate, title: "${controller.labelTextDetail['registered_on_label'] ?? "Registered on"}"),
                            ],
                          ),
                        ),
                      );
                    },
                    separatorBuilder: (context, index){
                      return SizedBox();
                    },
                  )
                ],
              ),
            ),
          );
        }
      }),
    );
  }
}
