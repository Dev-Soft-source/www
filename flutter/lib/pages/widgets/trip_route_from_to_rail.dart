import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import '../../consts/constFileLink.dart';

/// Shared origin/destination column: left rail (pickup [Icons.my_location] →
/// dashed line → destination pin) and right column (addresses + optional
/// subtitles). Used by trip cards and trip detail.
class TripRouteFromToRail extends StatelessWidget {
  const TripRouteFromToRail({
    super.key,
    required this.from,
    this.pickup = '',
    required this.to,
    this.dropOff = '',
    this.ringSize = 24.0,
    this.gapBeforeToBlock = 16.0,
    this.connectorGutterHeight = 36.0,
    this.showTopSpacer = true,
  });

  final String from;
  final String pickup;
  final String to;
  final String dropOff;
  final double ringSize;
  final double gapBeforeToBlock;
  final double connectorGutterHeight;
  final bool showTopSpacer;

  static const Color _ringBorder = Color(0xFFE5E7EB);
  static const Color _dashColor = Color.fromARGB(255, 114, 114, 114);

  @override
  Widget build(BuildContext context) {
    Widget ringedCircle({required Widget child}) {
      return Container(
        width: ringSize,
        height: ringSize,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          color: Colors.white,
          border: Border.all(color: _ringBorder, width: 1),
        ),
        alignment: Alignment.center,
        child: child,
      );
    }

    final pickupIcon = Container(
      width: ringSize,
      height: ringSize,
      decoration: const BoxDecoration(
        shape: BoxShape.circle,
        color: primaryColor,
      ),
      alignment: Alignment.center,
      child: const Icon(
        Icons.my_location,
        color: Colors.white,
        size: 17,
      ),
    );

    final destinationIcon = ringedCircle(
      child: Icon(Icons.location_on, color: Colors.black, size: 20),
    );

    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            if (showTopSpacer) 5.heightBox,
            pickupIcon,
            SizedBox(
              width: ringSize,
              height: connectorGutterHeight,
              child: CustomPaint(
                painter: _RouteVerticalDashesPainter(color: _dashColor),
              ),
            ),
            destinationIcon,
          ],
        ),
        10.widthBox,
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisSize: MainAxisSize.min,
                children: [
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Expanded(
                        child: txt16Size(
                          title: from,
                          context: context,
                          fontFamily: bold,
                        ),
                      ),
                    ],
                  ),
                  if (pickup.isNotEmpty)
                    txt14Size(
                      title: pickup,
                      context: context,
                      textColor: placeHolderColor,
                      fontFamily: regular,
                    ),
                ],
              ),
              SizedBox(height: gapBeforeToBlock),
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Expanded(
                    child: txt16Size(
                      title: to,
                      context: context,
                      fontFamily: bold,
                    ),
                  ),
                ],
              ),
              if (dropOff.isNotEmpty)
                txt14Size(
                  title: dropOff,
                  context: context,
                  textColor: placeHolderColor,
                  fontFamily: regular,
                ),
            ],
          ),
        ),
      ],
    );
  }
}

class _RouteVerticalDashesPainter extends CustomPainter {
  _RouteVerticalDashesPainter({required this.color});

  final Color color;

  static const double _dash = 4.0;
  static const double _gap = 3.0;

  @override
  void paint(Canvas canvas, Size size) {
    if (size.height <= 0 || size.width <= 0) return;
    final paint = Paint()
      ..color = color
      ..strokeWidth = 1.0
      ..strokeCap = StrokeCap.square;
    final double cx = size.width / 2;
    var y = 0.0;
    while (y < size.height) {
      final double remaining = size.height - y;
      final double seg = remaining < _dash ? remaining : _dash;
      canvas.drawLine(Offset(cx, y), Offset(cx, y + seg), paint);
      y += _dash + _gap;
    }
  }

  @override
  bool shouldRepaint(covariant _RouteVerticalDashesPainter oldDelegate) =>
      oldDelegate.color != color;
}
