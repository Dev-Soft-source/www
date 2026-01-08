import 'package:get/get.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/co_passenger/CoPassengerProvider.dart';
import 'package:proximaride_app/services/service.dart';

class CoPassengerController extends GetxController{

  final serviceController = Get.find<Service>();
  var isLoading = false.obs;
  var isOverlayLoading = false.obs;
  var tripId = "";

  var labelTextDetail = {}.obs;

  var coPassengers = List<dynamic>.empty(growable: true).obs;

  // var ride = {}.obs;

@override
  void onInit() async{
    // TODO: implement onInit
    super.onInit();
    tripId = Get.parameters['tripId'] ?? "";
    await getCoPassengers();

  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
  }


  getCoPassengers() async{
    try{
      isLoading(true);
      CoPassengerProvider().getCoPassengers(
        tripId,
        serviceController.token,
        serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['bookings'] != null){
            coPassengers.addAll(resp['data']['bookings']);
          }

          if(resp['data'] != null && resp['data']['coPassengerPageSetting'] != null){
            labelTextDetail.addAll(resp['data']['coPassengerPageSetting']);
          }
        }
        isLoading(false);
      },onError: (error){
        isLoading(false);
        serviceController.showDialogue(error.toString());

      });

    }catch (exception) {
      isLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }
}