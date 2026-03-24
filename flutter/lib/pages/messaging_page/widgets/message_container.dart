// import 'package:flutter/material.dart';
// import 'package:intl/intl.dart';
// import 'package:proximaride_app/consts/constFileLink.dart';
// import 'package:proximaride_app/pages/widgets/textWidget.dart';

// Widget messageContainer(
//     {context,
//     message = "N/A",
//     time = "12:00:00",
//     msgType = 0,
//     from = "",
//     to = "",
//     date = "",
//     rideTime = "",
//     onTap}) {
//   String tripDate = "";
//   String tripTime = "";

//   if (date != "") {
//     DateTime parsedDate = DateTime.parse(date);
//     DateFormat outputFormat = DateFormat('MMMM d, yyyy');
//     tripDate = outputFormat.format(parsedDate);

//     DateTime parsedTime = DateFormat("HH:mm:ss").parse(rideTime);
//     DateFormat outputTimeFormat = DateFormat("h:mm a");
//     tripTime = outputTimeFormat.format(parsedTime);
//   }

//   return InkWell(
//     onTap: onTap,
//     child: Container(
//       decoration: BoxDecoration(
//         color: msgType == 1 ? primaryColor : Colors.grey[200],
//         borderRadius: BorderRadius.only(
//             bottomRight: msgType == 1
//                 ? const Radius.circular(0.0)
//                 : const Radius.circular(10.0),
//             bottomLeft: msgType == 1
//                 ? const Radius.circular(10.0)
//                 : const Radius.circular(0.0),
//             topLeft: const Radius.circular(10.0),
//             topRight: const Radius.circular(10.0)),
//       ),
//       padding: EdgeInsets.all(getValueForScreenType<double>(
//         context: context,
//         mobile: 10.0,
//         tablet: 10.0,
//       )),
//       constraints: const BoxConstraints(maxWidth: 300, minWidth: 100),
//       child: Column(
//         crossAxisAlignment:
//             msgType == 1 ? CrossAxisAlignment.end : CrossAxisAlignment.start,
//         children: [
//           if (from == "") ...[
//             txt18Size(
//                 title: message.toString(),
//                 fontFamily: bold,
//                 context: context,
//                 textColor: msgType == 1 ? Colors.white : Colors.black),
//             2.heightBox,
//             txt14Size(
//                 title: time,
//                 fontFamily: regular,
//                 context: context,
//                 textColor: msgType == 1 ? Colors.white : Colors.black),
//           ] else ...[
//             Align(
//               alignment: Alignment.topCenter,
//               child: txt22Size(
//                   title: "Ride Details",
//                   fontFamily: bold,
//                   context: context,
//                   textColor: msgType == 1 ? Colors.white : Colors.black),
//             ),
//             2.heightBox,
//             Align(
//               alignment: Alignment.topLeft,
//               child: txt18Size(
//                   title: "From: ${from.toString()}",
//                   fontFamily: bold,
//                   context: context,
//                   textColor: msgType == 1 ? Colors.white : Colors.black),
//             ),
//             2.heightBox,
//             Align(
//               alignment: Alignment.topLeft,
//               child: txt18Size(
//                   title: "To: ${to.toString()}",
//                   fontFamily: bold,
//                   context: context,
//                   textColor: msgType == 1 ? Colors.white : Colors.black),
//             ),
//             2.heightBox,
//             Align(
//               alignment: Alignment.topLeft,
//               child: Row(
//                 mainAxisAlignment: MainAxisAlignment.start,
//                 children: [
//                   txt16Size(
//                       title: tripDate,
//                       context: context,
//                       textColor: Colors.white),
//                   3.widthBox,
//                   txt16Size(
//                       title: "at", context: context, textColor: Colors.white),
//                   3.widthBox,
//                   txt16Size(
//                       title: tripTime,
//                       context: context,
//                       textColor: Colors.white),
//                 ],
//               ),
//             ),
//             2.heightBox,
//             txt14Size(
//                 title: time,
//                 fontFamily: regular,
//                 context: context,
//                 textColor: msgType == 1 ? Colors.white : Colors.black),
//           ]
//         ],
//       ),
//     ),
//   );
// }
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

Widget messageContainer(
    {context,
    message = "N/A",
    time = "12:00:00",
    msgType = 0,
    deliveryStatus = "sent",
    from = "",
    to = "",
    date = "",
    rideTime = "",
    onTap}) {
  String tripDate = "";
  String tripTime = "";

  final dateValue = date?.toString() ?? "";
  final rideTimeValue = rideTime?.toString() ?? "";

  if (dateValue.isNotEmpty) {
    final parsedDate = DateTime.tryParse(dateValue);
    if (parsedDate != null) {
      DateFormat outputFormat = DateFormat('MMMM d, yyyy');
      tripDate = outputFormat.format(parsedDate);
    }
  }

  if (rideTimeValue.isNotEmpty) {
    final parsedTime = DateFormat("HH:mm:ss").tryParse(rideTimeValue);
    if (parsedTime != null) {
      DateFormat outputTimeFormat = DateFormat("h:mm a");
      tripTime = outputTimeFormat.format(parsedTime);
    }
  }

  // Check if this is a ride details message
  bool isRideDetails = from != "";

  final bubbleRadius = BorderRadius.only(
    topLeft: const Radius.circular(22),
    topRight: const Radius.circular(22),
    bottomLeft:
        msgType == 1 ? const Radius.circular(22) : const Radius.circular(6),
    bottomRight:
        msgType == 1 ? const Radius.circular(6) : const Radius.circular(22),
  );

  return InkWell(
    onTap: onTap,
    borderRadius: bubbleRadius,
    child: Column(
      crossAxisAlignment:
          msgType == 1 ? CrossAxisAlignment.end : CrossAxisAlignment.start,
      children: [
        Container(
          decoration: BoxDecoration(
            gradient: isRideDetails
                ? LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: msgType == 1
                        ? const [Color(0xFF2563EB), Color(0xFF3B82F6)]
                        : const [Color(0xFFCBD5E1), Color(0xFFE2E8F0)],
                  )
                : null,
            color: isRideDetails
                ? null
                : (msgType == 1 ? const Color(0xFF3B82F6) : const Color(0xFFE2E8F0)),
            borderRadius: bubbleRadius,
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(msgType == 1 ? 0.10 : 0.06),
                blurRadius: 18,
                offset: const Offset(0, 8),
              ),
            ],
          ),
          padding: EdgeInsets.all(getValueForScreenType<double>(
            context: context,
            mobile: isRideDetails ? 16.0 : 14.0,
            tablet: isRideDetails ? 18.0 : 16.0,
          )),
          constraints:
              BoxConstraints(maxWidth: isRideDetails ? 360 : 300, minWidth: 96),
          child: Column(
            crossAxisAlignment:
                msgType == 1 ? CrossAxisAlignment.end : CrossAxisAlignment.start,
            children: [
              if (!isRideDetails) ...[
                Text(
                  message.toString(),
                  style: TextStyle(
                    color: msgType == 1 ? Colors.white : const Color(0xFF1E293B),
                    fontSize: 16,
                    fontWeight: FontWeight.w500,
                    height: 1.45,
                    fontFamily: regular,
                  ),
                ),
              ] else ...[
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(msgType == 1 ? 0.12 : 0.42),
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(
                      color: Colors.white.withOpacity(msgType == 1 ? 0.18 : 0.55),
                    ),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(
                            Icons.directions_car_rounded,
                            color: msgType == 1 ? Colors.white : const Color(0xFF2563EB),
                            size: 18,
                          ),
                          8.widthBox,
                          Text(
                            "Ride Details",
                            style: TextStyle(
                              color: msgType == 1 ? Colors.white : const Color(0xFF0F172A),
                              fontSize: 16,
                              fontWeight: FontWeight.w700,
                              fontFamily: bold,
                            ),
                          ),
                        ],
                      ),
                      12.heightBox,
                      _buildLocationRow(
                        icon: Icons.my_location_rounded,
                        label: "From",
                        location: from.toString(),
                        context: context,
                        isSent: msgType == 1,
                      ),
                      10.heightBox,
                      _buildLocationRow(
                        icon: Icons.location_on_rounded,
                        label: "To",
                        location: to.toString(),
                        context: context,
                        isSent: msgType == 1,
                      ),
                      14.heightBox,
                      Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 12, vertical: 8),
                        decoration: BoxDecoration(
                          color:
                              Colors.white.withOpacity(msgType == 1 ? 0.14 : 0.5),
                          borderRadius: BorderRadius.circular(999),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(
                              Icons.schedule_rounded,
                              color: msgType == 1
                                  ? Colors.white
                                  : const Color(0xFF2563EB),
                              size: 16,
                            ),
                            6.widthBox,
                            Text(
                              "$tripDate • $tripTime",
                              style: TextStyle(
                                color: msgType == 1
                                    ? Colors.white
                                    : const Color(0xFF1E293B),
                                fontSize: 13,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ],
          ),
        ),
        6.heightBox,
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 6),
          child: Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                time.toString(),
                style: TextStyle(
                  color: msgType == 1
                      ? const Color(0xFF3B82F6)
                      : const Color(0xFF94A3B8),
                  fontSize: 12,
                  fontWeight: FontWeight.w500,
                  fontFamily: regular,
                ),
              ),
              if (msgType == 1 && deliveryStatus.toString() == 'sent') ...[
                4.widthBox,
                const Icon(
                  Icons.done_rounded,
                  size: 14,
                  color: Color(0xFF3B82F6),
                ),
              ],
            ],
          ),
        ),
      ],
    ),
  );
}

// Helper widget for location rows
Widget _buildLocationRow({
  required IconData icon,
  required String label,
  required String location,
  required BuildContext context,
  required bool isSent,
}) {
  return Row(
    crossAxisAlignment: CrossAxisAlignment.start,
    children: [
      Container(
        padding: EdgeInsets.all(6),
        decoration: BoxDecoration(
          color: Colors.white.withOpacity(isSent ? 0.2 : 0.55),
          borderRadius: BorderRadius.circular(6),
        ),
        child: Icon(
          icon,
          color: isSent ? Colors.white : const Color(0xFF2563EB),
          size: 16,
        ),
      ),
      12.widthBox,
      Expanded(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              label,
              style: TextStyle(
                fontSize: 12,
                color: isSent ? Colors.white70 : const Color(0xFF64748B),
                fontWeight: FontWeight.w600,
              ),
            ),
            2.heightBox,
            Text(
              location,
              style: TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w700,
                fontFamily: bold,
                color: isSent ? Colors.white : const Color(0xFF0F172A),
              ),
            ),
          ],
        ),
      ),
    ],
  );
}
