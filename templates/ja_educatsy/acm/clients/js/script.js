/**
 * ------------------------------------------------------------------------
 * Uber Template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */
 
(function($){

	//Add grayscale image for partners 
	$(window).on('load',function() {
		$('.img-grayscale img').each(function() {
			$(this).wrap('<span style="display:inline-block;width:' + this.width + 'px;height:' + this.height + 'px;">').clone().addClass('gotcolors').css({'position': 'absolute', 'opacity' : 0 }).insertBefore(this);
			this.src = grayscale(this.src);
		}).animate({opacity: 0.5}, 500);
	});
	
	$(document).ready(function() {
		$(".img-grayscale .client-item").hover(
			function() {
				$(this).find('.gotcolors').stop().animate({opacity: 1}, 200);
			}, 
			function() {
				$(this).find('.gotcolors').stop().animate({opacity: 0}, 500);
			}
		);
		
	});
	
	function grayscale(src) {
		var supportsCanvas = !!document.createElement('canvas').getContext;
		if (supportsCanvas) {
			var canvas = document.createElement('canvas'), 
			context = canvas.getContext('2d'), 
			imageData, px, length, i = 0, gray, 
			img = new Image();
			
			img.src = src;
			canvas.width = img.width;
			canvas.height = img.height;
			context.drawImage(img, 0, 0);
				
			imageData = context.getImageData(0, 0, canvas.width, canvas.height);
			px = imageData.data;
			length = px.length;
			
			for (; i < length; i += 4) {
				//gray = px[i] * .3 + px[i + 1] * .59 + px[i + 2] * .11;
				//px[i] = px[i + 1] = px[i + 2] = gray;
				px[i] = px[i + 1] = px[i + 2] = (px[i] + px[i + 1] + px[i + 2]) / 3;
			}
					
			context.putImageData(imageData, 0, 0);
			return canvas.toDataURL();
		} else {
			return src;
		}
	}
 })(jQuery);