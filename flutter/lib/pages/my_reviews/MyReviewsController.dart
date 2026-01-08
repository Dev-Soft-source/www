import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/pages/my_reviews/MyReviewsProvider.dart';
import 'package:proximaride_app/services/service.dart';

class MyReviewsController extends GetxController with GetSingleTickerProviderStateMixin{
  final serviceController = Get.find<Service>();

  var isLoading = false.obs;
  var isOverlayLoading = false.obs;

  late TabController tabController;
  late PageController pageController;

  //ReceivedReview
  var reviewsReceived = List<dynamic>.empty(growable: true).obs;
  ScrollController receivedScrollController = ScrollController();
  var receivedPage = 1;
  var receivedNoMoreData = false.obs;
  var receivedLoadMore = false.obs;

  //LeftReview
  var reviewsLeft = List<dynamic>.empty(growable: true).obs;
  ScrollController leftScrollController = ScrollController();
  var leftPage = 1;
  var leftNoMoreData = false.obs;
  var leftLoadMore = false.obs;

  var pageLimit = 6;

  late TextEditingController replyTextController;


  var dataReply = {}.obs;

  var dataType = ''.obs;
  final review = {}.obs;
  var firstTimePage = 0;
  var reviewType = "received".obs;
  var labelTextDetail = {}.obs;

  @override
  void onInit() async {
    // TODO: implement onInit
    super.onInit();
    tabController = TabController(length: 2, vsync: this);
    pageController = PageController(initialPage: 0);
    replyTextController = TextEditingController();
    await getAllReviews();
    paginateAllReviews();
  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
    tabController.dispose();
    pageController.dispose();
    replyTextController.dispose();
    receivedScrollController.dispose();
    leftScrollController.dispose();
  }

  getAllReviews() async {
    try {
      firstTimePage == 0 ? isLoading(true) : isOverlayLoading(true);
      MyReviewsProvider().getAllReviews(
        serviceController.token,
        pageLimit,
        reviewType.value == "received" ? receivedPage : leftPage,
        reviewType.value,
        serviceController.langId.value
      ).then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null && resp['data']['ratings'] != null) {
            if(reviewType.value == "received"){
              reviewsReceived.addAll(resp['data']['ratings']['data']);
            }else{
              reviewsLeft.addAll(resp['data']['ratings']['data']);
            }
          }
          print(resp['data']['reviewSettingPage']);
          if(resp['data'] != null && resp['data']['reviewSettingPage'] != null){
            labelTextDetail.addAll(resp['data']['reviewSettingPage']);
          }
        }
        firstTimePage == 0 ? isLoading(false) : isOverlayLoading(false);
        firstTimePage = 1;
      }, onError: (error) {
        firstTimePage == 0 ? isLoading(false) : isOverlayLoading(false);
        serviceController.showDialogue(error.toString());      });
    } catch (exception) {
      firstTimePage == 0 ? isLoading(false) : isOverlayLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }

  paginateAllReviews() {
    receivedScrollController.addListener(() async {
      if (receivedScrollController.position.pixels ==
          receivedScrollController.position.maxScrollExtent) {
        receivedPage++;
        await getMoreReviews();
      }
    });

    leftScrollController.addListener(() async {
      if (leftScrollController.position.pixels ==
          leftScrollController.position.maxScrollExtent) {
        leftPage++;
        await getMoreReviews();
      }
    });
  }

  getMoreReviews() async {
    try {

      if(reviewType.value == "received" && receivedNoMoreData.value == true){
        return;
      }else if(reviewType.value == "left" && leftNoMoreData.value == true){
        return;
      }
      reviewType.value == "received" ? receivedLoadMore(true) : leftLoadMore(true);
      await MyReviewsProvider().getAllReviews(
        serviceController.token,
        pageLimit,
        reviewType.value == "received" ? receivedPage : leftPage,
        reviewType.value,
        serviceController.langId.value
      )
          .then((resp) async {

        if(resp['data'] != null && resp['data']['bookings'] != null && resp['data']['bookings']['data'] != null && resp['data']['bookings']['data'].isNotEmpty){

        }else{
          reviewType.value == "received" ? receivedLoadMore(false) : leftLoadMore(false);
          reviewType.value == "received" ? receivedNoMoreData(true) : leftNoMoreData(true);
        }
        if(resp['data'] != null && resp['data']['ratings'] != null && resp['data']['ratings']['data'] != null){
          if(reviewType.value == "received"){
            reviewsReceived.addAll(resp['data']['ratings']['data']);
          }else{
            reviewsLeft.addAll(resp['data']['ratings']['data']);
          }
        }
        reviewType.value == "received" ? receivedLoadMore(false) : leftLoadMore(false);
      }, onError: (error) {
        reviewType.value == "received" ? receivedLoadMore(false) : leftLoadMore(false);
        serviceController.showDialogue(error.toString());      });
    } catch (exception) {
      reviewType.value == "received" ? receivedLoadMore(false) : leftLoadMore(false);
      serviceController.showDialogue(exception.toString());
    }
  }

  void loadReview(type, index) {
    review.clear();
    if (type == '1') {
      review.addAll(reviewsReceived[index]);
    } else {
      review.addAll(reviewsLeft[index]);
    }
  }

  addReply(ratingId, reply) async {
    try {
      isOverlayLoading(true);
      MyReviewsProvider()
          .addReply(ratingId, reply, serviceController.token)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          var ratingInfo = reviewsReceived.firstWhereOrNull((element) => element['id'] == ratingId);
          if(ratingInfo != null){
            ratingInfo['replies'] = resp['data']['reply'];
          }
          reviewsReceived.refresh();
          serviceController.showDialogue(resp['message']);        }
        isOverlayLoading(false);
      }, onError: (error) {
        isOverlayLoading(false);
        serviceController.showDialogue(error.toString());      });
    } catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }

  updateReviewPageValue(index) async{
    if(index == 0){
      reviewType.value = "received";
      if(reviewsReceived.isEmpty){
        receivedPage = 1;
        receivedLoadMore(false);
        receivedNoMoreData(false);
        await getAllReviews();
      }
    }else if(index == 1){
      reviewType.value = "left";
      if(reviewsLeft.isEmpty){
        leftPage = 1;
        leftLoadMore(false);
        leftNoMoreData(false);
        await getAllReviews();
      }
    }
  }
}
