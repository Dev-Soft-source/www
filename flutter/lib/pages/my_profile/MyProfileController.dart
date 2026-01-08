import 'package:get/get.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/service.dart';

class MyProfileController extends GetxController {

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
      await StageProvider().getLabelTextDetail(serviceController.langId.value, profilePageSetting, serviceController.token).then((resp) async {
        if (resp['status']!= null && resp['status'] == "Success") {
          if(resp['data'] != null && resp['data']['myProfilePage'] != null){
            labelTextDetail.addAll(resp['data']['myProfilePage']);
          }
          if(resp['data'] != null && resp['data']['logoutPage'] != null){
            serviceController.logoutLabelTextDetail.clear();
            serviceController.logoutLabelTextDetail.addAll(resp['data']['logoutPage']);
          }

          serviceController.termAndConditionLabel.value = resp['data']['termsAndConditionHeading'] ?? serviceController.termAndConditionLabel.value;
          serviceController.privacyPolicyLabel.value = resp['data']['privacyPolicyHeading'] ?? serviceController.privacyPolicyLabel.value;
          serviceController.termOfUseLabel.value = resp['data']['termsofuseHeading'] ?? serviceController.termOfUseLabel.value;
          serviceController.refundPolicyLabel.value = resp['data']['refundPolicyHeading'] ?? serviceController.refundPolicyLabel.value;
          serviceController.cancellationPolicyLabel.value = resp['data']['cancellationPolicyHeading'] ?? serviceController.cancellationPolicyLabel.value;
          serviceController.disputePolicyLabel.value = resp['data']['disputePolicyHeading'] ?? serviceController.disputePolicyLabel.value;
          serviceController.coffeeOnWallLabel.value = resp['data']['coffeeOnWallHeading'] ?? serviceController.coffeeOnWallLabel.value;
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
