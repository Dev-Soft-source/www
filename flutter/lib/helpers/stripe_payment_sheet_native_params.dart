import 'package:flutter/services.dart';
import 'package:flutter_stripe/flutter_stripe.dart';

/// Stripe's Dart model serializes Link as `linkDisplayParams` / `linkDisplay`, but
/// `stripe_android` / `stripe_ios` read `link` / `display` (react-native shape).
/// Without this remap, Link stays visible ("Pay with link", save-with-Link, etc.).
///
/// `termsDisplay` is sent as a single enum string from Dart while native expects
/// a map keyed by payment method type (e.g. `card` → `never`).
Map<String, dynamic> patchPaymentSheetParamsForNative(
  Map<String, dynamic> json,
) {
  final out = Map<String, dynamic>.from(json);

  final linkParams = out.remove('linkDisplayParams');
  if (linkParams is Map) {
    final m = Map<String, dynamic>.from(
      linkParams.map((k, v) => MapEntry(k.toString(), v)),
    );
    final display = m['linkDisplay'] ?? m['display'];
    if (display != null) {
      out['link'] = <String, dynamic>{'display': display};
    }
  }

  final terms = out['termsDisplay'];
  if (terms is String) {
    out['termsDisplay'] = <String, String>{'card': terms};
  }

  return out;
}

/// Same channel/codec as [MethodChannelStripeFactory] in stripe_platform_interface.
/// Use this instead of [Stripe.instance.initPaymentSheet] when Link/terms must be
/// honored on native (see [patchPaymentSheetParamsForNative]).
Future<PaymentSheetPaymentOption?> initPaymentSheetNativeJson(
  SetupPaymentSheetParameters params,
) async {
  const channel = MethodChannel(
    'flutter.stripe/payments',
    JSONMethodCodec(),
  );
  final patched = patchPaymentSheetParamsForNative(params.toJson());
  final dynamic raw = await channel.invokeMethod<dynamic>(
    'initPaymentSheet',
    <String, dynamic>{'params': patched},
  );

  if (raw is List) {
    return null;
  }
  if (raw is! Map) {
    throw const StripeError<PaymentSheetError>(
      message: 'initPaymentSheet: unexpected result',
      code: PaymentSheetError.unknown,
    );
  }
  final result = Map<String, dynamic>.from(
    raw.map((k, v) => MapEntry(k.toString(), v)),
  );

  if (result.isEmpty ||
      (result['paymentOption'] == null && result['error'] == null)) {
    return null;
  }
  if (result['paymentOption'] != null) {
    final po = result['paymentOption'];
    if (po is Map) {
      return PaymentSheetPaymentOption.fromJson(
        Map<String, dynamic>.from(
          po.map((k, v) => MapEntry(k.toString(), v)),
        ),
      );
    }
  }
  if (result['error'] != null) {
    result['runtimeType'] = 'failed';
    throw StripeException.fromJson(result);
  }
  throw StripeError<PaymentSheetError>(
    message: 'Unknown initPaymentSheet result: $result',
    code: PaymentSheetError.unknown,
  );
}
