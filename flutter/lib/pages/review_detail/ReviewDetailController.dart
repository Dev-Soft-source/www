import 'package:get/get.dart';
import 'package:proximaride_app/pages/review_detail/ReviewDetailProvider.dart';
import 'package:proximaride_app/services/service.dart';

class ReviewDetailController extends GetxController {
  final serviceController = Get.find<Service>();
  var isLoading = true.obs;
  var errorList = List.empty(growable: true).obs;
  var id = "";
  var fromToType = Get.parameters['type'] ?? "from";

  var reviewDetail = {}.obs;
  var average = 0.0.obs;
  var labelTextDetail = {}.obs;

  @override
  void onInit() async {
    // TODO: implement onInit
    super.onInit();
    id = Get.parameters['id'] ?? "";
    await getReviewDetail();

  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
  }

  getReviewDetail() async {
    try {
      isLoading(true);
      ReviewDetailProvider()
          .getReviewDetail(
        serviceController.token,
        id,
        serviceController.langId.value
      )
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null && resp['data']['rating'] != null) {
            reviewDetail.addAll(resp['data']['rating']);

            average.value = resp['data']['rating']['average_rating'] != null ?double.parse(resp['data']['rating']['average_rating'].toString()) : 0.0;

                // calculateAvg(
                // reviewDetail['timeliness'] != null ? reviewDetail['timeliness'].toString() : '0',
                // reviewDetail['vehicle_condition'] != null ? reviewDetail['vehicle_condition'].toString() : null,
                // reviewDetail['safety'] != null ? reviewDetail['safety'].toString(): '0',
                // reviewDetail['conscious'] != null ? reviewDetail['conscious'].toString() : '0',
                // reviewDetail['comfort'] != null ? reviewDetail['comfort'].toString() : '0',
                // reviewDetail['attitude'] != null ? reviewDetail['attitude'].toString() : '0',
                // reviewDetail['respect'] != null ? reviewDetail['respect'].toString() : '0',
                // reviewDetail['hygiene'] != null ? reviewDetail['hygiene'].toString() : '0',
                // reviewDetail['communication'] != null ? reviewDetail['communication'].toString() : '0');


          }

          if(resp['data'] != null && resp['data']['reviewDetailPage'] != null){
            labelTextDetail.addAll(resp['data']['reviewDetailPage']);
          }
        }
        isLoading(false);
      }, onError: (error) {
        isLoading(false);
        serviceController.showDialogue(error.toString());
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString());

    }
  }


  // double calculateAvg(time, safety, conscious, comfort, attitude, respect, hygiene, communication)
  // {
  //   var avg = (time +
  //           safety +
  //           conscious +
  //           comfort +
  //           attitude +
  //           respect +
  //           hygiene +
  //           communication) /
  //       8;
  //   return avg;
  // }



}
