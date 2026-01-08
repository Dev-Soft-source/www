import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/my_wallet/widgets/data_row_widget.dart';
import 'package:proximaride_app/pages/my_wallet/widgets/ride_detail_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import '../../consts/constFileLink.dart';
import '../widgets/second_appbar_widget.dart';
import 'MyWalletController.dart';

class RideFairDetail extends StatelessWidget {
  const RideFairDetail({super.key});



  @override
  Widget build(BuildContext context) {
    var controller = Get.find<MyWalletController>();

    var getData;
    var rideId = Get.parameters['rideId'];
    if(Get.parameters['type'] == "paidOut"){
      getData = controller.driverPaidOutList.firstWhereOrNull((element) => element['ride_id'] == int.parse(rideId.toString()));
    }else if(Get.parameters['type'] == "available"){
      getData = controller.driverAvailableList.firstWhereOrNull((element) => element['ride_id'] == int.parse(rideId.toString()));
    }

    return Scaffold(
      appBar: AppBar(
        leading: const BackButton(color: Colors.white),
        title: Obx(() => secondAppBarWidget(context: context, title: "${controller.labelTextDetail['ride_fare_main_heading'] ?? "Ride fair details"}")),
        backgroundColor: primaryColor,
      ),
      body: Obx(() =>
      controller.isLoading.value == true ?
      Center(child: progressCircularWidget(context)) :
      Container(
        padding: const EdgeInsets.only(top: 20, left: 8.5, right: 8.5),
        child: SingleChildScrollView(
          child: Column(
            crossAxisAlignment:
            CrossAxisAlignment.start,
            children: [
              20.heightBox,
              ListView.separated(
                physics:
                const NeverScrollableScrollPhysics(),
                shrinkWrap: true,
                itemCount: getData['ride']['bookings'].length,
                itemBuilder: (context, index) {
                  return rideDetailWidget(context: context, data: getData['ride']['bookings'][index], controller: controller);
                },
                separatorBuilder:
                    (context, index) {
                  return Column(
                    children: [20.heightBox],
                  );
                },
              ),
              20.heightBox,
            ],
          ),
        ),
      )
      ),
    );
  }
}
