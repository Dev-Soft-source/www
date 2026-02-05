import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/color.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

class ErrorStateWidget extends StatelessWidget {
  final String message;
  final ErrorType errorType;
  final VoidCallback onRetry;
  final String? retryButtonLabel;

  const ErrorStateWidget({
    Key? key,
    required this.message,
    required this.errorType,
    required this.onRetry,
    this.retryButtonLabel,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 24.0, vertical: 40.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            // Error Icon
            Container(
              width: 100,
              height: 100,
              decoration: BoxDecoration(
                color: _getBackgroundColor().withOpacity(0.1),
                shape: BoxShape.circle,
              ),
              child: Icon(
                _getIcon(),
                size: 50,
                color: _getIconColor(),
              ),
            ),
            const SizedBox(height: 24),

            // Error Title
            txt20Size(
              title: _getTitle(),
              fontFamily: bold,
              textColor: textColor,
              context: context,
            ),
            const SizedBox(height: 12),

            // Error Message
            txt16Size(
              title: message,
              fontFamily: regular,
              textColor: placeHolderColor,
              context: context,
            ),
            const SizedBox(height: 32),

            // Retry Button
            SizedBox(
              width: double.infinity,
              height: 50,
              child: ElevatedButton(
                onPressed: onRetry,
                style: ElevatedButton.styleFrom(
                  backgroundColor: btnPrimaryColor,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                  elevation: 0,
                ),
                child: txt16Size(
                  title: retryButtonLabel ?? 'Retry',
                  fontFamily: bold,
                  textColor: Colors.white,
                  context: context,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  IconData _getIcon() {
    switch (errorType) {
      case ErrorType.network:
        return Icons.wifi_off_rounded;
      case ErrorType.server:
        return Icons.cloud_off_rounded;
      case ErrorType.unknown:
        return Icons.error_outline_rounded;
    }
  }

  Color _getIconColor() {
    switch (errorType) {
      case ErrorType.network:
        return warningColor;
      case ErrorType.server:
        return errorColor;
      case ErrorType.unknown:
        return placeHolderColor;
    }
  }

  Color _getBackgroundColor() {
    switch (errorType) {
      case ErrorType.network:
        return warningColor;
      case ErrorType.server:
        return errorColor;
      case ErrorType.unknown:
        return placeHolderColor;
    }
  }

  String _getTitle() {
    switch (errorType) {
      case ErrorType.network:
        return 'No Internet Connection';
      case ErrorType.server:
        return 'Server Error';
      case ErrorType.unknown:
        return 'Something Went Wrong';
    }
  }
}
