
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/pages/review/ReviewProvider.dart';
import 'package:proximaride_app/services/service.dart';

class ReviewController extends GetxController {
  final serviceController = Get.find<Service>();
  var isLoading = false.obs;
  var isOverlayLoading = false.obs;
  var isScrollLoading = false.obs;
  var page = 1;
  var pageLimit = 6;
  var noMoreData = false.obs;
  ScrollController scrollController = ScrollController();
  late TextEditingController replyTextController;
  var profileType = "";
  var profileId = Get.parameters['id'].toString();
  var userDetail = {}.obs;
  var labelTextDetail = {}.obs;


  var reviews = List<dynamic>.empty(growable: true).obs;
  @override
  void onInit() async {
    // TODO: implement onInit
    super.onInit();
    if(profileId == "" || profileId == "0" ){
      profileId = serviceController.loginUserDetail['id'].toString();
    }
    profileType = Get.parameters['type']!;
    replyTextController = TextEditingController();
    await getReviews();
    paginateReviewList();
  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
    replyTextController.dispose();
    scrollController.dispose();
  }

  getReviews() async {
    try {
      isLoading(true);
      ReviewProvider()
          .getAllReviews(
        profileId,
        serviceController.token,
        pageLimit,
        page,
        profileType,
        serviceController.langId.value
      )
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null && resp['data']['ratings'] != null) {
            reviews.addAll(resp['data']['ratings']['data']);
          }
          if(resp['data'] != null && resp['data']['user'] != null){
            userDetail.addAll(resp['data']['user']);
          }
          if(resp['data'] != null && resp['data']['reviewSettingPage'] != null){
            labelTextDetail.addAll(resp['data']['reviewSettingPage']);
          }
        }
        isLoading(false);
      }, onError: (err) {
        isLoading(false);
        serviceController.showDialogue(err.toString());
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString());

    }
  }

  Future<void> getMoreReviews() async {
    try {
      isScrollLoading(true);
      await ReviewProvider()
          .getAllReviews(
        profileId,
        serviceController.token,
        pageLimit,
        page,
        profileType,
        serviceController.langId.value
      )
          .then((resp) async {
        if(resp['data']['ratings'] != null && resp['data']['ratings']['data'] != null){
          reviews.addAll(resp['data']['ratings']['data']);
          var temp = resp['data']['ratings']['data'];
          if (temp.length < pageLimit)
            {
              noMoreData.value = true;
            }
        }

        isScrollLoading(false);
      }, onError: (err) {
        isScrollLoading(false);
        serviceController.showDialogue(err.toString());
      });
    } catch (exception) {
      isScrollLoading(false);
      serviceController.showDialogue(exception.toString());

    }
  }

  void paginateReviewList() {
    scrollController.addListener(() async {
      if (scrollController.position.pixels ==
          scrollController.position.maxScrollExtent) {
        page++;
        await getMoreReviews();
      }
    });
  }


  addReply(ratingId,reply) async{
    try{
      isOverlayLoading(true);
      ReviewProvider().addReply(
          ratingId,reply,serviceController.token
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          reviews.clear();
          page = 1;
          getReviews();

        }
        isOverlayLoading(false);
      },onError: (err){
        isOverlayLoading(false);
        serviceController.showDialogue(err.toString());
      });
    }catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }


}
