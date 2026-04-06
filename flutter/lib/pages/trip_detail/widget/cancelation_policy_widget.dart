import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/card_shadow_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';
import 'package:url_launcher/url_launcher.dart';

class CancellationPolicyWidget extends StatefulWidget {
  final context;
  final String policyType;
  final double screenWidth;
  final int policyRate;
  final String heading;
  final String bookingTypeSlug;
  final String bookingTypeToolTip;
  final String discountLabel;
  final String cancellationPolicyUrl;

  const CancellationPolicyWidget({
    Key? key,
    required this.context,
    this.policyType = "",
    this.screenWidth = 0.0,
    this.policyRate = 0,
    this.heading = "Cancellation policy",
    this.bookingTypeSlug = "",
    this.bookingTypeToolTip = "",
    this.discountLabel = "discount",
    this.cancellationPolicyUrl = "",
  }) : super(key: key);

  @override
  State<CancellationPolicyWidget> createState() => _CancellationPolicyWidgetState();
}

class _CancellationPolicyWidgetState extends State<CancellationPolicyWidget> {
  final GlobalKey<TooltipState> _tooltipKey = GlobalKey<TooltipState>();
  bool _isTooltipVisible = false;

  Future<void> _openCancellationPolicy() async {
    try {
      final Uri url = Uri.parse(widget.cancellationPolicyUrl);
      final bool opened = await launchUrl(
        url,
        mode: LaunchMode.externalApplication,
      );

      if (!opened && mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Could not open cancellation policy')),
        );
      }
    } catch (_) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Could not open cancellation policy')),
        );
      }
    }
  }

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
          mainAxisAlignment: MainAxisAlignment.start,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            postRideWidget(
                title: widget.heading,
                screenWidth: widget.screenWidth,
                context: widget.context,
                bgColor: widget.bookingTypeSlug == "firm" ? Colors.red : primaryColor),
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
              message: widget.bookingTypeToolTip,
              textStyle: const TextStyle(
                fontSize: 20,
                color: Colors.white,
                fontFamily: carlito,
              ),
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
                      Row(
                        children: [
                          if (widget.bookingTypeSlug == "firm" &&
                              widget.cancellationPolicyUrl != "") ...[
                            InkWell(
                              onTap: _openCancellationPolicy,
                              child: Row(
                                children: [
                                  txt20SizeCapitalize(
                                      context: widget.context,
                                      title: widget.policyType,
                                      textColor: primaryColor),
                                  txt20Size(
                                      context: widget.context,
                                      title:
                                          ' (${widget.discountLabel} ${widget.policyRate}%)',
                                      textColor: primaryColor),
                                ],
                              ),
                            ),
                          ] else ...[
                            txt20SizeCapitalize(
                                context: widget.context, title: widget.policyType),
                            if (widget.bookingTypeSlug == "firm") ...[
                              txt20Size(
                                  context: widget.context,
                                  title: ' (${widget.discountLabel} ${widget.policyRate}%)'),
                            ]
                          ]
                        ],
                      ),
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

Widget cancellationPolicyWidget(
    {context,
    String policyType = "",
    double screenWidth = 0.0,
    int policyRate = 0,
    String heading = "Cancellation policy",
    String bookingTypeSlug = "",
    String bookingTypeToolTip = "",
    String discountLabel = "discount",
    String cancellationPolicyUrl = ""}) {
  return CancellationPolicyWidget(
    context: context,
    policyType: policyType,
    screenWidth: screenWidth,
    policyRate: policyRate,
    heading: heading,
    bookingTypeSlug: bookingTypeSlug,
    bookingTypeToolTip: bookingTypeToolTip,
    discountLabel: discountLabel,
    cancellationPolicyUrl: cancellationPolicyUrl,
  );
}
