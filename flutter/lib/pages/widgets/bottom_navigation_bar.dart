import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';

class BottomNavigationBarScaffold extends StatefulWidget {
  final Widget child;
  const BottomNavigationBarScaffold({super.key, required this.child});

  @override
  State<BottomNavigationBarScaffold> createState() => _BottomNavigationBarScaffoldState();
}

class _BottomNavigationBarScaffoldState extends State<BottomNavigationBarScaffold> {
  int currentIndex = 2;
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: widget.child,
      bottomNavigationBar: BottomNavigationBar(
        onTap: (value){
          // switch(value){
          //   case 0:
          //     context.go('/chats');
          //     break;
          //   case 1:
          //     context.go('/my_trips');
          //     break;
          //   case 2:
          //     context.go('/my_profile');
          //     break;
          //   default:
          //     context.go('/my_profile');
          //     break;
          // }
          setState(() {
            currentIndex = value;
          });
        },
        backgroundColor: Colors.grey.shade100,
        currentIndex: currentIndex,
        unselectedLabelStyle: const TextStyle(
          fontSize: 18,
          fontFamily: bold,
        ),
        selectedLabelStyle: const TextStyle(
          fontSize: 18,
          fontFamily: bold
        ),
        selectedItemColor: primaryColor,
        unselectedItemColor: Colors.grey.shade400,
        items: [
          BottomNavigationBarItem(icon: Image.asset(chatOutLineImage, width: 35, height: 35), activeIcon: Image.asset(chatFillImage, width: 35, height: 35), label: 'Chats'),
          BottomNavigationBarItem(icon: Image.asset(myTripsOutlineImage, width: 35, height: 35), activeIcon: Image.asset(myTripsFillImage, width: 35, height: 35), label: 'My Trips'),
          BottomNavigationBarItem(icon: Image.asset(profileOutLineImage, width: 35, height: 35), activeIcon: Image.asset(profileFillImage, width: 35, height: 35), label: 'My Profile'),
        ],
      ),
    );
  }
}
