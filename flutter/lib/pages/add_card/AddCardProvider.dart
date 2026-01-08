
import 'package:get/get.dart';

import '../../consts/const_api.dart';

class AddCardProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 180));


  Future addCard (token,cardName,cardNumber,cardType,month,year,cvvCode,address,primaryCard, tokenId) async {
    try {
      final data = FormData({});
      data.fields.add(MapEntry("name_on_card", cardName));
      data.fields.add(MapEntry("stripeToken", tokenId));
      data.fields.add(MapEntry("card_number", cardNumber));
      data.fields.add(MapEntry("card_type", cardType));
      data.fields.add(MapEntry("exp_month", month));
      data.fields.add(MapEntry("exp_year", year));
      data.fields.add(MapEntry("cvv_code", cvvCode));
      data.fields.add(MapEntry("address", address));
      data.fields.add(MapEntry("primary_card", primaryCard));
      final response = await getConnect.post(
          "$baseUrl/$paymentOptionsAddCard",
          data,
          headers: {
            'Authorization': 'Bearer $token',
            'X-Requested-With': 'XMLHttpRequest',
          }
      );
      if(response.status.hasError){
        if(response.status.code == 422){
          return response.body;
        }
        return Future.error(response.statusText as Object);
      }else{
        return response.body;
      }
    } catch (exception) {
      return Future.error(exception.toString());
    }
  }


}