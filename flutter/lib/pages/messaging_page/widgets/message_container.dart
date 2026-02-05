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
    from = "",
    to = "",
    date = "",
    rideTime = "",
    onTap}) {
  String tripDate = "";
  String tripTime = "";

  if (date != "") {
    DateTime parsedDate = DateTime.parse(date);
    DateFormat outputFormat = DateFormat('MMMM d, yyyy');
    tripDate = outputFormat.format(parsedDate);

    DateTime parsedTime = DateFormat("HH:mm:ss").parse(rideTime);
    DateFormat outputTimeFormat = DateFormat("h:mm a");
    tripTime = outputTimeFormat.format(parsedTime);
  }

  // Check if this is a ride details message
  bool isRideDetails = from != "";

  return InkWell(
    onTap: onTap,
    child: Container(
      decoration: BoxDecoration(
        // Use gradient for ride details, solid color for regular messages
        gradient: isRideDetails
            ? LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: msgType == 1
                    ? [
                        Color(0xFF2E7D32),
                        Color(0xFF4CAF50)
                      ] // Green gradient for sent ride details
                    : [
                        Color(0xFF1565C0),
                        Color(0xFF2196F3)
                      ], // Blue gradient for received ride details
              )
            : null,
        color: isRideDetails
            ? null
            : (msgType == 1 ? primaryColor : Colors.grey[200]),
        borderRadius: BorderRadius.only(
            bottomRight: msgType == 1
                ? const Radius.circular(0.0)
                : const Radius.circular(10.0),
            bottomLeft: msgType == 1
                ? const Radius.circular(10.0)
                : const Radius.circular(0.0),
            topLeft: const Radius.circular(10.0),
            topRight: const Radius.circular(10.0)),
        // Add shadow for ride details
        boxShadow: isRideDetails
            ? [
                BoxShadow(
                  color: Colors.black.withOpacity(0.1),
                  blurRadius: 8,
                  offset: Offset(0, 2),
                ),
              ]
            : null,
      ),
      padding: EdgeInsets.all(getValueForScreenType<double>(
        context: context,
        mobile: isRideDetails ? 16.0 : 10.0,
        tablet: isRideDetails ? 16.0 : 10.0,
      )),
      constraints:
          BoxConstraints(maxWidth: isRideDetails ? 350 : 300, minWidth: 100),
      child: Column(
        crossAxisAlignment:
            msgType == 1 ? CrossAxisAlignment.end : CrossAxisAlignment.start,
        children: [
          if (from == "") ...[
            // Regular message UI (unchanged)
            txt20Size(
                title: message.toString(),
                fontFamily: bold,
                context: context,
                textColor: msgType == 1 ? Colors.white : Colors.black),
            2.heightBox,
            txt20Size(
                title: time,
                fontFamily: regular,
                context: context,
                textColor: msgType == 1 ? Colors.white : Colors.black),
          ] else ...[
            // Enhanced Ride Details UI
            Container(
              padding: EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.1),
                borderRadius: BorderRadius.circular(8),
                border: Border.all(
                  color: Colors.white.withOpacity(0.2),
                  width: 1,
                ),
              ),
              child: Column(
                children: [
                  // Header with icon
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(
                        Icons.directions_car,
                        color: Colors.white,
                        size: 24,
                      ),
                      8.widthBox,
                      txt20Size(
                          title: "Ride Details",
                          fontFamily: bold,
                          context: context,
                          textColor: Colors.white),
                    ],
                  ),

                  8.heightBox,

                  // From location
                  _buildLocationRow(
                    icon: Icons.my_location,
                    label: "From",
                    location: from.toString(),
                    context: context,
                  ),

                  8.heightBox,

                  // 8.heightBox,

                  // To location
                  _buildLocationRow(
                    icon: Icons.location_on,
                    label: "To",
                    location: to.toString(),
                    context: context,
                  ),

                  16.heightBox,

                  // Date and time container
                  Container(
                    padding: EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.15),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(
                          Icons.schedule,
                          color: Colors.white,
                          size: 16,
                        ),
                        6.widthBox,
                        txt20Size(
                          title: tripDate,
                          context: context,
                          textColor: Colors.white,
                        ),
                        8.widthBox,
                        Container(
                          width: 1,
                          height: 12,
                          color: Colors.white.withOpacity(0.5),
                        ),
                        8.widthBox,
                        txt20Size(
                          title: tripTime,
                          context: context,
                          textColor: Colors.white,
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),

            // 12.heightBox,

            // // Timestamp
            // Align(
            //   child: txt14Size(
            //       title: time,
            //       fontFamily: regular,
            //       context: context,
            //       textColor: Colors.white70),
            // ),
          ]
        ],
      ),
    ),
  );
}

// Helper widget for location rows
Widget _buildLocationRow({
  required IconData icon,
  required String label,
  required String location,
  required BuildContext context,
}) {
  return Row(
    crossAxisAlignment: CrossAxisAlignment.start,
    children: [
      Container(
        padding: EdgeInsets.all(6),
        decoration: BoxDecoration(
          color: Colors.white.withOpacity(0.2),
          borderRadius: BorderRadius.circular(6),
        ),
        child: Icon(
          icon,
          color: Colors.white,
          size: 16,
        ),
      ),
      12.widthBox,
      Expanded(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            txt20Size(
                title: label, context: context, textColor: Colors.white70),
            2.heightBox,
            txt20Size(
                title: location,
                fontFamily: bold,
                context: context,
                textColor: Colors.white),
          ],
        ),
      ),
    ],
  );
}
