import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/post_ride_again/PostRideAgainProvider.dart';
import 'package:proximaride_app/services/service.dart';

class PostRideAgainController extends GetxController with GetSingleTickerProviderStateMixin{

  final serviceController = Get.find<Service>();
  var isLoading = false.obs;
  var isOverlayLoading = false.obs;
  var upcomingPostRideList = List<dynamic>.empty(growable: true).obs;
  var completedPostRideList = List<dynamic>.empty(growable: true).obs;
  var cancelledPostRideList = List<dynamic>.empty(growable: true).obs;
  late PageController pageController;
  var selectedTabIndex = 0.obs;
  var type = "upcoming".obs;
  ScrollController scrollController = ScrollController();
  late TabController tabController;
  var isScrollUpcomingLoading = false.obs;
  var upcomingPage = 1;
  var upcomingEnd = "".obs;
  var isScrollCompletedLoading = false.obs;
  var completedPage = 1;
  var completedEnd = "".obs;
  var isScrollCancelledLoading = false.obs;
  var cancelledPage = 1;
  var cancelledEnd = "".obs;
  var limit = 10;
  var labelTextDetail = {}.obs;

@override
  void onInit() async{
    // TODO: implement onInit
    super.onInit();
    tabController = TabController(length: 3, vsync: this);
    pageController = PageController(initialPage: selectedTabIndex.value);
    isLoading(true);
    await getPostRideList("upcoming");
    await getPostRideList("completed");
    await getPostRideList("cancelled");
    isLoading(false);
    paginatePostRideList();
  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();

    tabController.dispose();
    pageController.dispose();
    scrollController.dispose();

  }

  getPostRideList(status) async{
    try{

      await PostRideAgainProvider().getPostRideList(
          serviceController.token,
          status,
          status == "upcoming" ? upcomingPage : status == "completed" ? completedPage : cancelledPage,
          limit,
        serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['rides'] != null && resp['data']['rides']['data'] != null){
            if(status == "upcoming"){
              upcomingPostRideList.addAll(resp['data']['rides']['data']);
            }else if(status == "completed"){
              completedPostRideList.addAll(resp['data']['rides']['data']);
            }else if(status == "cancelled"){
              cancelledPostRideList.addAll(resp['data']['rides']['data']);
            }
          }

          if(resp['data'] != null && resp['data']['postRidePage'] != null){
            labelTextDetail.addAll(resp['data']['postRidePage']);
          }
        }

      },onError: (error){
        isLoading(false);
        serviceController.showDialogue(error.toString());

      });

    }catch (exception) {
      isLoading(false);
      serviceController.showDialogue(exception.toString());

    }
  }

  getMorePostRideList() async {
    try {
      type.value == "upcoming" ? isScrollUpcomingLoading(true) : type.value == "completed" ? isScrollCompletedLoading(true) : isScrollCancelledLoading(true);
      await PostRideAgainProvider().getPostRideList( serviceController.token, type.value,
          type.value == "upcoming" ? upcomingPage : type.value == "completed" ? completedPage : cancelledPage,
          limit,
          serviceController.langId.value).then((resp) async {
        if(resp['data']['rides']!= null && resp['data']['rides']['data'].isNotEmpty){

        }else{
          type.value == "upcoming" ? isScrollUpcomingLoading(false) : type.value == "completed" ? isScrollCompletedLoading(false) : isScrollCancelledLoading(false);
          type.value == "upcoming" ? upcomingEnd.value = "No more data found" : type.value == "completed" ? completedEnd.value = "No more data found": cancelledEnd.value = "No more data found";

        }
        if(resp['data']['rides'] != null && resp['data']['rides']['data'] != null){
          if(type.value == "upcoming"){
            upcomingPostRideList.addAll(resp['data']['rides']['data']);
          }else if(type.value == "completed"){
            completedPostRideList.addAll(resp['data']['rides']['data']);
          }else if(type.value == "cancelled"){
            cancelledPostRideList.addAll(resp['data']['rides']['data']);
          }
        }
        type.value == "upcoming" ? isScrollUpcomingLoading(false) : type.value == "completed" ? isScrollCompletedLoading(false) : isScrollCancelledLoading(false);
      }, onError: (error) {
        type.value == "upcoming" ? isScrollUpcomingLoading(false) : type.value == "completed" ? isScrollCompletedLoading(false) : isScrollCancelledLoading(false);
        serviceController.showDialogue(error.toString());

      });
    } catch (exception) {
      type.value == "upcoming" ? isScrollUpcomingLoading(false) : type.value == "completed" ? isScrollCompletedLoading(false) : isScrollCancelledLoading(false);
      serviceController.showDialogue(exception.toString());

    }
  }

  void paginatePostRideList() {
    scrollController.addListener(() async {
      if (scrollController.position.pixels ==
          scrollController.position.maxScrollExtent) {
        if(type.value == "upcoming" && upcomingEnd.value == ""){
          upcomingPage++;
          await getMorePostRideList();
        }else if(type.value == "completed" && completedEnd.value == ""){
          completedPage++;
          await getMorePostRideList();
        }else if(type.value == "cancelled" && cancelledEnd.value == ""){
          cancelledPage++;
          await getMorePostRideList();
        }
      }
    });
  }

  getTabIndex(index) async{
    selectedTabIndex.value = index;
    pageController.animateToPage(index, duration: const Duration(milliseconds: 2), curve: Curves.linear);
    if(index == 0){
      type.value = "upcoming";
    }else if(index == 1){
      type.value = "completed";
    }else if(index == 2){
      type.value = "cancelled";
    }
  }

  changeTabView(index) async{
    tabController.index = index;
    selectedTabIndex.value = index;
    if(index == 0){
      type.value = "upcoming";
    }else if(index == 1){
      type.value = "completed";
    }else if(index == 2){
      type.value = "cancelled";
    }
  }






}