import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/pages/payout_account/PayoutAccountProvider.dart';
import 'package:proximaride_app/services/service.dart';

class PayoutAccountController extends GetxController with GetTickerProviderStateMixin{

  late TextEditingController
  bankTitleTextEditingController,
  accountNumberTextEditingController,
  ibanTextEditingController,
  branchTextEditingController,
  branchAddressTextEditingController,
  branchNumberTextEditingController,
  institutionNumberTextEditingController,
  addressTextEditingController,
  userVerifyAmountTextEditingController,
  paypalEmailTextEditingController;

  final Map<String, FocusNode> focusNodes = {};
  var banks = List<dynamic>.empty(growable: true).obs;


  var isOverlayLoading = false.obs;
  var isLoading = false.obs;

  final secureStorage = const FlutterSecureStorage();
  final serviceController = Get.find<Service>();

  var errorList = List.empty(growable: true).obs;
  final errors = [].obs;


  var bankId = "".obs;
  var setDefault = "".obs;
  var mainPageIndex = 0.obs;

  var bankBtnText = 0.obs;
  var paypalBtnText = 0.obs;
  var readOnly = false.obs;
  var bankStatus = "".obs;
  late TabController tabController;
  late PageController pageController;
  var labelTextDetail = {}.obs;
  var validationMessageDetail = {}.obs;




  @override
  void onInit() async{
    // TODO: implement onInit
    super.onInit();
    bankTitleTextEditingController = TextEditingController();
    accountNumberTextEditingController = TextEditingController();
    ibanTextEditingController = TextEditingController();
    branchTextEditingController = TextEditingController();
    addressTextEditingController = TextEditingController();
    userVerifyAmountTextEditingController = TextEditingController();
    paypalEmailTextEditingController = TextEditingController();
    branchAddressTextEditingController = TextEditingController();
    branchNumberTextEditingController = TextEditingController();
    institutionNumberTextEditingController = TextEditingController();
    tabController = TabController(length: 2, vsync: this);
    pageController = PageController(initialPage: mainPageIndex.value);


    for (int i = 1; i <= 9; i++) {
      focusNodes[i.toString()] = FocusNode();
      // Attach the onFocusChange listener
      focusNodes[i.toString()]?.addListener(() {
        if (!focusNodes[i.toString()]!.hasFocus) {
          // Field has lost focus, trigger validation
          if (i == 1) {
            validateField('Branch', 'branch', branchTextEditingController.text);
          } else if (i == 2) {
            validateField('Institution number', 'institution_number', institutionNumberTextEditingController.text, type: "numeric");
          } else if (i == 3) {
            validateField('Branch address', 'branch_address', branchAddressTextEditingController.text);
          } else if (i == 4) {
            validateField('Branch number', 'branch_number', branchNumberTextEditingController.text, type: 'numeric');
          } else if (i == 5) {
            validateField('Bank title', 'account_holder_name', bankTitleTextEditingController.text);
          } else if (i == 6) {
            validateField('Bank title', 'account_holder_number', accountNumberTextEditingController.text);
          } else if (i == 7) {
            validateField('Address', 'account_holder_address', addressTextEditingController.text);
          } else if (i == 8) {
            validateField('User verify amount', 'user_verify_amount', userVerifyAmountTextEditingController.text, type: 'numeric');
          } else if (i == 9) {
            validateField('Paypal email', 'paypal_email', paypalEmailTextEditingController.text, type: 'email');
          }
        }
      });
    }

    await getBanks();
  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();

    bankTitleTextEditingController.dispose();
    accountNumberTextEditingController.dispose();
    ibanTextEditingController.dispose();
    branchTextEditingController.dispose();
    addressTextEditingController.dispose();
    userVerifyAmountTextEditingController.dispose();
    paypalEmailTextEditingController.dispose();
    branchAddressTextEditingController.dispose();
    branchNumberTextEditingController.dispose();
    institutionNumberTextEditingController.dispose();

  }

  void validateField(String fieldData, String fieldName, String fieldValue, {String type = 'string', bool isRequired = true, int wordsLimit = 50}) {
    errors.removeWhere((element) => element['title'] == fieldName);
    List<String> errorList = [];

    if (isRequired && fieldValue.isEmpty) {
      var message = validationMessageDetail['required'];
      if(fieldName == "bank_name"){
        message = message.replaceAll(":Attribute", labelTextDetail['bank_title_error'] ?? fieldData);
      }else if(fieldName == "acc_no"){
        message = message.replaceAll(":Attribute", labelTextDetail['acc_no_error'] ?? fieldData);
      }else if(fieldName == "branch"){
        message = message.replaceAll(":Attribute", labelTextDetail['branch_error'] ?? fieldData);
      }else if(fieldName == "address"){
        message = message.replaceAll(":Attribute", labelTextDetail['address_error'] ?? fieldData);
      }else if(fieldName == "user_verify_amount"){
        message = message.replaceAll(":Attribute", labelTextDetail['verify_amount_error'] ?? fieldData);
      }else if(fieldName == "paypal_email"){
        message = message.replaceAll(":Attribute", labelTextDetail['paypal_email_error'] ?? fieldData);
      }else if(fieldName == "branch_address"){
        message = message.replaceAll(":Attribute", labelTextDetail['branch_address'] ?? fieldData);
      }
      errorList.add(message ?? '$fieldData field is required');
      errors.add({
        'title': fieldName,
        'eList': errorList,
      });
      return;
    }

    switch (type) {

      case 'email':
        if (!isValidEmail(fieldValue)) {
          var message = validationMessageDetail['email'];
          errorList.add(message ?? 'Please use a valid email address, like: example@email.com');
        }
        break;
      case 'numeric':
        if (fieldValue.isNotEmpty && double.tryParse(fieldValue) == null) {
          var message = validationMessageDetail['required'];
          if(fieldName == "user_verify_amount"){
            message = message.replaceAll(":attribute", labelTextDetail['verify_amount_error'] ?? fieldData);
          }else if(fieldName == "acc_no"){
            message = message.replaceAll(":attribute", labelTextDetail['acc_no_error'] ?? fieldData);
          }

          errorList.add(message ?? '$fieldName must be a number');
        }
        break;
      case 'date':
        if (fieldValue.isNotEmpty && DateTime.tryParse(fieldValue) == null) {
          errorList.add('$fieldName must be a valid date');
        }
        break;
      case 'time':
        if (fieldValue.isNotEmpty && !RegExp(r'^\d{2}:\d{2}$').hasMatch(fieldValue)) {
          errorList.add('$fieldName must be in the format HH:MM');
        }
        break;
      case 'max_words':
        if (fieldValue.isNotEmpty && fieldValue.split(' ').length > wordsLimit) {
          errorList.add('$fieldName must have at most $wordsLimit words');
        }
        break;
      default:
        break;
    }

    if (errorList.isNotEmpty) {
      errors.add({
        'title': fieldName,
        'eList': errorList,
      });
    }
    update();
  }
  bool isValidEmail(String email) {
    // Regular expression for email validation
    final RegExp emailRegExp = RegExp(r'^[a-zA-Z0-9._-]+@[a-zA-Z0-9]+\.[a-zA-Z]{2,}$');
    return emailRegExp.hasMatch(email);
  }

  getBanks() async{
    try{
      isLoading(true);
      PayoutAccountProvider().getBanks(
          serviceController.token,
        serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['banks'] != null){
            banks.addAll(resp['data']['banks']);
          }

          if(resp['data'] != null && resp['data']['payoutOptionPage'] != null){
            labelTextDetail.addAll(resp['data']['payoutOptionPage']);
          }

          if(resp['data'] != null && resp['data']['validationMessages'] != null){
            validationMessageDetail.addAll(resp['data']['validationMessages']);
          }

          if(resp['data'] != null && resp['data']['userBankDetail'] != null){
            bankBtnText.value = resp['data']['userBankDetail']['bank_id'].toString() != "" ? 1 : 0;
            bankId.value = resp['data']['userBankDetail']['bank_id'].toString();
            bankTitleTextEditingController.text = resp['data']['userBankDetail']['bank_title'] ?? "";
            accountNumberTextEditingController.text = resp['data']['userBankDetail']['acc_no'] ?? "";
            ibanTextEditingController.text = resp['data']['userBankDetail']['iban'] ?? "";
            branchTextEditingController.text = resp['data']['userBankDetail']['branch'] ?? "";
            addressTextEditingController.text = resp['data']['userBankDetail']['address'] ?? "";
            branchNumberTextEditingController.text = resp['data']['userBankDetail']['branch_number'] ?? "";
            branchAddressTextEditingController.text = resp['data']['userBankDetail']['branch_address'] ?? "";
            institutionNumberTextEditingController.text = resp['data']['userBankDetail']['institution_number'] ?? "";
            setDefault.value = resp['data']['userBankDetail']['set_default'] ?? "";
            paypalEmailTextEditingController.text = resp['data']['userBankDetail']['paypal_email'] ?? "";
            paypalBtnText.value = resp['data']['userBankDetail']['paypal_email'] != null ? 1 : 0;
            bankStatus.value = resp['data']['userBankDetail']['status'] ?? "pending";
          }

        }
        isLoading(false);
      },onError: (err){
        isLoading(false);
        serviceController.showDialogue(err.toString());
      });
    }catch (exception) {
      serviceController.showDialogue(exception.toString());
    }
  }



  updateBankDetail() async{
    try{

      errors.clear();

      if (bankId.value.isEmpty) {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute", labelTextDetail['bank_error'] ?? 'Bank name');
        var err = {
          'title': "bank_name",
          'eList' : [message ?? 'Bank field is required']
        };
        errors.add(err);
      }

      if (branchTextEditingController.text.isEmpty) {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute", labelTextDetail['branch_error'] ?? 'Branch');
        var err = {
          'title': "branch",
          'eList' : [message ?? 'Branch field is required']
        };
        errors.add(err);
      }

      if (institutionNumberTextEditingController.text.isEmpty) {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute", labelTextDetail['institution_number_error'] ?? 'Institution number');
        var err = {
          'title': "institution_number",
          'eList' : [message ?? 'Institution number field is required']
        };
        errors.add(err);
      }

      if (branchAddressTextEditingController.text.isEmpty) {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute", labelTextDetail['branch_address'] ?? 'Branch address');
        var err = {
          'title': "branch_address",
          'eList' : [message ?? 'Branch address field is required']
        };
        errors.add(err);
      }

      if (branchNumberTextEditingController.text.isEmpty) {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute", labelTextDetail['branch_number_error'] ?? 'Branch number');
        var err = {
          'title': "branch_number",
          'eList' : [message ?? 'Branch number field is required']
        };
        errors.add(err);
      }

      if (bankTitleTextEditingController.text.isEmpty) {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute", labelTextDetail['bank_title_error'] ?? 'Bank title');
        var err = {
          'title': "account_holder_name",
          'eList' : [message ?? 'Bank title field is required']
        };
        errors.add(err);
      }

      if (accountNumberTextEditingController.text.isEmpty) {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute", labelTextDetail['acc_no_error'] ?? 'Account number');
        var err = {
          'title': "account_holder_number",
          'eList' : [message ?? 'Account number field is required']
        };
        errors.add(err);
      }

      if (addressTextEditingController.text.isEmpty) {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute", labelTextDetail['address_error'] ?? 'Address');
        var err = {
          'title': "account_holder_address",
          'eList' : [message ?? 'Address field is required']
        };
        errors.add(err);
      }
     if(errors.isNotEmpty){
       return;
     }
      isOverlayLoading(true);
      PayoutAccountProvider().updateBankDetail(
          bankId.value,
          bankTitleTextEditingController.text,
          accountNumberTextEditingController.text,
          ibanTextEditingController.text,
          branchTextEditingController.text,
          addressTextEditingController.text,
          setDefault.value,
          serviceController.token,
          serviceController.loginUserDetail['id']
        ).then((resp) async {
        errorList.clear();
        if(resp['status'] != null && resp['status'] == "Error"){
          serviceController.showDialogue(resp['message'].toString());
        }else if(resp['errors'] != null){
          if(resp['errors']['bank_name'] != null ){
            var err = {
              'title': "bank_name",
              'eList' : resp['errors']['bank_name']
            };
            errors.add(err);

          }if(resp['errors']['account_holder_name'] != null ){
            var err = {
              'title': "account_holder_name",
              'eList' : resp['errors']['account_holder_name']
            };
            errors.add(err);
          }if(resp['errors']['account_holder_number'] != null ){
            var err = {
              'title': "account_holder_number",
              'eList' : resp['errors']['account_holder_number']
            };
            errors.add(err);
          }if(resp['errors']['branch'] != null ){
            var err = {
              'title': "branch",
              'eList' : resp['errors']['branch']
            };
            errors.add(err);
          }if(resp['errors']['account_holder_address'] != null ){
            var err = {
              'title': "account_holder_address",
              'eList' : resp['errors']['account_holder_address']
            };
            errors.add(err);
          }if(resp['errors']['branch_number'] != null ){
            var err = {
              'title': "branch_number",
              'eList' : resp['errors']['branch_number']
            };
            errors.add(err);
          }if(resp['errors']['branch_address'] != null ){
            var err = {
              'title': "branch_address",
              'eList' : resp['errors']['branch_address']
            };
            errors.add(err);
          }if(resp['errors']['institution_number'] != null ){
            var err = {
              'title': "institution_number",
              'eList' : resp['errors']['institution_number']
            };
            errors.add(err);
          }
        }else if(resp['status'] != null && resp['status'] == "Success"){
          serviceController.showDialogue(resp['message'].toString());
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


  updatePageIndexValue(index) async{
    mainPageIndex.value = index;
  }

  updatePaypalDetail() async{
    try{

      errors.clear();
      if (paypalEmailTextEditingController.text.isEmpty) {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute", labelTextDetail['paypal_email_error'] ?? 'Paypal email');
        var err = {
          'title': "paypal_email",
          'eList' : [message ?? 'Paypal email field is required']
        };
        errors.add(err);
      }
      if(errors.isNotEmpty){
        return;
      }
      isOverlayLoading(true);
      PayoutAccountProvider().updatePaypalDetail(
          paypalEmailTextEditingController.text,
          setDefault.value,
          serviceController.token,
          serviceController.loginUserDetail['id']
      ).then((resp) async {
        errorList.clear();
        if(resp['status'] != null && resp['status'] == "Error"){
          serviceController.showDialogue(resp['message'].toString());
        }else if(resp['errors'] != null){
          if(resp['errors']['paypal_email'] != null ){
            var err = {
              'title': "paypal_email",
              'eList' : resp['errors']['paypal_email']
            };
            errors.add(err);

          }
        }else if(resp['status'] != null && resp['status'] == "Success"){
          serviceController.showDialogue(resp['message'].toString());
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

  verifyBank() async{
    try{

      errors.clear();
      if (userVerifyAmountTextEditingController.text.isEmpty) {

        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute", labelTextDetail['verify_amount_error'] ?? 'Amount');
        var err = {
          'title': "user_verify_amount",
          'eList' : [message ?? 'Amount field is required']
        };
        errors.add(err);
      }
      if(errors.isNotEmpty){
        return;
      }
      isOverlayLoading(true);
      PayoutAccountProvider().verifyBank(
          userVerifyAmountTextEditingController.text,
          serviceController.token,
          serviceController.loginUserDetail['id']
      ).then((resp) async {
        errorList.clear();
        if(resp['status'] != null && resp['status'] == "Error"){
          serviceController.showDialogue(resp['message'].toString());
        }else if(resp['errors'] != null){
          if(resp['errors']['user_verify_amount'] != null ){
            var err = {
              'title': "user_verify_amount",
              'eList' : resp['errors']['user_verify_amount']
            };
            errors.add(err);

          }
        }else if(resp['status'] != null && resp['status'] == "Success"){
          serviceController.showDialogue(resp['message'].toString());

          if(resp['data'] != null && resp['data']['bankDetail'] != null){
            bankStatus.value = resp['data']['bankDetail']['status'] ?? "pending";
          }
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