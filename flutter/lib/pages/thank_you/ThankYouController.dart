import 'package:get/get.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/service.dart';

class ThankYouController extends GetxController {

  var showOverly = false.obs;
  var isLoading = false.obs;
  final serviceController = Get.find<Service>();
  var labelTextDetail = {}.obs;


  @override
  void onInit() async {
    // TODO: implement onInit
    super.onInit();
    isLoading(true);
    await getLabelTextDetail();
    isLoading(false);
  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
  }

  Future<void> getLabelTextDetail() async {
    try {
      await StageProvider().getLabelTextDetail(serviceController.langId.value, thankYouPage, serviceController.token).then((resp) async {
        if (resp['status']!= null && resp['status'] == "Success") {
          if(resp['data'] != null && resp['data']['thankYouPage'] != null){
            labelTextDetail.addAll(resp['data']['thankYouPage']);
          }
        }
      }, onError: (error) {
        serviceController.showDialogue(error.toString());
        isLoading(false);
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString());
      isLoading(false);
    }
  }

}
