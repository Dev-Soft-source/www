import 'package:get/get.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/my_passenger/MyPassengerProvider.dart';
import 'package:proximaride_app/services/service.dart';

class MyPassengerController extends GetxController{

  final serviceController = Get.find<Service>();
  var isLoading = false.obs;
  var isOverlayLoading = false.obs;
  var tripId = "";

  var myPassengers = List<dynamic>.empty(growable: true).obs;

  var labelTextDetail = {}.obs;

  // var ride = {}.obs;

@override
  void onInit() async{
    // TODO: implement onInit
    super.onInit();
    tripId = Get.parameters['rideId'] ?? "";
    await getMyPassengers();

  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
  }


  getMyPassengers() async{
    try{
      isLoading(true);
      MyPassengerProvider().getMyPassengers(
        tripId,
        serviceController.token,
        serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['bookings'] != null){
            myPassengers.addAll(resp['data']['bookings']);
          }
          if(resp['data'] != null && resp['data']['myPassengerPage'] != null){
            labelTextDetail.addAll(resp['data']['myPassengerPage']);
          }

        }
        isLoading(false);
      },onError: (err){
        isLoading(false);

serviceController.showDialogue(err.toString());


      });

    }catch (exception) {
      isLoading(false);

      serviceController.showDialogue(exception.toString());
    }
  }

  noShowDriverData(bookingId, rideId, userId){
    try{
      isOverlayLoading(true);

      MyPassengerProvider().noShowDriverData(
          rideId,
          'passenger',
          bookingId,
          userId,
          serviceController.token
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Error"){
          serviceController.showDialogue(resp['message'].toString());
        }else if(resp['status'] != null && resp['status'] == "Success"){
          serviceController.showDialogue(resp['message'].toString());
        }
        isOverlayLoading(false);
      },onError: (error){
        isOverlayLoading(false);
        serviceController.showDialogue(error.toString());
      });

    }catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }
}