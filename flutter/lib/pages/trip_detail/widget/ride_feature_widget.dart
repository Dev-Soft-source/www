import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/card_shadow_widget.dart';
import 'package:proximaride_app/pages/widgets/circle_icon_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';

class RideFeatureWidget extends StatefulWidget {
  final context;
  final featureList;
  final rideDetail;
  final double screenWidth;
  final String heading;

  const RideFeatureWidget({
    Key? key,
    required this.context,
    required this.featureList,
    required this.rideDetail,
    this.screenWidth = 0.0,
    this.heading = "Ride features",
  }) : super(key: key);

  @override
  State<RideFeatureWidget> createState() => _RideFeatureWidgetState();
}

class _RideFeatureWidgetState extends State<RideFeatureWidget> {
  final Map<int, GlobalKey<TooltipState>> _tooltipKeys = {};
  final Map<int, bool> _tooltipVisibility = {};
  final GlobalKey<TooltipState> _luggageTooltipKey = GlobalKey<TooltipState>();
  bool _luggageTooltipVisible = false;

  void _toggleTooltip(int index) {
    setState(() {
      _tooltipVisibility[index] = !(_tooltipVisibility[index] ?? false);
      if (_tooltipVisibility[index] == true) {
        _tooltipKeys[index]?.currentState?.ensureTooltipVisible();
      } else {
        _tooltipKeys[index]?.currentState?.deactivate();
      }
    });
  }

  void _toggleLuggageTooltip() {
    setState(() {
      _luggageTooltipVisible = !_luggageTooltipVisible;
      if (_luggageTooltipVisible) {
        _luggageTooltipKey.currentState?.ensureTooltipVisible();
      } else {
        _luggageTooltipKey.currentState?.deactivate();
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return cardShadowWidget(
        context: widget.context,
        widgetChild: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            postRideWidget(
                title: widget.heading, screenWidth: widget.screenWidth, context: widget.context),
            Container(
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
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    if (widget.featureList.isNotEmpty) ...[
                      for (var i = 0; i < widget.featureList.length; i++) ...[
                        (widget.featureList[i]['title'] == 'Pink rides' ||
                                widget.featureList[i]['title'] == 'Extra-care rides')
                            ? Builder(
                                builder: (context) {
                                  if (!_tooltipKeys.containsKey(i)) {
                                    _tooltipKeys[i] = GlobalKey<TooltipState>();
                                    _tooltipVisibility[i] = false;
                                  }
                                  return Tooltip(
                                    key: _tooltipKeys[i],
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
                                    message: widget.featureList[i]['tooltip'],
                                    textStyle: const TextStyle(
                                        fontSize: 20,
                                        color: Colors.white,
                                        fontFamily: carlito,
                                      ),
                                    showDuration: const Duration(days: 100),
                                    waitDuration: Duration.zero,
                                    child: GestureDetector(
                                      behavior: HitTestBehavior.opaque,
                                      onTap: () => _toggleTooltip(i),
                                      child: Row(
                                        mainAxisAlignment: MainAxisAlignment.start,
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          circleIconWidget(
                                              width: 25,
                                              height: 25,
                                              imagePath: widget.featureList[i]['image'],
                                              context: widget.context),
                                          10.widthBox,
                                          Expanded(
                                              child: txt20Size(
                                                  title: widget.featureList[i]['title'],
                                                  context: widget.context)),
                                          5.widthBox,
                                          GestureDetector(
                                            onTap: () => _toggleTooltip(i),
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
                                      ),
                                    ),
                                  );
                                },
                              )
                            : Row(
                                mainAxisAlignment: MainAxisAlignment.start,
                                crossAxisAlignment: CrossAxisAlignment.start,
                                // mainAxisSize: MainAxisSize.max,
                                children: [
                                  circleIconWidget(
                                      width: 25,
                                      height: 25,
                                      imagePath: widget.featureList[i]['image'],
                                      context: widget.context),
                                  10.widthBox,
                                  Expanded(
                                    child: Padding(
                                      padding: const EdgeInsets.only(top: 2.0),
                                      child: txt18Size(
                                          title: widget.featureList[i]['title'],
                                          context: widget.context),
                                    ),
                                  ),
                                ],
                              ),
                      5.heightBox,
                    ]
                  ],
                  if (widget.rideDetail['smoking_label'] != null) ...[
                    Row(
                      mainAxisAlignment: MainAxisAlignment.start,
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        circleIconWidget(
                            width: 25,
                            height: 25,
                            imagePath: widget.rideDetail['smoke_image'] ?? "",
                            context: widget.context),
                        10.widthBox,
                        Expanded(
                            child: txt18Size(
                                title: widget.rideDetail['smoking_label'], context: widget.context))
                      ],
                    ),
                    5.heightBox,
                  ],
                  if (widget.rideDetail['luggage'] != null) ...[
                    Tooltip(
                      key: _luggageTooltipKey,
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
                      message: "${widget.rideDetail['luggage_tooltip']}",
                      textStyle: const TextStyle(
                        fontSize: 20,
                        color: Colors.white,
                        fontFamily: carlito,
                      ),
                      showDuration: const Duration(days: 100),
                      waitDuration: Duration.zero,
                      child: GestureDetector(
                        behavior: HitTestBehavior.opaque,
                        onTap: _toggleLuggageTooltip,
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.start,
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            circleIconWidget(
                                width: 25,
                                height: 25,
                                imagePath: widget.rideDetail['luggage_image'] ?? "",
                                context: widget.context),
                            10.widthBox,
                            Expanded(
                              child: Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  txt18Size(
                                      title: widget.rideDetail['luggage'],
                                      context: widget.context),
                                  GestureDetector(
                                    onTap: _toggleLuggageTooltip,
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
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                    5.heightBox,
                  ],
                  if (widget.rideDetail['pets_label'] != null) ...[
                    Row(
                      mainAxisAlignment: MainAxisAlignment.start,
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        circleIconWidget(
                            width: 25,
                            height: 25,
                            imagePath:
                                widget.rideDetail['animal_friendly_image'] ?? "",
                            context: widget.context),
                        10.widthBox,
                        Expanded(
                            child: txt18Size(
                                title: widget.rideDetail['pets_label'],
                                context: widget.context))
                      ],
                    ),
                    5.heightBox,
                  ],
                ])),
        ],
      ));
  }
}

Widget rideFeatureWidget(
    {context,
    featureList,
    rideDetail,
    double screenWidth = 0.0,
    String heading = "Ride features"}) {
  return RideFeatureWidget(
    context: context,
    featureList: featureList,
    rideDetail: rideDetail,
    screenWidth: screenWidth,
    heading: heading,
  );
}
