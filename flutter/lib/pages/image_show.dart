import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/network_cache_image_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/services/service.dart';

import '../consts/constFileLink.dart';

class ImageShow extends StatelessWidget {
  const ImageShow({super.key});

  @override
  Widget build(BuildContext context) {
    final serviceController = Get.find<Service>();
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: secondAppBarWidget(title: serviceController.imagePreviewLabel.value, context: context),
        leading: const BackButton(
          color: Colors.white,
        ),
      ),
      body: Center(child: networkCacheImageWidget(serviceController.showImage.value, BoxFit.contain, 0.0, 0.0)),
    );
  }
}
