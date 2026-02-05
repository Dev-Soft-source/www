import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/card_shadow_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';

class PaymentOptionWidget extends StatefulWidget {
  final context;
  final String payment;
  final double screenWidth;
  final String toolTipMessage;
  final String heading;

  const PaymentOptionWidget({
    Key? key,
    required this.context,
    this.payment = "",
    this.screenWidth = 0.0,
    this.toolTipMessage = "",
    this.heading = "Payment method",
  }) : super(key: key);

  @override
  State<PaymentOptionWidget> createState() => _PaymentOptionWidgetState();
}

class _PaymentOptionWidgetState extends State<PaymentOptionWidget> {
  final GlobalKey<TooltipState> _tooltipKey = GlobalKey<TooltipState>();
  bool _isTooltipVisible = false;

  void _toggleTooltip() {
    setState(() {
      _isTooltipVisible = !_isTooltipVisible;
      if (_isTooltipVisible) {
        _tooltipKey.currentState?.ensureTooltipVisible();
      } else {
        _tooltipKey.currentState?.deactivate();
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return cardShadowWidget(
        context: widget.context,
        widgetChild: Column(
          children: [
            postRideWidget(
                title: widget.heading, screenWidth: widget.screenWidth, context: widget.context),
            Tooltip(
              key: _tooltipKey,
              margin: EdgeInsets.fromLTRB(
                  getValueForScreenType<double>(
                    context: widget.context,
                    mobile: 15.0,
                    tablet: 15.0,
                  ),
                  getValueForScreenType<double>(
                    context: widget.context,
                    mobile: 0.0,
                    tablet: 0.0,
                  ),
                  getValueForScreenType<double>(
                    context: widget.context,
                    mobile: 15.0,
                    tablet: 15.0,
                  ),
                  getValueForScreenType<double>(
                    context: widget.context,
                    mobile: 0.0,
                    tablet: 0.0,
                  )),
              triggerMode: TooltipTriggerMode.manual,
              message: widget.toolTipMessage,
              textStyle: const TextStyle(fontSize: 20, color: Colors.white),
              showDuration: const Duration(days: 100),
              waitDuration: Duration.zero,
              child: Container(
                  padding: EdgeInsets.fromLTRB(
                      getValueForScreenType<double>(
                        context: widget.context,
                        mobile: 15.0,
                        tablet: 15.0,
                      ),
                      getValueForScreenType<double>(
                        context: widget.context,
                        mobile: 10.0,
                        tablet: 10.0,
                      ),
                      getValueForScreenType<double>(
                        context: widget.context,
                        mobile: 15.0,
                        tablet: 15.0,
                      ),
                      getValueForScreenType<double>(
                        context: widget.context,
                        mobile: 10.0,
                        tablet: 10.0,
                      )),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      txt20Size(title: widget.payment, context: widget.context),
                      GestureDetector(
                        onTap: _toggleTooltip,
                        child: Image.asset(
                          infoImage,
                          color: Colors.black,
                          width: getValueForScreenType<double>(
                            context: widget.context,
                            mobile: 20.0,
                            tablet: 20.0,
                          ),
                          height: getValueForScreenType<double>(
                            context: widget.context,
                            mobile: 20.0,
                            tablet: 20.0,
                          ),
                        ),
                      ),
                    ],
                  )),
            ),
          ],
        ));
  }
}

Widget paymentOptionWidget(
    {context,
    String payment = "",
    double screenWidth = 0.0,
    String toolTipMessage = "",
    String heading = "Payment method"}) {
  return PaymentOptionWidget(
    context: context,
    payment: payment,
    screenWidth: screenWidth,
    toolTipMessage: toolTipMessage,
    heading: heading,
  );
}
